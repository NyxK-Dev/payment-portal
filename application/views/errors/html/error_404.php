<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 Not Found | Payment Portal</title>

    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc; /* Sleek slate background to match modern dashboards */
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            padding: 2rem 1rem;
        }

        .error-container {
            width: 100%;
            max-width: 440px;
            text-align: center;
        }

        .error-logo {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            text-decoration: none;
            margin-bottom: 2.5rem;
        }

        .error-logo-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #10b981; /* Matching your green dashboard primary icon accent */
            color: #fff;
            font-size: 1rem;
        }

        .error-code {
            font-size: 6rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1;
            margin-bottom: 1rem;
            letter-spacing: -0.05em;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.75rem;
            letter-spacing: -0.025em;
        }

        .error-message {
            color: #64748b;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .error-path {
            display: inline-block;
            background: #e2e8f0;
            border-radius: 6px;
            padding: 0.35rem 0.75rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.85rem;
            font-weight: 500;
            color: #334155;
            margin-bottom: 2.5rem;
            word-break: break-all;
        }

        .error-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
        }

        .btn-error-primary,
        .btn-error-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-error-primary {
            background-color: #0f172a; /* Solid dark button mimicking your dashboard sidebar navigation style */
            color: #fff;
        }

        .btn-error-primary:hover {
            background-color: #1e293b;
            color: #fff;
        }

        .btn-error-secondary {
            background-color: #ffffff;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .btn-error-secondary:hover {
            background-color: #f1f5f9;
            color: #1e293b;
            border-color: #cbd5e1;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <a href="/" class="error-logo">
            <span class="error-logo-icon"><i class="fas fa-wallet"></i></span>
            PaymentPortal
        </a>

        <div class="error-code">404</div>
        <h1 class="error-title">Page not found</h1>
        <p class="error-message">
            Sorry, we couldn’t find the page you’re looking for. It might have been moved or deleted.
        </p>

        <?php if (!is_cli() && !empty($_SERVER['REQUEST_URI'])): ?>
            <div class="error-path">
                <?= html_escape($_SERVER['REQUEST_URI']); ?>
            </div>
        <?php endif; ?>

        <div class="error-actions">
            <a href="/" class="btn-error-primary">
                Back to Dashboard
            </a>
            <a href="javascript:history.back()" class="btn-error-secondary">
                Go Back
            </a>
        </div>
    </div>
</body>
</html>