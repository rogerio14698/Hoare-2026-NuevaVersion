<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class MenuAdmin extends Model
{
    protected $table = 'menu_admin';

    /**
     * Genera la estructura HTML basada en el nuevo diseño horizontal.
     */
    public function getHTML($items)
    {
        $html = '<div class="header">';
        $html .= '<div class="logo"><h1>horae</h1></div>';
        $html .= '<nav class="menu-horizontal">';

        // Filtramos los elementos raíz (parent_id = 0)
        $roots = $items->where('parent_id', 0)->sortBy('order');

        foreach ($roots as $item) {
            // Si el item es un separador, creamos un nuevo grupo de menú
            if ($item->separator == '1') {
                $html .= '<div class="menu-group">';
                $html .= '<span class="group-title">' . e($item->label) . '</span>';
                $html .= '<ul>';
                
                // Buscamos los hijos de este separador
                $children = $items->where('parent_id', $item->id)->sortBy('order');
                foreach ($children as $child) {
                    $html .= $this->renderMenuItem($child, $items);
                }

                $html .= '</ul>';
                $html .= '</div>';
            }
        }

        // Agregamos la sección de perfil al final del nav
        $html .= $this->renderProfileSection();

        $html .= '</nav>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Renderiza un <li> individual y sus submenús si los tiene.
     */
    private function renderMenuItem($item, $allIcons)
    {
        $hasChildren = $allIcons->where('parent_id', $item->id)->count() > 0;
        $class = $hasChildren ? 'class="has-children"' : '';
        $url = url('eunomia/' . $item->url);
        
        $res = "<li $class>";
        $res .= "<a href='{$url}' style='color:inherit; text-decoration:none;'>";
        
        if ($item->icon) {
            $res .= "<i class='fas fa-fw fa-{$item->icon}'></i> ";
        }
        
        $res .= e($item->label) . "</a>";

        if ($hasChildren) {
            $res .= '<ul class="submenu">';
            foreach ($allIcons->where('parent_id', $item->id)->sortBy('order') as $subChild) {
                $res .= $this->renderMenuItem($subChild, $allIcons);
            }
            $res .= '</ul>';
        }

        $res .= "</li>";
        return $res;
    }

    /**
     * Genera el bloque de perfil del usuario actual.
     */
    private function renderProfileSection()
    {
        $user = Auth::user();
        $userName = $user ? $user->name : 'Invitado';
        // Avatar se guarda en images/avatar/ con solo el nombre del archivo
        $photo = ($user && $user->avatar) ? asset('images/avatar/'.$user->avatar) : 'https://via.placeholder.com/35';

        return "
            <div class='menu-perfil'>
                <img src='{$photo}' alt='User'>
                <div class='seccion-perfil'>
                    <p>{$userName}</p>
                    <p>Online</p>
                </div>
            </div>";
    }
}