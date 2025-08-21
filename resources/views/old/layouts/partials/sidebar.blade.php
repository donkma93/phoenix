<div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show" id="sidebar">
    <div class="c-sidebar-brand">
        <div class="logo-text">phoenix</div>
    </div>
    <ul class="c-sidebar-nav">
        @foreach ($items as $item)
            @php
                if(isset($item['role']) && !in_array(Auth::user()->role, $item['role'])) {
                    continue;
                }
            @endphp
            @if (count($item) == 0)
                <li class="c-sidebar-nav-divider"></li>
            @else
                @php
                    $hasChildren = isset($item['children']);
                @endphp
                <li class="c-sidebar-nav-item {{ $hasChildren ? 'c-sidebar-nav-dropdown' : '' }}">
                    <a class="{{ $hasChildren ? 'c-sidebar-nav-dropdown-toggle' : 'c-sidebar-nav-link' }}" href="{{ $hasChildren ? '#' : $item['url'] }}">
                        <i class="fa {{$item['icon'] ?? 'fa-tachometer'}} c-sidebar-nav-icon" aria-hidden="true"></i>
                        {{ $item['text'] }}
                    </a>
                    @if ($hasChildren)
                    <ul class="c-sidebar-nav-dropdown-items">
                        @foreach ($item['children'] as $child)
                            <li class="c-sidebar-nav-item">
                                <a class="c-sidebar-nav-link" href="{{ $child['url'] }}">
                                    <span class="c-sidebar-nav-icon"></span> {{ $child['text'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    @endif
                </li>
            @endif
        @endforeach
    </ul>
    <button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent"
        data-class="c-sidebar-minimized"></button>
</div>
