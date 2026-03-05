{{--
  Template Name: Membresias Template
--}}
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-2 py-4 uppercase sm:px-4">
        <!-- Swiper styles -->
        <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
        <style>
            .swiper-slide {
                background: #000000;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
            }

            .swiper-button-next,
            .swiper-button-prev {
                color: #ff6600;
            }
        </style>

        <!-- Titulo -->
        <div class="flex flex-col flex-wrap content-center justify-center rounded-b-full bg-[#191919d0] py-4 text-center text-3xl font-bold text-white md:py-7 md:text-5xl">
            Membresias
            <span class="bg-gradient-to-r from-yellow-200 via-orange-600 to-red-600 bg-clip-text text-transparent">
                Premium
            </span>
        </div>

        <!-- Swiper container -->
        <div class="swiper swiper-container mt-10 h-[200px] w-full md:h-[450px]">
            <div class="swiper-wrapper text-white">

                @php
                    $membresias = [
                        ['img' => 'M4.png', 'name' => 'Legendary Guardian', 'url' => 'https://geekcollector.com/producto/legendary-guardian/'],
                        ['img' => 'M5.png', 'name' => 'Cosmic Overlord', 'url' => 'https://geekcollector.com/producto/cosmic-overlord/'],
                        ['img' => 'M1.png', 'name' => 'Byte Seeker', 'url' => 'https://geekcollector.com/producto/byte-seeker/'],
                        ['img' => 'M2.png', 'name' => 'Pixel Knight', 'url' => 'https://geekcollector.com/producto/pixel-knight/'],
                        ['img' => 'M3.png', 'name' => 'Realm Sorcerer', 'url' => 'https://geekcollector.com/producto/realm-sorcerer/'],
                        ['img' => 'M4.png', 'name' => 'Legendary Guardian', 'url' => 'https://geekcollector.com/producto/legendary-guardian/'],
                        ['img' => 'M5.png', 'name' => 'Cosmic Overlord', 'url' => 'https://geekcollector.com/producto/cosmic-overlord/'],
                        ['img' => 'M1.png', 'name' => 'Byte Seeker', 'url' => 'https://geekcollector.com/producto/byte-seeker/'],
                        ['img' => 'M2.png', 'name' => 'Pixel Knight', 'url' => 'https://geekcollector.com/producto/pixel-knight/'],
                        ['img' => 'M3.png', 'name' => 'Realm Sorcerer', 'url' => 'https://geekcollector.com/producto/realm-sorcerer/'],
                    ];
                @endphp

                @foreach ($membresias as $membresia)
                    <div class="swiper-slide text-white">
                        <a href="{{ $membresia['url'] }}">
                            <img class="h-full w-full object-cover" src="{{ asset('resources/images/membresias/' . $membresia['img']) }}" alt="{{ $membresia['name'] }}" />
                        </a>
                    </div>
                @endforeach

            </div>
            <div class="swiper-button-next invisible rounded-2xl bg-gray-300/50 p-8 md:visible"></div>
            <div class="swiper-button-prev invisible rounded-2xl bg-gray-300/50 p-8 md:visible"></div>
        </div>

        <!-- Tabla Beneficios -->
        <div class="container mx-auto px-2 py-4 sm:px-4">
            <div class="mt-15 flex flex-col items-center justify-center">
                <div class="flex flex-row items-center gap-1 md:gap-3">
                    <div><span class="text-xl font-bold text-white md:text-3xl">ACCEDE A</span></div>
                    <div
                        class="inline-block rounded-xl bg-gradient-to-r from-yellow-200 via-orange-600 to-red-600 p-[2px] transition-all duration-300 hover:from-yellow-300 hover:via-orange-500 hover:to-red-500">
                        <div class="rounded-xl bg-black px-2 py-2 text-xl font-bold text-white md:text-3xl">
                            Beneficios Exclusivos
                        </div>
                    </div>
                </div>
                <div class="mt-2 text-center text-xl text-white">
                    Personaliza tu experiencia y colecciona como un verdadero pro.
                </div>
            </div>

            @php
                $membresias = [
                    ['color' => '#f54a00', 'name' => 'Byte Seeker', 'url' => 'https://geekcollector.com/producto/byte-seeker/'],
                    ['color' => '#fb2c36', 'name' => 'Pixel Knight', 'url' => 'https://geekcollector.com/producto/pixel-knight/'],
                    ['color' => '#00668e', 'name' => 'Realm Sorcerer', 'url' => 'https://geekcollector.com/producto/realm-sorcerer/'],
                    ['color' => '#949494', 'name' => 'Legendary Guardian', 'url' => 'https://geekcollector.com/producto/legendary-guardian/'],
                    ['color' => '#e9c979', 'name' => 'Cosmic Overlord', 'url' => 'https://geekcollector.com/producto/cosmic-overlord/'],
                ];

                $data = [
                    [
                        'Capacidad Maxima' => 'ILIMITADA',
                        'Torneos Semanales' => 'NO',
                        'Subasta Comision' => 'NO',
                        'Apartado' => 'NO',
                        'Stream Area' => 'NO',
                        'Grading Mensual' => 'NO',
                        'Credito' => 'NO',
                        'Descuento' => 'NO',
                        'Mostrar Coleccion' => 'NO',
                    ],
                    [
                        'Capacidad Maxima' => 'ILIMITADA',
                        'Torneos Semanales' => '2 ENTRADAS (MES)',
                        'Subasta Comision' => 'NO',
                        'Apartado' => 'NO',
                        'Stream Area' => 'NO',
                        'Grading Mensual' => 'NO',
                        'Credito' => 'NO',
                        'Descuento' => 'NO',
                        'Mostrar Coleccion' => 'NO',
                    ],
                    [
                        'Capacidad Maxima' => '100 MIEMBROS',
                        'Torneos Semanales' => '5 ENTRADAS (MES)',
                        'Subasta Comision' => '1 ARTÍCULO',
                        'Apartado' => 'NO',
                        'Stream Area' => 'NO',
                        'Grading Mensual' => 'NO',
                        'Credito' => 'NO',
                        'Descuento' => 'NO',
                        'Mostrar Coleccion' => 'NO',
                    ],
                    [
                        'Capacidad Maxima' => '20 MIEMBROS',
                        'Torneos Semanales' => 'INCLUIDO',
                        'Subasta Comision' => '4 ARTÍCULOS',
                        'Apartado' => 'Máx. 2 Cajas',
                        'Stream Area' => 'SI',
                        'Grading Mensual' => '$500 MXN',
                        'Credito' => '$1,500 MXN',
                        'Descuento' => '5%',
                        'Mostrar Coleccion' => 'NO',
                    ],
                    [
                        'Capacidad Maxima' => '10 MIEMBROS',
                        'Torneos Semanales' => 'INCLUIDO',
                        'Subasta Comision' => '8 ARTÍCULOS',
                        'Apartado' => 'Máx. 4 Cajas',
                        'Stream Area' => 'SI',
                        'Grading Mensual' => '$1,000 MXN',
                        'Credito' => '$3,000 MXN',
                        'Descuento' => '10%',
                        'Mostrar Coleccion' => 'SI',
                    ],
                ];

                $beneficios = [
                    ['name' => 'CAPACIDAD MÁXIMA', 'key' => 'Capacidad Maxima'],
                    ['name' => 'ENTRADAS A TORNEOS SEMANALES', 'key' => 'Torneos Semanales'],
                    ['name' => 'ARTÍCULOS A SUBASTA SIN COMISIÓN', 'key' => 'Subasta Comision'],
                    ['name' => 'APARTADO PREFERENCIAL', 'key' => 'Apartado'],
                    ['name' => 'STREAM AREA', 'key' => 'Stream Area'],
                    ['name' => 'PSA GRADING MENSUAL', 'key' => 'Grading Mensual'],
                    ['name' => 'BONO EN CRÉDITO DE TIENDA', 'key' => 'Credito'],
                    ['name' => 'DESCUENTO EN MERCANCÍA**', 'key' => 'Descuento'],
                    ['name' => 'MOSTRAR COLECCIÓN EN TIENDA', 'key' => 'Mostrar Coleccion'],
                ];
            @endphp
            <div class="overflow-x-auto [-ms-overflow-style:none] [scrollbar-width:none]">
                <table class="mt-10 min-w-full table-fixed border-collapse divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="w-48 px-4 py-4 text-center text-xl font-bold uppercase tracking-wider text-white">
                                Beneficios
                            </th>

                            @foreach ($membresias as $membresia)
                                <th style="color: {{ $membresia['color'] }}" class="w-48 px-4 py-4 text-center text-2xl font-bold uppercase tracking-wider">
                                    {{ $membresia['name'] }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-500/50 text-white">
                        @foreach ($beneficios as $beneficio)
                            <tr>
                                <td class="w-52 px-4 py-3 text-lg font-medium md:w-64">
                                    {{ $beneficio['name'] }}
                                </td>
                                @foreach ($data as $columna)
                                    <td class="w-48 px-4 py-3 text-center align-middle text-lg">
                                        @if ($columna[$beneficio['key']] == 'NO')
                                            <span class="inline-flex items-center justify-center">
                                                <svg width="20" height="20" viewBox="0 0 100 100">
                                                    <line x1="20" y1="20" x2="80" y2="80" stroke="red" stroke-width="10"
                                                        stroke-linecap="round" />
                                                    <line x1="80" y1="20" x2="20" y2="80" stroke="red" stroke-width="10"
                                                        stroke-linecap="round" />
                                                </svg>
                                            </span>
                                        @elseif ($columna[$beneficio['key']] == 'SI')
                                            <span class="inline-flex items-center justify-center">
                                                <svg width="20" height="20" viewBox="0 0 100 100">
                                                    <path d="M20,50 L40,70 L80,30" fill="none" stroke="green" stroke-width="10" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                        @else
                                            {{ $columna[$beneficio['key']] }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        <tr>
                            <td></td>
                            <!-- Botones de carrito -->
                            @foreach ($membresias as $membresia)
                                <td class="px-1 py-4 text-center">
                                    @php
                                        $product_id = url_to_postid($membresia['url']);
                                    @endphp
                                    <form action="{{ $membresia['url'] }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="add-to-cart" value="{{ $product_id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit">
                                            <div
                                                class="w-42 inline-block cursor-pointer rounded-full bg-gradient-to-r from-yellow-200 via-orange-600 to-red-600 p-[2px] transition-all duration-300 hover:from-yellow-300 hover:via-orange-500 hover:to-red-500 md:w-60">
                                                <div class="rounded-full bg-black py-2 text-sm font-semibold text-white md:px-4 md:text-lg">
                                                    Agregar al Carrito
                                                </div>
                                            </div>
                                        </button>
                                    </form>

                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="px-6 text-xs text-gray-400">
            **Aplican Restricciones
        </div>
        <div class="mt-10 flex justify-center text-3xl font-bold text-white">
            Preguntas Frecuentes
        </div>

        <!-- Preguntas Frecuentes -->
        <div class="mt-5 grid grid-cols-12">
            <!-- Columna Izquierda -->
            <div class="col-span-12 flex flex-col md:col-span-6">
                <!-- Pregunta 1 -->
                <x-faq-accordion question="¿Qué son las membresías?">
                    Nuestras membresías son programas especiales diseñados para ofrecer beneficios exclusivos a nuestros Collectors más apasionados.
                    Al unirte, obtienes acceso anticipado a productos, descuentos especiales, dinámicas únicas, y experiencias personalizadas que fortalecen tu conexión
                    con la comunidad coleccionista.
                </x-faq-accordion>

                <!-- Pregunta 2 -->
                <x-faq-accordion question="¿Cuánto dura la membresía?">
                    Cada membresía tiene duración mensual. Se renueva automáticamente, a menos que decidas cancelarla antes del siguiente ciclo.
                </x-faq-accordion>

                <!-- Pregunta 3 -->
                <x-faq-accordion question="¿Puedo cambiar de nivel?">
                    Sí. Puedes cambiar tu nivel de membresía en cualquier momento. El nuevo nivel se activará al inicio del siguiente periodo de facturación. De haber
                    disponibilidad.
                </x-faq-accordion>

                <!-- Pregunta 4 -->
                <x-faq-accordion question="¿Qué pasa si cancelo mi membresía?">
                    Puedes cancelarla en cualquier momento desde tu cuenta. Sin embargo, ciertos beneficios únicos como bonos en crédito de tienda no se reactivan
                    si vuelves a contratar.
                </x-faq-accordion>

                <!-- Pregunta 5 -->
                <x-faq-accordion question="¿Cómo modifico mis intereses dentro de la membresía?">
                    Debes contactar directamente al gerente de tienda y solicitar los cambios que deseas realizar. El proceso puede tardar entre 48 a 72 horas.
                </x-faq-accordion>

                <!-- Pregunta 6 -->
                <x-faq-accordion question="¿Cómo activo mi membresía?">
                    Una vez que completes el pago, se activará tu membresía digital. No necesitas tarjeta física.
                </x-faq-accordion>
                {{-- Una vez que completes el pago, recibirás tu membresía digital directamente en tu wallet (Apple Wallet o Google Wallet). No necesitas tarjeta física. --}}

                <!-- Pregunta 7 -->
                <x-faq-accordion question="¿Qué necesito para usarla?">
                    Un smartphone compatible con wallets digitales (Apple Wallet, Google Wallet). Todo es digital y práctico.
                </x-faq-accordion>

                <!-- Pregunta 8 -->
                <x-faq-accordion question="¿Puedo usar mi membresía en tienda física?">
                    ¡Sí! Tu membresía digital es válida tanto online como en tienda física.
                </x-faq-accordion>

            </div>
            <!-- Columna Derecha -->
            <div class="col-span-12 flex flex-col md:col-span-6">
                <!-- Pregunta 1 -->
                <x-faq-accordion question="¿Qué son los créditos en tienda?">
                    Son puntos acumulables que obtienes al participar en torneos. Puedes usarlos para comprar cualquier producto en tienda sin restricciones.
                    También puedes pagar entradas a torneos con estos créditos.
                </x-faq-accordion>

                <!-- Pregunta 2 -->
                <x-faq-accordion question="¿Los créditos tienen vigencia?">
                    No. Los créditos acumulados no caducan y puedes usarlos cuando quieras, mientras tu cuenta siga activa.
                </x-faq-accordion>

                <!-- Pregunta 3 -->
                <x-faq-accordion question="¿Puedo transferir mis créditos a otro miembro?">
                    No. Los créditos son personales e intransferibles.
                </x-faq-accordion>

                <!-- Pregunta 4 -->
                <x-faq-accordion question="¿Los torneos incluidos en las membresías caducan?">
                    Si los torneos mensuales que incluyen las membresías son para uso del mes en curso, en caso de no usarlos no se acumulan para el siguiente
                    mes.
                </x-faq-accordion>

                <!-- Pregunta 5 -->
                <x-faq-accordion question="¿Cuántos torneos gratuitos tengo por membresía?">
                    Depende del nivel de tu membresía. Cada mes se resetea el contador. Si no usas tus torneos ese mes, no se acumulan para el siguiente.
                </x-faq-accordion>

                <!-- Pregunta 6 -->
                <x-faq-accordion question="¿Qué pasa si me inscribo a un torneo y no asisto?">
                    Debes confirmar tu asistencia al menos 2 horas antes del evento. Si no te presentas sin aviso, perderás esa entrada.
                </x-faq-accordion>

                <!-- Pregunta 7 -->
                <x-faq-accordion question="¿Qué es el apartado preferencial?">
                    Los niveles superiores pueden apartar productos antes de que estén disponibles para venta libre. El número de categorías permitidas depende de
                    tu nivel.
                </x-faq-accordion>

                <!-- Pregunta 8 -->
                <x-faq-accordion question="¿Qué es el psa grading incluido?">
                    En niveles altos puedes enviar una o más cartas mensualmente para certificación PSA (hasta cierto monto sin costo). Nosotros nos encargamos
                    del trámite.
                </x-faq-accordion>

                <!-- Pregunta 9 -->
                <x-faq-accordion question="¿Puedo mostrar mi colección en tienda?">
                    Sí, este beneficio está disponible para el nivel más alto de membresía. Puedes coordinarlo con el equipo de tienda.
                </x-faq-accordion>

            </div>
        </div>

        <div class="container mx-auto px-2 py-4 sm:px-4">
            <div class="mt-15 flex flex-col items-center justify-center">
                <div class="flex flex-row items-center gap-1 md:gap-3">
                    <div><span class="text-xl font-bold text-white md:text-3xl">Comunidad</span></div>
                    <div
                        class="inline-block rounded-xl bg-gradient-to-r from-yellow-200 via-orange-600 to-red-600 p-[2px] transition-all duration-300 hover:from-yellow-300 hover:via-orange-500 hover:to-red-500">
                        <div class="rounded-xl bg-black px-2 py-2 text-xl font-bold text-white md:text-3xl">
                            Geek Collection
                        </div>
                    </div>
                </div>
                <div class="mt-2 text-center text-xl text-white">
                    Personaliza tu experiencia y colecciona como un verdadero pro.
                </div>
            </div>
        </div>

        <div class="mt-5 py-2 md:mx-4 md:px-2">
            @php
                $posts = [
                    [
                        'img' => 'blog1.jpg',
                        'cliente' => 'Carlos Garcia',
                        'description' => 'La mejor tienda para comprar y vender cartas. Precios justos y trato 10/10.',
                    ],
                    [
                        'img' => 'blog2.webp',
                        'cliente' => 'Cesar Sucedo',
                        'description' => 'Siempre encuentro lo que busco. Gran stock y rapidez en los envíos. ¡Recomendadísima!',
                    ],
                    [
                        'img' => 'blog3.png',
                        'cliente' => 'Cesar Acosta',
                        'description' => 'Trueques seguros y productos en perfecto estado. Mi go-to para coleccionables.',
                    ],
                    [
                        'img' => 'blog1.jpg',
                        'cliente' => 'Paola Sofia',
                        'description' => 'Profesionales de verdad. Me ayudaron a armar mi colección sin compromiso.',
                    ],
                    [
                        'img' => 'blog2.webp',
                        'cliente' => 'Geek Collector',
                        'description' => 'Si eres serio en esto, es tu tienda. Sin estafas, todo transparente.',
                    ],
                ];
            @endphp

            <!-- Slider container -->
            <div class="swiper-post-container overflow-hidden px-2">
                <!-- Slider wrapper -->
                <div class="swiper-wrapper">
                    @foreach ($posts as $post)
                        <!-- Slide -->
                        <div class="swiper-slide">
                            <div class="relative flex h-auto w-auto flex-col rounded-2xl bg-gradient-to-t from-neutral-900 via-neutral-950 to-neutral-950 px-8 py-8">

                                <div class="flex items-center justify-center gap-x-2">

                                    <div class="flex-shrink-0">
                                        <div class="h-27 w-27 relative overflow-hidden rounded-full">
                                            <img src="{{ asset('resources/images/blog/' . $post['img']) }}" class="h-full w-full object-cover"
                                                alt="{{ $post['cliente'] }}">
                                        </div>
                                    </div>

                                    <div class="min-w-0">
                                        <div
                                            class="line-clamp-2 overflow-hidden px-2 text-center text-xl font-semibold uppercase tracking-wider text-white sm:text-base lg:text-2xl lg:leading-snug">
                                            {{ $post['cliente'] }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 justify-center text-white">
                                    <div class="text-center text-lg font-light normal-case sm:text-xl">
                                        "{{ $post['description'] }}"
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>

            </div>

        </div>

    </div>

    <!-- Swiper script -->
    <script type="module">
        import Swiper from 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.mjs';

        new Swiper('.swiper-container', {
            effect: 'coverflow',
            initialSlide: 2,
            grabCursor: false,
            centeredSlides: true,
            slidesPerView: 2,
            loop: true,
            autoplay: {
                delay: 2500,
                pauseOnMouseEnter: true
            },
            coverflowEffect: {
                rotate: 0,
                stretch: 0,
                depth: 150,
                modifier: 1,
                slideShadows: false
            },

            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                640: {
                    slidesPerView: 4
                }
            }

        });

        const swiperPost = new Swiper('.swiper-post-container', {
            // Optional parameters
            loop: true,
            slidesPerView: 1,
            centeredSlides: true,
            spaceBetween: 16,
            initialSlide: 0,
            rtl: false,

            // Responsive breakpoints
            breakpoints: {
                // when window width is >= 640px
                640: {
                    slidesPerView: 1,
                    centeredSlides: false,
                    spaceBetween: 16
                },
                // when window width is >= 768px
                768: {
                    slidesPerView: 3,
                    centeredSlides: false,
                    spaceBetween: 20
                },
                // when window width is >= 1024px
                1024: {
                    slidesPerView: 4,
                    centeredSlides: false,
                    spaceBetween: 24
                }
            }
        });
    </script>
@endsection
