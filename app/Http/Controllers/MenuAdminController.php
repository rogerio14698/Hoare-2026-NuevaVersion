<?php

namespace App\Http\Controllers;

use App\MenuAdmin;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use App\Departamento;
use App\Permission;
use App\Modulo;

class MenuAdminController extends Controller
{
    /**
     * Función para comparar dos índices entre dos arrays diferentes y así poder ordenarlos.
     * Utilizada para la lista de iconos de Awesome.
     */
    public static function cmp($a, $b)
    {
        $ia = isset($a['id']) ? (string)$a['id'] : '';
        $ib = isset($b['id']) ? (string)$b['id'] : '';
        return strcmp($ia, $ib);
    }


    public function getIndex()
    {
        if (!\Auth::user()->compruebaSeguridad('mostrar-elementos-menu-admin')) {
            return view('eunomia.mensajes.mensaje_error')->with('msj', '..no tiene permisos para acceder a esta sección');
        }

        $items = MenuAdmin::orderBy('order')->get();
        $menu  = (new MenuAdmin)->getHTML($items);

        $yamlPath = public_path('awesomeicons.yml');
        if (!is_file($yamlPath)) {
            abort(500, 'No se encuentra awesomeicons.yml en ' . $yamlPath);
        }

        try {
            $data = Yaml::parseFile($yamlPath) ?: [];
        } catch (ParseException $e) {
            \Log::error('YAML inválido en awesomeicons.yml: ' . $e->getMessage());
            $data = [];
        }

        // Normaliza a array plano de íconos
        $icons = isset($data['icons']) && is_array($data['icons']) ? $data['icons'] : [];
        usort($icons, [self::class, 'cmp']);

        $tables  = DB::select('SHOW TABLES');
        $modulos = Modulo::orderBy('nombre')->pluck('nombre', 'id');

        // Nota: pasamos $icons PLANO
        return view('eunomia.menu_admin.builder', compact('items', 'menu', 'icons', 'tables', 'modulos'));
    }


    static function sort_by_orden($a, $b)
    {
        return $a['id'] - $b['id'];
    }

    public function getEdit($id)
    {
        if (!\Auth::user()->compruebaSeguridad('editar-elemento-menu-admin')) {
            return view('eunomia.mensajes.mensaje_error')->with('msj', '..no tiene permisos para acceder a esta sección');
        }

        $item = MenuAdmin::findOrFail($id);

        $yamlPath = public_path('awesomeicons.yml');
        if (!is_file($yamlPath)) {
            abort(500, 'No se encuentra awesomeicons.yml en ' . $yamlPath);
        }

        try {
            $data = Yaml::parseFile($yamlPath) ?: [];
        } catch (ParseException $e) {
            \Log::error('YAML inválido en awesomeicons.yml: ' . $e->getMessage());
            $data = [];
        }

        $icons = isset($data['icons']) && is_array($data['icons']) ? $data['icons'] : [];
        usort($icons, [self::class, 'cmp']);

        $tables  = DB::select('SHOW TABLES');
        $modulos = Modulo::orderBy('nombre')->pluck('nombre', 'id');

        // Nota: pasamos $icons PLANO
        return view('eunomia.menu_admin.edit', compact('item', 'icons', 'tables', 'modulos'));
    }


    public function postEdit(Request $request)
    {
        if (\Auth::user()->compruebaSeguridad('editar-elemento-menu-admin') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj', '..no tiene permisos para acceder a esta sección');
        $item = MenuAdmin::find($request->id);
        $item->title         = $request->title;
        $item->label        = $request->title;
        $item->icon         = $request->icon;
        $item->label_color  = $request->label_color;

        $item->url = $request->url;
        $item->modulo_id = $request->modulo_id;
        $item->table = $request->table;

        if ($request->separator == 1)
            $item->separator = 1;
        else
            $item->separator = 0;

        if ($request->visible == 1)
            $item->visible = 1;
        else
            $item->visible = 0;

        $item->save();

        return redirect("eunomia/menu_admin");
    }

    // AJAX Reordering function
    public function postIndex(Request $request)
    {
        if (\Auth::user()->compruebaSeguridad('editar-elemento-menu-admin') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj', '..no tiene permisos para acceder a esta sección');
        //$source       = e(Input::get('source'));
        //$destination  = e(Input::get('destination',0));
        $source = $request->source;
        $destination = $request->destination;

        $item             = MenuAdmin::findOrFail($source);
        $item->parent_id  = $destination;
        $item->save();

        //$ordering       = json_decode(Input::get('order'));
        //$rootOrdering   = json_decode(Input::get('rootOrder'));

        $ordering       = json_decode($request->order);
        $rootOrdering   = json_decode($request->rootOrder);

        if ($ordering) {
            foreach ($ordering as $order => $item_id) {
                if ($itemToOrder = MenuAdmin::findOrFail($item_id)) {
                    $itemToOrder->order = $order;
                    $itemToOrder->save();
                }
            }
        } else {
            foreach ($rootOrdering as $order => $item_id) {
                if ($itemToOrder = MenuAdmin::findOrFail($item_id)) {
                    $itemToOrder->order = $order;
                    $itemToOrder->save();
                }
            }
        }

        return 'ok ';
    }

    public function postNew(Request $request)
    {
        if (\Auth::user()->compruebaSeguridad('crear-elemento-menu-admin') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj', '..no tiene permisos para acceder a esta sección');
        // Create a new menu item and save it
        $item = new MenuAdmin;

        $item->title         = $request->title;
        $item->label        = $request->title;
        $item->icon         = $request->icon;
        $item->label_color  = $request->label_color;

        $item->url = $request->url;
        $item->order     = MenuAdmin::max('order') + 1;
        $item->modulo_id = $request->modulo_id;
        $item->table = $request->table;

        if ($request->separator == 1)
            $item->separator = 1;
        else
            $item->separator = 0;

        if ($request->visible == 1)
            $item->visible = 1;
        else
            $item->visible = 0;

        $item->save();

        return redirect('eunomia/menu_admin');
    }

    public function postDelete(Request $request)
    {
        if (\Auth::user()->compruebaSeguridad('eliminar-elemento-menu-admin') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj', '..no tiene permisos para acceder a esta sección');
        $id = $request->delete_id;
        // Find all items with the parent_id of this one and reset the parent_id to zero
        $items = MenuAdmin::where('parent_id', $id)->get()->each(function ($item) {
            $item->parent_id = 0;
            $item->save();
        });

        // Find and delete the item that the user requested to be deleted
        $item = MenuAdmin::findOrFail($id);
        $item->delete();

        return redirect('eunomia/menu_admin');
    }
}
