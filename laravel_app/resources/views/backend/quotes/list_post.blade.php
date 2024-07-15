<div class="table-responsive">
    <div class="col-xs-12">
        <div>
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Quote No.</th>
                    <th>Material</th>
                    <th>Shape</th>
                    <th>Native arm pom colors</th>
                    <th>Own arm pom colors</th>
                    <th>Size</th>
                    <th>Project Name</th>
                    <th>Status</th>
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
                    <td><?php echo $single->quote_number;?></td>
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
                </tr>
                <?php
                $sr++;
                } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>