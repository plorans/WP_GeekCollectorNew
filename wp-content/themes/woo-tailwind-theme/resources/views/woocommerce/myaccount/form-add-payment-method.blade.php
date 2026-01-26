@php
    use Stripe\StripeClient;

    $user_id = get_current_user_id();
    $stripe_customer_id = get_user_meta($user_id, 'gc__stripe_customer_id', true);
    $stripe = new StripeClient(gc_get_stripe_secret_key());

    // Crear cliente si no existe
    if (!$stripe_customer_id) {
        $current_user = wp_get_current_user();
        $customer = $stripe->customers->create([
            'name' => $current_user->first_name . ' ' . $current_user->last_name,
            'email' => $current_user->user_email,
        ]);
        update_user_meta($user_id, 'gc__stripe_customer_id', $customer->id);
        $stripe_customer_id = $customer->id;
    }

    // Crear SetupIntent
    $intent = $stripe->setupIntents->create([
        'customer' => $stripe_customer_id,
    ]);
    $setup_intent_client_secret = $intent->client_secret;
@endphp

<style>
    .gc-loading {
        position: fixed;
        inset: 0;
        z-index: 99999;
        /* Matches your design system */
        background-color: color-mix(in oklab,
                var(--color-gray-900) 80%,
                transparent);
        backdrop-filter: blur(3px);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .gc-loading.hidden {
        display: none;
    }

    .gc-spinner {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        border: 4px solid rgba(251, 146, 60, 0.25);
        /* orange-400 */
        border-top-color: #fb923c;
        animation: gc-spin 0.9s linear infinite;
    }

    @keyframes gc-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .swal2-popup {
        background: #111827 !important;
        color: #f9fafb !important;
    }

    .swal2-confirm {
        background: linear-gradient(135deg, #fb923c, #f97316) !important;
        color: #111827 !important;
        font-weight: 600;
    }

    .swal2-cancel {
        background: #1f2937 !important;
        color: #e5e7eb !important;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer">
</script>

<div class="max-w-2/4">
    <div class="flex gap-1">
        <svg class="h-6 w-6 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 10h18M6 14h2m3 0h5M3 7v10a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1Z" />
        </svg>
        <span class="mb-4 text-gray-400">Tarjeta</span>
    </div>

    <form id="add-payment-method-form" method="post">
        @php wp_nonce_field('woocommerce-add-payment-method', 'woocommerce-add-payment-method-nonce'); @endphp

        <div id="card-element" class="h-12 rounded-lg border bg-white p-2 text-black"></div>
        <div id="card-errors" class="mt-2 text-red-500"></div>

        <button type="submit" class="mt-4 cursor-pointer rounded-lg bg-orange-500 px-4 py-2 text-white">
            Agregar método de pago
        </button>
    </form>
</div>

<div id="gc-loading-overlay" class="gc-loading hidden">
    <div class="gc-spinner"></div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const stripe = Stripe('{{ gc_get_stripe_publishable_key() }}');
    const elements = stripe.elements();
    const card = elements.create('card');
    card.mount('#card-element');

    const form = document.getElementById('add-payment-method-form');
    let isSubmitting = false;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (isSubmitting) return;
        isSubmitting = true;

        showLoading();

        const {
            setupIntent,
            error
        } = await stripe.confirmCardSetup(
            '{{ $setup_intent_client_secret }}', {
                payment_method: {
                    card: card
                }
            }
        );

        if (error) {
            hideLoading();
            isSubmitting = false;

            Swal.fire({
                title: 'Error',
                text: error.message,
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        fetch('{{ admin_url('admin-ajax.php') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: new URLSearchParams({
                    action: 'save_stripe_payment_method',
                    payment_method_id: setupIntent.payment_method
                })
            })
            .then(res => res.json())
            .then(data => {
                hideLoading();
                isSubmitting = false;

                if (data.success) {
                    Swal.fire({
                        title: 'Método agregado',
                        text: 'El método de pago fue agregado correctamente.',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    setTimeout(() => {
                        window.location.href = '<?php echo esc_url(wc_get_endpoint_url('payment-methods', '', wc_get_page_permalink('myaccount'))); ?>';
                    }, 1000);

                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message,
                        confirmButtonText: 'Aceptar'
                    });
                }
            })
            .catch(() => {
                hideLoading();
                isSubmitting = false;

                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error inesperado. Intenta nuevamente.',
                    confirmButtonText: 'Aceptar'
                });
            });
    });

    function showLoading() {
        document.getElementById('gc-loading-overlay').classList.remove('hidden');
    }

    function hideLoading() {
        document.getElementById('gc-loading-overlay').classList.add('hidden');
    }
</script>
