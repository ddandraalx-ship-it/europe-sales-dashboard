
<aside class="sidebar p-6">

    <div class="sidebar-brand mb-10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#123d75;box-shadow:0 6px 20px rgba(18,61,117,.28)">
                <img src="{{ asset('images/eu-flag.svg') }}" width="31" height="31" alt="Bendera Uni Eropa">
            </div>

            <div>
                <h1 class="text-sm font-bold tracking-wide">
                    EUROPE SALES
                </h1>

                <p class="text-xs text-slate-400 mt-1">
                    Sales intelligence
                </p>
            </div>
        </div>
    </div>

    <p class="sidebar-menu-label text-xs font-semibold uppercase tracking-widest text-slate-500 mb-4 px-3">
        Main Menu
    </p>

    <nav>

        <a href="{{ route('dashboard') }}"
           class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span>▦</span>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('drilldown') }}"
           class="menu-link {{ request()->routeIs('drilldown') ? 'active' : '' }}">
            <span>⌕</span>
            <span>Drill-Down Analysis</span>
        </a>

        <a href="{{ route('whatif') }}"
           class="menu-link {{ request()->routeIs('whatif') ? 'active' : '' }}">
            <span>◈</span>
            <span>What-If Analysis</span>
        </a>

        <a href="{{ route('etl') }}"
           class="menu-link {{ request()->routeIs('etl*') ? 'active' : '' }}">
            <span>⌘</span>
            <span>ETL Management</span>
        </a>

    </nav>

    <div class="sidebar-source">
        <p class="text-xs text-slate-500">
            DATA SOURCE
        </p>

        <p class="text-sm text-slate-300 mt-2">
            Europe Sales Dataset
        </p>

        <p class="text-xs text-slate-500 mt-1">
            Sales Management Dashboard
        </p>

        <p class="text-xs text-slate-500">
            MySQL · Europe
        </p>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="sidebar-logout">
        @csrf
        <button type="submit" class="menu-link">↪ <span>Sign out</span></button>
    </form>

</aside>
