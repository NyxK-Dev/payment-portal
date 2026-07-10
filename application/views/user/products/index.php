<div class="container mt-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h2 class="mb-3 mb-md-0">
            <i class="bi bi-box-seam me-2"></i>Products
        </h2>

        <div class="w-100 w-md-50" style="max-width: 350px;">
            <?php
            $this->load->view(
                'components/search_bar',
                [
                    'keyword' =>
                        $this->input->get('keyword'),

                    'placeholder' =>
                        'Search by product name or SKU'
                ]
            );
            ?>
        </div>
    </div>

    <div class="row g-4">

        <?php if (empty($products)): ?>

            <div class="col-12">
                <div class="alert alert-info text-center py-4">
                    <i class="bi bi-inbox display-6 d-block mb-2"></i>
                    No products found.
                </div>
            </div>

        <?php else: ?>

            <?php foreach ($products as $product): ?>

                <div class="col-sm-6 col-md-4 col-lg-3">

                    <div class="card h-100 shadow-sm border-0 product-card">

                        <div class="card-body d-flex flex-column">

                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                    <?= html_escape($product->category_name); ?>
                                </span>

                                <?php if (isset($product->stock_qty)): ?>
                                    <span class="badge <?= $product->stock_qty > 0 ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis'; ?>">
                                        <?= $product->stock_qty > 0 ? 'In Stock' : 'Out of Stock'; ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <h5 class="card-title mb-1">
                                <?= html_escape($product->name); ?>
                            </h5>

                            <p class="text-muted small mb-3">
                                SKU: <?= html_escape($product->sku ?? '—'); ?>
                            </p>

                            <h4 class="text-primary mt-auto mb-3">
                                $<?= number_format($product->price, 2); ?>
                            </h4>

                            <div class="d-flex gap-2 mt-auto">

                            

                                <?php if (isset($product->stock_qty) && $product->stock_qty > 0): ?>
                                    <a href="<?= site_url('products/' . $product->id); ?>"
                                   class="btn btn-outline-primary flex-fill">
                                    <i class="bi bi-eye me-1"></i>Buy
                                </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary flex-fill" disabled>
                                        <i class="bi bi-cart-x me-1"></i>Buy
                                    </button>
                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>

<style>
    .product-card {
        transition: transform 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.12) !important;
    }
</style>