<div style="padding: 24px; font-family: system-ui, -apple-system, sans-serif;">

    <!-- Flash Error Notification -->
    <?php if ($this->session->flashdata('error')): ?>
        <div style="background-color: #fff5f5; border: 1px solid #fed7d7; color: #c53030; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-size: 14px;">
            ⚠️ <?= $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>

    <!-- Outer Flex Wrapper (Ensures clear separation between columns) -->
    <div style="display: flex; flex-direction: row; gap: 24px; flex-wrap: wrap; align-items: flex-start;">

        <!-- Left Side: Order Items Box -->
        <div style="flex: 1; min-width: 320px;">
            <div style="background: #ffffff; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; padding: 24px;">

                <h3 style="margin: 0 0 20px 0; font-size: 18px; font-weight: 700; color: #0f172a; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9;">
                    Review Your Order
                </h3>

                <!-- Modern Item Rows (Replaces overlapping tables) -->
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <?php
                    $total = 0;
                    foreach ($cart as $item):
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 16px; border-bottom: 1px solid #f8fafc;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 48px; height: 48px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    📦
                                </div>
                                <div>
                                    <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 600; color: #1e293b;">
                                        <?= html_escape($item['name']); ?>
                                    </h4>
                                    <div style="font-size: 13px; color: #64748b;">
                                        Qty: <span style="font-weight: 600; color: #0f172a;"><?= $item['quantity']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 15px; font-weight: 700; color: #0f172a;">
                                    $<?= number_format($subtotal, 2); ?>
                                </div>
                                <small style="font-size: 12px; color: #94a3b8;">
                                    $<?= number_format($item['price'], 2); ?> each
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>

        <!-- Right Side: Totals & Payments -->
        <div style="width: 100%; max-width: 420px; min-width: 320px;">
            <div style="background: #ffffff; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; padding: 24px; position: sticky; top: 24px;">

                <h3 style="margin: 0 0 20px 0; font-size: 18px; font-weight: 700; color: #0f172a; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9;">
                    Order Summary
                </h3>

                <!-- Price Calculator Section -->
                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; font-size: 14px; color: #64748b;">
                        <span>Subtotal</span>
                        <span style="font-weight: 600; color: #1e293b;">$<?= number_format($total, 2); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; color: #64748b;">
                        <span>Processing Fee</span>
                        <span style="color: #10b981; font-weight: 600;">Free</span>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #e2e8f0; padding-top: 16px; margin-bottom: 24px;">
                    <span style="font-size: 15px; font-weight: 600; color: #0f172a;">Total to Pay</span>
                    <span style="font-size: 24px; font-weight: 800; color: #2563eb;">$<?= number_format($total, 2); ?></span>
                </div>

                <!-- Payment Processor Form -->
                <form method="post" action="<?= site_url('index.php/user/checkout/placeOrder'); ?>">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="idempotency_key" value="<?= bin2hex(random_bytes(16)); ?>">

                    <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 12px;">
                        Payment Method
                    </div>

                    <!-- Options Grid Wrapper -->
                    <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px;">

                        <!-- Stripe Card Target Choice -->
                        <label class="modern-pay-tile">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <input type="radio" name="payment_method" value="stripe" checked class="modern-radio-input">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 14px; font-weight: 600; color: #1e293b;">Credit Card</span>
                                    <span style="font-size: 12px; color: #64748b;">Pay safely via Stripe</span>
                                </div>
                            </div>
                            <!-- Inline SVG for Credit Cards -->
                            <svg width="36" height="24" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="36" height="24" rx="4" fill="#6366F1" />
                                <rect y="4" width="36" height="4" fill="#4338CA" />
                                <rect x="4" y="14" width="8" height="4" rx="1" fill="white" fill-opacity="0.8" />
                                <circle cx="28" cy="16" r="3" fill="white" fill-opacity="0.9" />
                                <circle cx="31" cy="16" r="3" fill="white" fill-opacity="0.6" />
                            </svg>
                        </label>

                        <!-- PayPal Target Choice -->
                        <label class="modern-pay-tile">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <input type="radio" name="payment_method" value="paypal" class="modern-radio-input">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 14px; font-weight: 600; color: #1e293b;">PayPal</span>
                                    <span style="font-size: 12px; color: #64748b;">Instant checkout transfer</span>
                                </div>
                            </div>
                            <!-- Inline SVG for PayPal -->
                            <svg width="36" height="24" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="36" height="24" rx="4" fill="#003087" />
                                <path d="M12.5 6.5H16.5C18.5 6.5 19.5 7.5 19.3 9.5C19.1 11.5 17.7 12.5 15.7 12.5H13.7L12.7 17.5H10.5L12.5 6.5Z" fill="#0079C1" />
                                <path d="M14.5 8.5H18.5C20.5 8.5 21.5 9.5 21.3 11.5C21.1 13.5 19.7 14.5 17.7 14.5H15.7L14.7 19.5H12.5L14.5 8.5Z" fill="#00457C" style="mix-blend-mode:multiply;" />
                                <path d="M14.5 8.5H18.5C20.5 8.5 21.5 9.5 21.3 11.5C21.1 13.5 19.7 14.5 17.7 14.5H15.7L14.7 19.5H12.5L14.5 8.5Z" fill="#0079C1" />
                            </svg>
                        </label>

                    </div>

                    <button type="submit" id="submitBtn" class="modern-checkout-btn">
                        <span id="btnText">Complete Payment</span>
                    </button>
                </form>

            </div>
        </div>

    </div>
</div>
<script>
    document.querySelector('form').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');

        // Disable the button
        btn.disabled = true;
        btn.style.cursor = 'not-allowed';
        btn.style.opacity = '0.8';

        // Inject the spinner and change text
        btnText.innerHTML = '<span class="spinner"></span> Processing...';
    });
</script>
<style>
    /* Spinner Styles */
    .spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #ffffff;
        animation: spin 0.8s linear infinite;
        margin-right: 8px;
        vertical-align: middle;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Absolute layout isolation styles to stop template inheritance bleeding */
    .modern-pay-tile {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 16px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        cursor: pointer !important;
        margin: 0 !important;
        transition: all 0.2s ease;
        background: #ffffff !important;
    }

    .modern-pay-tile:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
    }

    .modern-pay-tile:has(.modern-radio-input:checked) {
        border-color: #2563eb !important;
        background-color: #f0f5ff !important;
        box-shadow: 0 0 0 1px #2563eb !important;
    }

    .modern-radio-input {
        width: 18px !important;
        height: 18px !important;
        margin: 0 !important;
        cursor: pointer !important;
        accent-color: #2563eb !important;
    }

    .modern-checkout-btn {
        width: 100% !important;
        background-color: #2563eb !important;
        color: #ffffff !important;
        border: none !important;
        padding: 14px !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        border-radius: 12px !important;
        cursor: pointer !important;
        transition: background-color 0.15s ease !important;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.1), 0 2px 4px -2px rgba(37, 99, 235, 0.1) !important;
    }

    .modern-checkout-btn:hover {
        background-color: #1d4ed8 !important;
    }
</style>