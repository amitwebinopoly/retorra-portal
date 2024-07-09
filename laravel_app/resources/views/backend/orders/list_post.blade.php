<div class="table-responsive">
    <div class="col-xs-12">
        <div>
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Quote No.</th>
                    <th>QB Estimate ID</th>
                    <th>Email</th>
                    <th>Shopify Order ID</th>
                    <th>Status</th>
                </tr>
                </thead>

                <tbody>
                <?php
                $sr = $sr_start;
                foreach($all_list as $single){
                    $size = $single->width_feet.' '.$single->width_inch.' x '.$single->length_feet.' '.$single->length_inch;
                ?>
                <tr>
                    <td><?php echo $sr; ?></td>
                    <td><?php echo $single->quote_number;?></td>
                    <td><?php echo $single->qb_estimate_id;?></td>
                    <td><?php echo $single->shopify_customer_email;?></td>
                    <td><?php echo $single->shopify_order_id;?></td>
                    <td><?php echo $single->status;?></td>
                </tr>
                <?php
                $sr++;
                } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>