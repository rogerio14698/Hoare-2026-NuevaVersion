<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Modificación vacaciones</title>
</head>

<body>
    <h1>Modificación vacaciones</h1>
    <p>El usuario {{$usuario}} ha añadido días a sus vacaciones:</p>

    <p>Días de vacaciones cogidos:</p>

    @php
        use Carbon\Carbon;

        $fmtFecha = function ($s) {
            $s = trim((string) $s);
            if ($s === '')
                return '';
            foreach (['Y-m-d H:i:s', 'Y-m-d', 'd/m/Y H:i:s', 'd/m/Y', 'm/d/Y H:i:s', 'm/d/Y'] as $f) {
                try {
                    return Carbon::createFromFormat($f, $s)->format('d/m/Y');
                } catch (\Throwable $e) {
                }
            }
            // Último intento “libre”; si falla, devolvemos el texto tal cual escapado
            try {
                return Carbon::parse($s)->format('d/m/Y');
            } catch (\Throwable $e) {
                return e($s);
            }
        };
    @endphp

    @foreach ($fechas as $fecha)
        {{ $fmtFecha($fecha) }}<br>
    @endforeach

    </p>

</body>

</html>