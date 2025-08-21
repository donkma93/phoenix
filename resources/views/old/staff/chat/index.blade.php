@extends('layouts.staff')

@section('breadcrumb')
@include('layouts.partials.breadcrumb', [
    'items' => [
        [
            'text' => 'Dashboard'
        ]
    ]
])
@endsection

@section('content')
<div class="px-4 px-md-0">
    <div class="row">
        <div class="col-md-6">
            @foreach($listChat as $chat) 
                <div>
                    <span>
                        {{ $chat->chatBox->user->email }}
                    </span>
                    <span>
                        {{ $chat->user_count }}
                    </span>
                    <span>
                        {{ $chat->message }}
                    </span>
                </div>
            @endforeach
        </div>
        <div class="col-md-6">
            <div id="chat-content"></div>
            <input type="text" id="create_message" />
            <button onclick="sendMessage()">Enter</button>
        </div> 
    </div>
</div>
@endsection

@section('scripts')
<script>
    
</script>
@endsection
