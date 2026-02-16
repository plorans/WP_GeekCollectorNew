{{--
  Template Name: Torneos Template
--}}
@extends('layouts.app')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js"></script>

    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        /* HERO */
        .hero-banner {
            position: relative;
            height: 40vh;
            min-height: 280px;
            overflow: hidden;
        }

        .hero-banner::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.75));
            z-index: 1;
        }

        /* CALENDARIO */
        .calendar-container {
            background: #1a1a1a;
            border-radius: 12px;
        }

        .fc {
            --fc-page-bg-color: #1a1a1a;
            --fc-neutral-bg-color: #1a1a1a;
            --fc-border-color: #2a2a2a;
        }

        .fc .fc-scrollgrid,
        .fc .fc-daygrid,
        .fc .fc-daygrid-day {
            background-color: #1a1a1a;
        }

        .fc-theme-standard td,
        .fc-theme-standard th {
            border-color: #2a2a2a;
        }

        /* Header */
        .fc .fc-col-header-cell {
            background-color: #1a1a1a;
        }

        .fc .fc-col-header-cell-cushion {
            color: #f97316;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 6px 4px;
        }

        /* Buttons */
        .fc-button {
            background-color: #f97316 !important;
            color: #fff !important;
            border: none !important;
        }

        .fc-button:hover {
            background-color: #ea580c !important;
        }

        .fc-event-container {
            cursor: pointer;
        }

        /* EVENT CARD (MOBILE FIRST) */
        .fc-event {
            background: transparent;
            border: none;
        }

        .fc-daygrid-event {
            padding: 2px;
            white-space: normal !important;
        }

        .fc-event-container {
            background: linear-gradient(180deg, #1f1f1f, #161616);
            border: 1px solid #2a2a2a;
            border-radius: 8px;
            padding: 4px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
        }

        .fc-event-container:hover {
            border-color: #f97316;
        }

        .fc-toolbar-title {
            text-transform: capitalize;
        }

        /* Image */
        .event-icon {
            width: 22px;
            height: 22px;
            object-fit: contain;
        }

        /* Text container */
        .event-text {
            width: 100%;
            text-align: center;
            overflow: hidden;
        }

        /* Title */
        .event-title {
            font-size: 0.65rem;
            font-weight: 600;
            color: #e5e5e5;
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Time */
        .event-time {
            font-size: 0.55rem;
            color: #9ca3af;
        }

        /* Day height mobile */
        .fc-daygrid-day-frame {
            min-height: 70px;
            padding: 4px;
        }

        /* DESKTOP OVERRIDE */
        @media (min-width: 768px) {
            .fc-event-container {
                flex-direction: row;
                align-items: flex-start;
                padding: 6px 10px;
                gap: 8px;
            }

            .event-icon {
                width: 36px;
                height: 36px;
            }

            .event-text {
                text-align: left;
            }

            .event-title {
                font-size: 0.8rem;
                white-space: normal;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }

            .event-time {
                font-size: 0.65rem;
            }

            .fc-daygrid-day-frame {
                min-height: 110px;
                padding: 6px;
            }
        }

        /* Mobile */
        .fc-list {
            background: #1a1a1a;
        }

        .fc-list-event {
            background: #161616;
            border: 1px solid #2a2a2a;
            border-radius: 8px;
            margin-bottom: 6px;
            padding: 8px;
        }

        .fc-list-event:hover {
            border-color: #f97316;
        }

        :root {
            --fc-list-event-hover-bg-color: #1f1f1f !important;
        }
    </style>

    <!-- HERO -->
    <div class="-mt-2 overflow-hidden bg-black">
        <div class="hero-banner relative my-2 mb-10 aspect-[3/3] w-full overflow-hidden sm:aspect-[16/7] md:aspect-[16/6]">
            <img src="{{ asset('/resources/images/torneos4.png') }}" class="absolute h-full w-full object-cover object-center" alt="Banner principal" />
            <div class="absolute bottom-0 left-0 z-10 w-full p-6">
                <h1 class="mb-2 text-4xl font-bold text-white">Torneos</h1>
                <p class="text-xl text-orange-200">Compite y demuestra tu habilidad en nuestros eventos</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-2 py-4 sm:px-4">

        <!-- CALENDAR -->
        <div class="calendar-container mb-10 p-4 text-white md:p-6">
            <div id="calendar"></div>
        </div>

    </div>

    <!-- Mapa Mejorado -->
    <section class="animate-fade-in my-10 flex flex-col items-start justify-center overflow-hidden rounded-xl bg-black text-white shadow-xl lg:flex-row">
        <div class="pt-15 flex h-auto w-full md:mx-10 md:px-20 lg:w-[45%]">
            <ul class="flex w-full flex-col items-start justify-start">
                <li class="w-full font-bold">
                    <div class="flex w-full flex-row items-center">
                        <div class="mb-2 flex flex-col justify-center py-3">
                            <h2 class="text-4xl text-orange-500">Encuéntranos</h2>
                            <p class="mt-1 text-gray-300">Ven y únete a la comunidad</p>
                        </div>
                    </div>
                </li>

                <li class="w-full font-bold">
                    <div class="flex w-full flex-row items-center transition-colors duration-300 hover:text-orange-400">
                        <div class="mx-3 my-auto">
                            <img class="h-10 w-10" src="{{ get_template_directory_uri() . '/resources/images/nosotros/llamar.png' }}" alt="Ícono de Teléfono">
                        </div>
                        <div class="flex flex-col items-start justify-center py-3">
                            <h2 class="text-sm text-gray-400">TELÉFONO:</h2>
                            <p class="text-lg md:text-xl">+52 81 2080 2420</p>
                        </div>
                    </div>
                    <div class="h-[1px] w-full bg-gray-800"></div>
                </li>
                <li class="w-full font-bold">
                    <div class="flex w-full flex-row items-center transition-colors duration-300 hover:text-orange-400">
                        <div class="mx-3 my-auto">
                            <img class="h-10 w-10" src="{{ get_template_directory_uri() . '/resources/images/nosotros/localizacion.png' }}" alt="Ícono de Ubicación">
                        </div>
                        <div class="flex flex-col items-start justify-center py-3">
                            <h2 class="text-sm text-gray-400">UBICACIÓN:</h2>
                            <p class="text-lg md:text-xl">AVE. ALFONSO REYES 255</p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="h-80 w-full flex items-center lg:h-96 lg:w-[55%]">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3596.8868565144835!2d-100.27947329999999!3d25.641880399999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8662bf99d30c3957%3A0x3492755f898822d6!2sAv.%20Alfonso%20Reyes%20255%2C%20Contry%2C%2064860%20Monterrey%2C%20N.L.!5e0!3m2!1ses-419!2smx!4v1755549626022!5m2!1ses-419!2smx"
                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="rounded-r-xl"></iframe>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {

            const calendarEl = document.getElementById('calendar');
            const response = await fetch('/wp-json/gc/v1/tournaments');
            const events = await response.json();
            const isMobile = window.innerWidth < 768;

            const calendar = new FullCalendar.Calendar(calendarEl, {

                initialView: isMobile ? 'listWeek' : 'dayGridMonth',
                height: 'auto',
                locale: 'es',

                headerToolbar: {
                    left: '',
                    center: 'title',
                    right: 'prev,next'
                },

                titleFormat: {
                    year: 'numeric',
                    month: 'short'
                },

                events: events,

                eventDidMount: function(info) {
                    // Prevent FullCalendar internal link logic
                    info.el.removeAttribute('href');
                },

                eventClick: function(info) {
                    const link = info.event.extendedProps.link;
                    if (link) {
                        window.location.href = link;
                    }
                },

                eventContent: function(arg) {

                    const {
                        imageurl
                    } = arg.event.extendedProps;
                    const title = arg.event.title;
                    const startDate = arg.event.start;

                    const time = startDate ?
                        startDate.toLocaleTimeString('es-MX', {
                            hour: '2-digit',
                            minute: '2-digit'
                        }) :
                        '';

                    const container = document.createElement('div');
                    container.className = 'fc-event-container';

                    if (isMobile && arg.view.type === 'listWeek') {
                        const titleEl = document.createElement('div');
                        titleEl.textContent = title;
                        titleEl.style.fontWeight = '600';
                        titleEl.style.color = '#fff';

                        const timeEl = document.createElement('div');
                        timeEl.textContent = time;
                        timeEl.style.fontSize = '0.75rem';
                        timeEl.style.color = '#9ca3af';

                        container.appendChild(titleEl);
                        container.appendChild(timeEl);

                        return {
                            domNodes: [container]
                        };
                    }

                    if (imageurl) {
                        const img = document.createElement('img');
                        img.src = imageurl;
                        img.className = 'event-icon';
                        img.alt = title;
                        container.appendChild(img);
                    }

                    const text = document.createElement('div');
                    text.className = 'event-text';

                    const titleEl = document.createElement('div');
                    titleEl.className = 'event-title';
                    titleEl.textContent = title;

                    const timeEl = document.createElement('div');
                    timeEl.className = 'event-time';
                    timeEl.textContent = time;

                    text.appendChild(titleEl);
                    text.appendChild(timeEl);
                    container.appendChild(text);

                    return {
                        domNodes: [container]
                    };
                }
            });

            calendar.render();
        });
    </script>
@endsection
