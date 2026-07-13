<div class="container-fluid py-4">
s
    <div class="row g-4">


        <!-- LEFT -->

        <div class="col-lg-8">



            <!-- Customer -->
            <div class="card shadow-sm border-0 mb-4">


                <div class="card-header bg-white">

                    <h5 class="fw-bold mb-0">

                        <i class="fas fa-user text-primary me-2"></i>
                        Customer Information

                    </h5>

                </div>



                <div class="card-body">


                    <div class="row">


                        <div class="col-md-6 mb-3">

                            <label class="text-muted small">
                                Customer Name
                            </label>

                            <h6 class="fw-bold">
                                <?= $order->customer_name ?? '-' ?>
                            </h6>

                        </div>



                        <div class="col-md-6 mb-3">

                            <label class="text-muted small">
                                Email
                            </label>

                            <h6>
                                <?= $order->email ?? '-' ?>
                            </h6>

                        </div>



                        <div class="col-md-6">

                            <label class="text-muted small">
                                Order Number
                            </label>

                            <h6>
                                <?= $order->order_no ?>
                            </h6>

                        </div>



                        <div class="col-md-6">

                            <label class="text-muted small">
                                Order Date
                            </label>

                            <h6>
                                <?= date(
                                    'd M Y H:i',
                                    strtotime($order->created_at)
                                ) ?>
                            </h6>

                        </div>


                    </div>


                </div>


            </div>







            <!-- Products -->

            <div class="card shadow-sm border-0">


                <div class="card-header bg-white">


                    <h5 class="fw-bold mb-0">

                        <i class="fas fa-shopping-cart text-primary me-2"></i>
                        Ordered Products

                    </h5>


                </div>



                <div class="card-body p-0">


                    <div class="table-responsive">


                        <table class="table align-middle mb-0">


                            <thead class="table-light">


                                <tr>

                                    <th class="ps-4">
                                        Product
                                    </th>

                                    <th>
                                        Qty
                                    </th>

                                    <th>
                                        Price
                                    </th>

                                    <th>
                                        Subtotal
                                    </th>

                                </tr>


                            </thead>



                            <tbody>


                                <?php if (empty($order->items)): ?>


                                    <tr>

                                        <td colspan="4"
                                            class="text-center py-4">

                                            No products found

                                        </td>

                                    </tr>


                                <?php else: ?>



                                    <?php foreach ($order->items as $item): ?>


                                        <tr>


                                            <td class="ps-4">


                                                <strong>

                                                    <?= $item->product_name
                                                        ?? 'Unknown Product' ?>

                                                </strong>


                                            </td>



                                            <td>

                                                <?= $item->quantity ?>

                                            </td>



                                            <td>

                                                $<?= number_format(
                                                        $item->unit_price,
                                                        2
                                                    ) ?>

                                            </td>



                                            <td>

                                                <strong>

                                                    $<?= number_format(
                                                            $item->subtotal,
                                                            2
                                                        ) ?>

                                                </strong>

                                            </td>


                                        </tr>


                                    <?php endforeach; ?>



                                <?php endif; ?>



                            </tbody>


                        </table>


                    </div>


                </div>


            </div>





        </div>









        <!-- RIGHT -->

        <div class="col-lg-4">





            <!-- Summary -->

            <div class="card shadow-sm border-0 mb-4">


                <div class="card-header bg-white">

                    <h5 class="fw-bold mb-0">

                        Order Summary

                    </h5>

                </div>



                <div class="card-body">


                    <div class="d-flex justify-content-between mb-3">


                        <span class="text-muted">
                            Total Amount
                        </span>


                        <h5 class="fw-bold mb-0">

                            $<?= number_format(
                                    $order->total_amount,
                                    2
                                ) ?>

                        </h5>


                    </div>





                    <hr>



                    <label class="text-muted small">
                        Current Status
                    </label>



                    <?php

                    $badge = 'secondary';

                    switch ($order->status_name) {

                        case 'Pending':
                            $badge = 'warning text-dark';
                            break;

                        case 'Paid':
                            $badge = 'success';
                            break;

                        case 'Failed':
                            $badge = 'danger';
                            break;

                        case 'Cancelled':
                            $badge = 'dark';
                            break;

                        case 'Refunded':
                            $badge = 'info';
                            break;
                    }

                    ?>



                    <div class="mt-2">

                        <span class="badge bg-<?= $badge ?> fs-6">

                            <?= $order->status_name ?>

                        </span>


                    </div>


                </div>


            </div>








            <!-- Update Status -->

            <div class="card shadow-sm border-0">


                <div class="card-header bg-white">


                    <h5 class="fw-bold mb-0">

                        <i class="fas fa-edit text-primary me-2"></i>

                        Update Status

                    </h5>


                </div>




                <div class="card-body">



                    <form method="post"
                        action="<?= site_url(
                                    'admin/orders/updateStatus/' . $order->id
                                ) ?>">



                        <input type="hidden"
                            name="<?= $this->security->get_csrf_token_name(); ?>"
                            value="<?= $this->security->get_csrf_hash(); ?>">





                        <select name="status_lookup_id"
                            class="form-select">


                            <option value="5"
                                <?= $order->status_lookup_id == 5 ? 'selected' : '' ?>>
                                Pending
                            </option>


                            <option value="6"
                                <?= $order->status_lookup_id == 6 ? 'selected' : '' ?>>
                                Paid
                            </option>


                            <option value="7"
                                <?= $order->status_lookup_id == 7 ? 'selected' : '' ?>>
                                Failed
                            </option>


                            <option value="8"
                                <?= $order->status_lookup_id == 8 ? 'selected' : '' ?>>
                                Cancelled
                            </option>


                            <option value="9"
                                <?= $order->status_lookup_id == 9 ? 'selected' : '' ?>>
                                Refunded
                            </option>



                        </select>




                        <button class="btn btn-primary w-100 mt-3">

                            <i class="fas fa-save me-1"></i>

                            Save Status

                        </button>



                    </form>


                </div>


            </div>



        </div>


    </div>

<a href="<?= site_url('admin/orders') ?>"
                    class="btn btn-outline-secondary">

                    <i class="fas fa-arrow-left me-1"></i>
                    Back

                </a>

</div>