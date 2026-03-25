<?php

namespace App\Http\Controllers;

use App\Mailbox;
use App\MailMessage;
use App\MailMessageFlag;
use Illuminate\Http\Request;
use Webklex\IMAP\Client;
use Carbon\Carbon;
use Webklex\IMAP\Message;
use App\Userdata;
use Validator;
use File;
use Response;
use Storage;

class MailboxController extends Controller
{
    private $validate_cert = false;

    public function conexion()
    {
        $conexion = \Auth::user()->userdata;
        
        // Verificar que existe configuración y que los campos obligatorios están completos
        if (is_object($conexion) && 
            !empty($conexion->mail_host) && 
            !empty($conexion->mail_port) && 
            !empty($conexion->mail_username) && 
            !empty($conexion->mail_password)) {
            
            return new Client([
                'host' => $conexion->mail_host, 
                'port' => $conexion->mail_port, 
                'encryption' => $conexion->mail_encryption == 1 ? 'ssl' : false, 
                'validate_cert' => $this->validate_cert, 
                'username' => $conexion->mail_username, 
                'password' => $conexion->mail_password, 
                'message_limit' => $conexion->mail_message_limit ?: 50
            ]);
        } else {
            return null;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $oClient = $this->conexion();

        if (!is_object($oClient)) {
            // Redirigir a la configuración de usuario con un mensaje
            return redirect()->route('users.edit', ['user' => \Auth::user()->id])
                ->with('warning', 'Debes configurar tu cuenta de email en la pestaña "Configuración mail" antes de acceder al buzón.');
        }

        try {
            //Connect to the IMAP Server
            $oClient->connect();

            $oFolder = $oClient->getFolder('INBOX', 32);

            $message_limit = \Auth::user()->userdata->mail_message_limit ?: 50;
            $oNewMessages = $oFolder->getMessages("ALL", true, true, true, true, $message_limit);

            ini_set('memory_limit', '200M');

            return view('eunomia.mailbox.mailbox', compact('oFolder', 'oNewMessages'));
            
        } catch (\Exception $e) {
            // Si hay error de conexión, redirigir con mensaje específico
            return redirect()->route('users.edit', ['user' => \Auth::user()->id])
                ->with('error', 'Error de conexión al servidor de email: ' . $e->getMessage() . '. Verifica tu configuración en "Configuración mail".');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Mailbox $mailbox
     * @return \Illuminate\Http\Response
     */
    public function show(Mailbox $mailbox)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Mailbox $mailbox
     * @return \Illuminate\Http\Response
     */
    public function edit(Mailbox $mailbox)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Mailbox $mailbox
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mailbox $mailbox)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Mailbox $mailbox
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mailbox $mailbox)
    {
        //
    }

    public function devuelveMensajesCarpeta(Request $request)
    {
        $folder = $request->folder;
        $page = $request->page;

        $oClient = $this->conexion();

        $userdata = \Auth::user()->userdata;

        //Connect to the IMAP Server
        $oClient->connect();

        $oFolder = $oClient->getFolder($folder);

        $oClient->openFolder($oFolder->path);

        $message_limit = \Auth::user()->userdata->mail_message_limit;

        $total_mensajes = $oClient->countMessages();

        $oMessages = $oFolder->getMessages('ALL', null, false, false, true, $userdata->mail_message_limit>0?$userdata->mail_message_limit:25, $page);
        //dd($oMessages);
        //Metemos los nuevos mensajes en la tabla de mensajes

        foreach($oMessages as $message){
            $message_id = $message->getMessageId();
            $user_id = \Auth::user()->id;

            $mailMessage = MailMessage::where('user_id',$user_id)
                ->where('message_id', $message_id)->first();

            //dd($message);

            if (!is_object($mailMessage)){
                $from = '';
                if (count($message->getFrom()) > 0)
                    foreach($message->getFrom() as $elemento){
                        if ($from != '')
                            $from .= '::';
                        $from .= utf8_encode($elemento->full);
                    }

                $to = '';
                if (count($message->getTo()) > 0)
                    foreach($message->getTo() as $elemento){
                        if ($to != '')
                            $to .= '::';
                        $to .= utf8_encode($elemento->full);
                    }

                $cc = '';
                if (count($message->getCc()) > 0)
                    foreach($message->getCc() as $elemento){
                        if ($cc != '')
                            $cc .= '::';
                        $cc .= utf8_encode($elemento->full);
                    }

                $bcc = '';
                if (count($message->getBcc()) > 0)
                    foreach($message->getBcc() as $elemento){
                        if ($bcc != '')
                            $bcc .= '::';
                        $bcc .= utf8_encode($elemento->full);
                    }

                $reply_to = '';
                if (count($message->getReplyTo()) > 0)
                    foreach($message->getReplyTo() as $elemento){
                        if ($reply_to != '')
                            $reply_to .= '::';
                        $reply_to .= utf8_encode($elemento->full);
                    }

                $in_reply_to = '';
                if (is_array($message->getInReplyTo()) && count($message->getInReplyTo()) > 0)
                    foreach($message->getInReplyTo() as $elemento){
                        if ($in_reply_to != '')
                            $in_reply_to .= '::';
                        $in_reply_to .= utf8_encode($elemento->full);
                    }

                $sender = '';
                if (count($message->getSender()) > 0)
                    foreach($message->getSender() as $elemento){
                        if ($sender != '')
                            $sender .= '::';
                        $sender .= utf8_encode($elemento->full);
                    }

                $mailMessage = new MailMessage;

                $mailMessage->user_id = $user_id;
                $mailMessage->message_id = $message_id;
                $mailMessage->message_no = $message->getMsgn();
                $mailMessage->folder = $oFolder->name;
                $mailMessage->subject = utf8_encode($message->getSubject());
                $mailMessage->date = Carbon::parse($message->getDate())->format('Y-m-d H:i:s');
                $mailMessage->from = $from;
                $mailMessage->to = $to;
                $mailMessage->cc = $cc;
                $mailMessage->bcc = $bcc;
                $mailMessage->reply_to = $reply_to;
                $mailMessage->in_reply_to = $in_reply_to;
                $mailMessage->sender = $sender;
                $mailMessage->references = $message->getReferences();
                $mailMessage->uid = $message->getUid();
                $mailMessage->msglist = $message->getMsglist();
                //dd($mailMessage);

                try {
                    $mailMessage->save();
                } catch (\Exception $e){
                    dd($e);
                }

                //Flags
                $mailMessageFlag = new MailMessageFlag;

                $mailMessageFlag->message_id = $message_id;

            } else {
                $mailMessageFlag = MailMessageFlag::where('message_id', $message_id)->first();
                
                // Si no existe el flag, crear uno nuevo
                if (!$mailMessageFlag) {
                    $mailMessageFlag = new MailMessageFlag;
                    $mailMessageFlag->message_id = $message_id;
                }
            }

            // Verificar si los métodos están disponibles antes de usarlos
            $mailMessageFlag->recent = method_exists($message, 'isRecent') && $message->isRecent() ? 1 : 0;
            $mailMessageFlag->flagged = method_exists($message, 'isFlagged') && $message->isFlagged() ? 1 : 0;
            $mailMessageFlag->answered = method_exists($message, 'isAnswered') && $message->isAnswered() ? 1 : 0;
            $mailMessageFlag->deleted = method_exists($message, 'isDeleted') && $message->isDeleted() ? 1 : 0;
            $mailMessageFlag->seen = method_exists($message, 'isSeen') && $message->isSeen() ? 1 : 0;
            $mailMessageFlag->draft = method_exists($message, 'isDraft') && $message->isDraft() ? 1 : 0;

            $mailMessageFlag->save();
        }

        return view('eunomia.mailbox.messages', compact('oMessages', 'oFolder', 'page', 'total_mensajes', 'message_limit'));
    }

    public function devuelveCarpetas()
    {
        $oClient = $this->conexion();

        //Connect to the IMAP Server
        $oClient->connect();

        //Get all Mailboxes
        $aMailboxes = $oClient->getFolders();
        ini_set('memory_limit', '200M');
        
        // Calcular contadores de forma segura - solo para INBOX por ahora
        foreach ($aMailboxes as $mailbox) {
            $mailbox->unread_count = 0; // Por ahora, hardcodeado
        }

        return view('eunomia.mailbox.folders', compact('aMailboxes'));
    }

    public function leerMensaje(Request $request)
    {
        $oClient = $this->conexion();

        //Connect to the IMAP Server
        $oClient->connect();

        $oFolder = $oClient->getFolder($request->folder);

        $page = $request->page;

        $oMessage = $oFolder->getMessage($request->uid, null, null, true, true);
        
        // Marcar el mensaje como leído
        try {
            if (method_exists($oMessage, 'setFlag')) {
                $oMessage->setFlag('Seen');
            }
        } catch (\Exception $e) {
            // Fallar silenciosamente si no se puede marcar como leído
        }
        
        $oAttachments = $oMessage->getAttachments();
        $attachmentPath = storage_path('app/public/attachments');
        $mensaje = MailMessage::where('message_id', $oMessage->getMessageId())->first();
        if ($request->mostrarimagenes){
            $mensaje->images = 1;
            $mensaje->save();
        }

        $images = $mensaje->images?true:false;
        $body = $oMessage->hasHTMLBody() ? $oMessage->getHTMLBody($images) : ($oMessage->getTextBody());
        if (!$oMessage->hasHTMLBody()) {
            $body = $this->textFormated($body) . PHP_EOL;
            //$body = str_replace('');
        }

        return view('eunomia.mailbox.read_mail', compact('oMessage', 'oAttachments', 'oFolder', 'page', 'attachmentPath', 'body','images'));
    }

    public function descargaArchivo(Request $request)
    {
        $filename = $request->filename;
        $folder = $request->folder;
        $uid = $request->uid;
        $strFileType = strrev(substr(strrev($filename), 0, 4));

        //Buscamos el adjunto del mensaje
        $oClient = $this->conexion();

        //Connect to the IMAP Server
        $oClient->connect();

        $oFolder = $oClient->getFolder($folder);

        //$oMessages = $oFolder->getMessages();

        //dd($request);

        $oMessage = $oFolder->getMessage($uid, null, null, true, true);

        $fileContent = '';

        $oAttachments = $oMessage->getAttachments();
        foreach ($oAttachments as $oAttachment) {
            if ($oAttachment->name == $filename) {
                //dd(Storage::disk('mail_attachments')->getAdapter()->getPathPrefix());
                $oAttachment->save(Storage::disk('mail_attachments')->getAdapter()->getPathPrefix(),$filename);
                $contentType = $oAttachment->getContentType();

                // Define headers
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$filename");
                header("Content-Type: $contentType");
                header("Content-Transfer-Encoding: binary");

                // Read the file
                readfile(Storage::disk('mail_attachments')->getAdapter()->getPathPrefix() . $filename);
                exit;
            }
        }
    }

    public function getContentType($strFileType)
    {
        $ContentType = "application/octet-stream";

        if ($strFileType == ".asf") $ContentType = "video/x-ms-asf";
        if ($strFileType == ".avi") $ContentType = "video/avi";
        if ($strFileType == ".doc") $ContentType = "application/msword";
        if ($strFileType == ".zip") $ContentType = "application/zip";
        if ($strFileType == ".xls") $ContentType = "application/vnd.ms-excel";
        if ($strFileType == ".gif") $ContentType = "image/gif";
        if ($strFileType == ".jpg" || $strFileType == "jpeg") $ContentType = "image/jpeg";
        if ($strFileType == ".wav") $ContentType = "audio/wav";
        if ($strFileType == ".mp3") $ContentType = "audio/mpeg3";
        if ($strFileType == ".mpg" || $strFileType == "mpeg") $ContentType = "video/mpeg";
        if ($strFileType == ".rtf") $ContentType = "application/rtf";
        if ($strFileType == ".htm" || $strFileType == "html") $ContentType = "text/html";
        if ($strFileType == ".xml") $ContentType = "text/xml";
        if ($strFileType == ".xsl") $ContentType = "text/xsl";
        if ($strFileType == ".css") $ContentType = "text/css";
        if ($strFileType == ".php") $ContentType = "text/php";
        if ($strFileType == ".asp") $ContentType = "text/asp";
        if ($strFileType == ".pdf") $ContentType = "application/pdf";

        return $ContentType;
    }

    public function cargaNuevoMensaje()
    {
        return view('eunomia.mailbox.compose_mail');
    }

    public function eliminaMensajes(Request $request)
    {
        $oClient = $this->conexion();

        //Connect to the IMAP Server
        $oClient->connect();

        $uids = $request->uids;
        $oMessage = new Message(null, $uids, $oClient);
        $oMessage->move('INBOX.Trash');
    }

    public function subeAdjuntos(Request $request)
    {
        try {
            $input = $request->all();
            $rules = array('file' => 'file|max:3000'); // Cambio: file en lugar de image para permitir más tipos

            $validation = Validator::make($input, $rules);

            if ($validation->fails()) {
                return Response::make($validation->errors()->first(), 400);
            }

            $file = $request->file('file');
            
            if (!$file) {
                return Response::json('No file uploaded', 400);
            }

            $extension = $file->getClientOriginalExtension();
            $filename = sha1(time() . uniqid()) . ".{$extension}";
            
            // Usar el disco público en lugar de mail_attachments
            $path = $file->storeAs('mail_attachments', $filename, 'public');

            if ($path) {
                return Response::json([
                    'success' => true,
                    'filename' => $filename,
                    'path' => $path
                ], 200);
            } else {
                return Response::json('Upload failed', 400);
            }
        } catch (\Exception $e) {
            return Response::json('Error: ' . $e->getMessage(), 500);
        }
    }

    public function enviaEmail(Request $request)
    {
        try {
            // Validar los datos del formulario
            $request->validate([
                'to' => 'required|email',
                'subject' => 'required|string',
                'message' => 'required|string'
            ]);

            // Configurar el correo usando la configuración de Laravel
            $to = $request->input('to');
            $subject = $request->input('subject');
            $message = $request->input('message');
            
            // Usar Mail de Laravel para enviar el correo
            \Mail::raw($message, function($mail) use ($to, $subject) {
                $mail->to($to)
                     ->subject($subject)
                     ->from(config('mail.from.address'), config('mail.from.name'));
            });

            return response()->json([
                'success' => true,
                'message' => 'Correo enviado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changeFlag(Request $request)
    {
        $oClient = $this->conexion();

        $flag = $request->flag;
        $messageId = $request->messageId;
        $folder = $request->folder;
        $set = $request->set;
        $oFolder = $oClient->getFolder($folder);
        $oMessage = $oFolder->getMessage($messageId);
        if ($set) {
            $oMessage->setFlag($flag);
        } else
            $oMessage->unsetFlag($flag);
    }

    function textFormated($string=""){
        // normalizamos los saltos de línea
        $string = str_replace(["\r\n", "\r"], "\n", $string);
        // creamos un array de parrafos
        $strParrafos = explode("\n", $string);
        $string = '';
        //dd($strParrafos);
        foreach ($strParrafos as $parrafo){
            if ($parrafo != ''){
                $string .= '<span style="white-space:nowrap">' . $parrafo . '<br>';
            } else {
                $string .= '<br>';
            }
        }
        return $string;
    }

}
