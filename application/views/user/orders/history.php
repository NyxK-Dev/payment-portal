<div class="container-fluid py-4">


    <!-- Search Filter -->
    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="get">

                <div class="row g-3">


                    <div class="col-md-4">

                        <input
                            type="text"
                            name="keyword"
                            class="form-control"
                            placeholder="Search order number..."
                            value="<?= $this->input->get('keyword') ?>">

                    </div>



                    <div class="col-md-3">

                        <input
                            type="date"
                            name="from"
                            class="form-control"
                            value="<?= $this->input->get('from') ?>">

                    </div>



                    <div class="col-md-3">

                        <input
                            type="date"
                            name="to"
                            class="form-control"
                            value="<?= $this->input->get('to') ?>">

                    </div>



                    <div class="col-md-1">

                        <button class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>

                    </div>

                    <div class="col-md-1">
                        <button
                            type="button"
                            id="resetSearch"
                            class="btn btn-secondary w-100">
                            <i class="fa fa-sync"></i>
                        </button>

                    </div>


                </div>


            </form>


        </div>

    </div>





    <!-- Orders Table -->

    <div class="card shadow-sm">

        <div class="card-body p-0">


            <div class="table-responsive">


                <table class="table table-hover align-middle mb-0">


                    <thead class="table-light">

                        <tr>

                            <th class="ps-4">
                                Order No
                            </th>

                            <th>
                                Products
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Total
                            </th>

                            <th class="text-center">
                                Action
                            </th>

                        </tr>

                    </thead>



                    <tbody>


                        <?php if (empty($orders)): ?>


                            <tr>

                                <td colspan="6"
                                    class="text-center py-5">

                                    No orders found.

                                </td>

                            </tr>



                        <?php else: ?>



                            <?php foreach ($orders as $order): ?>


                                <tr>


                                    <td class="ps-4">

                                        <strong>
                                            <?= $order->order_no ?>
                                        </strong>

                                    </td>




                                    <!-- ALL PRODUCTS IN ONE ORDER -->

                                    <td>


                                        <?php

                                        $products = [];


                                        if (!empty($order->items)) {

                                            foreach ($order->items as $item) {
                                                $products[] =
                                                    $item->product_name
                                                    ?? 'Unknown Product';
                                            }
                                        }


                                        echo implode(
                                            ', ',
                                            $products
                                        );


                                        ?>


                                    </td>





                                    <td>

                                        <?= date(
                                            'd M Y',
                                            strtotime(
                                                $order->created_at
                                            )
                                        ) ?>

                                    </td>





                                    <td>


                                        <?php if ($order->status_lookup_id == 5): ?>


                                            <span class="badge bg-warning text-dark">
                                                Pending
                                            </span>



                                        <?php elseif ($order->status_lookup_id == 7): ?>


                                            <span class="badge bg-primary">
                                                Fail
                                            </span>


                                        <?php elseif ($order->status_lookup_id == 6): ?>


                                            <span class="badge bg-success">
                                                Paid
                                            </span>



                                        <?php elseif ($order->status_lookup_id == 8): ?>


                                            <span class="badge bg-danger">
                                                Cancelled
                                            </span>



                                        <?php else: ?>


                                            <span class="badge bg-secondary">
                                                Unknown
                                            </span>



                                        <?php endif; ?>


                                    </td>





                                    <td>

                                        <strong>

                                            $<?= number_format(
                                                    $order->total_amount,
                                                    2
                                                ) ?>

                                        </strong>

                                    </td>





                                    <td class="text-center">


                                        <button
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#order<?= $order->id ?>">


                                            <i class="fas fa-eye"></i>
                                            View


                                        </button>


                                    </td>



                                </tr>







                                <!-- ORDER DETAIL -->

                                <tr class="collapse bg-light"
                                    id="order<?= $order->id ?>">


                                    <td colspan="6">


                                        <div class="p-3">


                                            <h6 class="fw-bold mb-3">
                                                Order Items
                                            </h6>




                                            <table class="table table-bordered mb-0">


                                                <thead>

                                                    <tr>

                                                        <th>
                                                            Product
                                                        </th>

                                                        <th>
                                                            Quantity
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



                                                    <?php foreach ($order->items as $item): ?>


                                                        <tr>


                                                            <td>

                                                                <?= $item->product_name
                                                                    ?? 'Unknown Product' ?>

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

                                                                $<?= number_format(
                                                                        $item->subtotal,
                                                                        2
                                                                    ) ?>

                                                            </td>


                                                        </tr>



                                                    <?php endforeach; ?>



                                                </tbody>


                                            </table>


                                        </div>


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
<script>
    document.addEventListener('DOMContentLoaded', function() {


        var searchInput = document.getElementById('searchOrder');

        var resetButton = document.getElementById('resetSearch');

        var rows = document.querySelectorAll('#ordersTable tbody tr');




        function filterOrders() {


            var keyword = searchInput.value.toLowerCase().trim();



            rows.forEach(function(row) {


                var text = row.innerText.toLowerCase();



                if (text.indexOf(keyword) !== -1) {

                    row.style.display = '';

                } else {

                    row.style.display = 'none';

                }


            });



        }




        searchInput.addEventListener('keyup', function() {


            filterOrders();


        });





        resetButton.addEventListener('click', function() {


            searchInput.value = '';

            filterOrders();

            searchInput.focus();


        });



    });
</script>