<?php
// دریافت عبارت جستجو و صفحه فعلی
$search_query = get_search_query();
$current_page = max(1, get_query_var('paged'));

// متغیرهای پیش‌فرض
$products = [];
$total_results = 0;
$total_pages = 1;
$api_base_url = 'https://akamode.com'; // دامنه اصلی برای تصاویر

try {
    // اتصال به API
    $api = Laravel_API_Client::get_instance();
    // ارسال درخواست جستجو (فرض بر این است که متد search پارامتر صفحه را هم قبول می‌کند یا به کوئری اضافه می‌شود)
    // اگر متد search شما فقط آرگومان اول را می‌گیرد، باید آن را در کلاس کلاینت آپدیت کنید یا پارامترها را دستی بفرستید.
    // در اینجا فرض می‌کنیم متد search شما از پارامترهای GET هم پشتیبانی می‌کند یا شما آن را هندل کرده‌اید.
    // برای سادگی فعلی، ما فقط عبارت را می‌فرستیم.
    $api_response = $api->search($search_query); 

    if (!is_wp_error($api_response) && isset($api_response['data'])) {
        $products = $api_response['data'];
        
        // دریافت اطلاعات متا برای صفحه‌بندی
        if (isset($api_response['meta'])) {
            $total_results = $api_response['meta']['total'];
            $total_pages = $api_response['meta']['last_page'];
            $current_page = $api_response['meta']['current_page'];
        } else {
            $total_results = count($products);
        }
    }
} catch (Exception $e) {
    error_log('API Search Error: ' . $e->getMessage());
}

// تابع کمکی برای تبدیل اعداد به فارسی
function wbs_convert_digits($number) {
    $en = ['0','1','2','3','4','5','6','7','8','9'];
    $fa = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    return str_replace($en, $fa, number_format($number));
}
?>

<div class="main-container">
    <div class="search-container">
        <div class="page-title-container">
            <section class="page-title">
                <div class="breadcrumbs">خانه > نتایج جستجو</div>
                <h1 class="search-title">نتایج جستجو برای: "<?php echo esc_html($search_query); ?>"</h1>
                <p class="page-description">
                    <?php 
                    if ($total_results > 0) {
                        echo wbs_convert_digits($total_results) . " محصول یافت شد.";
                    } else {
                        echo "متاسفانه محصولی با این مشخصات یافت نشد.";
                    }
                    ?>
                </p>
            </section>
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
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                            // آماده‌سازی داده‌ها
                            $p_name = $product['name'];
                            $p_slug = $product['slug'];
                            
                            // هندل کردن تصویر
                            $p_image = get_template_directory_uri() . '/images/placeholder.png'; // تصویر پیش‌فرض
                            if (!empty($product['images']) && isset($product['images'][0]['url'])) {
                                $p_image = $api_base_url . $product['images'][0]['url'];
                            }

                            // هندل کردن قیمت
                            $p_price = 'ناموجود';
                            if (!empty($product['variants']) && isset($product['variants'][0]['price'])) {
                                $price_val = $product['variants'][0]['price'];
                                $p_price = wbs_convert_digits($price_val) . ' تومان';
                            }
                            
                            // لینک محصول (باید ساختار پرمالینک وردپرس شما با این هماهنگ باشد)
                            $p_link = home_url('/product/' . $p_slug);
                        ?>
                        
                        <a href="<?php echo esc_url($p_link); ?>" class="product-card">
                            <div class="product-image-wrapper">
                                <img src="<?php echo esc_url($p_image); ?>" alt="<?php echo esc_attr($p_name); ?>">
                            </div>
                            <h3 class="product-title"><?php echo esc_html($p_name); ?></h3>
                            <div class="product-price"><?php echo esc_html($p_price); ?></div>
                        </a>
                        
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                        <p>هیچ کالایی مطابق با جستجوی شما پیدا نشد.</p>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php 
            // دکمه قبلی
            if ($current_page > 1): ?>
                <a href="<?php echo add_query_arg('paged', $current_page - 1); ?>" class="page-btn next-btn"><i class="icon-right-open"></i></a>
            <?php endif; ?>

            <?php
            // نمایش شماره صفحات
            // برای سادگی فعلی تمام صفحات را لیست نمیکنیم اگر زیاد باشند، اما اینجا لاجیک ساده 1 تا Last Page است
            for ($i = 1; $i <= $total_pages; $i++): 
                $active_class = ($i == $current_page) ? 'active' : '';
                
                // نمایش هوشمند صفحات (اگر تعداد زیاد باشد)
                if ($i == 1 || $i == $total_pages || ($i >= $current_page - 1 && $i <= $current_page + 1)):
            ?>
                <a href="<?php echo add_query_arg('paged', $i); ?>" class="page-btn <?php echo $active_class; ?>">
                    <?php echo wbs_convert_digits($i); ?>
                </a>
            <?php elseif ($i == $current_page - 2 || $i == $current_page + 2): ?>
                <div class="page-dots">...</div>
            <?php endif; endfor; ?>

            <?php 
            // دکمه بعدی
            if ($current_page < $total_pages): ?>
                <a href="<?php echo add_query_arg('paged', $current_page + 1); ?>" class="page-btn next-btn"><i class="icon-left-open"></i></a>
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