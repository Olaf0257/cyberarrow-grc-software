
    <style nonce="{{ csp_nonce() }}">
        @media (min-width: 768px) {
            .logo-box span.logo img {
                padding: 6px;
            }
        }

        @media (max-width: 767px) and (min-width: 574px){


            .logo .logo-lg-text-light {
                color: #fff;
                font-weight: 700;
                font-size: 18px;
                text-transform: uppercase;
            }

            .card-body h5.head-text {
                display: none;
            }

            .card-body ul.rect-div {
                display: flex;
                justify-content: center;
                margin-top: 0;
            }
        }

        /********************
            RESPONSIVE CSS
        *********************/
        @media (min-width: 320px) and (max-width: 573px)  {



        .logo .logo-lg-text-light {
                color: #fff;
                font-weight: 500;
                font-size: 12px;
                text-transform: uppercase;
            }
        }

        @media (max-width: 767px) {
           .user-logout-dropdown-wp {
                padding-top: 12px;
           }
        }


    </style>

<!-- Navigation Bar-->
<header id="topnav" class="primary-bg-color">

<!-- Topbar Start -->
<div class="navbar-custom">
    <div class="container-fluid">
        <ul class="list-unstyled topnav-menu float-end mb-0">

            <li class="dropdown notification-list">
                <!-- Mobile menu toggle-->
                <a class="navbar-toggle nav-link">
                    <div class="lines">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </a>
                <!-- End mobile menu toggle-->
            </li>

            <li class="dropdown notification-list user-logout-dropdown-wp">
                <a class="nav-link dropdown-toggle nav-user me-0 waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="avatar">
                        {{ decodeHTMLEntity($loggedInUser->avatar) }}
                    </span>
                    <span class="pro-user-name ms-1">
                        {{\Str::limit(decodeHTMLEntity($loggedInUser->full_name, 16)) }} <i class="mdi mdi-chevron-down"></i>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                    <!-- item-->
                    <a href="{{ route('admin-user-management-edit', $loggedInUser->id) }}" class="dropdown-item notify-item">
                        <i class="fe-user"></i>
                        <span>My Account</span>
                    </a>

                    <div class="dropdown-divider"></div>

                    <!-- item-->
                    <a href="{{ $loggedInUser->is_sso_auth ? route('saml2.logout') :  route('admin-logout') }}" class="dropdown-item notify-item">
                        <i class="fe-log-out"></i>
                        <span>Logout</span>
                    </a>

                </div>
            </li>
        </ul>

        <!-- LOGO -->
        @php

            $logoRedirectRoute = '#';

            if($loggedInUser->roles->contains('name', 'Global Admin')){
                $logoRedirectRoute = route('global.dashboard') ;
            } else {
                if($loggedInUser->roles->contains('name', 'Contributor')){
                    $logoRedirectRoute = route('compliance-dashboard') ;
                }
            }
        @endphp
        <div class="logo-box">
            <a href="{{$logoRedirectRoute }}" class="logo text-center">
                <span class="logo">
                    <img src="{{ $globalSetting->company_logo =='assets/images/ebdaa-Logo.png' ? asset($globalSetting->company_logo): tenant_asset($globalSetting->company_logo) }}"  alt="Company Logo" width="70" height="">
                    <span class="logo-lg-text-light secondary-text-color">{{ decodeHTMLEntity($globalSetting->display_name) }}</span>
                </span>
            </a>
        </div>
    </div> <!-- end container-fluid-->
</div>
<!-- end Topbar -->

<div class="topbar-menu">
    <div class="container-fluid">
        <div id="navigation">
            <ul class="navigation-menu">
            @if($loggedInUser->hasAnyRole(['Global Admin']))
                <li>
                    <a href="{{route('global.dashboard')}}">
                        <i class="mdi mdi-earth"></i>Global Dashboards
                    </a>
                </li>
            @endif
            @if(
                $loggedInUser->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Contributor'])
            )
                <li class="has-submenu">
                    <a href="#" data-navlink="{{ route('compliance-dashboard') }}" class="first-child-redirect"> <i class="fe-shield"></i>Compliance<div class="arrow-down"></div></a>

                    <ul class="submenu">
                        <li>
                            <a href="{{route('compliance-dashboard')}}">My Dashboard</a>
                        </li>

                        <li>
                            <a href="{{ route('compliance-projects-view') }}">Projects</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if($loggedInUser->hasAnyRole(['Global Admin', 'Policy Administrator']))
                <li class="has-submenu">
                    <a href="#" data-navlink="{{ route('policy-management.campaigns') }}" class="first-child-redirect"> <i class="fe-layout"></i>Policy Management <div class="arrow-down"></div></a>

                    <ul class="submenu">
                        <li>
                            <a href="{{ route('policy-management.campaigns') }}">Campaigns</a>
                        </li>
                        <li>
                            <a href="{{ route('policy-management.policies') }}">Policies</a>
                        </li>
                        <li>
                            <a href="{{ route('policy-management.users-and-groups') }}">Users & Groups</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if($loggedInUser->hasAnyRole(['Global Admin', 'Risk Administrator']))
                <li class="has-submenu">
                    <a href="#" data-navlink="{{ route('risks.dashboard.index') }}" class="first-child-redirect"> <i class="fe-alert-triangle"></i>Risk Management <div class="arrow-down"></div></a>
                    <ul class="submenu">
                        <li>
                            <a href="{{ route('risks.dashboard.index') }}">Dashboard</a>
                        </li>
                        <li>
                            <a href="{{ route('risks.register.index') }}">Risk Register</a>
                        </li>
                        <li>
                            <a href="{{ route('risks.setup') }}">Risk Setup</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if($loggedInUser->hasAnyRole(['Global Admin', 'Compliance Administrator', 'Auditor', 'Contributor']))
                <li>
                    <a href="{{ route('compliance.implemented-controls') }}"> <i class="fe-sliders"></i>Controls</a>
                </li>
            @endif

            @if($loggedInUser->hasAnyRole(['Global Admin']))
                <li class="has-submenu last-elements">
                    <a data-navlink="{{ route('global-settings') }}" class="first-child-redirect" href="#">
                        <i class="fe-database"></i>Administration <div class="arrow-down"></div>
                    </a>

                    <ul class="submenu">
                        <li>
                            <a href="{{ route('global-settings') }}">Global Settings</a>
                        </li>

                        <li class="has-submenu">
                            <a href="{{ route('admin-user-management-view') }}">User Management
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('compliance-template-view') }}">Compliance Templates</a>
                        </li>
                    </ul>
                </li>

            @endif
            </ul>

            <div class="clearfix"></div>
        </div>
    </div>
</div>
<!-- end navbar-custom -->

</header>

<script nonce="{{ csp_nonce() }}">

    Array.from(document.getElementsByClassName("first-child-redirect")).forEach(function(element) {
        element.addEventListener('click', function() {
            var navHref = $(this).data('navlink');

            if (window.innerWidth > 991){
                window.location.href = navHref
            }
        });
    });

</script>
<!-- End Navigation Bar-->
<div class="wrapper">
<div class="container-fluid">
