<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Menu;
use App\Idioma;
use App\Content;
use Illuminate\Support\Facades\Session;
use App\Web;
use App\Company;


//Ruta principal

Route::get('/', function(){
    return redirect('/login');
})->name('principal');

Route::get('/home', function(){
    return redirect('/eunomia/home');
});

//Rutas para gestor de contenido

Auth::routes();

Route::group(['prefix' => 'eunomia' , 'middleware' => 'auth' ], function () {

    Route::get('/', 'HomeController@index')->name('home');

    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('users/password', 'HoraeUserController@password');

    Route::post('users/updatepassword', 'HoraeUserController@updatePassword');

    Route::resource('/users', 'HoraeUserController');

    Route::get('/customers/formularioClientes','HoraeCustomerController@muestraFormularioCliente');

    Route::resource('/customers', 'HoraeCustomerController');

    Route::get('/add_customers','HoraeCustomerController@addCustomers');

    Route::get('/projects/formularioProyectos/{customer_id}','HoraeProjectController@muestraFormularioProyecto');

    Route::resource('/projects', 'HoraeProjectController');

    Route::get('/add_projects/{customer_id?}','HoraeTaskController@addProjects');

    Route::resource('/proyhist', 'HoraeProjectHistController');

    Route::get('/tasks_WhithProject/{project}', 'HoraeTaskController@create_WhithProject')->name('create_WhithProject');

    Route::resource('/hist', 'HoraeTaskHistController');

    Route::resource('/tasks', 'HoraeTaskController');

    Route::get('/calendar', 'HoraeTaskCalendarController@index');

    Route::post('/todo/new','HoraeTodoTaskController@store');
    Route::post('/todo/edit','HoraeTodoTaskController@edit')->name('edit_TodoTask');
    Route::post('/todo/update','HoraeTodoTaskController@update');
    Route::post('/todo/delete','HoraeTodoTaskController@destroy');
    Route::post('/todo/updateOrden','HoraeTodoTaskController@postIndex');

    Route::get('/tasks/calendar_tasks/{user}','HoraeTaskCalendarController@index');

    Route::post('/calendar/update', 'HoraeTaskCalendarController@update')->name('edit_Calendar');

    Route::resource('/comments', 'CommentController');
    Route::resource('/holiday_days','HolidayDayController');
    Route::resource('/party_days','PartyDayController');
    Route::resource('/mailbox','MailboxController');
    Route::post('/mailbox/enviaEmail','MailboxController@enviaEmail')->name('enviaEmail');
    Route::post('/mailbox/subeAdjuntos','MailboxController@subeAdjuntos')->name('subeAdjuntos');

    Route::post('/comments/new', 'CommentController@store')->name('insert_Comment');
    Route::post('/comments/edit', 'CommentController@update')->name('edit_Comment');
    Route::post('/comments/delete', 'CommentController@destroy')->name('delete_Comment');

    Route::resource('/modulos', 'ModuloController');

    Route::post('/permisos/updatePermissionMatrix', 'PermissionController@updatePermissionMatrix')->name('permisos.updatePermissionMatrix');

    Route::get('/permisos/matrix/{id}', 'PermissionController@showPermissionMatrix');

    Route::resource('/permisos', 'PermissionController');

    Route::post('/roles/updateRoleMatrix', 'RolController@updateRoleMatrix')->name('roles.updateRoleMatrix');

    Route::get('/roles/matrix', 'RolController@showRoleMatrix');

    Route::resource('/roles', 'RolController');

    Route::resource('/configuracion','ConfiguracionController');

    Route::resource('/control_accesos','AccessControlController');

    Route::resource('/dominios','DominioController');

    //Menú Admin
    Route::get('/menu_admin', 'MenuAdminController@getIndex');
    Route::post('/menu_admin', 'MenuAdminController@postIndex');

    Route::post('/menu_admin/new', 'MenuAdminController@postNew');
    Route::post('/menu_admin/delete', 'MenuAdminController@postDelete');

    Route::get('/menu_admin/edit/{id}', 'MenuAdminController@getEdit');
    Route::post('/menu_admin/edit/{id}', 'MenuAdminController@postEdit');

    Route::post('/includes/vistaPreview',function($request){
        return view('eunomia.includes.vista_preview','request');
    })->name('vistaPreview');

    Route::post('/includes/ajax/reordenaTabla','HomeController@reordenaTabla')->name('reordenaTabla');

    Route::get('/laravel-filemanager', '\Unisharp\Laravelfilemanager\controllers\LfmController@show');
    Route::post('/laravel-filemanager/upload', '\Unisharp\Laravelfilemanager\controllers\UploadController@upload');

    Route::post('insertaDiasNoDisponibles','HolidayDayController@insertaDiasNoDisponibles')->name('insertaDiasNoDisponibles');
    Route::post('insertaDiasFestivos','PartyDayController@insertaDiasFestivos')->name('insertaDiasFestivos');

    Route::post('devuelveMensajesCarpeta','MailboxController@devuelveMensajesCarpeta')->name('devuelveMensajesCarpeta');
    Route::post('devuelveCarpetas','MailboxController@devuelveCarpetas')->name('devuelveCarpetas');
    Route::post('leerMensaje','MailboxController@leerMensaje')->name('leerMensaje');
    Route::post('descargaArchivo','MailboxController@descargaArchivo')->name('descargaArchivo');
    Route::post('cargaNuevoMensaje','MailboxController@cargaNuevoMensaje')->name('cargaNuevoMensaje');
    Route::post('eliminaMensajes','MailboxController@eliminaMensajes')->name('eliminaMensajes');
    Route::post('changeFlag', 'MailboxController@changeFlag')->name('changeFlag');

    Route::post('enviaMensajeChat', 'HomeController@enviaMensajeChat')->name('enviaMensajeChat');
    Route::post('actualizaChat', 'HomeController@actualizaChat')->name('actualizaChat');
    Route::post('marcaMensajeLeido', 'HomeController@marcaMensajeLeido')->name('marcaMensajeLeido');
    Route::post('cargaComentarios', 'HomeController@cargaComentarios')->name('cargaComentarios');



    Route::post('/customers/insertaClienteDesdeTarea','HoraeCustomerController@store')->name('insertaClienteDesdeTarea');

    Route::post('/customers/insertaProyectoDesdeTarea','HoraeProjectController@store')->name('insertaProyectoDesdeTarea');

    Route::get('tasks/muestraComentarios/{task_id}', 'CommentController@muestraComentariosTarea')->name('muestraComentariosTarea');

    Route::get('projects/muestraComentarios/{project_id}', 'CommentController@muestraComentariosProyecto')->name('muestraComentariosProyecto');

    Route::get('projects/muestraTareasProyecto/{project_id}', 'HoraeProjectController@muestraTareasProyecto')->name('muestraTareasProyecto');

    Route::get('customers/muestraProyectosCliente/{customer_id}', 'HoraeCustomerController@muestraProyectosCliente')->name('muestraProyectosCliente');

    Route::post('fichajes/recargaTiempoTrabajado','FichajeController@recargaTiempoTrabajado')->name('recargaTiempoTrabajado');

    Route::post('fichajes/muestraTablaTiempoTrabajado','FichajeController@muestraTablaTiempoTrabajado')->name('muestraTablaTiempoTrabajado');

    Route::get('fichajes/estableceHoraFichaje','FichajeController@estableceHoraFichaje')->name('estableceHoraFichaje');

    Route::post('fichajes/muestraTiempoTrabajadoSemana','FichajeController@muestraTiempoTrabajadoSemana')->name('muestraTiempoTrabajadoSemana');

    Route::get('fichajes/modificaHoraFichaje/{fichaje_id}','FichajeController@modificaHoraFichaje')->name('modificaHoraFichaje');

    Route::get('fichajes/informeHorasEmpleadoMes/{user_id}/{mes}/{anio}/{informe_completo}','FichajeController@informeHorasEmpleadoMes')->name('informeHorasEmpleadoMes');

    Route::get('fichajes/eligeAnioMesInformeHorasFichaje/{user_id}','FichajeController@eligeAnioMesInformeHorasFichaje')->name('eligeAnioMesInformeHorasFichaje');

    Route::post('fichajes/eligeAnioMesInformeHorasFichajeForm','FichajeController@eligeAnioMesInformeHorasFichajeForm')->name('eligeAnioMesInformeHorasFichajeForm');

    Route::resource('fichajes','FichajeController');

});

// Notifications

//Route::get('webpush','HomeNotificationController@index');

Route::get('subscription', function(){
    return view('web.subscription');
});

Route::get('notification', function(){
    return view('web.notification');
});

Route::post('notifications', 'NotificationController@store');
Route::get('notifications', 'NotificationController@index');
Route::get('notifications/last', 'NotificationController@last');
Route::patch('notifications/{id}/read', 'NotificationController@markAsRead');
Route::post('notifications/mark-all-read', 'NotificationController@markAllRead');
Route::post('notifications/{id}/dismiss', 'NotificationController@dismiss');
// Push Subscriptions
Route::post('subscriptions', 'PushSubscriptionController@update');
Route::post('subscriptions/delete', 'PushSubscriptionController@destroy');
Route::get('subscriptions/enviaNotificacion','PushSubscriptionController@enviaNotificacion');

Route::get('notification_messages/envia_mensajes_fecha','NotificationMessageController@enviaMensajesFecha');
