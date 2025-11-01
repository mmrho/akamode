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
</div>