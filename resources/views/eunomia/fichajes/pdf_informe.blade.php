{{-- Fragmento HTML para inyectar dentro del modal vía AJAX (sin @extends) --}}
<style>
    /* ======= Estilos del informe ======= */
    .informe-container { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
    .informe-header { display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #3C8DBC; }
    .informe-header .logo { flex: 0 0 180px; }
    .informe-header .logo img { max-width: 100%; height: auto; }
    .informe-header .empresa-info, .informe-header .periodo-info { flex: 1; min-width: 200px; }
    .informe-header .empresa-info p, .informe-header .periodo-info p { margin: 3px 0; font-size: 12px; }

    /* Tabla principal del informe */
    .tabla-informe { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 11px; }
    .tabla-informe th, .tabla-informe td { border: 1px solid #ddd; padding: 6px 8px; text-align: center; vertical-align: middle; }
    .tabla-informe thead th { background: #3C8DBC; color: #fff; font-weight: 600; font-size: 11px; }
    .tabla-informe tbody tr:nth-child(even) { background: #f9f9f9; }
    .tabla-informe tbody tr:hover { background: #eef5fb; }

    /* Columnas específicas */
    .tabla-informe .col-dia { width: 10%; font-weight: 600; }
    .tabla-informe .col-diaSemana { width: 10%; }
    .tabla-informe .col-fichajes { width: 45%; text-align: left; font-size: 10px; }
    .tabla-informe .col-total { width: 12%; font-weight: 600; }

    /* Etiquetas de tipo */
    .badge-entrada { background: #28a745; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 9px; }
    .badge-salida { background: #dc3545; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 9px; }
    .badge-abierto { background: #ffc107; color: #333; padding: 2px 6px; border-radius: 3px; font-size: 9px; }

    /* Fila sin fichajes */
    .fila-vacia td { color: #aaa; font-style: italic; }

    /* Fila de totales */
    .fila-total td { background: #3C8DBC; color: #fff; font-weight: 700; font-size: 13px; border: none; }

    /* Notas al pie */
    .informe-footer { margin-top: 10px; font-size: 10px; color: #666; }
    .informe-footer p { margin: 3px 0; }

    /* Responsive para impresión */
    @media print {
        .informe-container { font-size: 10px; }
        .tabla-informe th, .tabla-informe td { padding: 3px 5px; font-size: 9px; }
        .informe-header { border-bottom: 1px solid #333; }
        .tabla-informe thead th { background: #333 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .fila-total td { background: #333 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>

<div class="informe-container">

    {{-- ===== CABECERA ===== --}}
    <div class="informe-header">
        <div class="logo">
            <img src="{{ asset('images/logo_mglab.png') }}" alt="Logo">
        </div>

        <div class="empresa-info">
            @if($user->empresa)
                <p><strong>Empresa:</strong> {{ $user->empresa->nombre }}</p>
                <p><strong>CIF:</strong> {{ $user->empresa->cif }}</p>
                <p><strong>Domicilio:</strong> {{ $user->empresa->domicilio }}</p>
            @endif
            <p><strong>Trabajador/a:</strong> {{ $user->name }}</p>
        </div>

        <div class="periodo-info">
            <p><strong>Mes:</strong> {{ $mesLetra }}</p>
            <p><strong>Año:</strong> {{ $anio }}</p>
            <p><strong>Total fichajes:</strong> {{ $fichajes->count() }}</p>
        </div>
    </div>

    {{-- ===== TABLA DE FICHAJES POR DÍA ===== --}}
    <table class="tabla-informe">
        <thead>
            <tr>
                <th class="col-dia">Día</th>
                <th class="col-diaSemana">Día sem.</th>
                <th class="col-fichajes">Fichajes del día</th>
                <th class="col-total">Horas trabajadas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($resumenDias as $rd)
                @php
                    $fechaCarbon = \Carbon\Carbon::parse($rd['dia']);
                    $diaSemana   = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'][$fechaCarbon->dayOfWeekIso - 1];
                    $esFinDeSemana = $fechaCarbon->isWeekend();
                @endphp

                @if($rd['fichajes']->isEmpty())
                    <tr class="fila-vacia" @if($esFinDeSemana) style="background:#f5f5f5;" @endif>
                        <td class="col-dia">{{ $fechaCarbon->format('d/m') }}</td>
                        <td class="col-diaSemana">{{ $diaSemana }}</td>
                        <td class="col-fichajes">— Sin fichajes —</td>
                        <td class="col-total">0h 00m</td>
                    </tr>
                @else
                    <tr @if($esFinDeSemana) style="background:#fff8e1;" @endif>
                        <td class="col-dia">{{ $fechaCarbon->format('d/m') }}</td>
                        <td class="col-diaSemana">{{ $diaSemana }}</td>
                        <td class="col-fichajes">
                            @foreach($rd['fichajes']->sortBy('fecha') as $f)
                                @php $hora = \Carbon\Carbon::parse($f->fecha)->format('H:i'); @endphp
                                <span class="badge-{{ $f->tipo }}">{{ $f->tipo }}</span> {{ $hora }}
                                @if($f->comentarios)
                                    <em style="font-size:9px;color:#888;">({{ $f->comentarios }})</em>
                                @endif
                                &nbsp;
                            @endforeach
                            {{-- Indicar tramo abierto --}}
                            @foreach($rd['tramos'] as $tramo)
                                @if(!$tramo['salida'])
                                    <span class="badge-abierto">sin salida</span>
                                @endif
                            @endforeach
                        </td>
                        <td class="col-total">{{ $rd['horas_fmt'] }}</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="4" style="text-align:center; padding:20px; color:#999;">
                        No hay fichajes registrados para {{ $mesLetra }} {{ $anio }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="fila-total">
                <td colspan="3" style="text-align:right; padding-right:15px;">TOTAL MES:</td>
                <td>{{ $totalHorasMes }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- ===== NOTAS AL PIE ===== --}}
    <div class="informe-footer">
        <p>* Se descuentan 30 minutos cada día empleados en la pausa/café.</p>
        <p>* Los tramos marcados como "sin salida" no computan horas hasta que se registre la salida.</p>
        <p>Informe generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>
</div>
