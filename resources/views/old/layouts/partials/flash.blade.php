<div class="flash">
    @foreach ($messages as $message)
        <div class="flash-message {{ isset($message['type']) && $message['type'] == 'error' ? 'flash-error' : 'flash-success' }}">{{ $message['content'] }}</div>
    @endforeach
</div>
