<div class="container-fluid py-4">



    <!-- Orders Table Card -->
    <div class="card shadow-sm border-0">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th class="px-4">Order No</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (!empty($orders)): ?>


                        <?php foreach ($orders as $order): ?>

                            <tr>


                                <!-- Order Number -->
                                <td class="px-4">

                                    <span class="fw-semibold">
                                        #<?= $order->order_no ?>
                                    </span>

                                </td>



                                <!-- Customer -->
                                <td>

                                    <div class="d-flex align-items-center">

                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2"
                                             style="width:35px;height:35px;">

                                            <i class="fas fa-user text-secondary"></i>

                                        </div>


                                        <span>
                                            <?= html_escape($order->customer_name) ?>
                                        </span>

                                    </div>

                                </td>



                                <!-- Date -->
                                <td>

                                    <div>
                                        <?= date('d M Y', strtotime($order->created_at)) ?>
                                    </div>

                                    <small class="text-muted">
                                        <?= date('H:i', strtotime($order->created_at)) ?>
                                    </small>

                                </td>



                                <!-- Total -->
                                <td>

                                    <span class="fw-bold text-primary">
                                        $<?= number_format($order->total_amount,2) ?>
                                    </span>

                                </td>



                                <!-- Status -->
                                <td>


                                    <?php

                                    $badge = 'secondary';

                                    switch (strtolower($order->status_name)) {

                                        case 'pending':
                                            $badge = 'warning text-dark';
                                            break;

                                        case 'paid':
                                            $badge = 'success';
                                            break;

                                        case 'failed':
                                            $badge = 'danger';
                                            break;

                                        case 'cancelled':
                                            $badge = 'dark';
                                            break;

                                        case 'refunded':
                                            $badge = 'info';
                                            break;

                                    }

                                    ?>


                                    <span class="badge rounded-pill bg-<?= $badge ?> px-3 py-2">

                                        <?= $order->status_name ?>

                                    </span>


                                </td>



                                <!-- Action -->
                                <td class="text-center">


                                    <a href="<?= site_url('admin/orders/view/'.$order->id) ?>"
                                       class="btn btn-sm btn-outline-primary">

                                        <i class="fas fa-eye me-1"></i>
                                        View

                                    </a>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                    <?php else: ?>


                        <tr>

                            <td colspan="6" class="text-center py-5">


                                <div class="text-muted">

                                    <i class="fas fa-shopping-cart fa-2x mb-3"></i>

                                    <h6>
                                        No orders found
                                    </h6>

                                    <p class="mb-0">
                                        There are currently no orders available.
                                    </p>

                                </div>


                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>


                </table>


            </div>


        </div>

    </div>


</div>