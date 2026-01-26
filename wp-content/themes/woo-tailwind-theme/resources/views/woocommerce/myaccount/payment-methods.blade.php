@php
    use Stripe\StripeClient;

    $user_id = get_current_user_id();
    $stripe_customer_id = get_user_meta($user_id, 'gc__stripe_customer_id', true);
    $stripe = new StripeClient(gc_get_stripe_secret_key());

    // Obtener métodos guardados
    $saved_methods = [];
    $default_payment_method_id = null;

    if ($user_id && $stripe_customer_id) {
        try {
            $customer = $stripe->customers->retrieve($stripe_customer_id, []);
            $default_payment_method_id = $customer->invoice_settings->default_payment_method ?? null;

            $payment_methods = $stripe->paymentMethods->all([
                'customer' => $stripe_customer_id,
                'type' => 'card',
            ]);
            $saved_methods = $payment_methods->data ?? [];
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            $error = $e->getError();

            if ($error && isset($error->code) && $error->code === 'resource_missing') {
                delete_user_meta($user_id, 'gc__stripe_customer_id');

                $new_customer = $stripe->customers->create([
                    'email' => wp_get_current_user()->user_email,
                    'metadata' => [
                        'wp_user_id' => $user_id,
                    ],
                ]);

                update_user_meta($user_id, 'gc__stripe_customer_id', $new_customer->id);

                $saved_methods = [];
                $default_payment_method_id = null;
            } else {
                throw $e;
            }
        }
    }

    if ($user_id) {
        if ($stripe_customer_id) {
            $customer = $stripe->customers->retrieve($stripe_customer_id, []);
            $default_payment_method_id = $customer->invoice_settings->default_payment_method ?? null;

            $payment_methods = $stripe->paymentMethods->all([
                'customer' => $stripe_customer_id,
                'type' => 'card',
            ]);
            $saved_methods = $payment_methods->data;
        }
    }
@endphp

