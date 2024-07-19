<div class="table-responsive">
    <div class="col-xs-12">
        <div>
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    @if($user_param['role']=='Admin')<th>Action</th>@endif
                </tr>
                </thead>

                <tbody>
                <?php
                $sr = $sr_start;
                foreach($all_list as $single){
                    ?>
                    <tr>
                        <td><?php echo $sr; ?></td>
                        <td><?php echo $single->first_name.' '.$single->last_name;?></td>
                        <td><?php echo $single->email;?></td>
                        <td><?php echo $single->role;?></td>
                        @if($user_param['role']=='Admin')
                        <td><a href="{{route('edit_user',[$single->id])}}" class="btn btn-primary btn-sm">Edit</a></td>
                        @endif
                    </tr>
                    <?php
                    $sr++;
                } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>