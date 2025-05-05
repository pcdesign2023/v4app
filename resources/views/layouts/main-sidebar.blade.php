<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header">
        <!-- Desktop Logo -->
        <a class="desktop-logo logo-light" href="{{ url('/') }}">
            <img src="{{ URL::asset('assets/img/brand/main.png') }}" class="main-logo" alt="logo">
        </a>
        <a class="desktop-logo logo-dark" href="{{ url('/') }}">
            <img src="{{ URL::asset('assets/img/brand/logo-white.png') }}" class="main-logo dark-theme" alt="logo">
        </a>

        <!-- Mobile Logo -->
        <a class="logo-icon mobile-logo icon-light" href="{{ url('/') }}">
            <img src="{{ URL::asset('assets/img/brand/favicon.png') }}" class="logo-icon" alt="logo">
        </a>
        <a class="logo-icon mobile-logo icon-dark" href="{{ url('/') }}">
            <img src="{{ URL::asset('assets/img/brand/favicon-white.png') }}" class="logo-icon dark-theme" alt="logo">
        </a>
    </div>

    <div class="main-sidemenu">
        <!-- User Info -->
        <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body">
                <!--<div>
                   <img alt="user-img" class="avatar avatar-xl brround" src="{{ URL::asset('assets/img/faces/6.jpg') }}">
                    <span class="avatar-status profile-status bg-green"></span>
                </div >-->
                <div class="user-info">
                    <h4 class="font-weight-semibold mt-3 mb-0">{{ Auth::user()->name }}</h4>
                    <span class="mb-0 text-muted">{{ Auth::user()->role->label ?? 'No Role Assigned' }}</span>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <ul class="side-menu">
            <!-- Administrator Only -->
            @if(auth()->check() && auth()->user()->role->label === 'Administrateur')
                <li class="side-item side-item-category">Admin Panel</li>

                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fa fa-1s fa-users  side-menu__icon"></i>
                        <span class="side-menu__label">Gestion des Utilisateurs</span>
                        <i class="angle fe fe-chevron-down"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a class="slide-item" href="{{ route('list-users') }}">Liste des Utilisateurs</a></li>
                        <li><a class="slide-item" href="{{ route('roles') }}">Gestion des Rôles</a></li>
                        <li><a class="slide-item" href="{{ route('data_import.index') }}">Importation de Données</a></li>
                    </ul>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fa fa-archive side-menu__icon"></i>
                        <span class="side-menu__label">Éléments d'Entrée</span>
                        <i class="angle fe fe-chevron-down"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a class="slide-item" href="{{ route('clients.index') }}">Liste des Clients</a></li>
                        <li><a class="slide-item" href="{{ route('chaine.index') }}">Liste des Chaines</a></li>
                        <li><a class="slide-item" href="{{ route('products.index') }}">Liste des Produits</a></li>
                    </ul>
                </li>
            @endif

            <!-- Shared Access (Admin & Quality Agent) -->
            @if(auth()->check() && in_array(auth()->user()->role->label, ['Administrateur', 'Agent qualité']))
                <li class="side-item side-item-category">Qualité</li>
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('anomalies.index') }}">
                        <i class="fa fa-exclamation-circle side-menu__icon"></i>
                        <span class="side-menu__label">Défauts Qualité</span>
                    </a>
                </li>
            @endif

            <!-- Fabrication Access -->
            @if(auth()->check() && in_array(auth()->user()->role->label, ['Administrateur', 'Chef de Chaine']))
                <li class="side-item side-item-category">Gestion des OF</li>
                <li class="slide">
                    <a class="side-menu__item  {{ request()->routeIs('fab_chain.index') ? 'active' : '' }}" href="{{ route('fab_orders.index') }}">
                        <i class="fa fa-industry side-menu__icon"></i>
                        <span class="side-menu__label">Ordre de Fabrication</span>
                    </a>
                </li>
                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('fab_chain.index') ? 'active' : '' }}" href="{{ route('fab_chain.index') }}">
                        <i class="fa fa-cogs side-menu__icon"></i>
                        <span class="side-menu__label">Déclaration de Fabrication</span>
                    </a>
                </li>
            @endif

            <!-- Reporting Section (Admin Only) -->
            @if(auth()->check() && in_array(auth()->user()->role->label, ['Administrateur', 'Chef de Chaine']))
                <li class="side-item side-item-category">Reporting</li>
                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('fabrication.comparison') ? 'active' : '' }}" href="{{ route('fabrication.comparison') }}">
                        <i class="fa fa-chart-line side-menu__icon"></i>
                        <span class="side-menu__label">Reporting Fabrication</span>
                    </a>
                </li>
                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('fabrication.comparison-sale-order') ? 'active' : '' }}" href="{{ route('fabrication.comparison-sale-order') }}">
                        <i class="fa fa-shopping-cart side-menu__icon"></i>
                        <span class="side-menu__label">Reporting Commande</span>
                    </a>
                </li>
            @endif
            <!-- Reporting Section (Admin Only) -->
            @if(auth()->check() && in_array(auth()->user()->role->label, ['Administrateur']))
                <li class="side-item side-item-category">Planning </li>
                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('planning.index') ? 'active' : '' }}" href="{{ route('planning.index') }}">
                        <i class="fa fa-calendar side-menu__icon"></i>
                        <span class="side-menu__label">Planning</span>
                    </a>
                </li>

            @endif
            <!-- Reporting Section ( Only) -->
            @if(auth()->check() && in_array(auth()->user()->role->label, ['Administrateur']))
                <li class="side-item side-item-category">Prod Planning </li>
                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('ofplanning.index') ? 'active' : '' }}" href="{{ route('ofplanning.index') }}">
                        <i class="fa fa-calendar side-menu__icon"></i>
                        <span class="side-menu__label">Planning</span>
                    </a>
                </li>
                @if(auth()->check() && in_array(auth()->user()->role->label, ['Administrateur']))
                    <li class="slide">
                        <a class="side-menu__item {{ request()->routeIs('ofplanning.supchain') ? 'active' : '' }}" href="{{ route('ofplanning.supchain') }}">
                            <i class="fa fa-calendar side-menu__icon"></i>
                            <span class="side-menu__label">Planning MP</span>
                        </a>
                    </li>
                @endif
                @if(auth()->check() && in_array(auth()->user()->role->label, ['Administrateur']))
                    <li class="slide">
                        <a class="side-menu__item {{ request()->routeIs('ofplanning.cerigmp') ? 'active' : '' }}" href="{{ route('ofplanning.cerigmp') }}">
                            <i class="fa fa-calendar side-menu__icon"></i>
                            <span class="side-menu__label">Planning PF</span>
                        </a>
                    </li>
                @endif
            <!-- Reporting Section ( Only) -->

            @endif

            <!-- Quality Department -->
            @if(auth()->check() && in_array(auth()->user()->role->label, ['Administrateur', 'Quality Resp', 'Agent Qualité']))
                <li class="side-item side-item-category">Département Qualité</li>
                <li class="slide">
                    <a class="side-menu__item{{ request()->routeIs('fabrication.comparison-sale-order') ? 'active' : '' }}" href="{{ route('quality.index') }}">
                        <i class="fa fa-shield-alt side-menu__icon"></i>
                        <span class="side-menu__label">Déclaration Défault</span>
                    </a>
                </li>
                <li class="slide">

                <a class="side-menu__item{{ request()->routeIs('reportigQuality') ? 'active' : '' }}" href="{{ route('reportigQuality') }}">
                        <i class="fa fa-shield-alt side-menu__icon"></i>
                        <span class="side-menu__label">Reporting Qualité</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</aside>
<!-- End Main Sidebar -->
<style>
    .side-menu__item.active {
        color: blue !important;
        font-weight: bold;
    }
</style>
