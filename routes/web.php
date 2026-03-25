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
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


//Ruta principal

Route::get('/', function () {
    return redirect('/login');
})->name('principal');

// Route duplicada, se comenta para evitar confusión:
// Route::get('/home', function () {
//     return redirect('/eunomia/home');
// });

//Rutas para gestor de contenido

Auth::routes(); // Laravel UI instalado - rutas de autenticación activas

Route::group(['prefix' => 'eunomia', 'middleware' => ['web', 'auth']], function () {

    //Nuevo dashboard
    // routes/web.php (dentro del group 'eunomia')
    Route::get('dashboardNuevo', function () {return view('eunomia.dashboardNuevo');})->name('eunomia.dashboardNuevo');
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('eunomia.home');


    // --- Usuarios ---
    Route::get('users/password', [App\Http\Controllers\HoraeUserController::class, 'password'])->name('users.password');
    Route::post('users/updatepassword', [App\Http\Controllers\HoraeUserController::class, 'updatePassword']);
    Route::post('users/{id}/reactivar', [App\Http\Controllers\HoraeUserController::class, 'reactivar']);
    Route::get('users/mentions', [App\Http\Controllers\HoraeUserController::class, 'mentionUsers'])->name('users.mentions');
    Route::resource('/users', App\Http\Controllers\HoraeUserController::class);

    // --- Clientes ---
    Route::get('/customers/formularioClientes', [App\Http\Controllers\HoraeCustomerController::class, 'muestraFormularioCliente']);
    Route::resource('/customers', App\Http\Controllers\HoraeCustomerController::class);
    Route::post('/customers/{id}/restore', [App\Http\Controllers\HoraeCustomerController::class, 'restore'])->name('customers.restore');
    Route::get('/add_customers', [App\Http\Controllers\HoraeCustomerController::class, 'addCustomers']);

    // --- Proyectos ---
    Route::get('/projects/formularioProyectos/{customer_id}', [App\Http\Controllers\HoraeProjectController::class, 'muestraFormularioProyecto']);
    Route::resource('/projects', App\Http\Controllers\HoraeProjectController::class);
    Route::get('/add_projects/{customer_id?}', [App\Http\Controllers\HoraeTaskController::class, 'addProjects']);
    Route::resource('/proyhist', App\Http\Controllers\HoraeProjectHistController::class);

    // --- Tareas ---
    Route::get('/tasks_WhithProject/{project}', [App\Http\Controllers\HoraeTaskController::class, 'create_WhithProject'])->name('create_WhithProject');
    Route::resource('/hist', App\Http\Controllers\HoraeTaskHistController::class);
    Route::resource('/tasks', App\Http\Controllers\HoraeTaskController::class);

    // --- Calendario ---
    Route::get('/calendar', [App\Http\Controllers\HoraeTaskCalendarController::class, 'index'])->name('calendar.index');
    Route::get('/tasks/calendar_tasks/{user}', [App\Http\Controllers\HoraeTaskCalendarController::class, 'index'])->name('calendar.user');
    Route::post('/calendar/update', [App\Http\Controllers\HoraeTaskCalendarController::class, 'update'])->name('edit_Calendar');

    // --- Todo ---
    Route::post('/todo/new',         [App\Http\Controllers\HoraeTodoTaskController::class, 'store'])->name('todo.store');
    Route::post('/todo/edit',        [App\Http\Controllers\HoraeTodoTaskController::class, 'edit'])->name('todo.edit');
    Route::post('/todo/update',      [App\Http\Controllers\HoraeTodoTaskController::class, 'update'])->name('todo.update');
    Route::delete('/todo/delete',      [App\Http\Controllers\HoraeTodoTaskController::class, 'destroy'])->name('todo.delete');
    Route::post('/todo/updateOrden', [App\Http\Controllers\HoraeTodoTaskController::class, 'postIndex'])->name('todo.sort');

    // --- Comentarios ---
    Route::resource('/comments', App\Http\Controllers\CommentController::class);
    Route::post('/comments/new', [App\Http\Controllers\CommentController::class, 'store'])->name('insert_Comment');
    Route::post('/comments/edit', [App\Http\Controllers\CommentController::class, 'update'])->name('edit_Comment');
    Route::post('/comments/delete', [App\Http\Controllers\CommentController::class, 'destroy'])->name('delete_Comment');
    Route::get('tasks/muestraComentarios/{task_id}', [App\Http\Controllers\CommentController::class, 'muestraComentariosTarea'])->name('muestraComentariosTarea');
    Route::get('projects/muestraComentarios/{project_id}', [App\Http\Controllers\CommentController::class, 'muestraComentariosProyecto'])->name('muestraComentariosProyecto');

    // --- Modulos ---
    Route::get('/modulo/{slug}', [App\Http\Controllers\ModuloController::class, 'showBySlug'])->name('modulo.showBySlug');
    Route::resource('/modulos', App\Http\Controllers\ModuloController::class);

    // --- Permisos ---
    Route::post('/permisos/updatePermissionMatrix', [App\Http\Controllers\PermissionController::class, 'updatePermissionMatrix'])->name('permisos.updatePermissionMatrix');
    Route::get('/permisos/matrix/{id}', [App\Http\Controllers\PermissionController::class, 'showPermissionMatrix']);
    Route::resource('/permisos', App\Http\Controllers\PermissionController::class);

    // --- Roles ---
    Route::post('/roles/updateRoleMatrix', [App\Http\Controllers\RolController::class, 'updateRoleMatrix'])->name('roles.updateRoleMatrix');
    Route::get('/roles/matrix', [App\Http\Controllers\RolController::class, 'showRoleMatrix'])->name('roles.matrix');
    Route::resource('/roles', App\Http\Controllers\RolController::class)->names('roles');

    // --- Configuración ---
    Route::resource('/configuracion', App\Http\Controllers\ConfiguracionController::class);

    // --- Control de accesos ---
    Route::resource('/control_accesos', App\Http\Controllers\AccessControlController::class);
    Route::get('control_accesos/historial/{userId}', [App\Http\Controllers\AccessControlController::class, 'getHistorialUsuario'])->name('control_accesos.historial');

    // --- Dominios ---
    Route::resource('/dominios', App\Http\Controllers\DominioController::class);

    // --- Menú Admin ---
    Route::get('/menu_admin', [App\Http\Controllers\MenuAdminController::class, 'getIndex'])->name('menu_admin');
    Route::post('/menu_admin', [App\Http\Controllers\MenuAdminController::class, 'postIndex']);
    Route::post('/menu_admin/new', [App\Http\Controllers\MenuAdminController::class, 'postNew']);
    Route::post('/menu_admin/delete', [App\Http\Controllers\MenuAdminController::class, 'postDelete']);
    Route::get('/menu_admin/edit/{id}', [App\Http\Controllers\MenuAdminController::class, 'getEdit']);
    Route::post('/menu_admin/edit/{id}', [App\Http\Controllers\MenuAdminController::class, 'postEdit']);

    // --- Includes y utilidades ---
    Route::post('/includes/vistaPreview', function ($request) {
        return view('eunomia.includes.vista_preview', ['request' => $request]);
    })->name('vistaPreview');
    Route::post('/includes/ajax/reordenaTabla', [App\Http\Controllers\HomeController::class, 'reordenaTabla'])->name('reordenaTabla');

    // --- Filemanager ---
    Route::get('/laravel-filemanager', [\Unisharp\Laravelfilemanager\controllers\LfmController::class, 'show']);
    Route::post('/laravel-filemanager/upload', [\Unisharp\Laravelfilemanager\controllers\UploadController::class, 'upload']);

    // --- Holiday y Party Days ---
    Route::resource('/holiday_days', App\Http\Controllers\HolidayDayController::class);
    Route::resource('/party_days', App\Http\Controllers\PartyDayController::class);
    Route::post('insertaDiasNoDisponibles', [App\Http\Controllers\HolidayDayController::class, 'insertaDiasNoDisponibles'])->name('insertaDiasNoDisponibles');
    Route::post('insertaDiasFestivos', [App\Http\Controllers\PartyDayController::class, 'insertaDiasFestivos'])->name('insertaDiasFestivos');

    // --- Mailbox ---
    Route::resource('/mailbox', App\Http\Controllers\MailboxController::class);
    Route::post('/mailbox/enviaEmail', [App\Http\Controllers\MailboxController::class, 'enviaEmail'])->name('enviaEmail');
    Route::post('/mailbox/subeAdjuntos', [App\Http\Controllers\MailboxController::class, 'subeAdjuntos'])->name('subeAdjuntos');
    Route::post('devuelveMensajesCarpeta', [App\Http\Controllers\MailboxController::class, 'devuelveMensajesCarpeta'])->name('devuelveMensajesCarpeta');
    Route::post('devuelveCarpetas', [App\Http\Controllers\MailboxController::class, 'devuelveCarpetas'])->name('devuelveCarpetas');
    Route::post('leerMensaje', [App\Http\Controllers\MailboxController::class, 'leerMensaje'])->name('leerMensaje');
    Route::post('descargaArchivo', [App\Http\Controllers\MailboxController::class, 'descargaArchivo'])->name('descargaArchivo');
    Route::post('cargaNuevoMensaje', [App\Http\Controllers\MailboxController::class, 'cargaNuevoMensaje'])->name('cargaNuevoMensaje');
    Route::post('eliminaMensajes', [App\Http\Controllers\MailboxController::class, 'eliminaMensajes'])->name('eliminaMensajes');
    Route::post('changeFlag', [App\Http\Controllers\MailboxController::class, 'changeFlag'])->name('changeFlag');

    // --- Otros AJAX y utilidades ---
    Route::post('cargaComentarios', [App\Http\Controllers\HomeController::class, 'cargaComentarios'])->name('cargaComentarios');
    Route::post('/customers/insertaClienteDesdeTarea', [App\Http\Controllers\HoraeCustomerController::class, 'store'])->name('insertaClienteDesdeTarea');
    Route::post('/customers/insertaProyectoDesdeTarea', [App\Http\Controllers\HoraeProjectController::class, 'store'])->name('insertaProyectoDesdeTarea');
    Route::get('projects/muestraTareasProyecto/{project_id}', [App\Http\Controllers\HoraeProjectController::class, 'muestraTareasProyecto'])->name('muestraTareasProyecto');
    Route::get('customers/muestraProyectosCliente/{customer_id}', [App\Http\Controllers\HoraeCustomerController::class, 'muestraProyectosCliente'])->name('muestraProyectosCliente');

    // --- Fichajes ---
    Route::post('fichajes/recargaTiempoTrabajado', [App\Http\Controllers\FichajeController::class, 'recargaTiempoTrabajado'])->name('recargaTiempoTrabajado');
    Route::post('fichajes/muestraTablaTiempoTrabajado', [App\Http\Controllers\FichajeController::class, 'muestraTablaTiempoTrabajado'])->name('muestraTablaTiempoTrabajado');
    Route::get('fichajes/estableceHoraFichaje', [App\Http\Controllers\FichajeController::class, 'estableceHoraFichaje'])->name('estableceHoraFichaje');
    Route::post('fichajes/muestraTiempoTrabajadoSemana', [App\Http\Controllers\FichajeController::class, 'muestraTiempoTrabajadoSemana'])->name('muestraTiempoTrabajadoSemana');
    //Ver esta otra si existe y funciona correctamente.
    Route::get('fichajes/modificaHoraFichaje/{fichaje_id}', [App\Http\Controllers\FichajeController::class, 'modificaHoraFichaje'])->name('modificaHoraFichaje');
    // Allow PATCH/POST to the modal URL to update the fichaje (avoids MethodNotAllowed when JS/form target is the modal URL)
    Route::match(['patch', 'post'], 'fichajes/modificaHoraFichaje/{fichaje_id}', [App\Http\Controllers\FichajeController::class, 'update']);
    Route::get('fichajes/informeHorasEmpleadoMes/{user_id}/{mes}/{anio}/{informe_completo}', [App\Http\Controllers\FichajeController::class, 'informeHorasEmpleadoMes'])->name('informeHorasEmpleadoMes');
    Route::get('fichajes/eligeAnioMesInformeHorasFichaje/{user_id}', [App\Http\Controllers\FichajeController::class, 'eligeAnioMesInformeHorasFichaje'])->name('eligeAnioMesInformeHorasFichaje');
    Route::post('fichajes/eligeAnioMesInformeHorasFichajeForm', [App\Http\Controllers\FichajeController::class, 'eligeAnioMesInformeHorasFichajeForm'])->name('eligeAnioMesInformeHorasFichajeForm');
    Route::get('fichajes/get/{userId}', [App\Http\Controllers\FichajeController::class, 'getFichajesUsuario'])->name('fichajes.get');
    // Página para modificar fichajes de un usuario (por userId)
    Route::get('fichajes/modificar/{userId}', [App\Http\Controllers\FichajeController::class, 'modificarFichajesUsuario'])->name('fichajes.modificar');
    Route::resource('fichajes', App\Http\Controllers\FichajeController::class);
});



// // Notifications

// // Route::get('webpush','HomeNotificationController@index');

// Route::get('subscription', function(){
//     return view('web.subscription');
// });

// Route::get('notification', function(){
//     return view('web.notification');
// });

// Route::post('notifications', 'NotificationController@store');
// Route::get('notifications', 'NotificationController@index');
// Route::get('notifications/last', 'NotificationController@last');
// Route::patch('notifications/{id}/read', 'NotificationController@markAsRead');
// Route::post('notifications/mark-all-read', 'NotificationController@markAllRead');
// Route::post('notifications/{id}/dismiss', 'NotificationController@dismiss');
// // Push Subscriptions
// Route::post('subscriptions', 'PushSubscriptionController@update');
// Route::post('subscriptions/delete', 'PushSubscriptionController@destroy');
// Route::get('subscriptions/enviaNotificacion','PushSubscriptionController@enviaNotificacion');

// Route::get('notification_messages/envia_mensajes_fecha','NotificationMessageController@enviaMensajesFecha');
