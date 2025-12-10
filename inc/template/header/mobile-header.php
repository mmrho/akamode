<?php require_once THEME_TEMPLATE . 'header/shared-content.php'; ?>

<div class="mobile-header-container">
    <div class="mobile-header-content">
        <div class="mobile-header-action">
            <button class="mobile-menu-toggle" type="button" id="menuBtnIcon">
                <i class="<?php echo $support_info['hamburger-menu_icon']; ?>"></i>
                <span class="menu-label"></span>
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
            <a href="<?php echo esc_url($account_href); ?>" class="account_icon mobile-btn header-element" type="button" id="accountIcon">
                <i class="<?php echo $support_info['account_icon']; ?>"></i>
            </a>
            <a href="<?php echo home_url('/cart'); ?>" class="shopping-bag-icon mobile-btn header-element" id="shoppingBagIcon-mobile">
                <i class="<?php echo $support_info['shoping_bag_icon']; ?>"></i>
            </a>
            <button class="close-icon mobile-btn header-element" type="button" id="closeIcon">
                <i class="<?php echo $support_info['close_icon']; ?>"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Content -->
    <div class="mobile-nav" id="mobile-nav">
        <div class="mobile-nav-content">
            <ul class="mobile-nav-list">
                <?php foreach ($main_menu as $menu_item): ?>

                    <li class="mobile-nav-item <?php echo $menu_item['has_submenu'] ? 'has-submenu' : ''; ?>">
                        <a class="mobile-nav-link" href="<?php echo $menu_item['has_submenu'] ? 'javascript:void(0);' : $menu_item['url']; ?>"
                            <?php echo $menu_item['has_submenu'] ? 'data-has-submenu="true"' : ''; ?>>
                            <?php echo $menu_item['title']; ?>
                            <?php if ($menu_item['has_submenu']): ?>
                                <i class="icon-down-open"></i>
                            <?php endif; ?>
                        </a>
                        <?php if ($menu_item['has_submenu'] && isset($menu_item['submenu'])): ?>
                            <ul class="mobile-submenu">
                                <li>
                                    <a class="type-a" href="<?php echo $menu_item['url']; ?>">
                                        مشاهده همه <?php echo $menu_item['title']; ?>
                                        <i class="icon-up-left-arrow"></i>
                                    </a>
                                </li>
                                <?php foreach ($menu_item['submenu'] as $sub_item): ?>
                                    <li><a href="<?php echo $sub_item['url']; ?>"><?php echo $sub_item['title']; ?><i class="icon-up-left-arrow"></i></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- mobile Search Bar -->
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
                            <a href=""><span>کیف</span><i class="icon-up-left-arrow"></i>
                            </a>
                        </li>
                        <li>
                            <a href=""><span>کلاه</span><i class="icon-up-left-arrow"></i>
                            </a>
                        </li>
                        <li>
                            <a href=""><span>کفش زمستانی</span><i class="icon-up-left-arrow"></i>
                            </a>
                        </li>
                        <li>
                            <a href=""><span>لباس مردانه</span><i class="icon-up-left-arrow"></i>
                            </a>
                        </li>
                        <li>
                            <a href=""><span>اکسسوری</span><i class="icon-up-left-arrow"></i>
                            </a>
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