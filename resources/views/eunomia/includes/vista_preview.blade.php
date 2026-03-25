@extends('layouts.web')

@section('contenido')
    <?php
    $fecha='';
    if ($content->fecha != ''){
        $time= strtotime($content->fecha);
        $fecha = date('d/m/Y',$time);
    }
    ?>
    <!-- comienza contenido de la página-->
    <section id="migadepan">
        <div class="col-xs-12">
            <ul>
                @foreach($breadcrums as $breadcrum)
                    <li>
                        @if($breadcrum[1]!='')
                            <a href="{{ route($breadcrum[1].'_web_'.Session::get('idioma')) }}">{{ strtoupper($breadcrum[0]) }}</a>
                        @else
                            {{ strtoupper($breadcrum[0]) }}
                        @endif
                    </li>
                    @if(pos($breadcrum) != $textosidioma->titulo)
                        <li>//</li>
                    @endif
                @endforeach
            </ul>
        </div>

    </section><!-- fin migadepan -->
    <section id="noticias">
        <div class="row">
            <div class="col-xs-12">
                <article>
                    <hgroup>

                        <h1>{{$textosidioma->titulo}}</h1>
                        <h2>{{$textosidioma->subtitulo}}</h2>
                        <h3>{{$content->lugar!=''?strtoupper($content->lugar).'.':''}} {{$fecha}}</h3>
                    </hgroup>
                    <div class="{{$content->columnas=='2'?'columnas':'left'}}">
                        @if ($content->imagen != '' && $content->columnas == '2')
                            <picture>
                                <source media="(min-width: 1200px)" srcset="{{asset('images/contenido/l')}}/{{$content->imagen}}"><!-- pc -->
                                <source media="(min-width: 992px)" srcset="{{asset('images/contenido/m')}}/{{$content->imagen}}"><!-- medio pc -->
                                <source media="(min-width: 768px)" srcset="{{asset('images/contenido/s')}}/{{$content->imagen}}"><!-- tablet -->

                                <!-- img tag for browsers that do not support picture element -->
                                <img src="{{asset('images/contenido/l')}}/{{$content->imagen or 'sinimagen.png'}}" alt="{{$textosidioma->titulo}}" class="img-responsive"><!-- movil -->
                            </picture>
                        @endif
                        {!! $textosidioma->contenido !!}
                        @if ($content->imagen != '' && $content->columnas == '1')
                            <picture>
                                <source media="(min-width: 1200px)" srcset="{{asset('images/contenido/l')}}/{{$content->imagen}}"><!-- pc -->
                                <source media="(min-width: 992px)" srcset="{{asset('images/contenido/m')}}/{{$content->imagen}}"><!-- medio pc -->
                                <source media="(min-width: 768px)" srcset="{{asset('images/contenido/s')}}/{{$content->imagen}}"><!-- tablet -->

                                <!-- img tag for browsers that do not support picture element -->
                                <img src="{{asset('images/contenido/l')}}/{{$content->imagen or 'sinimagen.png'}}" alt="{{$textosidioma->titulo}}" class="img-responsive"><!-- movil -->
                            </picture>
                        @endif
                        <p><a title="Puedes usar este contenido con atribución a Gijón se come" class="cc"><span data-decimal="169" data-entity="©" data-id="45152">© GSC </span><i class="fa fa-cc"></i></a></p>
                    </div><!-- fin columnas -->
                    <div class="margin-top"></div>
                    <?php
                    $url_rrss = 'https://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
                    ?>
                    <div class="post-share">
                        <a class="facebook" href="https://www.facebook.com/sharer/sharer.php?u={{$url_rrss}}" target="_blank"><i class="fa fa-facebook"></i></a>
                        <a class="twitter" href="https://twitter.com/intent/tweet?url={{$url_rrss}}&text={{$textosidioma->titulo}}" target="_blank"><i class="fa fa-twitter"></i></a>
                        <a class="gplus" href="https://plus.google.com/share?url={{$url_rrss}}" target="_blank"><i class="fa fa-google-plus"></i></a>
                        <a class="mail" href="whatsapp://send?text={{$textosidioma->titulo}}" data-action="share/whatsapp/share"><i class="fa fa-whatsapp"></i></a>
                    </div>
                </article>

            </div><!-- fin col 12 -->

        </div>  <!-- fin row -->
    </section><!-- fin  noticias-->

    @if (is_object($registros))
        <section id="zonavariable">
            <div class="row row-eq-height"><!-- row zona variable-->
                <?php $cont = 1; ?>
                @foreach($registros as $registro)
                    <?php
                    $link = '';
                    $titulo = '';
                    $subtitulo = '';
                    $idioma_actual = Session::get('idioma');
                    if (is_object($registro)) {
                        $pagina = str_replace('-','',$registro->slug);
                        $ruta = route($pagina.'_web_'.$idioma_actual,[]);
                        $link = $ruta;
                        $titulo = $registro->titulo;
                        $subtitulo = $registro->subtitulo;
                    }
                    switch ($cont) {
                        case 1:
                            $clase = 'variantetercera';
                            $imgdecorativa = 'C_mord.png';
                            break;
                        case 2:
                            $clase = 'variantecuarta';
                            $imgdecorativa = 'D_mord.png';
                            break;
                        case 3:
                            $clase = 'variantequinta';
                            $imgdecorativa = 'E_mord.png';
                    }
                    ?>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 {{$clase}}">

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <picture>
                                <source media="(min-width: 1200px)" srcset="{{asset('images/contenido/m')}}/{{$registro->imagen or 'sinimagen.png'}}"><!-- pc -->
                                <source media="(min-width: 992px)" srcset="{{asset('images/contenido/m')}}/{{$registro->imagen or 'sinimagen.png'}}"><!-- medio pc -->
                                <source media="(min-width: 768px)" srcset="{{asset('images/contenido/s')}}/{{$registro->imagen or 'sinimagen.png'}}"><!-- tablet -->

                                <!-- img tag for browsers that do not support picture element -->
                                @if ($link != '')
                                    <a href="{{$link}}">
                                        @endif
                                        <img src="{{asset('images/contenido/m')}}/{{$registro->imagen or 'sinimagen.png'}}" alt="{{$titulo}}">
                                        @if ($link != '')
                                    </a><!-- movil -->
                                @endif
                            </picture>
                            <picture>
                                <source media="(min-width: 768px)" srcset="{{asset('images/graficos')}}/{{$imgdecorativa}}"><!-- tablet -->
                                <!-- img tag for browsers that do not support picture element -->
                                <img src="{{asset('images/graficos')}}/{{$imgdecorativa}}" alt="imagen decorativa" class="img-responsive mordisquitos"><!-- movil -->
                            </picture>
                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 row-eq-height destacado">
                            <article>
                                <hgroup>
                                    <h1><a href="{{$link}}">{{$titulo}}</a></h1>
                                    <h2>{{$subtitulo}}</h2>
                                </hgroup>
                            </article>
                        </div>
                    </div><!-- fin col lg 5 -->
                    <?php $cont++; ?>
                @endforeach
            </div>
        </section>

    @endif
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('css/detalle.css')}}"/>
@endsection