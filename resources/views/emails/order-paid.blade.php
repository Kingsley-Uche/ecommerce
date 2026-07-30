<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Successful - slayshapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,500;0,600;1,500&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --paper: #faf8f4;
            --paper-2: #f2efe7;
            --ink: #1c1a17;
            --ink-mid: #635d52;
            --ink-soft: #a59c8c;
            --line: #e4e0d8;
            --clay: #b5562e;
            --clay-dim: #e9d2c5;
            --font-display: 'Fraunces', Georgia, serif;
            --font-mono: 'Space Grotesk', 'Courier New', monospace;
        }

        body {
            background-color: var(--paper);
            font-family: var(--font-mono);
            color: var(--ink);
            margin: 0;
            padding: 40px 20px;
            font-size: 15px;
            line-height: 1.6;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 5px;
            padding: 40px 30px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.04);
        }

        .brand-header {
            text-align: center;
            border-bottom: 1px solid var(--line);
            padding-bottom: 25px;
            margin-bottom: 30px;
        }

        .brand-name {
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 2.5rem;
            line-height: 0.92;
            letter-spacing: -0.01em;
            color: var(--ink);
            margin: 0;
            text-transform: lowercase;
        }

        .brand-tagline {
            font-size: 0.75rem;
            color: var(--clay);
            letter-spacing: 0.14em;
            text-transform: uppercase;
            font-weight: 700;
            margin-top: 8px;
            display: block;
        }

        h2 {
            font-family: var(--font-display);
            font-size: 1.6rem;
            font-weight: 600;
            color: var(--ink);
            margin-top: 0;
        }

        p {
            color: var(--ink-mid);
            font-size: 0.95rem;
        }

        .order-meta {
            background: var(--paper-2);
            border: 1px solid var(--line);
            border-radius: 4px;
            padding: 15px 20px;
            margin: 25px 0;
            font-size: 0.88rem;
        }

        .order-meta p {
            margin: 6px 0;
            color: var(--ink);
        }

        .table-title {
            font-family: var(--font-display);
            font-style: italic;
            color: var(--clay);
            font-size: 1.1rem;
            margin-top: 35px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 0.88rem;
        }

        th {
            background-color: var(--paper-2);
            color: var(--ink);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            padding: 12px 10px;
            border-bottom: 1px solid var(--line);
            border-top: 1px solid var(--line);
            text-align: left;
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid var(--line);
            color: var(--ink);
        }

        .total-section {
            text-align: right;
            margin-top: 20px;
            border-top: 2px solid var(--ink);
            padding-top: 15px;
        }

        .total-section span {
            font-family: var(--font-display);
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--ink);
        }

        .footer-note {
            margin-top: 40px;
            text-align: center;
            font-size: 0.78rem;
            color: var(--ink-soft);
            border-top: 1px solid var(--line);
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="brand-header">
            <h1 class="brand-name">slayshapers</h1>
            <span class="brand-tagline">Payment Confirmed & Verified</span>
        </div>

        <h2>Thank you for your order!</h2>
        <p>Your payment has been successfully processed and verified. We are now preparing your items for fulfillment.</p>
        
        <div class="order-meta">
          <p>
    <strong>Order ID:</strong>
    #{{ $order->id }}/{{ \Carbon\Carbon::parse($order->created_at)->format('Ymd/Hi') }}
</p>
            <p><strong>Payment Reference:</strong> {{ $order->payment_ref }}</p>
            <p><strong>Date Processed:</strong> {{ $order->paid_at ?? now() }}</p>
        </div>

        <div class="table-title">Itemized Summary</div>
        
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Size</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    @php
                        $productName = $item->product->name ?? 'Product Unavailable';
                        $productPrice = $item->product->price ?? 0;
                        $itemTotal = $productPrice * $item->qty_bought;
                    @endphp
                    <tr>
                        <td>{{ $productName }}</td>
                        <td>{{ $item->size ?? 'N/A' }}</td>
                        <td>{{ $item->qty_bought }}</td>
                        <td>NGN {{ number_format($productPrice, 2) }}</td>
                        <td>NGN {{ number_format($itemTotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <span>Total Paid: NGN {{ number_format($order->total_paid, 2) }}</span>
        </div>

        <div class="footer-note">
    <p>
        We will send tracking details as soon as your items ship out.
        If you have any questions, we're happy to help.
    </p>

    <p style="margin:12px 0;">
        📧
        <a href="mailto:support@slayshapers.com.ng"
           style="color: var(--clay); text-decoration:none;">
            support@slayshapers.com.ng
        </a>
    </p>

    <p style="margin:12px 0;">
        📞
        <a href="tel:+2348073066284"
           style="color: var(--clay); text-decoration:none;">
            +234 807 306 6284
        </a>
    </p>

    <p style="margin:12px 0;">
        💬
        <a href="https://wa.me/2348073066284"
           style="color: var(--clay); text-decoration:none;">
            WhatsApp: +234 807 306 6284
        </a>
    </p>

    <p style="margin-top:20px;">
        &copy; {{ date('Y') }} slayshapers.com.ng. All rights reserved.
    </p>
</div>
    </div>
</body>
</html>