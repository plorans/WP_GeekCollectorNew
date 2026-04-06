<?php
/*
 * Template name: Registration Form
 */
?>
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

    @php
        if (is_user_logged_in()) {
            wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')));
        }
        do_action('woocommerce_before_customer_login_form');
    @endphp
    <section class="flex min-h-[90vh] w-full items-center justify-center bg-cover bg-center px-4 md:px-14"
        style="background-image: url('/wp-content/themes/woo-tailwind-theme/resources/images/login/fondologin.jpg');">
        <div class="w-full max-w-md bg-transparent">
            <div id="registerForm" class="form-container border-main rounded-[50px] border-4 border-orange-500 bg-black/60 p-8 uppercase text-white shadow-lg">
                <h2 class="upp mb-6 text-center text-4xl font-bold">CREA UNA CUENTA</h2>

                <form method="post" action="{{ wc_get_page_permalink('myaccount') }}" class="woocommerce-form woocommerce-form-register register">
                    @php do_action('woocommerce_register_form_start') @endphp

                    <div class="mb-4 space-y-4">
                        @if (get_option('woocommerce_registration_generate_username') === 'no')
                            <div>
                                <label for="reg_username" class="mb-2 block text-base font-bold">NOMBRE DE USUARIO</label>
                                <input type="text" class="focus:border-main focus:ring-main w-full rounded-full border-2 px-4 py-3 focus:outline-0 focus:ring"
                                    name="username" id="reg_username" autocomplete="username" value="{{ old('username') }}" required>
                            </div>
                        @endif

                        <div>
                            <label for="reg_email" class="mb-2 block text-base font-bold">CORREO ELECTRÓNICO</label>
                            <input type="email" class="focus:border-main focus:ring-main w-full rounded-full border-2 px-4 py-3 focus:outline-0 focus:ring-2" name="email"
                                id="reg_email" autocomplete="email" value="{{ old('email') }}" required>
                        </div>

                        @if (get_option('woocommerce_registration_generate_password') === 'no')
                            <div>
                                <label for="reg_password" class="mb-2 block text-base font-bold">CONTRASEÑA</label>
                                <input type="password" class="focus:border-main focus:ring-main w-full rounded-full border-2 px-4 py-3 focus:outline-0 focus:ring-2"
                                    name="password" id="reg_password" autocomplete="new-password" required>
                                <p class="mt-1 text-sm text-gray-400">Mínimo 8 caracteres con letras y números.</p>
                            </div>
                        @else
                            <p class="text-sm text-gray-400">Se enviará un enlace para establecer una nueva contraseña a su
                                correo.</p>
                        @endif
                    </div>

                    {{-- Campos ocultos necesarios para WooCommerce --}}
                    @php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce') @endphp
                    @php do_action('woocommerce_register_form') @endphp

                    @php do_action('woocommerce_register_form_end') @endphp

                    {{-- <div class="cf-turnstile" data-sitekey="0x4AAAAAACMifqRVcwd32BaJ"></div> --}}
                    <button type="submit" name="register" value="Register"
                        class="w-full rounded-full bg-gradient-to-r from-yellow-400 to-red-400 px-2 py-3 font-bold text-white">
                        REGISTRARSE
                    </button>

                    <a href="{{ site_url('my-account') }}">
                        <button type="button"
                            class="mt-4 w-full cursor-pointer rounded-full border-2 border-red-400 p-3 px-2 py-3 text-lg font-bold text-red-400 hover:bg-red-400 hover:text-white">
                            ¿Ya tienes cuenta? <span class="text-main font-semibold">Inicia sesión</span>
                        </button>
                    </a>

                </form>
            </div>
        </div>
    </section>
    @php
        do_action('woocommerce_after_customer_login_form');
    @endphp
@endsection
