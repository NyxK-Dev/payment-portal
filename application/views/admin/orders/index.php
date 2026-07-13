<div class="container-fluid py-4">


    <div class="card shadow-sm border-0 rounded-3">


        <!-- Header -->
        <div class="card-header bg-white border-0 py-3 ms-auto">

            <div class="row align-items-center">


                <!-- <div class="col-md-6">

                    <h5 class="mb-0 fw-bold">

                        <i class="fas fa-shopping-cart text-primary me-2"></i>

                        Orders

                    </h5>


                    <small class="text-muted">
                        Manage customer orders
                    </small>

                </div> -->



                <div class="col-md-12 mt-3 mt-md-0">


                    <div class="input-group">


                        <input
                            type="text"
                            id="searchOrder"
                            class="form-control"
                            placeholder="Search order no, customer, status...">



                        <button
                            type="button"
                            id="resetSearch"
                            class="btn btn-secondary">


                            <i class="fas fa-sync-alt me-1"></i>

                         


                        </button>


                    </div>


                </div>



            </div>


        </div>





        <!-- Table -->
        <div class="card-body p-0">


            <div class="table-responsive">


                <table 
                    class="table table-hover align-middle mb-0"
                    id="ordersTable">



                    <thead class="table-light">


                        <tr>


                            <th class="px-4">
                                Order No
                            </th>


                            <th>
                                Customer
                            </th>


                            <th>
                                Date
                            </th>


                            <th>
                                Total
                            </th>


                            <th>
                                Status
                            </th>


                            <th class="text-center">
                                Action
                            </th>


                        </tr>


                    </thead>





                    <tbody>


                    <?php if (!empty($orders)): ?>



                        <?php foreach ($orders as $order): ?>


                            <tr>



                                <!-- Order Number -->
                                <td class="px-4">


                                    <span class="fw-bold">

                                        #<?= html_escape($order->order_no) ?>

                                    </span>


                                </td>





                                <!-- Customer -->
                                <td>


                                    <div class="d-flex align-items-center">


                                        <div 
                                            class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2"
                                            style="width:38px;height:38px;">


                                            <i class="fas fa-user text-secondary"></i>


                                        </div>



                                        <span class="fw-semibold">

                                            <?= html_escape($order->customer_name) ?>

                                        </span>



                                    </div>


                                </td>






                                <!-- Date -->
                                <td>


                                    <div class="fw-semibold">

                                        <?= date('d M Y', strtotime($order->created_at)) ?>

                                    </div>


                                    <small class="text-muted">


                                        <i class="far fa-clock me-1"></i>


                                        <?= date('H:i', strtotime($order->created_at)) ?>


                                    </small>


                                </td>







                                <!-- Total -->
                                <td>


                                    <span class="fw-bold text-primary">


                                        $<?= number_format($order->total_amount, 2) ?>


                                    </span>


                                </td>







                                <!-- Status -->
                                <td>


                                    <?php


                                    $status = strtolower($order->status_name);


                                    $badge = 'secondary';


                                    switch ($status) {


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


                                        <?= ucfirst($order->status_name) ?>


                                    </span>



                                </td>








                                <!-- Action -->
                                <td class="text-center">


                                    <a
                                        href="<?= site_url('admin/orders/view/'.$order->id) ?>"
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


                                    <i class="fas fa-box-open fa-3x mb-3"></i>



                                    <h6 class="fw-bold">
                                        No Orders Found
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







<script>


document.addEventListener('DOMContentLoaded', function(){


    var searchInput = document.getElementById('searchOrder');

    var resetButton = document.getElementById('resetSearch');

    var rows = document.querySelectorAll('#ordersTable tbody tr');




    function filterOrders(){


        var keyword = searchInput.value.toLowerCase().trim();



        rows.forEach(function(row){


            var text = row.innerText.toLowerCase();



            if(text.indexOf(keyword) !== -1){

                row.style.display = '';

            }else{

                row.style.display = 'none';

            }


        });



    }




    searchInput.addEventListener('keyup', function(){


        filterOrders();


    });





    resetButton.addEventListener('click', function(){


        searchInput.value = '';

        filterOrders();

        searchInput.focus();


    });



});


</script>