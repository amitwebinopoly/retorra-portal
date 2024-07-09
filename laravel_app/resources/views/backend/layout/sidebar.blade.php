<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('admin_home')}}">
        <svg class="icon icon-logo" xmlns="http://www.w3.org/2000/svg" id="cls-logo2a" data-name="Слой 2" viewBox="0 0 159.4 30">
            <defs>
                <style>
                    .cls-logo {
                        fill: #404041;
                    }
                </style>
            </defs>
            <g id="cls-logo2" data-name="Layer 1">
                <g>
                    <path class="cls-logo" d="M19.75,9.91c-.05,2.26-.82,4.22-2.3,5.86-1.48,1.65-3.32,2.57-5.51,2.77l-.83,.08,8.03,10.81h-3.57L5.77,16.32h2.83c2.54,0,4.55-.65,6.03-1.96,1.48-1.31,2.17-2.95,2.07-4.94-.08-1.81-.73-3.28-1.96-4.41-1.33-1.23-3.09-1.85-5.28-1.85H2.98V29.43H0V.45H9.27c3.22,0,5.78,.89,7.69,2.66,1.91,1.77,2.84,4.04,2.79,6.8Z"></path>
                    <path class="cls-logo" d="M38.29,29.43h-8.33c-2.19,0-3.98-.62-5.39-1.85-1.41-1.23-2.11-2.99-2.11-5.28V7.58c0-2.29,.7-4.05,2.11-5.28,1.41-1.23,3.2-1.85,5.39-1.85h8.33V3.17h-8.35c-1.51,0-2.57,.3-3.18,.9-.96,1.01-1.44,2.01-1.44,3.02v6.26h9.72v2.79h-9.72v6.69c0,1,.48,2,1.44,3,.6,.6,1.66,.9,3.18,.9h8.35v2.71Z"></path>
                    <path class="cls-logo" d="M56.88,3.17h-6.6V29.43h-2.86V3.17h-6.56V.45h16.02V3.17Z"></path>
                    <path class="cls-logo" d="M84.54,14.7c0,4.2-1.49,7.8-4.47,10.8-2.98,3-6.55,4.5-10.72,4.5s-7.73-1.44-10.76-4.33c-3.03-2.89-4.54-6.37-4.54-10.44s1.48-7.75,4.43-10.74c2.95-2.99,6.51-4.48,10.68-4.48s7.78,1.43,10.82,4.28c3.04,2.85,4.56,6.33,4.56,10.42Zm-2.79,.19c-.05-3.38-1.3-6.24-3.75-8.58-2.45-2.34-5.37-3.52-8.76-3.52s-6.22,1.2-8.63,3.59c-2.41,2.39-3.62,5.27-3.62,8.62s1.21,6.19,3.62,8.58c2.41,2.39,5.3,3.6,8.67,3.63,3.42,.03,6.36-1.17,8.82-3.59,2.46-2.42,3.68-5.33,3.66-8.73Z"></path>
                    <path class="cls-logo" d="M107.08,9.91c-.05,2.26-.82,4.22-2.3,5.86-1.48,1.65-3.32,2.57-5.51,2.77l-.83,.08,8.03,10.81h-3.57l-9.8-13.12h2.83c2.54,0,4.55-.65,6.03-1.96,1.48-1.31,2.17-2.95,2.07-4.94-.08-1.81-.73-3.28-1.96-4.41-1.33-1.23-3.09-1.85-5.28-1.85h-6.48V29.43h-2.98V.45h9.27c3.22,0,5.78,.89,7.69,2.66,1.91,1.77,2.84,4.04,2.79,6.8Z"></path>
                    <path class="cls-logo" d="M130,9.91c-.05,2.26-.82,4.22-2.3,5.86-1.48,1.65-3.32,2.57-5.51,2.77l-.83,.08,8.03,10.81h-3.57l-9.8-13.12h2.83c2.54,0,4.55-.65,6.03-1.96,1.48-1.31,2.17-2.95,2.07-4.94-.08-1.81-.73-3.28-1.96-4.41-1.33-1.23-3.09-1.85-5.28-1.85h-6.48V29.43h-2.98V.45h9.27c3.22,0,5.78,.89,7.69,2.66,1.91,1.77,2.84,4.04,2.79,6.8Z"></path>
                    <path class="cls-logo" d="M159.4,29.43h-3.24l-4.12-9.8h-13.44l-4.06,9.8h-3.67L143.83,.41h3.24l12.32,29.02Zm-8.38-12.4l-5.51-13.26-5.64,13.26h11.14Z"></path>
                </g>
            </g>
        </svg>
        {{--<div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">{{$user_param['role']}}</div>--}}
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item sb_check_active" data-route="admin_home">
        <a class="nav-link" href="{{route('admin_home')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item sb_check_active" data-route="list_user,add_user,edit_user">
        <a class="nav-link" href="{{route('list_user')}}">
            <i class="fas fa-fw fa-users"></i>
            <span>Users</span>
        </a>
    </li>

    <li class="nav-item sb_check_active" data-route="list_quote">
        <a class="nav-link" href="{{route('list_quote')}}">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Quotes</span>
        </a>
    </li>

    <li class="nav-item sb_check_active" data-route="list_order">
        <a class="nav-link" href="{{route('list_order')}}">
            <i class="fas fa-fw fa-list"></i>
            <span>Orders</span>
        </a>
    </li>

    <li class="nav-item sb_check_active" data-route="list_sample">
        <a class="nav-link" href="{{route('list_sample')}}">
            <i class="fas fa-fw fa-table"></i>
            <span>Samples</span>
        </a>
    </li>

    <li class="nav-item sb_check_active" data-route="settings">
        <a class="nav-link" href="{{route('settings')}}">
            <i class="fas fa-fw fa-cog"></i>
            <span>Settings</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>


</ul>

<script>
    $(document).ready(function () {
        var current_route_name = '{{\Request::route()->getName()}}';
        var this_a = '';
        $('.sb_check_active').each(function () {
            this_a = $(this);
            var route_string = this_a.data('route');
            if(route_string!=""){
                var route_array = route_string.split(',');
                if($.inArray(current_route_name,route_array)>=0){
                    this_a.addClass('active');         // <li>
                    /*if(this_a.parent().parent().hasClass('sub-menu')){      //check <ul class="sub-menu">
                        this_a.parent().parent().parent().addClass('active open');  //<ul class="sub-menu active open">
                        if(this_a.parent().parent().parent().children().first().find('span.arrow').length !== 0){       //check <span class="arrow"> in <a>
                            this_a.parent().parent().parent().children().first().find('span.arrow').addClass('open');   // <span class="arrow open">
                        }
                    }*/
                }
            }
        });
    });
</script>