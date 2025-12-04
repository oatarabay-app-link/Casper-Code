<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-title">
                @lang('menus.backend.sidebar.general')
            </li>
            <li class="nav-item">
                <a class="nav-link {{active_class(Route::is('admin/dashboard'))}}"
                   href="{{ route('admin.dashboard') }}">
                    <i class="nav-icon icon-speedometer"></i> @lang('menus.backend.sidebar.dashboard')
                </a>
            </li>

{{--            <li class="nav-item nav-dropdown {{ active_class(Active::checkUriPattern('admin/connected-users')) }}">--}}
{{--                <a class="nav-link nav-dropdown-toggle {{ active_class(Active::checkUriPattern('admin/auth*')) }}"--}}
{{--                   href="#">--}}
{{--                    <i class="nav-icon icon-user"></i> Connected Users--}}
{{--                </a>--}}

{{--                <ul class="nav-dropdown-items">--}}
{{--                    <li class="nav-item">--}}
{{--                        <a class="nav-link {{ active_class(Active::checkUriPattern('admin/connected-users')) }}"--}}
{{--                           href="{{ route('admin.connected_users') }}">--}}
{{--                            <i class="nav-icon icon-speedometer"></i> List Users--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    <li class="nav-item">--}}
{{--                        <a class="nav-link {{ active_class(Active::checkUriPattern('admin/connected-users-by-country')) }}"--}}
{{--                           href="{{ route('admin.connected_users_by_country') }}">--}}
{{--                            <i class="nav-icon icon-map"></i> Users By Country--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    <li class="nav-item">--}}
{{--                        <a class="nav-link {{ active_class(Active::checkUriPattern('admin/connected-users-per-server-by-country')) }}"--}}
{{--                           href="{{ route('admin.connected_users_per_server_by_country') }}">--}}
{{--                            <i class="nav-icon icon-directions"></i> Users Per Server By Country--}}
{{--                        </a>--}}
{{--                    </li>--}}


{{--                </ul>--}}
{{--            </li>--}}

            <li class="nav-title">
                @lang('menus.backend.sidebar.system')
            </li>

            @if ($logged_in_user->isAdmin())
{{--                <li class="nav-item nav-dropdown {{ active_class(Route::is('admin/auth*'), 'open') }}">--}}
{{--                    <a class="nav-link nav-dropdown-toggle {{ active_class(Route::is('admin/auth*')) }}"--}}
{{--                       href="#">--}}
{{--                        <i class="nav-icon icon-user"></i> @lang('menus.backend.access.title')--}}

{{--                        @if ($pending_approval > 0)--}}
{{--                            <span class="badge badge-danger">{{ $pending_approval }}</span>--}}
{{--                        @endif--}}
{{--                    </a>--}}

{{--                    <ul class="nav-dropdown-items">--}}
                        <li class="nav-item">
                            <a class="nav-link {{ active_class(Route::is('admin/auth/user*')) }}"
                               href="#">
{{--                                {{ route('admin.auth.user.index') }}--}}
                                @lang('labels.backend.access.users.management')

                                @if ($pending_approval > 0)
                                    <span class="badge badge-danger">{{ $pending_approval }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ active_class(Route::is('admin/auth/role*')) }}"
                               href="{{ route('admin.auth.role.index') }}">
                                @lang('labels.backend.access.roles.management')
                            </a>
                        </li>


