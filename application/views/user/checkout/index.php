<div class="container mt-4">


    <h3>
        Checkout
    </h3>


    <?php if ($this->session->flashdata('error')): ?>

        <div class="alert alert-danger">

            <?= $this->session->flashdata('error'); ?>

        </div>

    <?php endif; ?>



    <div class="row">


        <div class="col-md-8">


            <div class="card shadow">


                <div class="card-header">

                    Order Items

                </div>


                <div class="card-body">


                    <table class="table">


                        <thead>

                            <tr>

                                <th>
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


                            <?php

                            $total = 0;

                            ?>


                            <?php foreach ($cart as $item): ?>

                                <?php

                                $subtotal =
                                    $item['price']
                                    *
                                    $item['quantity'];

                                $total += $subtotal;

                                ?>

                                <tr>

                                    <td>
                                        <?= html_escape($item['name']); ?>
                                    </td>


                                    <td>
                                        <?= $item['quantity']; ?>
                                    </td>


                                    <td>
                                        $
                                        <?= number_format($item['price'], 2); ?>
                                    </td>


                                    <td>
                                        $
                                        <?= number_format($subtotal, 2); ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>


                        </tbody>


                    </table>


                </div>


            </div>


        </div>



        <div class="col-md-4">


            <div class="card shadow">


                <div class="card-header">

                    Payment

                </div>


                <div class="card-body">


                    <h4>

                        Total:

                        <span class="text-primary">

                            $
                            <?= number_format(
                                $total,
                                2
                            ); ?>

                        </span>

                    </h4>


                    <hr>



                    <form method="post" action="<?= site_url(
                        'index.php/user/checkout/placeOrder'
                    ); ?>">



                   <input type="hidden" 
name="<?= $this->security->get_csrf_token_name(); ?>"
value="<?= $this->security->get_csrf_hash(); ?>">

<input
    type="hidden"
    name="idempotency_key"
    value="<?= bin2hex(random_bytes(16)); ?>"
>



                        <button class="btn btn-primary w-100">

                            <i class="bi bi-credit-card"></i>

                            Pay With Stripe

                        </button>



                    </form>


                </div>


            </div>


        </div>



    </div>


</div>