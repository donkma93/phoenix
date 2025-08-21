<div class="c-subheader px-3">
    <ol class="breadcrumb border-0 m-0">
        @foreach ($items as $index => $item)
            <li class="breadcrumb-item {{ $index == count($items) - 1 ? 'active' : '' }}">
                @if (isset($item['url']))
                    <a href="{{ $item['url'] }}">{{ $item['text'] }}</a>
                @else
                    {{ $item['text'] }}
                @endif
            </li>
        @endforeach
    </ol>
</div>
