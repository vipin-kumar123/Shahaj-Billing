<aside class="mdc-drawer mdc-drawer--dismissible mdc-drawer--open">
    <div class="p-3 border-bottom">
        <a href="{{ route('home') }}" class="text-decoration-none fw-bold fs-5 d-block text-center text-white">
            <!-- <img src="assets/backend/images/logo.svg" alt="logo" class="img-fluid" /> -->
            SAHAJ BILLING
        </a>
    </div>

    <div class="mdc-drawer__content">

        <div class="mdc-list-group">
            <nav class="mdc-list mdc-drawer-menu">

                <!-- DASHBOARD -->
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link d-flex align-items-center rounded
        {{ request()->routeIs('home') ? 'bg-light bg-opacity-25 text-white fw-bold' : '' }}"
                        href="{{ route('home') }}">
                        <i class="bi bi-person-vcard fs-5 me-2"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                @php
                    $userManagementOpen =
                        request()->routeIs('users.*') ||
                        request()->routeIs('roles.*') ||
                        request()->routeIs('permissions.*');
                @endphp

                <div class="mdc-list-item mdc-drawer-item">

                    <a class="mdc-drawer-link has-submenu" href="javascript:void(0)">
                        <i class="bi bi-person me-2 fs-5"></i>
                        User Management
                        <i class="material-icons mdc-drawer-arrow">chevron_right</i>
                    </a>

                    <div class="mdc-drawer-submenu-wrapper {{ $userManagementOpen ? 'submenu-open' : '' }}">
                        <nav class="mdc-list mdc-drawer-submenu">

                            <a class="mdc-drawer-link {{ request()->routeIs('users.*') ? 'is-current' : '' }}"
                                href="{{ route('users.index') }}">
                                <i class="bi bi-dot fs-5"></i>Users
                            </a>

                            <a class="mdc-drawer-link {{ request()->routeIs('roles.*') ? 'is-current' : '' }}"
                                href="{{ route('roles.index') }}">
                                <i class="bi bi-dot fs-5"></i>Roles
                            </a>

                            <a class="mdc-drawer-link {{ request()->routeIs('permissions.*') ? 'is-current' : '' }}"
                                href="{{ route('permissions.index') }}">
                                <i class="bi bi-dot fs-5"></i>Permissions
                            </a>

                        </nav>
                    </div>
                </div>

                <!-- CUSTOMER -->

                @php
                    $productManagement =
                        request()->routeIs('categories.*') ||
                        request()->routeIs('subcategories.*') ||
                        request()->routeIs('products.*') ||
                        request()->routeIs('brands.*');

                    $customerManagement = request()->routeIs('customers.*') || request()->routeIs('supplier.*');

                    $purchaseReturn = request()->routeIs('purchase.return.*');
                    $purchase = request()->routeIs('purchase.*') && !$purchaseReturn;

                    $saleReturn = request()->routeIs('sale.return.*');
                    $sale = request()->routeIs('sale.*') && !$saleReturn;

                    $inventoryMenu = request()->routeIs('inventory.*');

                    $currentStock = request()->routeIs('inventory.stock');
                    $stockLedger = request()->routeIs('inventory.ledger');
                    $stockAdjustment = request()->routeIs('inventory.adjustment');
                    $lowStock = request()->routeIs('inventory.low.stock');
                @endphp


                @php
                    // SALES MENU
                    $salesMenu =
                        request()->routeIs('reports.sales') ||
                        request()->routeIs('reports.sales.submit') ||
                        request()->routeIs('reports.item') ||
                        request()->routeIs('reports.items.submit') ||
                        request()->routeIs('reports.payment') ||
                        request()->routeIs('reports.payment.submit');

                    // PURCHASE MENU
                    $purchaseMenu =
                        request()->routeIs('reports.purchase') ||
                        request()->routeIs('reports.purchase.submit') ||
                        request()->routeIs('reports.item.purchase') ||
                        request()->routeIs('reports.item.submit') ||
                        request()->routeIs('reports.purchase.payment') ||
                        request()->routeIs('reports.purchase.payment.submit');

                    $reportsMenu = $salesMenu || $purchaseMenu;
                @endphp

                <div class="mdc-list-item mdc-drawer-item">

                    <a class="mdc-drawer-link has-submenu" href="javascript:void(0)">
                        <i class="bi bi-people me-2 fs-5"></i>
                        Contacts
                        <i class="material-icons mdc-drawer-arrow">chevron_right</i>
                    </a>

                    <div class="mdc-drawer-submenu-wrapper {{ $customerManagement ? 'submenu-open' : '' }}">
                        <nav class="mdc-list mdc-drawer-submenu">

                            <a class="mdc-drawer-link {{ request()->routeIs('customers.*') ? 'is-current' : '' }}"
                                href="{{ route('customers.index') }}">
                                <i class="bi bi-dot fs-5"></i>Customers
                            </a>

                            <a class="mdc-drawer-link {{ request()->routeIs('supplier.*') ? 'is-current' : '' }}"
                                href="{{ route('supplier.index') }}">
                                <i class="bi bi-dot fs-5"></i>Suppliers
                            </a>


                        </nav>
                    </div>
                </div>


                <!---account menu -->
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link d-flex align-items-center rounded
        {{ request()->routeIs('accounts*') ? 'bg-light bg-opacity-25 text-white fw-bold' : '' }}"
                        href="{{ route('accounts.index') }}">
                        <i class="bi bi-person-vcard fs-5 me-2"></i>
                        <span>Accounts</span>
                    </a>
                </div>

                {{-- sale menu  --}}
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link has-submenu" href="javascript:void(0)">
                        <i class="bi bi-cart me-2 fs-5"></i>
                        Sale
                        <i class="material-icons mdc-drawer-arrow ms-auto"
                            style="{{ $sale || $saleReturn ? 'transform: rotate(90deg);' : '' }}">
                            chevron_right
                        </i>
                    </a>

                    <div class="mdc-drawer-submenu-wrapper {{ $sale || $saleReturn ? 'submenu-open' : '' }}">
                        <nav class="mdc-list mdc-drawer-submenu">

                            <a class="mdc-drawer-link {{ $sale ? 'is-current' : '' }}"
                                href="{{ route('sale.index') }}">
                                <i class="bi bi-dot fs-5"></i> Sale Invoice
                            </a>

                            <a class="mdc-drawer-link {{ $saleReturn ? 'is-current' : '' }}"
                                href="{{ route('sale.return.index') }}">
                                <i class="bi bi-dot fs-5"></i> Sale Return
                            </a>

                        </nav>
                    </div>
                </div>

                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link has-submenu" href="javascript:void(0)">
                        <i class="bi bi-tag me-2 fs-5"></i>
                        Purchase
                        <i class="material-icons mdc-drawer-arrow">chevron_right</i>
                    </a>

                    <div class="mdc-drawer-submenu-wrapper {{ $purchase || $purchaseReturn ? 'submenu-open' : '' }}">
                        <nav class="mdc-list mdc-drawer-submenu">

                            <a class="mdc-drawer-link {{ $purchase ? 'is-current' : '' }}"
                                href="{{ route('purchase.index') }}">
                                <i class="bi bi-dot fs-5"></i> Purchase Bill
                            </a>

                            <a class="mdc-drawer-link {{ $purchaseReturn ? 'is-current' : '' }}"
                                href="{{ route('purchase.return.index') }}">
                                <i class="bi bi-dot fs-5"></i> Purchase Return
                            </a>

                        </nav>
                    </div>
                </div>

                <div class="mdc-list-item mdc-drawer-item">

                    <a class="mdc-drawer-link has-submenu" href="javascript:void(0)">
                        <i class="bi bi-box-seam me-2 fs-5"></i>
                        Inventory
                        <i class="material-icons mdc-drawer-arrow">
                            {{ $inventoryMenu ? 'expand_more' : 'chevron_right' }}
                        </i>
                    </a>

                    <div class="mdc-drawer-submenu-wrapper {{ $inventoryMenu ? 'submenu-open' : '' }}">
                        <nav class="mdc-list mdc-drawer-submenu">

                            <a class="mdc-drawer-link {{ $currentStock ? 'is-current' : '' }}"
                                href="{{ route('inventory.stock') }}">
                                <i class="bi bi-dot fs-5"></i> Current Stock
                            </a>

                            <a class="mdc-drawer-link {{ $stockLedger ? 'is-current' : '' }}"
                                href="{{ route('inventory.ledger') }}">
                                <i class="bi bi-dot fs-5"></i> Stock Ledger
                            </a>

                            <a class="mdc-drawer-link {{ $stockAdjustment ? 'is-current' : '' }}"
                                href="{{ route('inventory.adjustment') }}">
                                <i class="bi bi-dot fs-5"></i> Stock Adjustment
                            </a>

                            <a class="mdc-drawer-link {{ $lowStock ? 'is-current' : '' }}"
                                href="{{ route('inventory.low.stock') }}">
                                <i class="bi bi-dot fs-5"></i> Low Stock
                            </a>

                        </nav>
                    </div>

                </div>



                <div class="mdc-list-item mdc-drawer-item">

                    <a class="mdc-drawer-link has-submenu" href="javascript:void(0)">
                        <i class="bi bi-basket me-2 fs-5"></i>
                        Products
                        <i class="material-icons mdc-drawer-arrow">chevron_right</i>
                    </a>

                    <div class="mdc-drawer-submenu-wrapper {{ $productManagement ? 'submenu-open' : '' }}">
                        <nav class="mdc-list mdc-drawer-submenu">

                            <a class="mdc-drawer-link {{ request()->routeIs('brands.*') ? 'is-current' : '' }}"
                                href="{{ route('brands.index') }}">
                                <i class="bi bi-dot fs-5"></i>Brands
                            </a>

                            <a class="mdc-drawer-link {{ request()->routeIs('categories.*') ? 'is-current' : '' }}"
                                href="{{ route('categories.index') }}">
                                <i class="bi bi-dot fs-5"></i>Categories
                            </a>

                            <a class="mdc-drawer-link {{ request()->routeIs('subcategories.*') ? 'is-current' : '' }}"
                                href="{{ route('subcategories.index') }}">
                                <i class="bi bi-dot fs-5"></i>Sub Categories
                            </a>

                            <a class="mdc-drawer-link {{ request()->routeIs('products.*') ? 'is-current' : '' }}"
                                href="{{ route('products.index') }}">
                                <i class="bi bi-dot fs-5"></i>Products
                            </a>

                        </nav>
                    </div>
                </div>

                {{-- report menu  --}}
                <div class="mdc-list-item mdc-drawer-item">

                    <!-- REPORTS -->
                    <a class="mdc-drawer-link has-submenu" href="javascript:void(0)">
                        <i class="bi bi-bar-chart me-2 fs-5"></i>
                        Reports
                        <i class="material-icons mdc-drawer-arrow">chevron_right</i>
                    </a>

                    <!-- REPORTS WRAPPER -->
                    <div class="mdc-drawer-submenu-wrapper {{ $reportsMenu ? 'submenu-open' : '' }}">
                        <nav class="mdc-list mdc-drawer-submenu">

                            <!-- ================= SALES ================= -->
                            <div class="mdc-list-item mdc-drawer-item">

                                <a class="mdc-drawer-link has-submenu" href="javascript:void(0)">
                                    <i class="bi bi-dot fs-5"></i> Sales
                                    <i class="material-icons mdc-drawer-arrow">chevron_right</i>
                                </a>

                                <div class="mdc-drawer-submenu-wrapper {{ $salesMenu ? 'submenu-open' : '' }}">
                                    <nav class="mdc-list mdc-drawer-submenu">

                                        <!-- Sale -->
                                        <a class="mdc-drawer-link 
    {{ request()->routeIs('reports.sales') || request()->routeIs('reports.sales.submit') ? 'is-current' : '' }}"
                                            href="{{ route('reports.sales') }}">
                                            <i class="bi bi-dot"></i> Sale
                                        </a>

                                        <!-- Item Sale -->
                                        <a class="mdc-drawer-link 
    {{ request()->routeIs('reports.item') || request()->routeIs('reports.items.submit') ? 'is-current' : '' }}"
                                            href="{{ route('reports.item') }}">
                                            <i class="bi bi-dot"></i> Item Sale
                                        </a>

                                        <!-- Payment -->
                                        <a class="mdc-drawer-link 
    {{ request()->routeIs('reports.payment') || request()->routeIs('reports.payment.submit') ? 'is-current' : '' }}"
                                            href="{{ route('reports.payment') }}">
                                            <i class="bi bi-dot"></i> Payment
                                        </a>

                                    </nav>
                                </div>

                            </div>

                            <!-- ================= PURCHASE ================= -->
                            <div class="mdc-list-item mdc-drawer-item">

                                <a class="mdc-drawer-link has-submenu" href="javascript:void(0)">
                                    <i class="bi bi-dot fs-5"></i> Purchase
                                    <i class="material-icons mdc-drawer-arrow">chevron_right</i>
                                </a>

                                <div class="mdc-drawer-submenu-wrapper {{ $purchaseMenu ? 'submenu-open' : '' }}">
                                    <nav class="mdc-list mdc-drawer-submenu">

                                        <!-- Purchase -->
                                        <a class="mdc-drawer-link 
    {{ request()->routeIs('reports.purchase') || request()->routeIs('reports.purchase.submit') ? 'is-current' : '' }}"
                                            href="{{ route('reports.purchase') }}">
                                            <i class="bi bi-dot"></i> Purchase
                                        </a>

                                        <!-- Item Purchase -->
                                        <a class="mdc-drawer-link 
    {{ request()->routeIs('reports.item.purchase') || request()->routeIs('reports.item.submit') ? 'is-current' : '' }}"
                                            href="{{ route('reports.item.purchase') }}">
                                            <i class="bi bi-dot"></i> Item Purchase
                                        </a>

                                        <!-- Payment -->
                                        <a class="mdc-drawer-link 
    {{ request()->routeIs('reports.purchase.payment') || request()->routeIs('reports.purchase.payment.submit') ? 'is-current' : '' }}"
                                            href="{{ route('reports.purchase.payment') }}">
                                            <i class="bi bi-dot"></i> Payment
                                        </a>

                                    </nav>
                                </div>

                            </div>

                        </nav>
                    </div>

                </div>


                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link d-flex align-items-center rounded
        {{ request()->routeIs('expenses*') ? 'bg-light bg-opacity-25 text-white fw-bold' : '' }}"
                        href="{{ route('expenses.index') }}">
                        <i class="bi bi-cash-stack fs-5 me-2"></i>
                        <span>Expenses</span>
                    </a>
                </div>

                {{--
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="pages/charts/chartjs.html">
                        <i class="material-icons mdc-list-item__start-detail">pie_chart_outlined</i>
                        Charts
                    </a>
                </div> --}}

            </nav>
        </div>
    </div>
</aside>
