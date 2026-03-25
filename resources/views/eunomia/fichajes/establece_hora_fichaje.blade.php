<!-- Start Content -->
<div id="content">
    <div class="row">

        <div class="col-md-12">

            <!-- Classic Heading -->
            <h4 class="classic-title"><span>Establece hora</span></h4>

            <!-- Start Contact Form -->

            <form action="{{ route('fichajes.store') }}" method="POST" name="form_establece_hora_fichaje" class="contact-form" id="contact-form">
                @csrf
            <div class="form-group">
                <div class="controls">
                    <label for="hora">Hora</label>
                    <input type="time" name="hora" id="hora" class="form-control" required value="{{ old('hora', date('H:i')) }}">
                </div>
            </div>

            <div class="form-group">
                <div class="controls">
                    <label for="comentarios">Comentarios</label>
                    <textarea name="comentarios" id="comentarios" class="form-control">{{ old('comentarios') }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <button id="boton_establece_hora_fichaje" class="btn btn-info float">Enviar</button>
            </div>
            </form>
        <!-- End Contact Form -->

        </div>
    </div>
</div>
