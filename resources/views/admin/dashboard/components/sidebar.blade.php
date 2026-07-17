<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">

              <li>
    <a href="{{ route('admin.dashboard') }}" class="waves-effect">
        <i class="ri-dashboard-line"></i>
        <span>Dashboard</span>
    </a>
</li>

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
                 <li>
    <a href="javascript:void(0);" class="has-arrow waves-effect">
        <i class="ri-file-list-3-line"></i>
        <span>Orders</span>
    </a>
    <ul class="sub-menu" aria-expanded="false">
        <li>
            <a href="{{ route('admin.orders.index') }}">View Orders</a>
        </li>
    </ul>
</li>

                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-store-2-line"></i>
                        <span>Store Details</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="{{ route('admin.store_details.index') }}">View</a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('admin.logout') }}" class="waves-effect text-danger">
                        <i class="ri-shut-down-line text-danger"></i>
                        <span>Logout</span>
                    </a>
                </li>

            </ul>
        </div> </div> </div>