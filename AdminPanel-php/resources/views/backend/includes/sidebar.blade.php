<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-title">
                @lang('menus.backend.sidebar.general')
            </li>
            <li class="nav-item">
                <a class="nav-link {{active_class(Route::is('admin/dashboard'))}}"
                   href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-archway"></i> @lang('menus.backend.sidebar.dashboard')
                </a>
            </li>
            <li class="nav-title">
                @lang('menus.backend.sidebar.system')
            </li>

            @if ($logged_in_user->isAdmin())
                <li class="nav-item">
                    <a class="nav-link {{active_class(Route::is('admin/auth/user*')) }}"
                       href="{{route('admin.auth.user.index') }}">
                        <i class="fas fa-user"></i>
                        @lang('labels.backend.access.users.management')

                        @if ($pending_approval > 0)
                            <span class="badge badge-danger">{{ $pending_approval }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active_class(Route::is('admin/auth/role*')) }}"
                       href="{{ route('admin.auth.role.index') }}">
                        <i class="fas fa-user-tag"></i>
                        @lang('labels.backend.access.roles.management')
                    </a>
                </li>
                {{--Subscription Menu--}}

                <li class="nav-item">
                    <a class="nav-link {{ active_class(Route::is('admin/subscriptions/')) }}"
                       href="{{ route('admin.subscriptions.index') }}">
                        <i class="fas fa-box"></i>
                        Subscription List
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ active_class(Route::is('admin/subscriptions/')) }}"
                       href="{{ route('admin.subscriptions.index') }}">
                        <i class="fas fa-box"></i>
                        Payments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active_class(Route::is('admin/protocols')) }}"
                       href="{{ route('admin.protocols.index') }}">
                        <i class="fas fa-network-wired"></i>
                        Protocols List
                    </a>
                </li>

                <li class="nav-title">
                    SERVICES
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ active_class(Route::is('admin/service-providers/')) }}"
                       href="{{ route('admin.service-providers.index') }}">
                        <i class="fas fa-satellite-dish"></i>
                        Service Provider List
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active_class(Route::is('admin/services')) }}"
                       href="{{ route('admin.services.index') }}">
                        <i class="fas fa-cogs"></i>
                        Services List
                    </a>
                </li>
                <li class="nav-title">
                    SERVERS
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active_class(Route::is('admin/v-p-n-servers/*')) }}"
                       href="{{ route('admin.v-p-n-servers.index') }}">
                        <i class="fas fa-server"></i>
                        VPN Servers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active_class(Route::is('admin/v-p-n-servers/*')) }}"
                       href="{{ route('admin.v-p-n-servers.index') }}">
                        <i class="fas fa-anchor"></i>
                        Domains
                    </a>
                </li>

                <li class="nav-title">
                    REPORTS
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active_class(Route::is('admin/intercom-marketing-data/')) }}"
                       href="{{ route('admin.intercom-marketing-data.index') }}">
                        <i class="fas fa-intercom"></i>
                        Intercom
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active_class(Route::is('admin/intercom-marketing-data/')) }}"
                       href="{{ route('admin.admin.intercom-marketing-data.datag') }}">
                        <i class="fas fa-intercom"></i>
                        Intercom Datagrid
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active_class(Route::is('admin/connected-users')) }}"
                       href="{{ route('admin.connected_users') }}">
                        <i class="fas fa-connectdevelop"></i> Current Connected Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active_class(Route::is('admin/connected-users-by-country')) }}"
                       href="{{ route('admin.connected_users_by_country') }}">
                        <i class="nav-icon cib-apple"></i> Users By Country
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active_class(Route::is('admin/connected-users-per-server-by-country')) }}"
                       href="{{ route('admin.connected_users_per_server_by_country') }}">
                        <i class="nav-icon icon-directions"></i> Users Per Server By Country
                    </a>
                </li>

                <li class="divider"></li>
                <li class="nav-title">
                    LOGS
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ active_class(Route::is('admin/log-viewer')) }}"
                       href="{{ route('log-viewer::dashboard') }}">
                        @lang('menus.backend.log-viewer.dashboard')
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ active_class(Route::is('admin/log-viewer/logs*')) }}"
                       href="{{ route('log-viewer::logs.list') }}">
                        @lang('menus.backend.log-viewer.logs')
                    </a>
                </li>
            @endif



        </ul>
    </nav>

    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div><!--sidebar-->
