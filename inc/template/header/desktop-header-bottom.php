<?php require_once THEME_TEMPLATE . 'header/shared-content.php'; ?>


<div class="col-12" style="padding: 0px;">
    <div class="site-header-bottom">
        <!-- Main Navigation -->
        <nav class="site-nav">
            <ul class="site-nav-list">
                <?php foreach ($main_menu as $menu_item): ?>
                    <li class="site-nav-item <?php echo $menu_item['has_submenu'] ? 'has-submenu' : ''; ?>">
                        <a class="site-nav-link" href="<?php echo $menu_item['url']; ?>">
                            <?php echo $menu_item['title']; ?>
                            <?php if ($menu_item['has_submenu']): ?>
                                <i class="icon-down"></i>
                            <?php endif; ?>
                        </a>
                        <?php if ($menu_item['has_submenu'] && isset($menu_item['submenu'])): ?>
                            <ul class="submenu">
                                <?php foreach ($menu_item['submenu'] as $sub_item): ?>
                                    <li><a href="<?php echo $sub_item['url']; ?>"><?php echo $sub_item['title']; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</div>
</div>
<!-- desktop Search Bar -->
<div class="desktop-search-bar" id="desktop-search-bar">
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
<!-- desktop Navigation Overlay -->
<div class="desktop-nav-overlay" id="desktop-nav-overlay"></div>
</div>