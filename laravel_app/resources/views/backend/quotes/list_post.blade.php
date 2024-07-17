<div class="table-responsive">
    <div class="col-xs-12">
        <div>
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Quote No.</th>
                    <th>Pattern</th>
                    <th>Material</th>
                    <th>Shape</th>
                    <th>Native ars pom colors</th>
                    <th>Own ars pom colors</th>
                    <th>Size</th>
                    <th>Project Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                <?php
                $sr = $sr_start;
                foreach($all_list as $single){
                    $size = $single->width_feet.' '.$single->width_inch.' x '.$single->length_feet.' '.$single->length_inch;
                    $native_arm_pom_color_arr = explode('|',$single->native_arm_pom_color);
                ?>
                <tr>
                    <td><?php echo $sr; ?></td>
                    <td><?php echo $single->shopify_customer_name;?></td>
                    <td><?php echo $single->quote_number;?></td>
                    <td><?php echo $single->shopify_product_title;?></td>
                    <td><?php echo $single->material;?></td>
                    <td><?php echo $single->shape;?></td>
                    <td>
                        <?php for($i=0; $i<count($native_arm_pom_color_arr); $i++){
                            echo '<div class="quote-color" style=\'background-color:'.$native_arm_pom_color_arr[$i].'\'> &nbsp; </div>';
                        }
                        ?>
                    </td>
                    <td><?php echo $single->own_arm_pom_color;?></td>
                    <td><?php echo $size;?></td>
                    <td><?php echo $single->project_name;?></td>
                    <td><?php echo $single->qb_status;?></td>
                    <td>
                        <a href="{{route('api_download_quote_pdf',[$single->shopify_customer_id,$single->qb_estimate_id])}}" class="btn btn-primary btn-circle btn-sm" target="_blank">
                            <i class="fas fa-download"></i>
                        </a>
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