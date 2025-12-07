<?php
require_once THEME_TEMPLATE . 'header/shared-content.php';

use YourTheme\Utils\NumberConverter;
?>

<div class="desktop-header-container">
    <div class="row">
        <div class="col-12" style="padding: 0px;">
            <div class="site-header-top">
                <!-- Support and Sales Info -->
                <div class="site-header-top-support">
                    <a href="tel:<?php echo tr_num_en($support_info['phone_number']); ?>" class="site-header-top-support-online-a">
                        <i class="<?php echo $support_info['support_icon']; ?>"></i><?php echo $support_info['support_text']; ?>

                        <span class="site-header-top-support-number">
                            <?php echo $support_info['phone_number']; ?>
                            <?php echo $support_info['phone_label']; ?>
                        </span>
                    </a>
                </div>
                <!-- Brand Logo and Name -->
                <a href="<?php echo $site_data['url']; ?>" class="site-header-top-brand">
                    <img class="img-fluid site-header-top-brand-logo"
                        src="<?php echo $site_data['logo']; ?>"
                        alt="<?php echo $site_data['name']; ?> لوگو">
                </a>
                <!-- Support and Sales Info -->
                <div class="site-header-top-icons">
                    <button class="search-icon desktop-btn header-element" type="button" id="searchIcon-D">
                        <i class="<?php echo $support_info['search_icon']; ?>"></i>
                    </button>
                    <a href="<?php echo esc_url($account_href); ?>" class="account_icon desktop-btn header-element" type="button" id="accountIcon">
                        <i class="<?php echo $support_info['account_icon']; ?>"></i>
                    </a>
                    <a href="<?php echo home_url('/cart'); ?>" class="shopping-bag-icon desktop-btn header-element" id="shoppingBagIcon-desktop">
                        <i class="<?php echo $support_info['shoping_bag_icon']; ?>"></i>
                    </a>
                </div>
            </div>
        </div>