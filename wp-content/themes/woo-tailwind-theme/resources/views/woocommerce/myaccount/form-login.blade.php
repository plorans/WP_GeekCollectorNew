{{-- resources/views/woocommerce/myaccount/form-login.blade.php --}}
@extends('layouts.app')

@section('content')
    <style>
        /* Estilos para autocompletado */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-text-fill-color: #ffffff !important;
            -webkit-box-shadow: 0 0 0px 1000px transparent inset !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* Ocultar campos ocultos de WooCommerce */
        input[type="hidden"] {
            display: none !important;
        }
    </style>

    <section class="flex min-h-screen w-full items-center justify-center bg-cover bg-center px-4 md:px-14"
        style="background-image: url('/wp-content/themes/woo-tailwind-theme/resources/images/login/fondologin.jpg');">

        <div class="w-full max-w-md bg-transparent">
            {{-- Formulario de Login --}}
            <div id="loginForm" class="form-container visible-form rounded-[50px] border-4 border-orange-500 bg-black/60 p-8 text-gray-200 shadow-lg">
                <h2 class="mb-6 text-center text-4xl font-bold">INICIAR SESIÓN</h2>

                <form class="woocommerce-form woocommerce-form-login login" method="post">
                    @php do_action('woocommerce_login_form_start') @endphp

                    <div class="mb-4 space-y-4">
                        <div>
                            <label for="username" class="mb-2 block text-base font-bold">NOMBRE DE USUARIO O CORREO ELECTRÓNICO</label>
                            <input type="text"
                                class="w-full rounded-full border-2 border-white px-4 py-3 text-white focus:border-orange-500 focus:outline-0 focus:ring focus:ring-orange-500"
                                name="username" id="username" autocomplete="username" value="{{ old('username') }}" required>
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-base font-bold">CONTRASEÑA</label>
                            <input type="password"
                                class="w-full rounded-full border-2 border-white px-4 py-3 text-white focus:border-orange-500 focus:outline-0 focus:ring focus:ring-orange-500"
                                name="password" id="password" autocomplete="current-password" required>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="rememberme" class="rounded border-gray-300 text-orange-500">
                                <span>Recuérdame</span>
                            </label>
                            <a href="{{ wp_lostpassword_url() }}" class="text-sm font-bold text-orange-400 hover:text-orange-300">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    </div>

                    <button type="submit" name="login" value="Log in"
                        class="w-full rounded-full bg-gradient-to-r from-yellow-400 to-red-400 px-2 py-3 font-bold text-white">
                        INICIAR SESIÓN
                    </button>

                    {{-- Campos ocultos necesarios para WooCommerce --}}
                    @php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce') @endphp
                    @php do_action('woocommerce_login_form_end') @endphp

                    <a href="{{ site_url('registro') }}">
                        <button type="button"
                            class="mt-4 w-full cursor-pointer rounded-full border-2 border-red-400 p-3 px-2 py-3 text-lg font-bold text-red-400 hover:bg-red-400 hover:text-white">
                            ¿No tienes cuenta? <span class="font-extrabold">Regístrate</span>
                        </button>
                    </a>
                </form>
            </div>

        </div>
    </section>
@endsection
