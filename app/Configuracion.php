<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Session;

class Configuracion extends Model
{
    protected $table = 'configuracion';

    protected $tipo_contenido = 8; // 1 - Contenido, 2 - Imágenes Slide, 3 - Noticias, 4 - Portada, 5 - Galerías, 6 - Menú, 7 - Multimedia, 8 - Configuracion

    public function textos_idioma()
    {
        return $this->belongsTo('App\TextosIdioma', 'id', 'contenido_id')->where('visible', '1')->where('idioma_id', Idioma::fromCodigo(Session::get('idioma'))->id)->where('tipo_contenido_id', $this->tipo_contenido);
    }

}
