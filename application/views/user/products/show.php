<div class="container mt-4" style="max-width: 700px;">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= site_url('products'); ?>" class="text-decoration-none">Products</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= site_url('products/' . $product->id); ?>" class="text-decoration-none">
                    <?= html_escape($product->name); ?>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Buy</li>
        </ol>
    </nav>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger">
            <?= $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>

    <?= form_open('user/cart/add', ['id' => 'addToCartForm']); ?>

    <input type="hidden" name="product_id" value="<?= $product->id; ?>">

    <!-- Order Summary -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">

        <div class="card-header bg-white border-0 rounded-top-4 pt-4 px-4">
            <strong class="fs-5">Order Summary</strong>
        </div>

        <div class="card-body px-4 pb-4">

            <hr class="mt-0">

            <div class="d-flex justify-content-between align-items-start mb-3">

                <div>
                    <h5 class="mb-1"><?= html_escape($product->name); ?></h5>
                    <p class="mb-1" style="color:#b08a4e;">
                        SKU: <?= html_escape($product->sku); ?>
                    </p>
                    <p class="mb-0" style="color:#1f9c8a;">
                        <?= html_escape($product->category_name); ?>
                    </p>
                </div>

                <h4 class="text-primary mb-0" id="unitPrice" data-price="<?= $product->price; ?>">
                    $<?= number_format($product->price, 2); ?>
                </h4>

            </div>

            <hr>

            <div class="row align-items-center">

                <div class="col-auto">
                    <span class="fs-6">Quantity</span>
                </div>

                <div class="col-auto ms-auto">
                    <div class="input-group" style="width: 150px;">
                        <button type="button" class="btn btn-outline-secondary" id="qtyMinus">-</button>
                        <input type="number" id="quantity" name="quantity" class="form-control text-center"
                            style="border-left:0; border-right:0;" value="1" min="1" max="<?= $product->stock_qty; ?>">
                        <button type="button" class="btn btn-outline-secondary" id="qtyPlus">+</button>
                    </div>
                </div>

            </div>

            <div class="text-end">
                <small class="text-muted"><?= $product->stock_qty; ?> available</small>
            </div>

            <div class=" mt-4">
                <button type="submit" id="addToCartBtn" class="btn btn-primary btn-lg rounded-3">
                    <i class="bi bi-cart-plus me-1"></i>Add to Cart
                </button>
                <a href="<?= site_url('products') ?>" class="btn btn-secondary btn-lg rounded-3">

                    Cancel

                </a>
            </div>

        </div>

    </div>

    <?= form_close(); ?>

</div>

<script>
    (function () {
        const qtyInput = document.getElementById('quantity');
        const qtyMinus = document.getElementById('qtyMinus');
        const qtyPlus = document.getElementById('qtyPlus');
        const maxQty = parseInt(qtyInput.max, 10) || 1;

        function clampQty() {
            let qty = parseInt(qtyInput.value, 10);

            if (isNaN(qty) || qty < 1) qty = 1;
            if (qty > maxQty) qty = maxQty;

            qtyInput.value = qty;
        }

        qtyMinus.addEventListener('click', function () {
            qtyInput.value = Math.max(1, parseInt(qtyInput.value, 10) - 1);
        });

        qtyPlus.addEventListener('click', function () {
            qtyInput.value = Math.min(maxQty, parseInt(qtyInput.value, 10) + 1);
        });

        qtyInput.addEventListener('input', clampQty);
        qtyInput.addEventListener('blur', clampQty);
    })();
</script>