<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('tenant.partials.theme')
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-sm">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">

            <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h2 class="text-xl font-semibold text-gray-800 mb-2 text-center">¡Pedido recibido!</h2>
            <p class="text-sm text-gray-400 mb-6 text-center">Tu pedido ha sido enviado al restaurante.</p>

            @php $deliveryCost = $order->total - $order->subtotal; @endphp
            <div class="bg-gray-50 rounded-xl p-4 mb-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Número de pedido</span>
                    <span class="font-semibold text-gray-800">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="text-gray-700">${{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                @if ($deliveryCost > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Costo de domicilio</span>
                        <span class="text-gray-700">${{ number_format($deliveryCost, 0, ',', '.') }}</span>
                    </div>
                @else
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Domicilio</span>
                        <span class="text-green-500 font-medium">Gratis</span>
                    </div>
                @endif
                <div class="flex justify-between text-sm border-t border-gray-200 pt-2">
                    <span class="font-semibold text-gray-700">Total</span>
                    <span class="font-bold text-gray-800">${{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Pago</span>
                    <span class="font-semibold text-gray-800">
                        {{ $order->payment_method === 'transfer' ? 'Transferencia' : 'Efectivo contra entrega' }}
                    </span>
                </div>
            </div>

            @if ($order->payment_method === 'transfer')
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
                    <p class="text-sm text-yellow-700 font-medium mb-3 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Instrucciones de pago
                    </p>
                    @if (!empty($settings['bank_name']))
                        <div class="space-y-1 text-sm text-yellow-700 mb-3">
                            @if (!empty($settings['bank_name']))
                                <p><span class="font-medium">Banco:</span> {{ $settings['bank_name'] }}</p>
                            @endif
                            @if (!empty($settings['bank_account_type']))
                                <p><span class="font-medium">Tipo:</span> {{ $settings['bank_account_type'] }}</p>
                            @endif
                            @if (!empty($settings['bank_account']))
                                <p><span class="font-medium">Cuenta:</span> {{ $settings['bank_account'] }}</p>
                            @endif
                            @if (!empty($settings['bank_owner']))
                                <p><span class="font-medium">Titular:</span> {{ $settings['bank_owner'] }}</p>
                            @endif
                            @if (!empty($settings['bank_nit']))
                                <p><span class="font-medium">NIT/CC:</span> {{ $settings['bank_nit'] }}</p>
                            @endif
                        </div>
                    @endif
                    <p class="text-xs text-yellow-600">El restaurante verificará tu pago y despachará tu pedido.</p>
                </div>
            @endif

            <a href="{{ $redirectRoute }}"
                class="block w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-xl font-medium text-sm transition text-center">
                {{ session('customer') ? 'Ir a mi cuenta' : 'Volver al inicio' }}
            </a>

        </div>

    </div>

</body>

</html>
