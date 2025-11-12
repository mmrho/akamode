<?php require_once THEME_TEMPLATE . 'header/shared-content.php'; ?>

<div class="mobile-header-container">
    <div class="mobile-header-content">
        <div class="mobile-header-action">
            <button class="mobile-menu-toggle" type="button" id="menuBtnIcon">
                <i class="<?php echo $support_info['hamburger-menu_icon']; ?>"></i>
            </button>
        </div>
        <!-- Logo and Site Name -->
        <div class="mobile-header-brand header-element">
            <a href="<?php echo $site_data['url']; ?>" class="mobile-brand-link">
                <img class="img-fluid site-header-top-brand-logo"
                    src="<?php echo $site_data['logo']; ?>"
                    alt="<?php echo $site_data['name']; ?> لوگو">
            </a>
        </div>

        <!-- Search, Account and Shopping Cart Icons -->
        <div class="mobile-header-actions">
            <button class="search-icon mobile-btn header-element" type="button" id="searchIcon">
                <i class="<?php echo $support_info['search_icon']; ?>"></i>
            </button>
            <button class="account_icon mobile-btn header-element" type="button" id="accountIcon">
                <i class="<?php echo $support_info['account_icon']; ?>"></i>
            </button>
            <button class="shopping-bag-icon mobile-btn header-element" type="button" id="shoppingBagIcon">
                <i class="<?php echo $support_info['shoping_bag_icon']; ?>"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Content -->
    <div class="mobile-nav" id="mobile-nav">
        <div class="mobile-nav-content">
            <ul class="mobile-nav-list">
                <?php foreach ($main_menu as $menu_item): ?>
                    <li class="mobile-nav-item <?php echo $menu_item['has_submenu'] ? 'has-submenu' : ''; ?>">
                        <a class="mobile-nav-link" href="<?php echo $menu_item['url']; ?>"
                            <?php echo $menu_item['has_submenu'] ? 'data-has-submenu="true"' : ''; ?>>
                            <?php echo $menu_item['title']; ?>
                            <?php if ($menu_item['has_submenu']): ?>
                                <i class="icon-down-open"></i>
                            <?php endif; ?>
                        </a>
                        <?php if ($menu_item['has_submenu'] && isset($menu_item['submenu'])): ?>
                            <ul class="mobile-submenu">
                                <?php foreach ($menu_item['submenu'] as $sub_item): ?>
                                    <li><a href="<?php echo $sub_item['url']; ?>"><?php echo $sub_item['title']; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Mobile Action Buttons -->
            <div class="mobile-nav-buttons">
                <button class="<?php echo $action_buttons['service']['class']; ?> mobile-service-btn">
                    <span class="service-button-text"><?php echo $action_buttons['service']['text']; ?></span>
                </button>
                <button class="<?php echo $action_buttons['login']['class']; ?> mobile-login-btn">
                    <span class="login-button-text"><?php echo $action_buttons['login']['text']; ?></span>
                </button>
            </div>

            <!-- Support Info -->
            <div class="mobile-nav-support">
                <span class="site-header-top-sale-number">
                    <?php echo $support_info['phone_label']; ?> : <?php echo $support_info['phone_number']; ?>
                </span>
                <a href="#" class="site-header-top-support-online-a">
                    <i class="<?php echo $support_info['support_icon']; ?>"></i><?php echo $support_info['support_text']; ?>
                </a>
            </div>
        </div>
    </div>

    <!-- desktop Search Bar -->
    <div class="mobile-search-bar" id="mobile-search-bar">
        <div class="search-container">
            <div class="search-header">
                <div class="search-input-wrapper">
                    <i class="icon-search-aka"></i>
                    <input type="text" placeholder="<?php echo $site_data['search_placeholder']; ?>" />
                </div>
            </div>
            <div class="search-content">
                <div class="search-suggestions">
                    <ul>
                        <li>
                            <a href=""><span>کت و مانتو</span><i class="icon-up-left-arrow"></i>
                            </a>
                        </li>
                        <li>
                            کیف
                        </li>
                        <li>کلاه</li>
                        <li>
                            کفش زمستانی
                        </li>
                        <li>
                            لباس مردانه
                        </li>
                        <li>
                            اکسسوری
                        </li>
                    </ul>
                </div>
                <div class="search-results hidden">
                    <ul>
                        <li>
                            <a href="#">
                                <div class="search-results-blur">
                                    <div class="search-results-badge">
                                        <img class="search-results-badge-inner" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-1.jpg">
                                    </div>
                                    <div class="search-results-caption">
                                        <span>کیف سر دوشی چرم زنانه - چرم گاوی</span>
                                        <i class="icon-up-left-arrow"></i>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <div class="search-results-blur">
                                    <div class="search-results-badge">
                                        <img class="search-results-badge-inner" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-1.jpg">
                                    </div>
                                    <div class="search-results-caption">
                                        <span>کیف سر دوشی چرم زنانه - چرم گاوی</span>
                                        <i class="icon-up-left-arrow"></i>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <div class="search-results-blur">
                                    <div class="search-results-badge">
                                        <img class="search-results-badge-inner" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-1.jpg">
                                    </div>
                                    <div class="search-results-caption">
                                        <span>کیف سر دوشی چرم زنانه - چرم گاوی</span>
                                        <i class="icon-up-left-arrow"></i>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Shopping Panel -->
    <div class="mobile-shopping-panel" id="mobile-shopping-panel">
        <div class="shopping-header">
            <div class="shopping-container">
                <p>سبد خرید شما خالی است.</p>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Overlay -->
    <div class="mobile-nav-overlay" id="mobile-nav-overlay"></div>
</div>