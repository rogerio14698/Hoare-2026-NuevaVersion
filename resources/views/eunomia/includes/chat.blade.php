<!-- DIRECT CHAT -->
<div class="box box-warning direct-chat direct-chat-warning">
    <div class="box-header with-border">
        <h3 class="box-title">Direct Chat</h3>

        <div class="box-tools pull-right">
            <span data-toggle="tooltip" title="{{$unreadCount}} mensajes nuevos" class="badge bg-yellow">{{$unreadCount}}</span>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="Contacts"
                    data-widget="chat-pane-toggle">
                <i class="fa fa-comments"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <!-- Conversations are loaded here -->
        <div id="chat_messages" class="direct-chat-messages" style="height: 330px; overflow: auto;">

        </div>
        <!--/.direct-chat-messages-->

        <!-- Contacts are loaded here -->
        <div class="direct-chat-contacts">
            <ul class="contacts-list">
                @foreach($conversation->users as $user)
                    <li>
                        <a href="#">
                            <img class="contacts-list-img" src="/images/avatar/{{ isset($user->avatar)?$user->avatar:'sinavatar.jpg'}}" alt="User Image">

                            <div class="contacts-list-info">
                                <span class="contacts-list-name">
                                  {{$user->name}}
                                    <small class="contacts-list-date pull-right">{{$user->created_at->format('d/m/Y')}}</small>
                                </span>
                                <span class="contacts-list-msg">{{$user->email}}</span>
                            </div>
                            <!-- /.contacts-list-info -->
                        </a>
                    </li>
                    <!-- End Contact Item -->
                @endforeach
            </ul>
            <!-- /.contatcts-list -->
        </div>
        <!-- /.direct-chat-pane -->
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        <form action="{{ route('enviaMensajeChat') }}" method="POST">
            @csrf
        <div class="input-group">
            <input type="text" name="message" id="message" placeholder="Escribe un mensaje ..." class="form-control" value="{{ old('message') }}">
            <input type="hidden" name="conversation_id" id="conversation_id" value="{{ $conversation->id }}">
            <span class="input-group-btn">
                        <button id="enviar_chat" type="button" class="btn btn-warning btn-flat">Enviar</button>
                    </span>
        </div>
        </form>
    </div>
    <!-- /.box-footer-->
</div>
<!--/.direct-chat -->