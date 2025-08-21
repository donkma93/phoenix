<header class="c-header c-header-light c-header-fixed c-header-with-subheader">
    <button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar" data-class="c-sidebar-show">
        <i class="fa fa-bars c-icon c-icon-lg" aria-hidden="true"></i>
    </button>
    <button class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button" data-target="#sidebar" data-class="c-sidebar-lg-show" responsive="true">
        <i class="fa fa-bars c-icon c-icon-lg" aria-hidden="true"></i>
    </button>
    <ul class="c-header-nav ml-auto mr-4 dropdown">
        @if (auth()->user()->isUser())
        <li class="c-header-nav-item mx-2 dropdown">
            <a class="c-header-nav-link" href="#" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-bell c-icon mr-2" aria-hidden="true"></i>
                <span id="notification_unread_total" class="badge bg-danger text-white"></span>
            </a>

            <div class="dropdown-menu dropdown-menu-right pt-0">
                <div class="dropdown-header bg-light py-2">
                    <strong> New Notification </strong>
                </div>
                <div class="noti-diplay" id="noti-menu">
                </div>
            </div>
        </li>
        @elseif (auth()->user()->isStaff())
        <li class="c-header-nav-item mx-2">
            <div class="dropdown" data-toggle="dropdown" aria-expanded="false">
                <a class="c-header-nav-link" href="#">
                    <i class="fa fa-comment c-icon mr-2" aria-hidden="true"></i>
                    <span class="badge bg-danger text-white message_unread">0</span>
                </a>
                <div class="dropdown-menu shadow border-0 dropdown-menu-right" id="message-menu" aria-labelledby="dropdownMenuButton">
                    @if(isset($notification))
                    <a class="dropdown-item" href="#">Item</a>
                    <a class="dropdown-item" href="#">Another Item</a>
                    <a class="dropdown-item" href="#">One more item</a>
                    @else
                    <div class="text-nowrap ap-8">
                        You have <a href="{{ route('staff.messenger') }}"><span id="message-new">0</span> new message</a>!
                    </div>
                    @endif
                </div>
            </div>
        </li>
        <li class="c-header-nav-item mx-2">
            <div class="dropdown" data-toggle="dropdown" aria-expanded="false">
                <a class="c-header-nav-link" href="#">
                    <i class="fa fa-bell c-icon mr-2" aria-hidden="true"></i>
                    <span class="badge bg-danger text-white notification_unread">0</span>
                </a>
                <div class="dropdown-menu shadow border-0" id="noti-menu" aria-labelledby="dropdownMenuButton">
                    @if(isset($notification))
                    <a class="dropdown-item" href="#">Item</a>
                    <a class="dropdown-item" href="#">Another Item</a>
                    <a class="dropdown-item" href="#">One more item</a>
                    @else
                    <div class="text-nowrap ap-8">
                        You have <a href="{{ route('staff.request.list', ['status' => 0]) }}"><span id="notification-new">0</span> new request</a>!
                    </div>
                    @endif
                </div>
            </div>
        </li>
        @else
            <li class="c-header-nav-item mx-2">
                <div class="dropdown" data-toggle="dropdown" aria-expanded="false">
                    <a class="c-header-nav-link" href="#">
                        <i class="fa fa-bell c-icon mr-2" aria-hidden="true"></i>
                        <span class="badge bg-danger text-white notification_unread">0</span>
                    </a>
                    <div class="dropdown-menu shadow border-0" id="noti-menu" aria-labelledby="dropdownMenuButton">
                        @if(isset($notification))
                        <a class="dropdown-item" href="#">Item</a>
                        <a class="dropdown-item" href="#">Another Item</a>
                        <a class="dropdown-item" href="#">One more item</a>
                        @else
                        <div class="text-nowrap ap-8">
                            You have <a href="{{ route('admin.pricing.list') }}"><span id="notification-new">0</span> new request</a>!
                        </div>
                        @endif
                    </div>
                </div>
            </li>
        @endif
        <li class="c-header-nav-item dropdown">
            <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <div class="c-avatar">
                    <img class="c-avatar-img" src="
                        @if(isset(auth()->user()->profile->avatar))
                            {{ asset(auth()->user()->profile->avatar) }}
                        @else
                            {{
                                asset('images/default.jpg')
                            }}
                        @endif
                        " alt="{{ auth()->user()->email }}">
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right pt-0 shadow border-0">
                <div class="dropdown-header bg-light py-2">
                    <strong>Account</strong>
                </div>
                <a class="dropdown-item" href="{{ route(App\Models\UserProfile::$profileRoute[auth()->user()->role]) }}">
                    <i class="fa fa-user c-icon mr-2" aria-hidden="true"></i> Profile
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('logout', ['locale' => app()->getLocale()]) }}" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                    <i class="fa fa-sign-out c-icon mr-2" aria-hidden="true"></i> {{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout', ['locale' => app()->getLocale()]) }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
    @yield('breadcrumb')
</header>
