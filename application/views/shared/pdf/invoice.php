<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice - <?= html_escape($invoice->invoice_no); ?></title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #2b2b2b;
            line-height: 1.5;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }

        .invoice-box {
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table,
        .details-table,
        .items-table {
            margin-bottom: 30px;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .company-title {
            color: #0d6efd;
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }

        .document-type {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #777;
            font-weight: bold;
        }

        .statement-title {
            font-size: 16px;
            color: #777;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            font-weight: bold;
        }

        .reference-box {
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
            padding: 8px 12px;
            display: inline-block;
            font-size: 12px;
        }

        hr {
            border: none;
            border-top: 1px solid #e9ecef;
            margin: 20px 0 30px 0;
        }

        .metadata-label {
            text-transform: uppercase;
            font-size: 11px;
            color: #777;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .items-table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 12px;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .items-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #efefef;
            vertical-align: top;
        }

        .summary-row td {
            border: none;
            padding: 8px 12px;
        }

        .grand-total td {
            font-size: 18px;
            color: #0d6efd;
            font-weight: bold;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }

        .font-mono {
            font-family: monospace;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td>
                    <h1 class="company-title">PAYMENT PORTAL, INC.</h1>
                    <div class="document-type">Official Commercial Statement</div>
                </td>
                <td class="text-end" style="vertical-align: top;">
                    <h2 class="statement-title">Invoice Statement</h2>
                    <div class="reference-box">
                        <span class="text-muted">ORDER REF:</span>
                        <strong><?= html_escape($invoice->order_no); ?></strong>
                    </div>
                </td>
            </tr>
        </table>

        <hr>

        <!-- Context Details -->
        <table class="details-table">
            <tr>
                <td width="50%" style="vertical-align: top;">
                    <div class="metadata-label">Bill To</div>
                    <div style="font-weight: bold; font-size: 14px; margin-bottom: 4px;">Customer Account</div>
                    <div class="font-mono" style="color: #555;"><?= html_escape($invoice->customer_email); ?></div>
                </td>
                <td width="50%" class="text-end" style="vertical-align: top;">
                    <div class="metadata-label">Invoice Details</div>
                    <div style="margin-bottom: 4px;">Invoice No: <strong><?= html_escape($invoice->invoice_no); ?></strong></div>
                    <div style="margin-bottom: 4px;">Issued Date: <strong><?= date('Y-m-d H:i', strtotime($invoice->issued_at)); ?></strong></div>
                    <div>Currency: <strong>USD ($)</strong></div>
                </td>
            </tr>
        </table>

        <!-- Items Ledger -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="text-align: left;" width="50%">Item Description</th>
                    <th class="text-center" width="10%">Qty</th>
                    <th class="text-end" width="20%">Unit Price</th>
                    <th class="text-end" width="20%">Line Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div class="fw-bold" style="font-size: 14px; margin-bottom: 4px;"><?= html_escape($item->product_name); ?></div>
                            <?php if (!empty($item->product_description)): ?>
                                <div style="font-size: 11px; color: #666; margin-bottom: 4px;"><?= html_escape($item->product_description); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($item->sku)): ?>
                                <span class="font-mono text-muted" style="font-size: 11px;">SKU: <?= html_escape($item->sku); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center font-mono"><?= $item->quantity; ?></td>
                        <td class="text-end font-mono">$<?= $item->formatted_unit_price; ?></td>
                        <td class="text-end font-mono fw-bold">$<?= $item->formatted_line_total; ?></td>
                    </tr>
                <?php endforeach; ?>

                <tr class="summary-row">
                    <td colspan="2"></td>
                    <td class="text-end" style="padding-top: 20px; color: #777;">Subtotal:</td>
                    <td class="text-end font-mono" style="padding-top: 20px; color: #777;">$<?= $invoice->subtotal_aggregate; ?></td>
                </tr>
                <tr class="summary-row grand-total">
                    <td colspan="2"></td>
                    <td class="text-end">Total Due:</td>
                    <td class="text-end font-mono">$<?= $invoice->formatted_total_due; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>