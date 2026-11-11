
<header class="border-b border-slate-200 px-6 md:px-8 py-5">

    <div class="flex flex-wrap justify-between items-center gap-3">

        <div>
            <p class="text-xs text-slate-400 uppercase tracking-wider">
                EUROPE SALES <span class="mx-1 text-slate-500">/</span> ANALYTICS
            </p>

            <h2 class="text-lg font-bold text-slate-800 mt-1">
                @yield('page-heading', 'Executive Dashboard')
            </h2>
        </div>

        <div class="flex items-center gap-3">

            <div class="text-right">
                <p class="text-sm font-semibold text-slate-700">
                    Europe workspace
                </p>

                <p class="text-xs text-slate-400">
                    Retail · Sales data
                </p>
            </div>

            <div class="w-10 h-10 flex items-center justify-center" title="European Union">
                <img src="{{ asset('images/eu-flag.svg') }}" alt="Bendera Uni Eropa" class="w-10 h-10">
            </div>

        </div>

    </div>

</header>
