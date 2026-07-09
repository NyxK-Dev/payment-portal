<div class="card">

    <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">
        Products
    </h5>

    <a href="<?= site_url('admin/products/create'); ?>"
       class="btn btn-primary ms-auto">
        Add Product
    </a>
</div>


    <div class="card-body">


        <?php $this->load->view('components/alerts'); ?>
        <!-- $this->load->view('components/alerts'); -->


        <table class="table table-bordered table-striped">

            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th width="180">
                    Action
                </th>
            </tr>
            </thead>


            <tbody>

            <?php if(!empty($products)): ?>

                <?php foreach($products as $index=>$product): ?>

                <tr>

                    <td>
                        <?= $index + 1; ?>
                    </td>


                    <td>
                        <?= html_escape($product->name); ?>
                    </td>


                    <td>
                        <?= html_escape($product->sku); ?>
                    </td>


                    <td>
                        <?= $product->category_name ?? '-'; ?>
                    </td>


                    <td>
                        <?= number_format($product->price,2); ?>
                    </td>


                    <td>
                        <?= $product->stock_qty; ?>
                    </td>


                    <td>

                        <?php if( $product->status_name): ?>

                            <span class="badge bg-success">
                                <?= $product->status_name; ?>
                            </span>

                        <?php endif; ?>

                    </td>


                    <td>

                        <a href="<?= site_url('admin/products/show/'.$product->id); ?>"
                           class="btn btn-sm btn-info">
                            View
                        </a>


                        <a href="<?= site_url('admin/products/edit/'.$product->id); ?>"
                           class="btn btn-sm btn-warning">
                            Edit
                        </a>


                        <a href="<?= site_url('admin/products/delete/'.$product->id); ?>"
                           onclick="return confirm('Delete this product?')"
                           class="btn btn-sm btn-danger">
                            Delete
                        </a>

                    </td>


                </tr>

                <?php endforeach; ?>


            <?php else: ?>

                <tr>
                    <td colspan="8"
                        class="text-center">

                        No products found

                    </td>
                </tr>

            <?php endif; ?>


            </tbody>

        </table>


        <?php $this->load->view('components/pagination'); ?>


    </div>

</div>