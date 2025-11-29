<?php
// =========================================================================
// 1. PHP Logic: Fetch Data from API
// =========================================================================

$api = Laravel_API_Client::get_instance();
$base_api_url = defined('LARAVEL_API_URL') ? LARAVEL_API_URL : 'https://akamode.com';
$site_url_clean = untrailingslashit($base_api_url);

// Get the current slug (Assumes this file is used in a context where $post is available)
global $post;
$current_slug = get_query_var('product_slug') ?: $post->post_name;

// Fetch Product Data
$response = $api->get_product_single($current_slug);
$product_data = (!is_wp_error($response) && !empty($response['data'])) ? $response['data'] : null;

// Default values if API fails
if (!$product_data) {
    echo '<div class="container" style="padding:50px; text-align:center;">Product data not found via API.</div>';
    return; // Stop execution of this template part
}

// Extract Data
$p_id    = $product_data['product_id'] ?? '';
$p_name  = $product_data['name'] ?? 'Untitled';
$p_desc  = $product_data['description'] ?? '';
$p_care  = $product_data['care'] ?? '';
$gallery = $product_data['images'] ?? [];
$videos  = $product_data['videos'] ?? [];
$related = $product_data['related_products'] ?? [];
$variants = $product_data['variants'] ?? [];

// Process Variants for UI (Unique Colors & Sizes)
$unique_colors = [];
$unique_sizes  = [];
$min_price     = 0;
$current_price = 0;
$discount_price = 0;

if (!empty($variants)) {
    // Set initial price from first variant
    $current_price  = $variants[0]['price'] ?? 0;
    $discount_price = $variants[0]['discount_price'] ?? 0;

    foreach ($variants as $v) {
        if (!empty($v['color'])) {
            // Use color name as key to avoid duplicates
            $unique_colors[$v['color']] = $v['color'];
        }
        if (!empty($v['size'])) {
            $unique_sizes[$v['size']] = $v['size'];
        }
    }
}

// Fallback image
$fallback_img = get_template_directory_uri() . '/images/temp/akamode-19.jpg';
?>

