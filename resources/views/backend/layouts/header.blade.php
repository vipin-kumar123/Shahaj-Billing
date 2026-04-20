<header class="mdc-top-app-bar">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="mdc-top-app-bar__row">
                <div class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
                    <button class="material-icons mdc-top-app-bar__navigation-icon mdc-icon-button sidebar-toggler">
                        menu
                    </button>
                    <span class="mdc-top-app-bar__title">Effortless Billing. Smarter Business.</span>
                    {{-- <div
                class="mdc-text-field mdc-text-field--outlined mdc-text-field--with-leading-icon search-text-field d-none d-md-flex">
                <i class="material-icons mdc-text-field__icon">search</i>
                <input class="mdc-text-field__input" id="text-field-hero-input" />
                <div class="mdc-notched-outline">
                    <div class="mdc-notched-outline__leading"></div>
                    <div class="mdc-notched-outline__notch">
                        <label for="text-field-hero-input" class="mdc-floating-label">Search..</label>
                    </div>
                    <div class="mdc-notched-outline__trailing"></div>
                </div>
            </div> --}}
                </div>
                <div class="mdc-top-app-bar__section mdc-top-app-bar__section--align-end mdc-top-app-bar__section-right">
                    <div class="menu-button-container menu-profile d-none d-md-block">
                        <button class="mdc-button mdc-menu-button">
                            <span class="d-flex align-items-center">

                                <span class="figure"
                                    style="width:35px; height:35px; border-radius:50%; overflow:hidden; display:inline-block;">

                                    <img src="{{ Auth::user()->photo ? asset(Auth::user()->photo) : asset('assets/default-user.png') }}"
                                        alt="user"
                                        style="width:100%; height:100%; object-fit:cover; object-position:center;">
                                </span>

                                <span class="user-name" style="margin-left:8px;">
                                    {{ Auth::user()->name }}
                                </span>

                            </span>
                        </button>


                        <div class="mdc-menu mdc-menu-surface" tabindex="-1">
                            <ul class="mdc-list" role="menu" aria-hidden="true" aria-orientation="vertical">
                                <li class="mdc-list-item" role="menuitem">
                                    <div class="item-thumbnail item-thumbnail-icon-only">
                                        <i class="mdi mdi-account-edit-outline text-primary"></i>
                                    </div>
                                    <div
                                        class="item-content d-flex align-items-start flex-column justify-content-center">
                                        <a href="{{ route('profile.edit') }}" class="text-decoration-none text-dark">
                                            <h6 class="item-subject font-weight-normal">
                                                <i class="bi bi-person"></i> Profile
                                            </h6>
                                        </a>
                                    </div>
                                </li>
                                <li class="mdc-list-item" role="menuitem">
                                    <div class="item-thumbnail item-thumbnail-icon-only">
                                        <i class="mdi mdi-settings-outline text-primary"></i>
                                    </div>
                                    <div
                                        class="item-content d-flex align-items-start flex-column justify-content-center">
                                        <a href="{{ route('setting.appSetting') }}"
                                            class="text-decoration-none text-dark">
                                            <h6 class="item-subject font-weight-normal">
                                                <i class="bi bi-gear"></i> Settings
                                            </h6>
                                        </a>
                                    </div>
                                </li>
                                <li class="mdc-list-item" role="menuitem">
                                    <div class="item-thumbnail item-thumbnail-icon-only">
                                        <i class="mdi mdi-email text-primary"></i>
                                    </div>
                                    <div
                                        class="item-content d-flex align-items-start flex-column justify-content-center">
                                        <h6 class="item-subject font-weight-normal">
                                            <i class="bi bi-envelope"></i> Mail Template
                                        </h6>
                                    </div>
                                </li>
                                <li class="mdc-list-item" role="menuitem">
                                    <div class="item-thumbnail item-thumbnail-icon-only">
                                        <i class="mdi mdi-settings-outline text-primary"></i>
                                    </div>
                                    <div
                                        class="item-content d-flex align-items-start flex-column justify-content-center">
                                        <a href="{{ route('doc.index') }}" class="text-decoration-none text-dark">
                                            <h6 class="item-subject font-weight-normal">
                                                <i class="bi bi-files"></i> Doc Upload
                                            </h6>
                                        </a>
                                    </div>
                                </li>
                                <li class="mdc-list-item" role="menuitem">
                                    <div class="item-thumbnail item-thumbnail-icon-only">
                                        <i class="mdi mdi-logout-variant"></i>
                                    </div>
                                    <div
                                        class="item-content d-flex align-items-start flex-column justify-content-center">
                                        <a href="{{ route('logout') }}" class="text-decoration-none text-dark"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            <h6 class="item-subject font-weight-normal"><i
                                                    class="bi bi-box-arrow-in-right"></i> Logout</h6>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                class="d-none">
                                                @csrf
                                            </form>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- <div class="menu-button-container">
                <button class="mdc-button mdc-menu-button">
                    <i class="mdi mdi-bell"></i>
                </button>
                <div class="mdc-menu mdc-menu-surface" tabindex="-1">
                    <h6 class="title">
                        <i class="mdi mdi-bell-outline mr-2 tx-16"></i>
                        Notifications
                    </h6>
                    <ul class="mdc-list" role="menu" aria-hidden="true" aria-orientation="vertical">
                        <li class="mdc-list-item" role="menuitem">
                            <div class="item-thumbnail item-thumbnail-icon">
                                <i class="mdi mdi-email-outline"></i>
                            </div>
                            <div class="item-content d-flex align-items-start flex-column justify-content-center">
                                <h6 class="item-subject font-weight-normal">
                                    You received a new message
                                </h6>
                                <small class="text-muted"> 6 min ago </small>
                            </div>
                        </li>
                        <li class="mdc-list-item" role="menuitem">
                            <div class="item-thumbnail item-thumbnail-icon">
                                <i class="mdi mdi-account-outline"></i>
                            </div>
                            <div class="item-content d-flex align-items-start flex-column justify-content-center">
                                <h6 class="item-subject font-weight-normal">
                                    New user registered
                                </h6>
                                <small class="text-muted"> 15 min ago </small>
                            </div>
                        </li>
                        <li class="mdc-list-item" role="menuitem">
                            <div class="item-thumbnail item-thumbnail-icon">
                                <i class="mdi mdi-alert-circle-outline"></i>
                            </div>
                            <div class="item-content d-flex align-items-start flex-column justify-content-center">
                                <h6 class="item-subject font-weight-normal">
                                    System Alert
                                </h6>
                                <small class="text-muted"> 2 days ago </small>
                            </div>
                        </li>
                        <li class="mdc-list-item" role="menuitem">
                            <div class="item-thumbnail item-thumbnail-icon">
                                <i class="mdi mdi-update"></i>
                            </div>
                            <div class="item-content d-flex align-items-start flex-column justify-content-center">
                                <h6 class="item-subject font-weight-normal">
                                    You have a new update
                                </h6>
                                <small class="text-muted"> 3 days ago </small>
                            </div>
                        </li>
                    </ul>
                </div>
            </div> --}}


                </div>
            </div>
        </div>
    </div>
</header>
