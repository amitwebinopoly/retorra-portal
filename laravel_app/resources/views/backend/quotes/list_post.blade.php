<div class="table-responsive">
    <div class="col-xs-12">
        <div>
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Quote No.</th>
                    <th>Shape</th>
                    <th>Colors</th>
                    <th>Size</th>
                    <th>Status</th>
                </tr>
                </thead>

                <tbody>
                <?php
                $sr = $sr_start;
                foreach($all_list as $single){
                    $color_arr = [];
                    if(isset($single->ars_pom_color_1) && !empty($single->ars_pom_color_1)){
                        array_push($color_arr,$single->ars_pom_color_1);
                    }
                    if(isset($single->ars_pom_color_2) && !empty($single->ars_pom_color_2)){
                        array_push($color_arr,$single->ars_pom_color_2);
                    }
                    if(isset($single->ars_pom_color_3) && !empty($single->ars_pom_color_3)){
                        array_push($color_arr,$single->ars_pom_color_3);
                    }

                    $size = $single->width_feet.' '.$single->width_inch.' x '.$single->length_feet.' '.$single->length_inch;
                ?>
                <tr>
                    <td><?php echo $sr; ?></td>
                    <td><?php echo $single->quote_number;?></td>
                    <td><?php echo $single->shape;?></td>
                    <td><?php echo implode(',',$color_arr);?></td>
                    <td><?php echo $size;?></td>
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