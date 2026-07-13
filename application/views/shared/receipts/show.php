<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<section class="content pt-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <a href="<?= site_url($receiptRoute); ?>" class="text-secondary text-decoration-none small fw-medium d-inline-flex align-items-center">
                <i class="fas fa-arrow-left me-2 text-muted"></i> Back to Receipts
            </a>
            <div>
                <a href="<?= site_url($receiptRoute . '/download/' . $receipt->id); ?>" class="btn btn-sm btn-outline-dark px-3 rounded-pill fw-medium" style="font-size: 0.85rem;">
                    <i class="fas fa-file-pdf me-1"></i> Download PDF
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm p-4 p-md-5 bg-white">
            <?php
            $this->load->view('shared/documents/_receipt_header', ['receipt' => $receipt]);
            $this->load->view('shared/documents/_receipt_summary', ['receipt' => $receipt]);
            ?>
        </div>
    </div>
</section>