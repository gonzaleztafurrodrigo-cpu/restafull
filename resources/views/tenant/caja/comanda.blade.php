<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comanda #{{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 80mm;
            padding: 4mm;
            background: white;
            color: black;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }

        .divider-solid {
            border-top: 1px solid #000;
            margin: 4px 0;
        }

        .header {
            margin-bottom: 8px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
        }

        .header h2 {
            font-size: 13px;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            border: 1px solid #000;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: bold;
        }

        .items-table {
            width: 100%;
        }

        .items-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .items-table .qty {
            width: 20px;
            font-weight: bold;
        }

        .items-table .price {
            text-align: right;
            width: 50px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }

        .total-row.main {
            font-weight: bold;
            font-size: 14px;
            margin-top: 4px;
        }

        .footer {
            margin-top: 8px;
            text-align: center;
            font-size: 10px;
        }

        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 4mm;
            }

            .no-print {
                display: none !important;
            }

            @page {
                margin: 0;
                size: 80mm auto;
            }
        }
    </style>
    @include('tenant.partials.theme')
</head>

<body>

    <!-- Botones no imprimibles -->
    <div class="no-print" style="margin-bottom: 12px; display: flex; gap: 8px;">
        <button onclick="window.print()"
            style="background: #f97316; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: bold;">
            Imprimir comanda
        </button>
        <button onclick="window.close()"
            style="background: #e5e7eb; color: #374151; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 13px;">
            Cerrar
        </button>
    </div>

    <!-- Comanda -->
    <div class="header center">
        <h1>{{ strtoupper($tenant->name) }}</h1>
        <div class="divider-solid"></div>
        <h2>COMANDA</h2>
        <p style="font-size: 18px; font-weight: bold; margin: 4px 0;">{{ $order->order_number }}</p>
        <div style="margin: 4px 0;">
            @if ($order->type === 'delivery')
                <span class="badge">DOMICILIO</span>
            @else
                <span class="badge">MESA: {{ strtoupper($order->table->name ?? '') }}</span>
            @endif
        </div>
        <p style="font-size: 10px; margin-top: 4px;">
            {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y h:i a') }}</p>
    </div>

    <div class="divider"></div>

    @if ($order->type === 'delivery')
        <div style="margin: 4px 0;">
            <p class="bold">Cliente: {{ $order->customer_name }}</p>
            <p>Tel: {{ $order->customer_phone }}</p>
            <p>Dir: {{ $order->delivery_address }}</p>
            <p>Pago: {{ $order->payment_method === 'transfer' ? 'TRANSFERENCIA' : 'EFECTIVO' }}</p>
            @if ($order->payment_method === 'transfer')
                <p style="margin-top: 4px; font-size: 10px;">*** PENDIENTE VERIFICAR PAGO ***</p>
            @endif
        </div>
        <div class="divider"></div>
    @endif

    <table class="items-table">
        <tbody>
            @php $currentCategory = null; @endphp
            @foreach ($order->items as $item)
                @if ($currentCategory !== $item->category_name)
                    @php $currentCategory = $item->category_name; @endphp
                    <tr>
                        <td colspan="3" style="padding-top: 6px; padding-bottom: 2px;">
                            <span style="font-size: 10px; font-weight: bold; text-decoration: underline;">
                                {{ strtoupper($item->category_name) }}
                            </span>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td class="qty">{{ $item->quantity }}x</td>
                    <td>{{ strtoupper($item->product_name) }}
                        @if ($item->notes)
                            <br><span style="font-size: 10px;">* {{ $item->notes }}</span>
                        @endif
                    </td>
                    <td class="price">${{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    @php $deliveryCost = $order->total - $order->subtotal; @endphp

    <div class="total-row">
        <span>Subtotal</span>
        <span>${{ number_format($order->subtotal, 0, ',', '.') }}</span>
    </div>

    @if ($deliveryCost > 0)
        <div class="total-row">
            <span>Domicilio</span>
            <span>${{ number_format($deliveryCost, 0, ',', '.') }}</span>
        </div>
    @endif

    <div class="total-row main">
        <span>TOTAL</span>
        <span>${{ number_format($order->total, 0, ',', '.') }}</span>
    </div>

    @if ($order->notes)
        <div class="divider"></div>
        <p style="font-size: 10px;"><span class="bold">NOTA:</span> {{ $order->notes }}</p>
    @endif

    <div class="divider"></div>

    <div class="footer">
        <p>Powered by RestaFull</p>
        <p style="margin-top: 2px;">{{ now()->format('d/m/Y h:i a') }}</p>
    </div>

</body>

</html>
