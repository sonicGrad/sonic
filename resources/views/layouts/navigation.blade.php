<div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
    <script type="text/javascript">
        try{ace.settings.loadState('sidebar')}catch(e){}
    </script>
    <div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">

        <ul class="menu-nav">
            
            <div class="p-1" style="padding-top: 0px !important">
                <input type="text" class="form-control mb-1" placeholder="{{__( "Search in List ...")}}" data-action="navbar-filter" />
            </div>
       
            <li class="menu-item  @if(isset($data['activePage']['dashboard'])) menu-item-active   @endif" aria-haspopup="true">
                <a href="{{route('dashboard')}}" class="menu-link" >
                    <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo4/dist/../src/media/svg/icons/Home/Home.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"/>
                            <path d="M3.95709826,8.41510662 L11.47855,3.81866389 C11.7986624,3.62303967 12.2013376,3.62303967 12.52145,3.81866389 L20.0429,8.41510557 C20.6374094,8.77841684 21,9.42493654 21,10.1216692 L21,19.0000642 C21,20.1046337 20.1045695,21.0000642 19,21.0000642 L4.99998155,21.0000673 C3.89541205,21.0000673 2.99998155,20.1046368 2.99998155,19.0000673 L2.99999828,10.1216672 C2.99999935,9.42493561 3.36258984,8.77841732 3.95709826,8.41510662 Z M10,13 C9.44771525,13 9,13.4477153 9,14 L9,17 C9,17.5522847 9.44771525,18 10,18 L14,18 C14.5522847,18 15,17.5522847 15,17 L15,14 C15,13.4477153 14.5522847,13 14,13 L10,13 Z" fill="#000000"/>
                        </g>
                    </svg><!--end::Svg Icon--></span>
                    <span class="menu-text dashbord" >{{__('Home')}}</span>
                </a>
            </li>
            <li class="menu-item  @if(isset($data['activePage']['users']) && $data['activePage']['users'] == 'change-password') menu-item-active   @endif" aria-haspopup="true">
                <a class="menu-link" data-toggle="modal" data-target="#exampleModal" id="chp">
                    <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo4/dist/../src/media/svg/icons/General/Lock.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <mask fill="white">
                                <use xlink:href="#path-1"/>
                            </mask>
                            <g/>
                            <path d="M7,10 L7,8 C7,5.23857625 9.23857625,3 12,3 C14.7614237,3 17,5.23857625 17,8 L17,10 L18,10 C19.1045695,10 20,10.8954305 20,12 L20,18 C20,19.1045695 19.1045695,20 18,20 L6,20 C4.8954305,20 4,19.1045695 4,18 L4,12 C4,10.8954305 4.8954305,10 6,10 L7,10 Z M12,5 C10.3431458,5 9,6.34314575 9,8 L9,10 L15,10 L15,8 C15,6.34314575 13.6568542,5 12,5 Z" fill="#000000"/>
                        </g>
                    </svg><!--end::Svg Icon--></span>
                    <span class="menu-text dashbord" >{{__('Change Password')}}</span>
                </a>
            </li>
         
            {{-- <li class="@if(isset($data['activePage']['products']) && $data['activePage']['products'] == 'categories') active @endif">
                <a href="{{route('categories.manage')}}">
                    <i class="fa-solid fa-sitemap "></i>
                    <span class="menu-text"> {{__('Manage Categories')}}</span>
                </a>

                <b class="arrow"></b>
            </li> --}}
            {{-- <li class="@if(isset($data['activePage']['products']) && $data['activePage']['products'] == 'products') active @endif">
                <a href="{{route('products.manage')}}">
                    <i class="fa-solid fa-list"></i>
                    <span class="menu-text"> {{__('Manage Products')}}</span>
                </a>

                <b class="arrow"></b>
            </li> --}}
            @if(\Auth::user()->can('products_module_categories_manage') || \Auth::user()->can('products_module_category_status_manage'))

                <li class="menu-item menu-item-submenu @if(isset($data['activePage']['categories'])) menu-item-open menu-item-here  @endif " aria-haspopup="true" data-menu-toggle="hover">
                    <a href="javascript:;" class="menu-link menu-toggle">
                        <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Text/Bullet-list.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M10.5,5 L19.5,5 C20.3284271,5 21,5.67157288 21,6.5 C21,7.32842712 20.3284271,8 19.5,8 L10.5,8 C9.67157288,8 9,7.32842712 9,6.5 C9,5.67157288 9.67157288,5 10.5,5 Z M10.5,10 L19.5,10 C20.3284271,10 21,10.6715729 21,11.5 C21,12.3284271 20.3284271,13 19.5,13 L10.5,13 C9.67157288,13 9,12.3284271 9,11.5 C9,10.6715729 9.67157288,10 10.5,10 Z M10.5,15 L19.5,15 C20.3284271,15 21,15.6715729 21,16.5 C21,17.3284271 20.3284271,18 19.5,18 L10.5,18 C9.67157288,18 9,17.3284271 9,16.5 C9,15.6715729 9.67157288,15 10.5,15 Z" fill="#000000"/>
                                <path d="M5.5,8 C4.67157288,8 4,7.32842712 4,6.5 C4,5.67157288 4.67157288,5 5.5,5 C6.32842712,5 7,5.67157288 7,6.5 C7,7.32842712 6.32842712,8 5.5,8 Z M5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 C6.32842712,10 7,10.6715729 7,11.5 C7,12.3284271 6.32842712,13 5.5,13 Z M5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 C6.32842712,15 7,15.6715729 7,16.5 C7,17.3284271 6.32842712,18 5.5,18 Z" fill="#000000" opacity="0.3"/>
                            </g>
                        </svg><!--end::Svg Icon--></span>
                        <span class="menu-text">{{__('Categories')}}</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <li class="menu-item menu-item-parent" aria-haspopup="true">
                                <span class="menu-link">
                                    <span class="menu-text">{{__('Categories')}}</span>
                                </span>
                            </li>
                            @if(\Auth::user()->can('products_module_categories_manage') )
                                <li class="menu-item  @if(isset($data['activePage']['categories']) && $data['activePage']['categories'] == 'categories') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('categories.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Manage Categories')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(\Auth::user()->can('products_module_categories_status_manage'))
                                <li class="menu-item  @if(isset($data['activePage']['categories']) && $data['activePage']['categories'] == 'category_status') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('category_status.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Category Status')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(\Auth::user()->can('products_module_category_attribute_types_manage'))
                                <li class="menu-item  @if(isset($data['activePage']['categories']) && $data['activePage']['categories'] == 'category_attribute_types') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('category_attribute_types.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Categories Attribute')}}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                
                </li>
            @endIf            
            @if(\Auth::user()->can('products_module_products_manage') || \Auth::user()->can('products_module_product_status_manage'))

                <li class="menu-item menu-item-submenu @if(isset($data['activePage']['products'])) menu-item-open menu-item-here  @endif " aria-haspopup="true" data-menu-toggle="hover">
                    <a href="javascript:;" class="menu-link menu-toggle">
                        <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Text/Bullet-list.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M10.5,5 L19.5,5 C20.3284271,5 21,5.67157288 21,6.5 C21,7.32842712 20.3284271,8 19.5,8 L10.5,8 C9.67157288,8 9,7.32842712 9,6.5 C9,5.67157288 9.67157288,5 10.5,5 Z M10.5,10 L19.5,10 C20.3284271,10 21,10.6715729 21,11.5 C21,12.3284271 20.3284271,13 19.5,13 L10.5,13 C9.67157288,13 9,12.3284271 9,11.5 C9,10.6715729 9.67157288,10 10.5,10 Z M10.5,15 L19.5,15 C20.3284271,15 21,15.6715729 21,16.5 C21,17.3284271 20.3284271,18 19.5,18 L10.5,18 C9.67157288,18 9,17.3284271 9,16.5 C9,15.6715729 9.67157288,15 10.5,15 Z" fill="#000000"/>
                                <path d="M5.5,8 C4.67157288,8 4,7.32842712 4,6.5 C4,5.67157288 4.67157288,5 5.5,5 C6.32842712,5 7,5.67157288 7,6.5 C7,7.32842712 6.32842712,8 5.5,8 Z M5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 C6.32842712,10 7,10.6715729 7,11.5 C7,12.3284271 6.32842712,13 5.5,13 Z M5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 C6.32842712,15 7,15.6715729 7,16.5 C7,17.3284271 6.32842712,18 5.5,18 Z" fill="#000000" opacity="0.3"/>
                            </g>
                        </svg><!--end::Svg Icon--></span>
                        <span class="menu-text">{{__('Products')}}</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <li class="menu-item menu-item-parent" aria-haspopup="true">
                                <span class="menu-link">
                                    <span class="menu-text">{{__('Products')}}</span>
                                </span>
                            </li>
                            @if(\Auth::user()->can('products_module_products_manage') )
                                <li class="menu-item  @if(isset($data['activePage']['products']) && $data['activePage']['products'] == 'products') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('products.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Manage Products')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(\Auth::user()->can('products_module_products_status_manage'))
                                <li class="menu-item  @if(isset($data['activePage']['products']) && $data['activePage']['products'] == 'product_status') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('product_status.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Product Status')}}</span>
                                    </a>
                                </li>
                            @endif
                          
                        </ul>
                    </div>
                
                </li>
            @endIf            
            @if(\Auth::user()->can('products_module_orders_manage') || \Auth::user()->can('products_module_order_status_manage'))

                <li class="menu-item menu-item-submenu @if(isset($data['activePage']['orders'])) menu-item-open menu-item-here  @endif " aria-haspopup="true" data-menu-toggle="hover">
                    <a href="javascript:;" class="menu-link menu-toggle">
                        <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Text/Bullet-list.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M10.5,5 L19.5,5 C20.3284271,5 21,5.67157288 21,6.5 C21,7.32842712 20.3284271,8 19.5,8 L10.5,8 C9.67157288,8 9,7.32842712 9,6.5 C9,5.67157288 9.67157288,5 10.5,5 Z M10.5,10 L19.5,10 C20.3284271,10 21,10.6715729 21,11.5 C21,12.3284271 20.3284271,13 19.5,13 L10.5,13 C9.67157288,13 9,12.3284271 9,11.5 C9,10.6715729 9.67157288,10 10.5,10 Z M10.5,15 L19.5,15 C20.3284271,15 21,15.6715729 21,16.5 C21,17.3284271 20.3284271,18 19.5,18 L10.5,18 C9.67157288,18 9,17.3284271 9,16.5 C9,15.6715729 9.67157288,15 10.5,15 Z" fill="#000000"/>
                                <path d="M5.5,8 C4.67157288,8 4,7.32842712 4,6.5 C4,5.67157288 4.67157288,5 5.5,5 C6.32842712,5 7,5.67157288 7,6.5 C7,7.32842712 6.32842712,8 5.5,8 Z M5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 C6.32842712,10 7,10.6715729 7,11.5 C7,12.3284271 6.32842712,13 5.5,13 Z M5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 C6.32842712,15 7,15.6715729 7,16.5 C7,17.3284271 6.32842712,18 5.5,18 Z" fill="#000000" opacity="0.3"/>
                            </g>
                        </svg><!--end::Svg Icon--></span>
                        <span class="menu-text">{{__('Orders')}}</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <li class="menu-item menu-item-parent" aria-haspopup="true">
                                <span class="menu-link">
                                    <span class="menu-text">{{__('Orders')}}</span>
                                </span>
                            </li>
                            @if(\Auth::user()->can('products_module_orders_manage') )
                                <li class="menu-item  @if(isset($data['activePage']['orders']) && $data['activePage']['orders'] == 'orders') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('orders.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Manage Orders')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(\Auth::user()->can('products_module_order_status_manage'))
                                <li class="menu-item  @if(isset($data['activePage']['orders']) && $data['activePage']['orders'] == 'order_status') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('order_status.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Order Status')}}</span>
                                    </a>
                                </li>
                            @endif
                          
                        </ul>
                    </div>
                
                </li>
            @endIf            
            @if(\Auth::user()->can('vendors_module_vendors_manage') 
            || \Auth::user()->can('vendors_module_vendors_status_manage')
            || \Auth::user()->can('vendors_module_vendors_types_manage')
            || \Auth::user()->can('vendors_module_coupons_manage')
            || \Auth::user()->can('vendors_module_offers_manage')
            )

                <li class="menu-item menu-item-submenu @if(isset($data['activePage']['vendors'])) menu-item-open menu-item-here  @endif " aria-haspopup="true" data-menu-toggle="hover">
                    <a href="javascript:;" class="menu-link menu-toggle">
                        <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Text/Bullet-list.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M10.5,5 L19.5,5 C20.3284271,5 21,5.67157288 21,6.5 C21,7.32842712 20.3284271,8 19.5,8 L10.5,8 C9.67157288,8 9,7.32842712 9,6.5 C9,5.67157288 9.67157288,5 10.5,5 Z M10.5,10 L19.5,10 C20.3284271,10 21,10.6715729 21,11.5 C21,12.3284271 20.3284271,13 19.5,13 L10.5,13 C9.67157288,13 9,12.3284271 9,11.5 C9,10.6715729 9.67157288,10 10.5,10 Z M10.5,15 L19.5,15 C20.3284271,15 21,15.6715729 21,16.5 C21,17.3284271 20.3284271,18 19.5,18 L10.5,18 C9.67157288,18 9,17.3284271 9,16.5 C9,15.6715729 9.67157288,15 10.5,15 Z" fill="#000000"/>
                                <path d="M5.5,8 C4.67157288,8 4,7.32842712 4,6.5 C4,5.67157288 4.67157288,5 5.5,5 C6.32842712,5 7,5.67157288 7,6.5 C7,7.32842712 6.32842712,8 5.5,8 Z M5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 C6.32842712,10 7,10.6715729 7,11.5 C7,12.3284271 6.32842712,13 5.5,13 Z M5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 C6.32842712,15 7,15.6715729 7,16.5 C7,17.3284271 6.32842712,18 5.5,18 Z" fill="#000000" opacity="0.3"/>
                            </g>
                        </svg><!--end::Svg Icon--></span>
                        <span class="menu-text">{{__('Vendors')}}</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <li class="menu-item menu-item-parent" aria-haspopup="true">
                                <span class="menu-link">
                                    <span class="menu-text">{{__('Vendors')}}</span>
                                </span>
                            </li>
                            @if(\Auth::user()->can('vendors_module_vendors_manage') )
                                <li class="menu-item  @if(isset($data['activePage']['vendors']) && $data['activePage']['vendors'] == 'vendors') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('vendors.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Manage Vendors')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(\Auth::user()->can('vendors_module_vendors_types_manage') )
                                <li class="menu-item  @if(isset($data['activePage']['vendors']) && $data['activePage']['vendors'] == 'vendors_types') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('vendor_types.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Vendors Types')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(\Auth::user()->can('vendors_module_vendors_status_manage'))
                                <li class="menu-item  @if(isset($data['activePage']['vendors']) && $data['activePage']['vendors'] == 'vendor_status') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('vendor_status.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Vendor Status')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(\Auth::user()->can('vendors_module_vendors_coupons_manage'))
                                <li class="menu-item  @if(isset($data['activePage']['vendors']) && $data['activePage']['vendors'] == 'coupons') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('coupons.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Vendors Coupons')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(\Auth::user()->can('vendors_module_vendors_offers_manage'))
                                <li class="menu-item  @if(isset($data['activePage']['vendors']) && $data['activePage']['vendors'] == 'offers') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('offers.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Vendors Offers')}}</span>
                                    </a>
                                </li>
                            @endif
                          
                        </ul>
                    </div>
                
                </li>
            @endIf            
           
           @if(\Auth::user()->can('drivers_module_drivers_manage') 
            || \Auth::user()->can('drivers_module_driver_status_manage')
            || \Auth::user()->can('drivers_module_drivers_types_manage')
            )

                <li class="menu-item menu-item-submenu @if(isset($data['activePage']['drivers'])) menu-item-open menu-item-here  @endif " aria-haspopup="true" data-menu-toggle="hover">
                    <a href="javascript:;" class="menu-link menu-toggle">
                        <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Text/Bullet-list.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M10.5,5 L19.5,5 C20.3284271,5 21,5.67157288 21,6.5 C21,7.32842712 20.3284271,8 19.5,8 L10.5,8 C9.67157288,8 9,7.32842712 9,6.5 C9,5.67157288 9.67157288,5 10.5,5 Z M10.5,10 L19.5,10 C20.3284271,10 21,10.6715729 21,11.5 C21,12.3284271 20.3284271,13 19.5,13 L10.5,13 C9.67157288,13 9,12.3284271 9,11.5 C9,10.6715729 9.67157288,10 10.5,10 Z M10.5,15 L19.5,15 C20.3284271,15 21,15.6715729 21,16.5 C21,17.3284271 20.3284271,18 19.5,18 L10.5,18 C9.67157288,18 9,17.3284271 9,16.5 C9,15.6715729 9.67157288,15 10.5,15 Z" fill="#000000"/>
                                <path d="M5.5,8 C4.67157288,8 4,7.32842712 4,6.5 C4,5.67157288 4.67157288,5 5.5,5 C6.32842712,5 7,5.67157288 7,6.5 C7,7.32842712 6.32842712,8 5.5,8 Z M5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 C6.32842712,10 7,10.6715729 7,11.5 C7,12.3284271 6.32842712,13 5.5,13 Z M5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 C6.32842712,15 7,15.6715729 7,16.5 C7,17.3284271 6.32842712,18 5.5,18 Z" fill="#000000" opacity="0.3"/>
                            </g>
                        </svg><!--end::Svg Icon--></span>
                        <span class="menu-text">{{__('Drivers')}}</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <li class="menu-item menu-item-parent" aria-haspopup="true">
                                <span class="menu-link">
                                    <span class="menu-text">{{__('Drivers')}}</span>
                                </span>
                            </li>
                            @if(\Auth::user()->can('drivers_module_drivers_manage') )
                                <li class="menu-item  @if(isset($data['activePage']['drivers']) && $data['activePage']['drivers'] == 'drivers') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('drivers.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Manage Drivers')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(\Auth::user()->can('drivers_module_drivers_manage') )
                                <li class="menu-item  @if(isset($data['activePage']['drivers']) && $data['activePage']['drivers'] == 'drivers_types') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('driver_types.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Drivers Types')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(\Auth::user()->can('drivers_module_drivers_status_manage'))
                                <li class="menu-item  @if(isset($data['activePage']['drivers']) && $data['activePage']['drivers'] == 'driver_status') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('driver_status.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Driver Status')}}</span>
                                    </a>
                                </li>
                            @endif
                          
                        </ul>
                    </div>
                
                </li>
            @endIf            
           @if(\Auth::user()->can('users_module_users_manage') 
            || \Auth::user()->can('users_module_user_status_manage')
            || \Auth::user()->can('users_module_otps_manage')
            || \Auth::user()->can('users_module_roles_manage')
            )

                <li class="menu-item menu-item-submenu @if(isset($data['activePage']['users'])) menu-item-open menu-item-here  @endif " aria-haspopup="true" data-menu-toggle="hover">
                    <a href="javascript:;" class="menu-link menu-toggle">
                        <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Text/Bullet-list.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M10.5,5 L19.5,5 C20.3284271,5 21,5.67157288 21,6.5 C21,7.32842712 20.3284271,8 19.5,8 L10.5,8 C9.67157288,8 9,7.32842712 9,6.5 C9,5.67157288 9.67157288,5 10.5,5 Z M10.5,10 L19.5,10 C20.3284271,10 21,10.6715729 21,11.5 C21,12.3284271 20.3284271,13 19.5,13 L10.5,13 C9.67157288,13 9,12.3284271 9,11.5 C9,10.6715729 9.67157288,10 10.5,10 Z M10.5,15 L19.5,15 C20.3284271,15 21,15.6715729 21,16.5 C21,17.3284271 20.3284271,18 19.5,18 L10.5,18 C9.67157288,18 9,17.3284271 9,16.5 C9,15.6715729 9.67157288,15 10.5,15 Z" fill="#000000"/>
                                <path d="M5.5,8 C4.67157288,8 4,7.32842712 4,6.5 C4,5.67157288 4.67157288,5 5.5,5 C6.32842712,5 7,5.67157288 7,6.5 C7,7.32842712 6.32842712,8 5.5,8 Z M5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 C6.32842712,10 7,10.6715729 7,11.5 C7,12.3284271 6.32842712,13 5.5,13 Z M5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 C6.32842712,15 7,15.6715729 7,16.5 C7,17.3284271 6.32842712,18 5.5,18 Z" fill="#000000" opacity="0.3"/>
                            </g>
                        </svg><!--end::Svg Icon--></span>
                        <span class="menu-text">{{__('Users')}}</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <li class="menu-item menu-item-parent" aria-haspopup="true">
                                <span class="menu-link">
                                    <span class="menu-text">{{__('Users')}}</span>
                                </span>
                            </li>
                            @if(\Auth::user()->can('users_module_users_manage') )
                                <li class="menu-item  @if(isset($data['activePage']['users']) && $data['activePage']['users'] == 'users') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('users.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Manage Users')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(\Auth::user()->can('users_module_user_status_manage'))
                                <li class="menu-item  @if(isset($data['activePage']['users']) && $data['activePage']['users'] == 'user_status') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('user_status.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('User Status')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(\Auth::user()->can('users_module_otps_manage'))
                                <li class="menu-item  @if(isset($data['activePage']['users']) && $data['activePage']['users'] == 'otps') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('otps.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Otp')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(\Auth::user()->can('users_module_roles_manage'))
                                <li class="menu-item  @if(isset($data['activePage']['users']) && $data['activePage']['users'] == 'roles') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('roles.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Roles & Permision')}}</span>
                                    </a>
                                </li>
                            @endif
                          
                        </ul>
                    </div>
                
                </li>
             @endIf            
            @if (\Auth::user()->can('cms_module_contact_us_manage') 
            || \Auth::user()->can('cms_module_social_media_links_manage') 
            || \Auth::user()->can('cms_module_terms_manage') 
            )
 
                 <li class="menu-item menu-item-submenu @if(isset($data['activePage']['cms'])) menu-item-open menu-item-here  @endif " aria-haspopup="true" data-menu-toggle="hover">
                     <a href="javascript:;" class="menu-link menu-toggle">
                         <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Text/Bullet-list.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                             <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                 <rect x="0" y="0" width="24" height="24"/>
                                 <path d="M10.5,5 L19.5,5 C20.3284271,5 21,5.67157288 21,6.5 C21,7.32842712 20.3284271,8 19.5,8 L10.5,8 C9.67157288,8 9,7.32842712 9,6.5 C9,5.67157288 9.67157288,5 10.5,5 Z M10.5,10 L19.5,10 C20.3284271,10 21,10.6715729 21,11.5 C21,12.3284271 20.3284271,13 19.5,13 L10.5,13 C9.67157288,13 9,12.3284271 9,11.5 C9,10.6715729 9.67157288,10 10.5,10 Z M10.5,15 L19.5,15 C20.3284271,15 21,15.6715729 21,16.5 C21,17.3284271 20.3284271,18 19.5,18 L10.5,18 C9.67157288,18 9,17.3284271 9,16.5 C9,15.6715729 9.67157288,15 10.5,15 Z" fill="#000000"/>
                                 <path d="M5.5,8 C4.67157288,8 4,7.32842712 4,6.5 C4,5.67157288 4.67157288,5 5.5,5 C6.32842712,5 7,5.67157288 7,6.5 C7,7.32842712 6.32842712,8 5.5,8 Z M5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 C6.32842712,10 7,10.6715729 7,11.5 C7,12.3284271 6.32842712,13 5.5,13 Z M5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 C6.32842712,15 7,15.6715729 7,16.5 C7,17.3284271 6.32842712,18 5.5,18 Z" fill="#000000" opacity="0.3"/>
                             </g>
                         </svg><!--end::Svg Icon--></span>
                         <span class="menu-text">{{__('cms')}}</span>
                         <i class="menu-arrow"></i>
                     </a>
                     <div class="menu-submenu">
                         <i class="menu-arrow"></i>
                         <ul class="menu-subnav">
                             <li class="menu-item menu-item-parent" aria-haspopup="true">
                                 <span class="menu-link">
                                     <span class="menu-text">{{__('CMS')}}</span>
                                 </span>
                             </li>
                             @if(\Auth::user()->can('cms_module_contact_us_manage') )
                                 <li class="menu-item  @if(isset($data['activePage']['cms']) && $data['activePage']['cms'] == 'contact_us') menu-item-active @endif" aria-haspopup="true">
                                     <a href="{{route('contact_us.manage')}}" class="menu-link">
                                         <i class="menu-bullet menu-bullet-dot">
                                             <span></span>
                                         </i>
                                         <span class="menu-text">{{__('Archive Of Messages For Admin')}}</span>
                                     </a>
                                 </li>
                             @endif
                             @if(\Auth::user()->can('cms_module_terms_manage') )
                                 <li class="menu-item  @if(isset($data['activePage']['cms']) && $data['activePage']['cms'] == 'terms') menu-item-active @endif" aria-haspopup="true">
                                     <a href="{{route('terms.manage')}}" class="menu-link">
                                         <i class="menu-bullet menu-bullet-dot">
                                             <span></span>
                                         </i>
                                         <span class="menu-text">{{__('Terms & Policies & About Us')}}</span>
                                     </a>
                                 </li>
                             @endif
                             @if(\Auth::user()->can('cms_module_social_media_links_manage'))
                                 <li class="menu-item  @if(isset($data['activePage']['cms']) && $data['activePage']['cms'] == 'social_media_links') menu-item-active @endif" aria-haspopup="true">
                                     <a href="{{route('social_media_links.manage')}}" class="menu-link">
                                         <i class="menu-bullet menu-bullet-dot">
                                             <span></span>
                                         </i>
                                         <span class="menu-text">{{__('Social Media Links')}}</span>
                                     </a>
                                 </li>
                             @endif
                           
                         </ul>
                     </div>
                 
                 </li>
            @endIf
            @if (\Auth::user()->can('core_module_county_province_manage') 
            || \Auth::user()->can('core_module_ads_manage') 
            )
 
                <li class="menu-item menu-item-submenu @if(isset($data['activePage']['core'])) menu-item-open menu-item-here  @endif " aria-haspopup="true" data-menu-toggle="hover">
                    <a href="javascript:;" class="menu-link menu-toggle">
                        <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Text/Bullet-list.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M10.5,5 L19.5,5 C20.3284271,5 21,5.67157288 21,6.5 C21,7.32842712 20.3284271,8 19.5,8 L10.5,8 C9.67157288,8 9,7.32842712 9,6.5 C9,5.67157288 9.67157288,5 10.5,5 Z M10.5,10 L19.5,10 C20.3284271,10 21,10.6715729 21,11.5 C21,12.3284271 20.3284271,13 19.5,13 L10.5,13 C9.67157288,13 9,12.3284271 9,11.5 C9,10.6715729 9.67157288,10 10.5,10 Z M10.5,15 L19.5,15 C20.3284271,15 21,15.6715729 21,16.5 C21,17.3284271 20.3284271,18 19.5,18 L10.5,18 C9.67157288,18 9,17.3284271 9,16.5 C9,15.6715729 9.67157288,15 10.5,15 Z" fill="#000000"/>
                                <path d="M5.5,8 C4.67157288,8 4,7.32842712 4,6.5 C4,5.67157288 4.67157288,5 5.5,5 C6.32842712,5 7,5.67157288 7,6.5 C7,7.32842712 6.32842712,8 5.5,8 Z M5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 C6.32842712,10 7,10.6715729 7,11.5 C7,12.3284271 6.32842712,13 5.5,13 Z M5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 C6.32842712,15 7,15.6715729 7,16.5 C7,17.3284271 6.32842712,18 5.5,18 Z" fill="#000000" opacity="0.3"/>
                            </g>
                        </svg><!--end::Svg Icon--></span>
                        <span class="menu-text">{{__('System constants')}}</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <li class="menu-item menu-item-parent" aria-haspopup="true">
                                <span class="menu-link">
                                    <span class="menu-text">{{__('System constants')}}</span>
                                </span>
                            </li>
                            @if(\Auth::user()->can('core_module_ads_manage') )
                                <li class="menu-item  @if(isset($data['activePage']['core']) && $data['activePage']['core'] == 'ads') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('ads.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Ads')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(\Auth::user()->can('core_module_county_province_manage') )
                                <li class="menu-item  @if(isset($data['activePage']['core']) && $data['activePage']['core'] == 'county_province') menu-item-active @endif" aria-haspopup="true">
                                    <a href="{{route('county_province.manage')}}" class="menu-link">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">{{__('Country Provinces')}}</span>
                                    </a>
                                </li>
                            @endif
                        
                        </ul>
                    </div>
                
                </li>
            @endIf
            
        </ul><!-- /.nav-list -->
    </div>
    <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
        <i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">{{__('Change Password')}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form  id="change-password" action="" method="post" class="form-horizontal">
        <div class="modal-body">
                @csrf
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">{{__('Current Password')}}</label>
                    <div class="col-sm-10">

                        <input type="text" class="form-control @error('current_pass') is-invalid @enderror" name="current_pass" >
                    </div>

                    @error('current_pass')
                    <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">{{__('new Password')}}</label>
                    <div class="col-sm-10">

                        <input type="text" class="form-control @error('password') is-invalid @enderror" name="password" >
                    </div>

                    @error('password')
                    <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">{{__('confirm new Password')}}</label>
                    <div class="col-sm-10">

                        <input type="text" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" >
                    </div>

                    @error('password_confirmation')
                    <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            </form>
      </div>
    </div>
  </div>