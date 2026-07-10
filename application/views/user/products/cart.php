<div class="container mt-4">

    <h2>Shopping Cart</h2>

    <?php if (empty($cart)): ?>

        <div class="alert alert-info">
            Cart is empty.
        </div>

    <?php else: ?>

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th width="250">Quantity</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

                <?php
                $grandTotal = 0;
                ?>

                <?php foreach ($cart as $item): ?>

                    <?php
                    $subtotal =
                        $item['price'] * $item['quantity'];

                    $grandTotal += $subtotal;
                    ?>

                    <tr>

                        <td><?= html_escape($item['name']); ?></td>

                        <td><?= html_escape($item['sku']); ?></td>

                        <td>
                            $<?= number_format($item['price'], 2); ?>
                        </td>

                        <td>

                            <div class="input-group" style="width:150px;">

    <a href="<?= site_url('user/cart/decrease/'.$item['product_id']); ?>"
       class="btn btn-outline-secondary">
        -
    </a>

    <input type="text"
           class="form-control text-center"
           value="<?= $item['quantity']; ?>"
           readonly>

    <a href="<?= site_url('user/cart/increase/'.$item['product_id']); ?>"
       class="btn btn-outline-secondary">
        +
    </a>

</div>

                        </td>

                        <td>
                            $<?= number_format($subtotal, 2); ?>
                        </td>

                        <td>

                            <a href="<?= site_url('user/cart/remove/' . $item['product_id']); ?>" class="btn btn-danger">

                                Remove

                            </a>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

            <tfoot>

                <tr>

                    <th colspan="4" class="text-end">
                        Grand Total
                    </th>

                    <th>
                        $<?= number_format($grandTotal, 2); ?>
                    </th>

                    <th></th>

                </tr>

            </tfoot>

        </table>

        <div class="d-flex gap-2">


            <a href="<?= site_url('user/cart/clear'); ?>" class="btn btn-warning">

                Clear Cart

            </a>

            <a href="<?= site_url('user/cart/checkout'); ?>" class="btn btn-success">

                Checkout

            </a>

            <a href="<?= site_url('user/products'); ?>" class="btn btn-secondary">

                Continue Shopping

            </a>

        </div>

    <?php endif; ?>

</div>