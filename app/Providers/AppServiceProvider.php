<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\DB;
use App\MenuAdmin;
use App\Employee;
use App\User;
use Session;
use Auth;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    protected $carpeta_admin = 'eunomia'.'/';
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        Schema::defaultStringLength(191);

        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            try {
                if (\Auth::user()->compruebaSeguridad('mostrar-elementos-menu-admin') == true){
                    //Este nodo siempre se pinta si no se han definido permisos para poder crear la estructura del panel de control
                    $event->menu->add(['header' => 'ADMINISTRACIÓN']);
                    $event->menu->add([
                        'text' => 'Menú Administración',
                        'url' => $this->carpeta_admin . 'menu_admin',
                        'icon' => 'fas fa-fw fa-bars',
                    ]);
                }
                
                $elements = MenuAdmin::orderBy('order')->get();
                foreach($elements as $element) {
                    if ($element->separator) {
                        if ($element->label) {
                            $event->menu->add(['header' => strtoupper($element->label)]);
                        }
                    } else {
                        if ($element->table != '')
                            $label = DB::table($element->table)->count();
                        else
                            $label = '';
                            
                        if (\Auth::user()->compruebaSeguridadMenu($element->modulo_id)) {
                            // Validar que los campos requeridos existan
                            if (!$element->label || !$element->url) {
                                continue;
                            }
                            
                            $menuItem = [
                                'text' => $element->label,
                                'url' => $this->carpeta_admin . $element->url,
                            ];
                            
                            // Icon es opcional pero recomendado
                            if ($element->icon) {
                                $menuItem['icon'] = 'fas fa-fw fa-' . $element->icon;
                            }
                            
                            // Solo agregar label si tiene valor
                            if ($label !== '') {
                                $menuItem['label'] = $label;
                                
                                // Mapeo de colores para badges
                                $colorMap = [
                                    'primary' => 'primary',
                                    'success' => 'success', 
                                    'info' => 'info',
                                    'warning' => 'warning',
                                    'danger' => 'danger',
                                    'default' => 'secondary',
                                    'secondary' => 'secondary',
                                ];
                                
                                $labelColor = $element->label_color;
                                if ($labelColor && isset($colorMap[$labelColor])) {
                                    $menuItem['label_color'] = $colorMap[$labelColor];
                                } elseif ($labelColor) {
                                    $menuItem['label_color'] = $labelColor;
                                }
                            }
                            
                            $event->menu->add($menuItem);
                        }
                    }
                }
                
                //Añadimos los enlaces a la gestión de la cuenta del usuario (Perfil y cambio de contraseña)
                if(!\Auth::user()->isRole('cliente')) {
                    $event->menu->add([
                        'text' => 'Perfil',
                        'url' => $this->carpeta_admin . 'users/'.\Auth::user()->id.'/edit',
                        'icon' => 'fas fa-fw fa-user',
                    ]);
                }
                
                $event->menu->add([
                    'text' => 'Cambiar contraseña',
                    'url' => $this->carpeta_admin . 'users/password',
                    'icon' => 'fas fa-fw fa-key',
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Error building menu: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
