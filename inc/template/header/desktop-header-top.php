<?php require_once THEME_TEMPLATE . 'header/shared-content.php'; ?>

<div class="desktop-header-container">
    <div class="row">
        <div class="col-12" style="padding: 0px;">
            <div class="site-header-top">
                <!-- Support and Sales Info -->
                <div class="site-header-top-support">
                    <a href="#" class="site-header-top-support-online-a">
                        <i class="<?php echo $support_info['support_icon']; ?>"></i><?php echo $support_info['support_text']; ?>
                    </a>
                    <span class="site-header-top-support-number">
                        <?php echo $support_info['phone_number']; ?>
                        <?php echo $support_info['phone_label']; ?>
                    </span>

                </div>
                <!-- Brand Logo and Name -->
                <a href="<?php echo $site_data['url']; ?>" class="site-header-top-brand">
                    <img class="img-fluid site-header-top-brand-logo"
                        src="<?php echo $site_data['logo']; ?>"
                        alt="<?php echo $site_data['name']; ?> لوگو">

                </a>

                <!-- Support and Sales Info -->
                <div class="site-header-top-icons">
                    <a href="#" class="site-header-top-icons-a">
                        <i class="<?php echo $support_info['search_icon']; ?>"></i>
                    </a>
                    <a href="#" class="site-header-top-icons-a">
                        <i class="<?php echo $support_info['account_icon']; ?>"></i>
                    </a>
                    <a href="#" class="site-header-top-icons-a">
                        <i class="<?php echo $support_info['shoping_bag_icon']; ?>"></i>
                    </a>
                </div>
            </div>
        </div>