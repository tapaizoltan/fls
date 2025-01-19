<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Ajánlat</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .total-row {
            color:red;
            font-weight: bold;
        }

        .footer-info {
            margin-top: 20px;
        }

        .footer-p {
            margin-bottom:-10px;
        }
    </style>
</head>
<body>
    <p>Tisztelt {{ $customerName }}!</p>
    <p>Ajánlatunk a következő:</p>
    
    <table>
        <thead>
            <tr>
                <th>Termék</th>
                <th>Mennyiség</th>
                <th>Netto egységár</th>
                <th>Nettó összesen</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0; // A teljes nettó összeg kezdeti értéke
            @endphp
            @foreach ($offer->priceofferitems as $item)
                @php
                    // Lekérjük a terméket a product_id alapján
                    $product = \App\Models\Product::find($item->product_id);
                    $brand = $product ? \App\Models\Brand::find($product->brand_id) : null;
                    $productName = $product ? sprintf('%d/%d%s%d', $product->width, $product->height, strtoupper($product->structure), $product->rim_diameter) : 'Nincs termék adat';
                @endphp
                <tr>
                    <td>
                        @if ($brand)
                            <b>{{ $brand->name }}</b> {{ $productName }}
                        @else
                            {{ $productName }}
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->netprice, 0, ',', ' ') }} Ft</td>
                    <td>{{ number_format($item->net_total_price, 0, ',', ' ') }} Ft</td>
                </tr>
                @php
                    // Hozzáadjuk a tétel nettó végösszegét a teljes összeghez
                    $total += $item->net_total_price;
                @endphp
            @endforeach
            <!-- Összesen sor -->
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">Összesen:</td>
                <td>{{ number_format($total, 0, ',', ' ') }} Ft</td>
            </tr>
        </tbody>
    </table>

    <div class="footer-info">
        <p class="footer-p"><strong>Jelen ajánlatunk érvényes:</strong> {{ now()->addDays(8)->format('Y.m.d') }}</p>
        <p class="footer-p"><strong>Ajánlat azonosítója:</strong> {{ $offer->price_offer_id }}-{{ $offer->id }}</p>
        <p class="footer-p"><strong>Az ajánlat készítője:</strong> 
            @php
                $user = \App\Models\User::find($offer->user_id);
            @endphp
            {{ $user ? $user->name : 'Nincs elérhető felhasználó' }}
        </p>
    </div>
</body>
</html>