<div class="container">
    <div class="singleProduct-container">
        <div class="product-Content-layout">
            <div class="product-gallery-layout">
                <div class="gallery-track" id="galleryTrack">
                    <?php if (!empty($gallery)): ?>
                        <?php foreach ($gallery as $img):
                            $img_url = $site_url_clean . ($img['url'] ?? '');
                        ?>
                            <div class="slide"><img class="slide-img" src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img['alt_text'] ?? $p_name); ?>"></div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="slide"><img class="slide-img" src="<?php echo esc_url($fallback_img); ?>" alt="Default"></div>
                    <?php endif; ?>
                </div>
                <div class="controls-container">
                    <div class="thumbnails-glass-box">
                        <?php if (!empty($gallery)): ?>
                            <?php foreach ($gallery as $index => $img):
                                $img_url = $site_url_clean . ($img['url'] ?? '');
                                $active = ($index === 0) ? 'active' : '';
                            ?>
                                <img src="<?php echo esc_url($img_url); ?>" class="thumb <?php echo $active; ?>" data-index="<?php echo $index; ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button class="nav-arrow prev" id="prevBtn">
                        <i class="icon-right-arrow"></i>
                    </button>
                    <button class="nav-arrow next" id="nextBtn">
                        <i class="icon-left-arrow"></i>
                    </button>
                </div>
            </div>
            <div class="product-related-content-layout">
                <?php if (!empty($related)): ?>
                    <section class="carousel-products">
                        <div class="carousel-products-container">
                            <div class="carousel-products-main">
                                <div class="carousel-products-bar">
                                    <div class="carousel-products-meta">
                                        <span>محصولات مناسب برای این آیتم</span>
                                        <a href="#" class="carousel-products-all">
                                            <span>مشاهده بیشتر</span>
                                            <i class="icon-left-arrow"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="carousel-products-rail">
                                    <?php foreach ($related as $rel):
                                        $r_name = $rel['name'] ?? 'Product';
                                        $r_slug = $rel['slug'] ?? '#';
                                        // Related image logic (Assuming API sends it, or fallback)
                                        $r_img = get_template_directory_uri() . '/images/temp/akamode-default-image.png';
                                    ?>
                                        <a href="<?php echo home_url('/product/' . $r_slug); ?>" class="carousel-products-card">
                                            <figure class="carousel-products-media" role="img" aria-label="<?php echo esc_attr($r_name); ?>">
                                                <img class="img ph" src="<?php echo esc_url($r_img); ?>" alt="<?php echo esc_attr($r_name); ?>">
                                            </figure>
                                            <figcaption class="carousel-products-caption">
                                                <div class="caption" style="text-align: right;"><?php echo esc_html($r_name); ?></div>
                                                <div class="price" style="text-align: left;">مشاهده</div>
                                            </figcaption>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <div class="break">
                    <div class="break-container">
                        <hr />
                    </div>
                </div>

                <section class="comments-section">
                    <div class="container">
                        <?php
                        // Standard WordPress comments
                        $item_id = $post->ID;
                        if (file_exists(get_template_directory() . '/comment.php')) {
                            include get_template_directory() . '/comment.php';
                        } else {
                            comments_template();
                        }
                        ?>
                    </div>
                </section>
            </div>
        </div>

        <div class="selector-panel">
            <div class="selector-panel-container">
                <h1><?php echo esc_html($p_name); ?></h1>
                <?php if (!empty($unique_colors)): ?>
                    <div class="color-section">
                        <div class="label" id="colorLabel">رنگ : انتخاب کنید</div>
                        <div class="color-options">
                            <?php
                            $i = 0;
                            foreach ($unique_colors as $color_name):
                                $active = ($i === 0) ? 'active' : '';
                            ?>
                                <div class="swatch <?php echo esc_attr($color_name); ?> <?php echo $active; ?>"
                                    data-name="<?php echo esc_attr($color_name); ?>"
                                    onclick="selectColor(this)">
                                </div>
                            <?php $i++;
                            endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($unique_sizes)): ?>
                    <div class="accordion open" onclick="toggleAccordion(this)">
                        <div class="accordion-header">
                            <div  class="accordion-title" id="sizeHeader">سایز : انتخاب کنید</div>
                            <i class="icon-down-open"></i>
                        </div>
                        <div class="accordion-content">
                            <div class="accordion-inner" onclick="event.stopPropagation()">
                                <button id="btn-open-size-guide" class="size-guide-trigger-btn">
                                    <i class="icon-ruler-aka"></i>
                                    <span>راهنمای سایز</span>
                                </button>
                                <div class="size-grid">
                                    <?php
                                    $j = 0;
                                    foreach ($unique_sizes as $size_name):
                                        $selected = ($j === 0) ? 'selected' : '';
                                    ?>
                                        <div class="size-box <?php echo $selected; ?>" onclick="selectSize(this)">
                                            <?php echo esc_html($size_name); ?>
                                        </div>
                                    <?php $j++;
                                    endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="action-area">
                    <div class="price-wrapper">
                        <?php if ($discount_price > 0 && $discount_price < $current_price): ?>
                            <div class="price" style="font-size: 16px; color: #888; text-decoration: line-through;">
                                <?php echo number_format($current_price); ?> تومان
                            </div>
                            <div class="price">
                                <?php echo number_format($discount_price); ?> تومان
                            </div>
                        <?php else: ?>
                            <div class="price">
                                <?php echo number_format($current_price); ?> تومان
                            </div>
                        <?php endif; ?>
                    </div>
                    <button class="btn-add" id="addToCartBtn" onclick="addToCart()">افزودن به سبد خرید</button>
                </div>

                <div class="accordion" onclick="toggleAccordion(this)">
                    <div class="accordion-header">
                        <div class="accordion-title">توضیحات</div>
                        <i class="icon-down-open"></i>
                    </div>
                    <div class="accordion-content">
                        <div class="accordion-inner">
                            <p><?php echo nl2br(esc_html($p_desc)); ?></p>
                        </div>
                    </div>
                </div>

                <?php if (!empty($p_care)): ?>
                    <div class="accordion" onclick="toggleAccordion(this)">
                        <div class="accordion-header">
                            <div class="accordion-title">مراقبت و نگهداری</div>
                            <i class="icon-down-open"></i>
                        </div>
                        <div class="accordion-content">
                            <div class="accordion-inner">
                                <p><?php echo nl2br(esc_html($p_care)); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="sku">
                    <span>شناسه کالا: </span>
                    <span><?php echo esc_html($p_id);?></span>
                </div>

                <?php if (!empty($videos)): ?>
                    <div class="video-list">
                        <?php foreach ($videos as $video):
                            $v_url = $site_url_clean . ($video['content'] ?? '');
                            // Using first image as thumbnail fallback
                            $v_thumb = !empty($gallery) ? $site_url_clean . $gallery[0]['url'] : $fallback_img;
                        ?>
                            <div class="video-card"
                                onclick="openVideoModal(this)"
                                data-title="<?php echo esc_attr($video['name']); ?>"
                                data-desc=""
                                data-video="<?php echo esc_url($v_url); ?>">
                                <div class="video-thumbnail">
                                    <img src="<?php echo esc_url($v_thumb); ?>" alt="Video Thumb">
                                    <i class="icon-play-in-circle"></i>
                                </div>
                                <div class="video-text">
                                    <?php echo esc_html($video['name']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div class="fs-modal-overlay" id="fsModal">
        <button class="fs-close-btn" onclick="closeFullscreen()">
            <i class="icon-Multiplication"></i>
        </button>
        <div class="fs-content-wrapper">
            <button class="fs-nav prev" id="fsPrevBtn">
                <i class="icon-right-arrow"></i>
            </button>
            <div class="fs-image-box">
                <img src="" alt="Full View" id="fsMainImage">
            </div>
            <button class="fs-nav next" id="fsNextBtn">
                <i class="icon-left-arrow"></i>
            </button>
        </div>
        <div class="fs-thumbnails-container">
            <div class="fs-thumbnails-track" id="fsThumbsTrack"></div>
        </div>
    </div>

    <div class="video-popup-overlay" id="videoPopup">
        <div class="video-popup-container">
            <button class="vp-close-btn" onclick="closeVideoModal()">
                <i class="icon-Multiplication"></i>
            </button>
            <div class="vp-content">
                <h2 id="vpTitle">عنوان ویدیو</h2>
                <div class="vp-description" id="vpDesc">...</div>
                <div class="vp-video-wrapper">
                    <video id="vpPlayer" controls playsinline>
                        <source src="" type="video/mp4">
                        مرورگر شما از ویدیو پشتیبانی نمی‌کند.
                    </video>
                </div>
            </div>
        </div>
    </div>

    <!-- size-guide-overlay -->
    <div class="size-guide-overlay" id="modal-size-guide">
        <div class="size-guide-container">

            <div class="size-guide-header">
                <h3>راهنمای سایز</h3>

                <button class="size-guide-close-btn" id="btn-close-size-guide">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div class="size-guide-body">

                <div class="size-guide-table-wrapper">
                    <table class="size-guide-table">
                        <thead>
                            <tr>
                                <th>سایز</th>
                                <th>L</th>
                                <th>M</th>
                                <th>S</th>
                                <th>XS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>دور کمر</td>
                                <td>۱۱۰ cm</td>
                                <td>۱۱۰ cm</td>
                                <td>۷۰ cm</td>
                                <td>۱۱۰ cm</td>
                            </tr>
                            <tr>
                                <td>دور باسن</td>
                                <td>۱۱۰ cm</td>
                                <td>۱۱۰ cm</td>
                                <td>۷۰ cm</td>
                                <td>۱۱۰ cm</td>
                            </tr>
                            <tr>
                                <td>قد لباس</td>
                                <td>۱۱۰ cm</td>
                                <td>۱۱۰ cm</td>
                                <td>۷۵ cm</td>
                                <td>۱۱۰ cm</td>
                            </tr>
                            <tr>
                                <td>قد آستین</td>
                                <td>۱۱۰ cm</td>
                                <td>۱۱۰ cm</td>
                                <td>۷۰ cm</td>
                                <td>۱۱۰ cm</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="size-guide-description">
                    <h4>توضیحات</h4>
                    <p>
                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است.
                    </p>
                </div>

                <div class="size-guide-steps">
                    <div class="sg-step-item">
                        <span class="sg-step-number">۱</span>
                        <p>لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است.</p>
                    </div>
                    <div class="sg-step-item">
                        <span class="sg-step-number">۲</span>
                        <p>لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است.</p>
                    </div>
                </div>
            </div>

            <div class="size-guide-footer">
                <a href="#" class="sg-question-link">
                    سوالی دارید؟
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M7 17l9.2-9.2M17 17V7H7" />
                    </svg>
                </a>
            </div>

        </div>
    </div>
</div>

<script>
    var productVariants = <?php echo json_encode($variants); ?>;
</script>