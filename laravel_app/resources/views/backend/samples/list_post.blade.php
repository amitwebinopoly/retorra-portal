<div class="row table-responsive">
    <div class="col-xs-12">
        <div>
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Checkout Date</th>
                    <th>Customer</th>
                    <th>Sample Name</th>
                    <th>Status</th>
                    <th>Details</th>
                </tr>
                </thead>

                <tbody>
                <?php
                $sr = $sr_start;
                foreach($all_list as $single){
                ?>
                <tr>
                    <td><?php echo $sr; ?></td>
                    <td><?php echo $single['checkout_on'];?></td>
                    <td><?php echo $single['assigned_to_user_name'];?></td>
                    <td><?php echo $single['name'];?></td>
                    <td><?php echo $single['state'];?></td>
                    <td>
                        <a href="javascript:;" class="btn btn-danger btn-circle btn-sm modal_tr_data" data-sequence_num="<?php echo $single['sequence_num']; ?>">
                            <i class="fas fa-image"></i>
                        </a>
                        <a href="javascript:;" class="btn btn-primary btn-circle btn-sm show_tr_data" data-sr="<?php echo $sr; ?>">
                            <i class="fas fa-angle-down"></i>
                        </a>
                    </td>
                </tr>
                <tr style="display: none;" id="tr_data_<?php echo $sr; ?>">
                    <td colspan="6">
                        <table style="width: 100%">
                            <tr>
                                <th>Price By Sq.Ft.</th>
                                <th>Materials</th>
                                <th>Weave Type</th>
                                <th>Days Checkout Out</th>
                                <th>AIN Number</th>
                            </tr>
                            <tr>
                                <td><?php echo $single['price'];?></td>
                                <td>-</td>
                                <td><?php echo $single['asset_type'];?></td>
                                <td>-</td>
                                <td><?php echo $single['identifier'];?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php
                $sr++;
                } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>