{{--                    </ul>--}}
{{--                </li>--}}
{{--                Subscription Menu--}}
{{--                <li class="nav-item nav-dropdown {{ active_class(Active::checkUriPattern('admin/subscriptions*'), 'open') }}">--}}
{{--                    <a class="nav-link nav-dropdown-toggle {{ active_class(Active::checkUriPattern('admin/subscriptions*')) }}"--}}
{{--                       href="#">--}}
{{--                        <i class="nav-icon icon-user"></i> Subscriptions--}}
{{--                    </a>--}}
{{--                    <ul class="nav-dropdown-items">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/subscriptions/')) }}"--}}
{{--                               href="{{ route('admin.subscriptions.index') }}">--}}
{{--                                Subscription List--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/subscriptions/create*')) }}"--}}
{{--                               href="{{ route('admin.subscriptions.create') }}">--}}
{{--                                Add Subscription--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                    <ul class="nav-dropdown-items">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/protocols')) }}"--}}
{{--                               href="{{ route('admin.protocols.index') }}">--}}
{{--                                Protocols List--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/protocols/create*')) }}"--}}
{{--                               href="{{ route('admin.protocols.create') }}">--}}
{{--                                Add Protocols--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}


{{--                </li>--}}
{{--                --}}{{--Subscription Menu End--}}


{{--                --}}{{--Service Providers Menu--}}
{{--                <li class="nav-item nav-dropdown {{ active_class(Active::checkUriPattern('admin/service-providers*'), 'open') }}">--}}
{{--                    <a class="nav-link nav-dropdown-toggle {{ active_class(Active::checkUriPattern('admin/service-providers*')) }}"--}}
{{--                       href="#">--}}
{{--                        <i class="nav-icon icon-user"></i> Service Providers--}}
{{--                    </a>--}}
{{--                    <ul class="nav-dropdown-items">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/service-providers/')) }}"--}}
{{--                               href="">--}}
{{--                                {{ route('admin.service-providers.index') }}--}}
{{--                                Service Provider List--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/service-providers/create*')) }}"--}}
{{--                               href="">--}}
{{--                                {{ route('admin.service-providers.create') }}--}}
{{--                                Add Service Provider--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                    <ul class="nav-dropdown-items">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/services')) }}"--}}
{{--                               href="">--}}
{{--                                {{ route('admin.services.index') }}--}}
{{--                                Services List--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/services/create*')) }}"--}}
{{--                               href="">--}}
{{--                                {{ route('admin.services.create') }}--}}
{{--                                Add Services--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}


{{--                </li>--}}
{{--                --}}{{--Service Providers Menu End--}}


{{--                VPN Servers Menu--}}
{{--                <li class="nav-title">--}}
{{--                    Servers--}}
{{--                </li>--}}
{{--                <li class="nav-item nav-dropdown {{ active_class(Active::checkUriPattern('admin/v-p-n-servers*'), 'open') }}">--}}
{{--                    <a class="nav-link nav-dropdown-toggle {{ active_class(Active::checkUriPattern('admin/sv-p-n-servers*')) }}"--}}
{{--                       href="#">--}}
{{--                        <i class="nav-icon icon-user"></i> VPN Servers--}}
{{--                    </a>--}}
{{--                    <ul class="nav-dropdown-items">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/v-p-n-servers/')) }}"--}}
{{--                               href="{{ route('admin.v-p-n-servers.index') }}">--}}

{{--                                VPN Server List--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/v-p-n-servers/create*')) }}"--}}
{{--                               href="">--}}
{{--                                {{ route('admin.v-p-n-servers.create') }}--}}
{{--                                Add VPN Server--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </li>--}}
{{--                Service Providers Menu End--}}

{{--                --}}{{--VPN Servers Menu--}}
{{--                <li class="nav-item nav-dropdown {{ active_class(Active::checkUriPattern('admin/intercom-marketing-data*'), 'open') }}">--}}
{{--                    <a class="nav-link nav-dropdown-toggle {{ active_class(Active::checkUriPattern('admin/intercom-marketing-data*')) }}"--}}
{{--                       href="#">--}}
{{--                        <i class="nav-icon icon-user"></i> Intercom--}}
{{--                    </a>--}}
{{--                    <ul class="nav-dropdown-items">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/intercom-marketing-data/')) }}"--}}
{{--                               href="{{ route('admin.intercom-marketing-data.index') }}">--}}
{{--                               --}}
{{--                                List--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/intercom-marketing-data/')) }}"--}}
{{--                               href="{{ route('admin.admin.intercom-marketing-data.datag') }}">--}}
{{--                                --}}{{--                               --}}
{{--                                List Data Grid--}}
{{--                            </a>--}}
{{--                        </li>--}}

{{--                    </ul>--}}
{{--                </li>--}}
{{--                --}}{{--Service Providers Menu End--}}
            @endif


{{--            <li class="divider"></li>--}}

{{--            <li class="nav-item nav-dropdown {{ active_class(Active::checkUriPattern('admin/log-viewer*'), 'open') }}">--}}
{{--                <a class="nav-link nav-dropdown-toggle {{ active_class(Active::checkUriPattern('admin/log-viewer*')) }}"--}}
{{--                   href="#">--}}
{{--                    <i class="nav-icon icon-list"></i> @lang('menus.backend.log-viewer.main')--}}
{{--                </a>--}}

{{--                <ul class="nav-dropdown-items">--}}
{{--                    <li class="nav-item">--}}
{{--                        <a class="nav-link {{ active_class(Active::checkUriPattern('admin/log-viewer')) }}"--}}
{{--                           href="{{ route('log-viewer::dashboard') }}">--}}
{{--                            @lang('menus.backend.log-viewer.dashboard')--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    <li class="nav-item">--}}
{{--                        <a class="nav-link {{ active_class(Active::checkUriPattern('admin/log-viewer/logs*')) }}"--}}
{{--                           href="{{ route('log-viewer::logs.list') }}">--}}
{{--                            @lang('menus.backend.log-viewer.logs')--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                </ul>--}}
{{--            </li>--}}
        </ul>
    </nav>

    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div><!--sidebar-->
