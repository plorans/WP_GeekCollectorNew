{{-- 
Template Name: Stats
--}}

@extends('layouts.app')

@section('content')
    @php
        use TCGStats\Models\TournamentStat;

        $currentTcg = $_GET['tcg'] ?? null;
        $currentMonth = $_GET['month'] ?? null;

        $stats = [];
        $semestre = [];
        $semestreDate = [];

        if ($currentTcg && $currentMonth) {
            $semestreDate = tcg_semester_for_month($currentTcg, $currentMonth);

            if ($semestreDate) {
                $stats = TournamentStat::torneos($currentTcg, $currentMonth);
                $semestre = TournamentStat::global($currentTcg, $currentMonth);
            }
        }

        $isAdmin = current_user_can('manage_options');

        $torneos = collect($stats)->groupBy('torneo');
        $torneo1 = $torneos->get(1, collect());
        $torneo2 = $torneos->get(2, collect());

    @endphp

    <style>
        .hs-select>div.markup {
            position: absolute;
            right: 0.75rem;
            left: auto;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .bg-gradient {
            background:
                radial-gradient(120% 120% at top left,
                    #ff6a00 0%,
                    #e63900 18%,
                    #b11226 38%,
                    #6b0f14 58%,
                    rgba(0, 0, 0, 0.95) 75%,
                    #000 100%);
        }
    </style>

    <div class="bg-gradient">
        <div class="mx-auto min-h-[70vh] max-w-7xl px-4 pt-10 md:px-0">
            <div class="mb-8 text-5xl font-semibold text-white">
                Leaderboard
            </div>

            <div class="mb-8 text-lg font-semibold text-white md:text-2xl">
                Ranking de las ligas semestrales de GeekCollector. Aqui puedes revisar tu lugar en el <br class="hidden md:block"> standing y tus stats de los torneos.
            </div>

            <form method="get" class="mb-2 flex flex-col items-center gap-4 md:flex-row md:gap-8">
                <div class="mr-auto w-40 md:m-0">
                    <select name="tcg"
                        data-hs-select='{
                        "placeholder": "TCG",
                        "toggleTag": "<button type=\"button\" aria-expanded=\"false\"></button>",
                        "toggleClasses": " py-3 ps-4 pe-9 pl-2 flex gap-x-2 w-full cursor-pointer bg-[#252524] text-white border border-gray-200 rounded-lg text-start text-sm",
                        "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-[#252524] text-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300",
                        "dropdownVerticalFixedPlacement": "bottom",
                        "optionClasses": "py-2 px-4 w-full text-sm text-white cursor-pointer hover:bg-gray-500 rounded-lg focus:outline-hidden focus:bg-gray-100 ",
                        "optionTemplate": "<div class=\"flex justify-between items-center w-full\"><span data-title></span><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-3.5 text-blue-600 \" xmlns=\"http:.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"20 6 9 17 4 12\"/></svg></span></div>",
                        "extraMarkup": "<div class=\" markup \"><svg class=\"shrink-0 size-3.5 text-gray-500 \" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                        }'
                        class="hs-submit-on-change relative hidden">
                        <option value="">Choose</option>
                        <option value="onepiece" @selected($currentTcg === 'onepiece')>One Piece</option>
                        <option value="riftbound" @selected($currentTcg === 'riftbound')>Riftbound</option>
                        <option value="magic" @selected($currentTcg === 'magic')>Magic The Gathering</option>
                        <option value="dragonball" @selected($currentTcg === 'dragonball')>Dragon Ball</option>
                        <option value="digimon" @selected($currentTcg === 'digimon')>Digimon</option>
                        <option value="pokemon" @selected($currentTcg === 'pokemon')>Pokemon</option>
                        <option value="lorcana" @selected($currentTcg === 'lorcana')>Lorcana</option>
                        <option value="starwars" @selected($currentTcg === 'starwars')>Star Wars</option>
                        <option value="gundam" @selected($currentTcg === 'gundam')>Gundam</option>
                    </select>
                </div>

                @php
                    $startDate = null;
                    $months = [];
                    if ($currentTcg) {
                        $season = DB::table('tcg_seasons')->where('tcg_slug', $currentTcg)->first();
                        if ($season) {
                            $startDate = $season->season_start_date;
                        }
                    }
                    if ($startDate) {
                        $start = new DateTime($startDate);
                        $now = new DateTime('first day of this month');

                        $formatter = new IntlDateFormatter('es_MX', IntlDateFormatter::LONG, IntlDateFormatter::NONE, null, null, 'LLLL yyyy');

                        while ($start <= $now) {
                            $months[] = [
                                'label' => ucfirst($formatter->format($start)),
                                'value' => $start->format('Y-m'),
                            ];

                            $start->modify('+1 month');
                        }

                        $months = array_reverse($months);
                    }

                @endphp

                <div class="mr-auto w-40 md:m-0">
                    <select name="month"
                        data-hs-select='{
                        "placeholder": "Mes",
                        "toggleTag": "<button type=\"button\" aria-expanded=\"false\"></button>",
                        "toggleClasses": " py-3 ps-4 pe-9 pl-2 flex gap-x-2 w-full cursor-pointer bg-[#252524] text-white border border-gray-200 rounded-lg text-start text-sm",
                        "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-[#252524] text-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300",
                        "dropdownVerticalFixedPlacement": "bottom",
                        "optionClasses": "py-2 px-4 w-full text-sm text-white cursor-pointer hover:bg-gray-500 rounded-lg focus:outline-hidden focus:bg-gray-100 ",
                        "optionTemplate": "<div class=\"flex justify-between items-center w-full\"><span data-title></span><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-3.5 text-blue-600 \" xmlns=\"http:.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"20 6 9 17 4 12\"/></svg></span></div>",
                        "extraMarkup": "<div class=\" markup \"><svg class=\"shrink-0 size-3.5 text-gray-500 \" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                        }'
                        class="hs-submit-on-change relative hidden">
                        <option value="">Choose</option>
                        @foreach ($months as $month)
                            <option value="{{ $month['value'] }}" @selected($currentMonth === $month['value'])>
                                {{ $month['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mr-auto md:ml-auto md:mr-0">
                    <input class="rounded-xl border border-white bg-[#252524] px-4 py-3 text-white" id="jugadorSearch" placeholder="Busqueda de Jugador" type="text">
                </div>

            </form>

            <div class="mb-6 text-right text-sm text-gray-400">Es necesario tener usuario de geek para ver las posiciones.</div>

            <div class="mb-10 grid grid-cols-1 gap-4 md:grid-cols-4 lg:max-h-[470px]">
                <div class="max-h-[450px] overflow-scroll rounded-xl bg-[#252524] px-2 py-4">
                    <div class="pl-2"><span class="text-3xl font-semibold text-orange-500">Torneo #1 </span> <br> <span class="text-white">de la Semana</span></div>

                    <table class="mt-5 w-full" id="statsTable-1">
                        <thead class="border-b border-white">
                            <tr class="gap-2 text-white">
                                <th class="px-2">#</th>
                                <th class="px-3">Jugador</th>
                                <th class="px-2">Puntos</th>
                                <th class="px-3">OMW%</th>
                            </tr>
                        </thead>

                        @php  $i = 0 @endphp

                        <body>
                            @foreach ($torneo1 as $torneo)
                                @php $i += 1; @endphp

                                <tr class="stat-row py-2 text-center text-white">
                                    <td>{{ $i }}</td>
                                    <td class="jugador max-w-29 py-1 text-left">
                                        @if ($torneo['geek_tag'] || $isAdmin)
                                            <div class="flex min-w-0 items-center gap-2">
                                                <img src="{{ gc_get_avatar_url($torneo['geek_tag']) }}" alt="" class="h-5 w-5 shrink-0 rounded-full object-cover"
                                                    loading="lazy">
                                                <span class="line-clamp-1">
                                                    {{ $torneo['jugador'] }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">Sin usuario Geek</span>
                                        @endif
                                    </td>

                                    <td>{{ $torneo['puntos'] }}</td>
                                    <td>{{ $torneo['omw'] }}%</td>
                                </tr>
                            @endforeach
                        </body>
                    </table>
                </div>
                <div class="max-h-[450px] overflow-scroll rounded-xl bg-[#252524] px-2 py-4">
                    <div class="px-2"><span class="text-3xl font-semibold text-orange-500">Torneo #2 </span> <br> <span class="text-white">de la Semana</span></div>
                    <table class="mt-5 w-full" id="statsTable-2">
                        <thead class="border-b border-white pb-2">
                            <tr class="w-full gap-2 text-white">
                                <th class="px-2">#</th>
                                <th class="px-3">Jugador</th>
                                <th class="px-2">Puntos</th>
                                <th class="px-3">OMW%</th>
                            </tr>
                        </thead>

                        @php  $i = 0; @endphp

                        <body>
                            @foreach ($torneo2 as $torneo)
                                @php $i += 1; @endphp

                                <tr class="stat-row text-center text-white">
                                    <td>{{ $i }}</td>
                                    <td class="jugador max-w-29 py-1 text-left">
                                        @if ($torneo['geek_tag'] || $isAdmin)
                                            <div class="flex min-w-0 items-center gap-2">
                                                <img src="{{ gc_get_avatar_url($torneo['geek_tag']) }}" alt="" class="h-5 w-5 shrink-0 rounded-full object-cover"
                                                    loading="lazy">
                                                <span class="line-clamp-1">
                                                    {{ $torneo['jugador'] }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">Sin usuario Geek</span>
                                        @endif
                                    </td>

                                    <td>{{ $torneo['puntos'] }}</td>
                                    <td>{{ $torneo['omw'] }}%</td>
                                </tr>
                            @endforeach
                        </body>
                    </table>
                </div>
                <div class="max-h-[450px] overflow-scroll rounded-xl bg-[#252524] px-6 py-4 md:col-span-2">
                    <div>
                        <span class="text-3xl font-semibold text-orange-500">
                            Leaderboard Semestral
                        </span>
                        <br>
                        @if ($semestreDate)
                            <span class="text-white capitalize">
                                {{ $semestreDate['start']->translatedFormat('F Y') }}
                                -
                                {{ $semestreDate['end']->translatedFormat('F Y') }}
                            </span>
                        @else
                            <span class="text-sm text-gray-400">
                                Selecciona un TCG y un mes
                            </span>
                        @endif
                    </div>

                    <table class="" id="statsTable-global">
                        <thead class="border-b border-white">
                            <tr class="gap-2 text-white">
                                <th class="px-3">#</th>
                                <th class="min-w-45 px-4">Jugador</th>
                                <th class="px-4">Puntos</th>
                                <th class="px-4">Torneos <br> Ganados</th>
                                <th class="px-4">Victorias</th>
                                <th class="px-4">OMW%</th>
                            </tr>
                        </thead>

                        @php  $i = 0; @endphp

                        <body>
                            @foreach ($semestre as $jugador)
                                @php $i += 1; @endphp
                                <tr class="stat-row border-b border-gray-600 text-center text-white md:border-b-0">
                                    <td> {{ $i }} </td>
                                    <td class="jugador max-w-29 py-1 text-left">
                                        @if ($jugador['geek_tag'] || $isAdmin)
                                            <div class="flex min-w-0 items-center gap-2">
                                                <img src="{{ gc_get_avatar_url($jugador['geek_tag']) }}" alt="" class="h-5 w-5 shrink-0 rounded-full object-cover"
                                                    loading="lazy">
                                                <span class="line-clamp-1">
                                                    {{ $jugador['jugador'] }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">Sin usuario Geek</span>
                                        @endif
                                    </td>

                                    <td> {{ $jugador['puntos'] }} </td>
                                    <td> {{ $jugador['torneos_ganados'] }} </td>
                                    <td> {{ $jugador['total_victorias'] }} </td>
                                    <td> {{ $jugador['omw'] }}%</td>
                                </tr>
                            @endforeach
                        </body>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('change.hs.select', function(e) {
            const select = e.target;

            if (!select.classList.contains('hs-submit-on-change')) return;

            const form = select.closest('form');
            if (form) {
                form.submit();
            }
        });

        document.getElementById('jugadorSearch').addEventListener('input', function() {
            const search = this.value.toLowerCase();
            const rows = document.querySelectorAll(
                'table[id^="statsTable"] tbody tr'
            );

            rows.forEach(row => {
                const jugador = row.querySelector('.jugador')
                    .textContent
                    .toLowerCase();

                row.style.display = jugador.includes(search) ? '' : 'none';
            });
        });
    </script>
@endsection
