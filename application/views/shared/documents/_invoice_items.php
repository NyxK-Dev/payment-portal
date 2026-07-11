<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light text-muted text-uppercase small fw-bold border-bottom">
            <tr>
                <th>Item Description</th>
                <th class="text-center" style="width: 10%;">Qty</th>
                <th class="text-end" style="width: 20%;">Unit Price</th>
                <th class="text-end" style="width: 20%;">Total Price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <span class="d-block text-dark fw-bold mb-1" style="font-size: 0.95rem CONTAINER;"><?= html_escape($item->product_name); ?></span>
                        <small class="text-muted d-block mb-1"><?= html_escape($item->product_description); ?></small>
                        <small class="text-muted font-monospace bg-light px-2 py-0.5 rounded" style="font-size: 0.7rem;">SKU: <?= html_escape($item->sku); ?></small>
                    </td>
                    <td class="text-center font-monospace text-secondary"><?= $item->quantity; ?></td>
                    <td class="text-end font-monospace text-muted">$<?= $item->formatted_unit_price; ?></td>
                    <td class="text-end font-monospace fw-bold text-dark">$<?= $item->formatted_line_total; ?></td>
                </tr>
            <?php endforeach; ?>