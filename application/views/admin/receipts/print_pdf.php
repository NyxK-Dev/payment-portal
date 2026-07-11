<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Receipt - <?= html_escape($receipt->receipt_no); ?></title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #2b2b2b;
            line-height: 1.5;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }

        .receipt-box {
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

        .badge {
            padding: 4px 8px;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
            border-radius: 3px;
        }

        .bg-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .bg-warning {
            background-color: #fff3cd;
            color: #664d03;
        }

        .bg-danger {
            background-color: #f8d7da;
            color: #842029;
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
    <div class="receipt-box">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td>
                    <h1 class="company-title">PAYMENT PORTAL, INC.</h1>
                    <div class="document-type">Official Payment Acknowledgment</div>
                </td>
                <td class="text-end" style="vertical-align: top;">
                    <h2 class="statement-title">Receipt Statement</h2>
                    <div class="reference-box">
                        <span class="text-muted">LINKED INVOICE REF:</span>
                        <strong><?= html_escape($receipt->invoice_no ?? 'N/A'); ?></strong>
                    </div>
                </td>
            </tr>
        </table>

        <hr>

        <!-- Context Details -->
        <table class="details-table">
            <tr>
                <td width="50%" style="vertical-align: top;">
                    <div class="metadata-label">Transaction Context</div>
                    <div style="margin-bottom: 4px;">System Reference ID: <strong>#<?= $receipt->id; ?></strong></div>
                    <div>Authorized Agent: <span class="font-mono" style="color: #555;"><?= html_escape($receipt->issuer_name ?? 'System Processing'); ?></span></div>
                </td>
                <td width="50%" class="text-end" style="vertical-align: top;">
                    <div class="metadata-label">Receipt Details</div>
                    <div style="margin-bottom: 4px;">Receipt No: <strong><?= html_escape($receipt->receipt_no); ?></strong></div>
                    <div style="margin-bottom: 4px;">Issued Date: <strong><?= $receipt->issued_at ? date('Y-m-d H:i', strtotime($receipt->issued_at)) : 'N/A'; ?></strong></div>
                    <div>Currency: <strong>USD ($)</strong></div>
                </td>
            </tr>
        </table>

        <!-- Items Ledger -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="text-align: left;">Transaction Description</th>
                    <th class="text-center" width="20%">Status</th>
                    <th class="text-end" width="25%">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="fw-bold" style="font-size: 14px; margin-bottom: 4px;">Payment Received against Invoice #<?= html_escape($receipt->invoice_no ?? 'N/A'); ?></div>
                        <span class="font-mono text-muted" style="font-size: 11px;">Receipt Reference Key: <?= html_escape($receipt->receipt_no); ?></span>
                    </td>
                    <td class="text-center" style="vertical-align: middle;">
                        <?php
                        $status = strtolower($receipt->status_name ?? 'pending');
                        $badgeClass = 'bg-warning';
                        if (in_array($status, ['paid', 'completed', 'approved', 'success'])) {
                            $badgeClass = 'bg-success';
                        } elseif (in_array($status, ['cancelled', 'void', 'failed'])) {
                            $badgeClass = 'bg-danger';
                        }
                        ?>
                        <span class="badge <?= $badgeClass; ?>">
                            <?= html_escape($receipt->status_name ?? 'Pending'); ?>
                        </span>
                    </td>
                    <td class="text-end font-mono fw-bold" style="vertical-align: middle; font-size: 14px;">$<?= number_format($receipt->amount, 2); ?></td>
                </tr>

                <tr class="summary-row">
                    <td colspan="1"></td>
                    <td class="text-end" style="padding-top: 20px; color: #777;">Subtotal:</td>
                    <td class="text-end font-mono" style="padding-top: 20px; color: #777;">$<?= number_format($receipt->amount, 2); ?></td>
                </tr>
                <tr class="summary-row grand-total">
                    <td colspan="1"></td>
                    <td class="text-end">Total Settled:</td>
                    <td class="text-end font-mono">$<?= number_format($receipt->amount, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>