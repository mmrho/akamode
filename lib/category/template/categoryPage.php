<?php

/**
 * Template Name: API Category Page
 * Description: Dynamic category page connected to Laravel
 */

// =========================================================================
// 1. View and receive information (Backend Logic)
// =========================================================================

$api = Laravel_API_Client::get_instance();

$base_api_url = defined('LARAVEL_API_URL') ? LARAVEL_API_URL : 'https://akamode.com';

$site_url_clean = untrailingslashit($base_api_url);


$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;


$categories_response = $api->get_categories();
$categories_list = [];
if (!is_wp_error($categories_response) && isset($categories_response['data'])) {
    $categories_list = $categories_response['data'];
}


$products_response = $api->get_products($paged);
$products_list = [];
$meta = [];

if (!is_wp_error($products_response)) {

    $products_list = $products_response['data'] ?? [];
    $meta = $products_response['meta'] ?? [];
}
?>

<div class="main-container">
    <div class="category-container">
        <div class="page-title-container">

            <section class="page-title">
                <div class="breadcrumbs">خانه > دسته بندی ها</div>
                <h2 class="page-title">دسته بندی ها</h2>
                <p class="page-description">لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم با هدف بهبود ابزارهای کاربردی می باشد کتابهای زیادی در شصت و سه درصد گذشته حال و آینده شناخت فراوان جامعه و متخصصان را می طلبد تا با نرم افزارها شناخت بیشتری را برای طراحان رایانه ای علی الخصوص طراحان خلاقی و فرهنگ پیشرو در زبان فارسی ایجاد کرد</p>
            </section>

            <?php if (!empty($categories_list)): ?>
                <section class="sub-categories">
                    <?php foreach ($categories_list as $cat): ?>
                        <?php

                        $c_name = $cat['name'] ?? 'بدون نام';
                        $c_slug = $cat['slug'] ?? '#';
                        $c_link = home_url('/category/' . $c_slug);


                        if (!empty($cat['image_path'])) {
                            $c_img = $site_url_clean . '/storage/' . ltrim($cat['image_path'], '/');
                        } else {
                            $c_img = get_template_directory_uri() . '/images/temp/akamode-default-image.png';
                        }
                        ?>
                        <a href="<?php echo esc_url($c_link); ?>" class="sub-cat-item">
                            <img src="<?php echo esc_url($c_img); ?>" alt="<?php echo esc_attr($c_name); ?>">
                        </a>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
        </div>

        <div class="page-content-container">

            <section class="toolbar">
                <div class="sort-wrapper">
                    <div class="toolbar-btn" id="sortTrigger">
                        <i class="icon-sort-aka"></i>
                        <span id="sortLabel">مرتب‌سازی بر اساس : جدیدترین</span>
                    </div>
                    <div class="sort-dropdown" id="sortDropdown">
                        <span class="sort-option active">جدیدترین</span>
                        <span class="sort-option">گران‌ترین</span>
                        <span class="sort-option">ارزان‌ترین</span>
                        <span class="sort-option">پر‌فروش‌ترین</span>
                    </div>
                </div>

                <div class="toolbar-btn" id="filterTrigger">
                    <i class="icon-filter-aka"></i>
                    <span>فیلتر</span>
                </div>
            </section>

            <section class="product-grid">
                <?php if (!empty($products_list)): ?>
                    <?php foreach ($products_list as $product): ?>
                        <?php
                        
                        $p_title = $product['name'] ?? 'محصول';
                        $p_slug = $product['slug'] ?? '#';
                        $p_link = home_url('/product/' . $p_slug); 
                        $p_img = get_template_directory_uri() . '/images/temp/akamode-default-image.png';

                        if (!empty($product['images']) && is_array($product['images'])) {
                            $first_img_url = $product['images'][0]['url'] ?? null;
                            if ($first_img_url) {
                                $p_img = $site_url_clean . $first_img_url;
                            }
                        }

                        $price_html = '<div class="product-price">ناموجود</div>';

                        if (!empty($product['variants']) && is_array($product['variants'])) {
                            
                            $variant = $product['variants'][0];
                            $main_price = $variant['price'] ?? 0;
                            $sale_price = $variant['discount_price'] ?? 0;

                           
                            if ($sale_price > 0 && $sale_price < $main_price) {
                                $price_html = '<div class="product-price">
                                        <del>' . number_format($main_price) . '</del>
                                        <span>' . number_format($sale_price) . ' تومان</span>
                                    </div>';
                            } elseif ($main_price > 0) {
                                $price_html = '<div class="product-price">' . number_format($main_price) . ' تومان</div>';
                            }
                        }
                        ?>

                        <a href="<?php echo esc_url($p_link); ?>" class="product-card">
                            <div class="product-image-wrapper">
                                <img src="<?php echo esc_url($p_img); ?>" alt="<?php echo esc_attr($p_title); ?>">
                            </div>
                            <h3 class="product-title"><?php echo esc_html($p_title); ?></h3>
                            <?php echo $price_html; ?>
                        </a>

                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-products" style="grid-column: 1/-1; text-align:center; padding: 60px 20px;">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/temp/empty-box.png" alt="Empty-box" style="max-width:400px; opacity:0.9;">
                        <h3 style="margin-top:20px; color:#666;">محصولی یافت نشد</h3>
                        <p style="color:#999;">در این صفحه محصولی برای نمایش وجود ندارد.</p>
                        <?php if ($paged > 1): ?>
                            <a href="?paged=1" style="display:inline-block; margin-top:15px; padding:10px 20px; background:#000; color:#fff; text-decoration:none; border-radius:4px;">بازگشت به صفحه اول</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <?php if (isset($meta['last_page']) && $meta['last_page'] > 1): ?>
            <div class="pagination">
                <?php
                $curr = $meta['current_page'];
                $last = $meta['last_page'];
                function get_page_link_api($page)
                {
                    return add_query_arg('paged', $page);
                }
                ?>

                <?php if ($curr > 1): ?>
                    <a href="<?php echo get_page_link_api($curr - 1); ?>" class="page-btn next-btn"><i class="icon-right-open"></i></a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $last; $i++): ?>
                    <?php if ($i == $curr): ?>
                        <div class="page-btn active"><?php echo $i; ?></div>
                    <?php elseif ($i == 1 || $i == $last || ($i >= $curr - 2 && $i <= $curr + 2)): ?>
                        <a href="<?php echo get_page_link_api($i); ?>" class="page-btn"><?php echo $i; ?></a>
                    <?php elseif ($i == $curr - 3 || $i == $curr + 3): ?>
                        <div class="page-dots">...</div>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($curr < $last): ?>
                    <a href="<?php echo get_page_link_api($curr + 1); ?>" class="page-btn next-btn"><i class="icon-left-open"></i></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
    <div class="filter-overlay" id="overlay"></div>
    <aside class="filter-sidebar" id="sidebar">
        <div class="filter-header">
            <span class="filter-title">فیلترها</span>
            <div class="close-btn" id="closeFilter">
                <i class="icon-Multiplication"></i>
            </div>
        </div>
        <div class="filter-body">
            <div class="f-section">
                <div class="f-head">
                    <span>دسته بندی</span>
                    <i class="icon-down-open"></i>
                </div>
                <div class="f-content">
                    <label class="chk-row">
                        <input type="checkbox">
                        <div class="chk-visual"></div>
                        <span>کفش و کیف</span>
                    </label>
                    <label class="chk-row">
                        <input type="checkbox">
                        <div class="chk-visual"></div>
                        <span>پوشاک آقایان</span>
                    </label>
                    <label class="chk-row">
                        <input type="checkbox">
                        <div class="chk-visual"></div>
                        <span>اکسسوری</span>
                    </label>
                </div>
            </div>
            <div class="f-section">
                <div class="f-head">
                    <span>رنگ</span>
                    <i class="icon-down-open"></i>
                </div>
                <div class="f-content">
                    <div class="color-wrap">
                        <div class="color-opt selected">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-25.jpg" alt="Product-color">
                            <span>قهوه‌ای</span>
                        </div>
                        <div class="color-opt">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-26.jpg" alt="Product-color">
                            <span>یشمی</span>
                        </div>
                        <div class="color-opt">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-27.jpg" alt="Product-color">
                            <span>زرشکی</span>
                        </div>
                        <div class="color-opt">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-28.jpg" alt="Product-color">
                            <span>مشکی</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="f-section">
                <div class="f-head">
                    <span>سایز</span>
                    <i class="icon-down-open"></i>
                </div>
                <div class="f-content">
                    <div class="size-wrap">
                        <div class="size-opt">36</div>
                        <div class="size-opt selected">37</div>
                        <div class="size-opt">38</div>
                        <div class="size-opt">39</div>
                        <div class="size-opt">40</div>
                        <div class="size-opt">41</div>
                        <div class="size-opt">42</div>
                        <div class="size-opt">43</div>
                        <div class="size-opt">44</div>
                        <div class="size-opt">45</div>
                    </div>
                </div>
            </div>
            <div class="f-section" style="border:none;">
                <div class="f-head">
                    <span>محدوده قیمت</span>
                    <i class="icon-down-open"></i>
                </div>
                <div class="f-content">
                    <div class="slider-container" id="sliderContainer">
                        <div class="slider-track">
                            <div class="slider-fill" id="sliderFill"></div>
                            <div class="slider-thumb" id="sliderThumb"></div>
                        </div>
                    </div>
                    <div class="price-info">
                        <span>۰ تومان</span>
                        <span id="priceValue">۲۳,۰۰۰,۰۰۰ تومان</span>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</div>
</div>