<div class="payment-methods-container">
    @if (count($saved_methods))
        <div class="animate-fade-in mb-10 overflow-x-auto rounded-lg shadow-lg">
            <table class="woocommerce-MyAccount-paymentMethods shop_table shop_table_responsive account-payment-methods-table w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tarjeta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Últimos 4 dígitos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Vence</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Predeterminada</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach ($saved_methods as $method)
                        <tr
                            class="payment-method-row {{ $method->id === $default_payment_method_id ? 'default-payment-method bg-blue-50 border-l-4 border-blue-500' : '' }} transition-all duration-300 hover:bg-blue-50">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex items-center">
                                    <div class="payment-method-icon mr-3">
                                        @php
                                            $brand = strtolower($method->card->brand);
                                            $icon_class = 'text-gray-400 text-xl';

                                            if (strpos($brand, 'visa') !== false) {
                                                $icon_class = 'text-blue-600 text-xl';
                                                echo '<i class="fa-brands fa-cc-visa"></i>';
                                            } elseif (strpos($brand, 'mastercard') !== false) {
                                                $icon_class = 'text-red-600 text-xl';
                                                echo '<i class="fa-brands fa-cc-mastercard"></i>';
                                            } elseif (strpos($brand, 'amex') !== false || strpos($brand, 'american') !== false) {
                                                $icon_class = 'text-blue-400 text-xl';
                                                echo '<i class="fa-brands fa-cc-amex"></i>';
                                            } elseif (strpos($brand, 'discover') !== false) {
                                                $icon_class = 'text-orange-600 text-xl';
                                                echo '<i class="fa-brands fa-cc-discover"></i>';
                                            } else {
                                                echo '<i class="fa-credit-card fa-solid ' . $icon_class . '"></i>';
                                            }
                                        @endphp
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">{{ strtoupper($method->card->brand) }}</div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-mono text-sm text-gray-900">•••• {{ $method->card->last4 }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="text-sm text-gray-900">{{ sprintf('%02d/%d', $method->card->exp_month, $method->card->exp_year) }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($method->id === $default_payment_method_id)
                                    <span class="inline-flex rounded-full bg-green-200 px-2 text-xs font-semibold leading-5 text-green-800">
                                        Predeterminada
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">
                                        No predeterminada
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                @if ($method->id !== $default_payment_method_id)
                                    <form method="post" action="{{ esc_url(wc_get_endpoint_url('set-default-payment-method')) }}" class="inline-block">
                                        @php wp_nonce_field('set-default-payment-method', 'set-default-payment-method-nonce'); @endphp
                                        <input type="hidden" name="payment_method_id" value="{{ $method->id }}">
                                        <button type="submit"
                                            class="set-default-btn rounded-md bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-600 transition-colors duration-200 hover:bg-indigo-100 hover:text-indigo-900">
                                            Usar como predeterminada
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <button class="remove-card rounded-md px-3 py-1 text-sm font-medium text-red-800 transition-colors duration-200 hover:bg-indigo-100"
                                    data-payment-method-id="<?= esc_attr($method->id) ?>">
                                    Eliminar
                                </button>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="mb-10 animate-pulse rounded-lg bg-white p-6 text-center shadow-md">
            <div class="mb-4 text-lg text-gray-500">
                <i class="fa-credit-card fa-regular fa-3x mb-3"></i>
                <p>No se han encontrado métodos de pago guardados.</p>
            </div>
        </div>
    @endif

    @if (WC()->payment_gateways->get_available_payment_gateways())
        <div class="mt-8 text-center">
            <a class="add-payment-btn inline-flex transform items-center rounded-md border border-transparent bg-gradient-to-r from-orange-500 to-orange-600 px-4 py-2 font-semibold uppercase tracking-widest text-white shadow-md transition duration-150 ease-in-out hover:-translate-y-0.5 hover:from-orange-600 hover:to-orange-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                href="{{ esc_url(wc_get_endpoint_url('add-payment-method')) }}">
                <i class="fa-plus fa-solid mr-2"></i> Agregar método de pago
            </a>
        </div>
    @endif
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }

    .payment-method-row {
        transition: all 0.3s ease;
    }

    .set-default-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    @media (max-width: 768px) {
        .account-payment-methods-table {
            display: block;
            width: 100%;
        }

        .account-payment-methods-table thead {
            display: none;
        }

        .account-payment-methods-table tbody,
        .account-payment-methods-table tr,
        .account-payment-methods-table td {
            display: block;
            width: 100%;
        }

        .account-payment-methods-table tr {
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
        }

        .account-payment-methods-table td {
            padding: 0.5rem;
            text-align: right;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .account-payment-methods-table td::before {
            content: attr(data-label);
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.75rem;
            color: #718096;
            text-align: left;
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Agregar etiquetas de datos para responsividad
        const table = document.querySelector('.account-payment-methods-table');
        if (table) {
            const headers = table.querySelectorAll('thead th');
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                cells.forEach((cell, index) => {
                    if (index < headers.length) {
                        cell.setAttribute('data-label', headers[index].textContent.trim());
                    }
                });
            });
        }

        // Animación para botones de establecer predeterminado
        const defaultButtons = document.querySelectorAll('.set-default-btn');
        defaultButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                this.innerHTML = '<i class="fa-spinner fa-solid animate-spin mr-1"></i> Procesando...';
                this.classList.add('opacity-75', 'cursor-not-allowed');
            });
        });
    });

    document.querySelectorAll('.remove-card').forEach(btn => {
        btn.addEventListener('click', () => {
            Swal.fire({
                title: 'Eliminar método de pago',
                text: '¿Seguro que deseas eliminar este método de pago? Esta acción no se puede deshacer.',
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then(result => {
                if (!result.isConfirmed) return;

                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            action: 'remove_stripe_payment_method',
                            payment_method_id: btn.dataset.paymentMethodId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: 'El método de pago fue eliminado correctamente.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
            });
        });
    });
</script>
