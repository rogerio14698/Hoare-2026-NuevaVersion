<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Nuevo comentario</title>
  <style>
.contenedor {
            font-family: 'Roboto', sans-serif;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            max-width: 600px;
            margin: auto;
        }
        .autorComentario {
            font-weight: bold;
            color: #333;
        }
        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 15px 0;
        }

        .textoComentario {
            margin-top: 15px;
            color: #555;
            text-align: justify;
        }
  </style>
</head>
<body>
  <div class="contenedor">
        <p class="autorComentario">{{ $meta['author_name'] ?? 'Alguien' }} ha publicado un nuevo comentario</p>
        <hr>
        <div class="textoComentario">{!! str_replace(['{', '}'], '', $comment->comment) !!}</div>
    </div>

</body>
</html>
