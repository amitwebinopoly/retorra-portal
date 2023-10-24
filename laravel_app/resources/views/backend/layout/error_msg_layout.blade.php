@if(Session::has('SUCCESS'))
    <script>
        (function(){
            <?php if(Session::get('SUCCESS')=='TRUE'){ ?>
            toastr.success('<?php echo Session::get('MESSAGE'); ?>');
            <?php }else{ ?>
            toastr.error('<?php echo Session::get('MESSAGE'); ?>');
            <?php }?>
        })();
    </script>

    <?php
    Session::forget('SUCCESS');
    Session::forget('MESSAGE');
    ?>
@endif
@if (isset($errors) && count($errors) > 0)
    <script>
        (function(){
            @foreach ($errors->all() as $error)
            toastr.error('{{ $error }}');
            @endforeach
        })();
    </script>
@endif