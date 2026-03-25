@foreach($messages as $message)
    <!-- Message. Default to the left -->
    <div id="msg_{{$message->id}}" class="direct-chat-msg{{$message->user_id==\Auth::user()->id?' right':''}}">
        <div class="direct-chat-info clearfix">
            <span class="direct-chat-name pull-left">{{$message->user()->name}}</span>
            <span class="direct-chat-timestamp pull-right">{{$message->created_at->format('d/m/Y H:i')}}</span>
        </div>
        <!-- /.direct-chat-info -->
        <img class="direct-chat-img" src="/images/avatar/{{ isset($message->user()->avatar)?$message->user()->avatar:'sinavatar.jpg'}}" alt="message user image">
        <!-- /.direct-chat-img -->
        <div class="direct-chat-text">
            {!! $message->body !!}
        </div>
        <!-- /.direct-chat-text -->
    </div>
    <!-- /.direct-chat-msg -->
@endforeach