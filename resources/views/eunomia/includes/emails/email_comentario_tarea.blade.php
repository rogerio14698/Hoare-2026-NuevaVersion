<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nuevo comentario de tarea en Horae</title>
</head>
<body>
<h1>Nuevo comentario de tarea en Horae</h1>

<p>
    Título proyecto: <a href="{{$link_proyecto}}" target="_blank">{!! $titulo_proyecto !!}</a>
</p>
<p>
    Título tarea: <a href="{{$link_tarea}}" target="_blank">{!! $titulo_tarea !!}</a>
</p>
<p>
    Comentario: {!! $comentario !!}
</p>

</body>
</html>
