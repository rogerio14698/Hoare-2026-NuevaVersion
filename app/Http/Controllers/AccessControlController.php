<?php

namespace App\Http\Controllers;

use App\AccessControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;

use App\User;


class AccessControlController extends Controller
{
    public function index()
    {
        /** @var \App\User|null $user */
        $user = Auth::user();

        // Seguridad: solo usuarios autenticados y con permiso
        if (!Auth::check() || !$user || !$user->compruebaSeguridad('mostrar-control-de-accesos')) {
            return view('eunomia.mensajes.mensaje_error')
                ->with('msj', '..no tiene permisos para acceder a esta sección');
        }

        // Mostrar lista de usuarios (igual que fichajes): filtro por estado
        $filtro_estado = request()->get('estado', 'activos');
        $query = User::whereIn('role_id', [1,2]);

        switch($filtro_estado) {
            case 'todos':
                break;
            case 'inactivos':
                $query->where('baja', 1);
                break;
            case 'activos':
            default:
                $query->where('baja', 0);
                break;
        }

        $usuarios = $query->get();

        return view('eunomia.access_control.listado_access_control', compact('usuarios', 'filtro_estado'));
    }

    /**
     * Devuelve el historial de accesos para un usuario (HTML parcial) - AJAX
     */
    public function getHistorialUsuario($userId)
    {
        if(\Auth::user()->compruebaSeguridad('mostrar-control-de-accesos') == false)
            return response('Sin permisos', 403);

        $periodo = request()->get('periodo', 'mes_actual');
        $page = (int) request()->get('page', 1);
        $perPage = 20;

        $query = AccessControl::where('user_id', $userId);

        switch ($periodo) {
            case 'mes_actual':
                $query->whereMonth('created_at', \Carbon\Carbon::now()->month)
                      ->whereYear('created_at', \Carbon\Carbon::now()->year);
                break;
            case 'ultimo_mes':
                $query->where('created_at', '>=', \Carbon\Carbon::now()->subMonth());
                break;
            case 'ultimos_3_meses':
                $query->where('created_at', '>=', \Carbon\Carbon::now()->subMonths(3));
                break;
            case 'todos':
            default:
                break;
        }

        $total = $query->count();

        $accesos = $query->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Construir HTML similar a fichajes.getFichajesUsuario
        $periodo_texto = [
            'mes_actual' => 'Mes actual (' . \Carbon\Carbon::now()->format('F Y') . ')',
            'ultimo_mes' => 'Últimos 30 días',
            'ultimos_3_meses' => 'Últimos 3 meses',
            'todos' => 'Todos'
        ];

        $html = '<div class="accesos-container" data-user-id="' . $userId . '">';
        $html .= '<div class="row" style="margin-bottom: 10px;">
                    <div class="col-md-8">
                        <div class="btn-group btn-group-sm filtros-periodo">
                            <button type="button" class="btn ' . ($periodo == 'mes_actual' ? 'btn-primary' : 'btn-default') . '" data-periodo="mes_actual">Mes actual</button>
                            <button type="button" class="btn ' . ($periodo == 'ultimo_mes' ? 'btn-primary' : 'btn-default') . '" data-periodo="ultimo_mes">Último mes</button>
                            <button type="button" class="btn ' . ($periodo == 'ultimos_3_meses' ? 'btn-primary' : 'btn-default') . '" data-periodo="ultimos_3_meses">3 meses</button>
                            <button type="button" class="btn ' . ($periodo == 'todos' ? 'btn-primary' : 'btn-default') . '" data-periodo="todos">Todos</button>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <small class="text-muted"><i class="fa fa-info-circle"></i> ' . ($periodo_texto[$periodo] ?? '') . ': ' . $total . ' acceso(s)</small>
                    </div>
                </div>';

        if ($total == 0) {
            $html .= '<div class="alert alert-info"><i class="fa fa-info-circle"></i> No hay accesos para el período seleccionado.</div></div>';
            return $html;
        }

        $html .= '<table class="table table-striped table-condensed"><thead><tr><th>Fecha</th><th>IP</th><th>Cod.Postal</th><th>Localidad</th></tr></thead><tbody>';

        foreach ($accesos as $a) {
            $fecha = \Carbon\Carbon::parse($a->created_at)->format('d/m/Y');
            $hora = \Carbon\Carbon::parse($a->created_at)->format('H:i:s');
            $html .= '<tr>';
            $html .= '<td><strong>' . $fecha . '</strong> ' . $hora . '</td>';
            $html .= '<td>' . e($a->ip) . '</td>';
            $html .= '<td>' . e($a->zip_code) . '</td>';
            $html .= '<td>' . e((!empty($a->location) ? $a->location : '—')) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        // Paginación simple
        $totalPages = ceil($total / $perPage);
        if ($totalPages > 1) {
            $html .= '<div class="row"><div class="col-md-6"><small class="text-muted">Página ' . $page . ' de ' . $totalPages . '</small></div><div class="col-md-6 text-right"><div class="btn-group btn-group-sm paginacion-accesos">';
            if ($page > 1) $html .= '<button type="button" class="btn btn-default" data-page="' . ($page - 1) . '" data-user-id="' . $userId . '" data-periodo="' . $periodo . '"><i class="fa fa-chevron-left"></i> Anterior</button>';
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            for ($i = $start; $i <= $end; $i++) {
                $html .= '<button type="button" class="btn ' . ($i == $page ? 'btn-primary' : 'btn-default') . '" data-page="' . $i . '" data-user-id="' . $userId . '" data-periodo="' . $periodo . '">' . $i . '</button>';
            }
            if ($page < $totalPages) $html .= '<button type="button" class="btn btn-default" data-page="' . ($page + 1) . '" data-user-id="' . $userId . '" data-periodo="' . $periodo . '">Siguiente <i class="fa fa-chevron-right"></i></button>';
            $html .= '</div></div></div>';
        }

        $html .= '</div>';

        return $html;
    }
}
