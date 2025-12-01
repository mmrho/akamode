<?php
// 1. دریافت اطلاعات از API
$client = Laravel_API_Client::get_instance();
$api_data = $client->get_menus();

$footer_columns = [];

// 2. بررسی صحت داده‌ها و تقسیم لینک‌ها به دو ستون
if (!is_wp_error($api_data) && !empty($api_data['footer_links'])) {
    $links = $api_data['footer_links'];
    if (count($links) > 0) {
        // تقسیم آرایه به دو قسمت برای نمایش در دو ستون (مشابه طرح اصلی شما)
        $chunk_size = ceil(count($links) / 2);
        $footer_columns = array_chunk($links, $chunk_size);
    }
}
?>

<div class="container-fluid">
    <div class="site-footer-container mt-5">
        <div class="footer-top">
            <div class="Special-features-icons">
                <ul>
                    <li><a href="#"><i class="icon-box"></i><span>ارسال سریع</span></a></li>
                    <li><a href="#"><i class="icon-curved-arrow"></i><span>ضمانت مرجوعی</span></a></li>
                    <li><a href="#"><i class="icon-quality-assurance"></i><span>تضمین کیفیت</span></a></li>
                    <li><a href="#"><i class="icon-support"></i><span>خدمات پس از فروش</span></a></li>
                </ul>
            </div>
            <div class="Special-features-email">
                <a href=""><span>دریافت ایمیل‌های محصولات جدید</span><i class="icon-up-left-arrow"></i></a>
            </div>
        </div>
        <div class="footer-middle">
            <div class="footer-column contact-column">
                <ul>
                    <li><a href="#"><i class="icon-support_icon"></i><span>+۹۸۴۱۳۳۳۱۲۳۱۲</span></a></li>
                    <li><a href="#"><i class="icon-support_icon"></i><span>+۹۸۴۱۳۳۳۱۲۳۱۲</span></a></li>
                    <li><a href="#"><i class="icon-gps-logo"></i><span>جاده الگولی - فلکه خیام - کوچه سوم - پلاک ۴۳</span></a></li>
                    <li><a href="#"><i class="icon-envelope"></i><span>info@akamod.com</span></a></li>
                </ul>
            </div>
            
            <div class="footer-links-group">
                <?php if (!empty($footer_columns)) : ?>
                    <?php foreach ($footer_columns as $column_links) : ?>
                        <div class="footer-column links-column">
                            <ul>
                                <?php foreach ($column_links as $link) : ?>
                                    <li>
                                        <a href="<?php echo esc_url($link['link_url']); ?>">
                                            <span><?php echo esc_html($link['name']); ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="footer-column links-column">
                        <ul>
                             <li><a href="#"><span>تماس با ما</span></a></li>
                             <li><a href="#"><span>ارسال کالا</span></a></li>
                        </ul>
                    </div>
                    <div class="footer-column links-column">
                        <ul>
                             <li><a href="#"><span>قوانین و مقررات</span></a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <div class="footer-column img-column">
                <ul>
                    <li><img class="slide-bg" src="<?php echo get_template_directory_uri(); ?>/images/temp/nomad.png" alt="نماد"></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <div>
                <span>تمامی حقوق برای آکامد محفوظ است.</span>
            </div>
            <div class="social-icons">
                <a href="#"><i class="icon-whatsapp"></i></a>
                <a href="#"><i class="icon-instagram-aka"></i></a>
                <a href="#"><i class="icon-telegram-aka"></i></a>
            </div>
        </div>
    </div>
</div>