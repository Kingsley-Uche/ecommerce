<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!-- User details -->
        <div class="user-profile text-center mt-3">
            <div>
                <img src="assets/images/users/avatar-1.jpg" alt="" class="avatar-md rounded-circle">
            </div>
            <div class="mt-3">
                <h4 class="font-size-16 mb-1">Julia Hudda</h4>
                <span class="text-muted">
                    <i class="ri-record-circle-line align-middle font-size-14 text-success"></i>
                    Online
                </span>
            </div>
        </div>

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">

                <li class="menu-title">Menu</li>

                <!-- Dashboard -->
                <li>
                    <a href="index.html" class="waves-effect">
                        <i class="ri-dashboard-3-line"></i>
                        <span class="badge rounded-pill bg-success float-end">3</span>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Product Categories -->
                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-price-tag-3-line"></i>
                        <span>Product Category</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="{{ route('admin.product-category.index') }}">View</a>
                        </li>
                    </ul>
                </li>

                <!-- Products -->
                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-shopping-bag-3-line"></i>
                        <span>Product</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="{{ route('admin.product.index') }}">View</a>
                        </li>
                    </ul>
                </li>

                <!-- Store Details -->
                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-store-2-line"></i>
                        <span>Store Details</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="{{ route('admin.store_details.index') }}">View</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.store_details.create') }}">Create</a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->

    </div>
</div>
<!-- Left Sidebar End -->