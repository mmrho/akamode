<div class="container">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <!-- Breadcrumb -->
            <nav class="breadcrumb">
                <?php if (function_exists('yoast_breadcrumb')) {
                    yoast_breadcrumb('<div class="breadcrumb-wrapper">', '</div>');
                } ?>
            </nav>
            <div class="container-img">
                <section class="slider-section">
                    <div class="slider-container">
                        <div class="slide active">
                            <img class="slide-bg" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-1.jpg" alt="NBA 2K21">
                            <div class="slider-blur">
                                <span class="slider-caption">کیف سر دوشی چرم زنانه - چرم گاوی</span>
                                <div class="slider-badge">
                                    <img class="slider-badge-inner" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-1.jpg">
                                </div>
                            </div>
                        </div>
                        <div class="slide">
                            <img class="slide-bg" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-2.jpg" alt="NBA 2K21 Gameplay">
                            <div class="slide-content">
                                <h2>فروشگاه لباس آکامد</h2>
                                <p>لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت</p>
                                <a href="#" class="download-btn">فروشگاه</a>
                            </div>
                            <div class="slider-blur">
                                <span class="slider-caption">کیف چرم زنانه - چرم گاوی</span>
                                <div class="slider-badge">
                                    <img class="slider-badge-inner" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-2.jpg">
                                </div>
                            </div>
                        </div>
                        <div class="slide">
                            <img class="slide-bg" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-3.jpg" alt="NBA 2K21 Features">
                            <div class="slide-content">
                                <h2>فروشگاه لباس آکامد</h2>
                                <p>لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت</p>
                                <a href="#" class="download-btn">فروشگاه</a>
                            </div>
                            <div class="slider-blur">
                                <span class="slider-caption">کیف شانه ای چرم زنانه - چرم گاوی</span>
                                <div class="slider-badge">
                                    <img class="slider-badge-inner" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-3.jpg">
                                </div>
                            </div>
                        </div>
                        <div class="slider-nav">
                            <button class="nav-btn next"><i class="icon-right-arrow"></i></button>
                            <button class="nav-btn prev"><i class="icon-left-arrow"></i></button>
                        </div>
                        <button class="play-btn"></button>
                    </div>
                </section>


            </div>
            <div class="container-theRest">
                <div class="Product-related">
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
                                    <!-- Repeatable product card -->
                                    <a href="#" class="carousel-products-card">
                                        <figure class="carousel-products-media" role="img" aria-label="کت سوییت">
                                            <img class="img ph" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-13.png" alt="دستکش چرمی قهوه‌ای">
                                        </figure>
                                        <figcaption class="carousel-products-caption">
                                            <div class="caption" style="text-align: right;">سویشرت مردانه هومنیتی مدل L</div>
                                            <div class="price" style="text-align: left;">۱۰۰۰۰ریال</div>
                                        </figcaption>
                                    </a>
                                    <a href="#" class="carousel-products-card">
                                        <figure class="carousel-products-media" role="img" aria-label="کت سوییت">
                                            <img class="img ph" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-12.png" alt="دستکش چرمی قهوه‌ای">
                                        </figure>
                                        <figcaption class="carousel-products-caption">
                                            <div class="caption" style="text-align: right;">سویشرت مردانه هومنیتی مدل L</div>
                                            <div class="price" style="text-align: left;">۱۰۰۰۰ریال</div>
                                        </figcaption>
                                    </a>
                                    <a href="#" class="carousel-products-card">
                                        <figure class="carousel-products-media" role="img" aria-label="کت سوییت">
                                            <img class="img ph" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-11.png" alt="دستکش چرمی قهوه‌ای">
                                        </figure>
                                        <figcaption class="carousel-products-caption">
                                            <div class="caption" style="text-align: right;">سویشرت مردانه هومنیتی مدل L</div>
                                            <div class="price" style="text-align: left;">۱۰۰۰۰ریال</div>
                                        </figcaption>
                                    </a>
                                    <a href="#" class="carousel-products-card">
                                        <figure class="carousel-products-media" role="img" aria-label="کت سوییت">
                                            <img class="img ph" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-10.png" alt="دستکش چرمی قهوه‌ای">
                                        </figure>
                                        <figcaption class="carousel-products-caption">
                                            <div class="caption" style="text-align: right;">سویشرت مردانه هومنیتی مدل L</div>
                                            <div class="price" style="text-align: left;">۱۰۰۰۰ریال</div>
                                        </figcaption>
                                    </a>
                                    <a href="#" class="carousel-products-card">
                                        <figure class="carousel-products-media" role="img" aria-label="کت سوییت">
                                            <img class="img ph" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-9.png" alt="دستکش چرمی قهوه‌ای">
                                        </figure>
                                        <figcaption class="carousel-products-caption">
                                            <div class="caption" style="text-align: right;">سویشرت مردانه هومنیتی مدل L</div>
                                            <div class="price" style="text-align: left;">۱۰۰۰۰ریال</div>
                                        </figcaption>
                                    </a>
                                    <a href="#" class="carousel-products-card">
                                        <figure class="carousel-products-media" role="img" aria-label="کت سوییت">
                                            <img class="img ph" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-8.png" alt="دستکش چرمی قهوه‌ای">
                                        </figure>
                                        <figcaption class="carousel-products-caption">
                                            <div class="caption" style="text-align: right;">سویشرت مردانه هومنیتی مدل L</div>
                                            <div class="price" style="text-align: left;">۱۰۰۰۰ریال</div>
                                        </figcaption>
                                    </a>
                                    <a href="#" class="carousel-products-card">
                                        <figure class="carousel-products-media" role="img" aria-label="کت سوییت">
                                            <img class="img ph" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-7.png" alt="دستکش چرمی قهوه‌ای">
                                        </figure>
                                        <figcaption class="carousel-products-caption">
                                            <div class="caption" style="text-align: right;">سویشرت مردانه هومنیتی مدل L</div>
                                            <div class="price" style="text-align: left;">۱۰۰۰۰ریال</div>
                                        </figcaption>
                                    </a>
                                    <a href="#" class="carousel-products-card">
                                        <figure class="carousel-products-media" role="img" aria-label="کت سوییت">
                                            <img class="img ph" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-6.png" alt="دستکش چرمی قهوه‌ای">
                                        </figure>
                                        <figcaption class="carousel-products-caption">
                                            <div class="caption" style="text-align: right;">سویشرت مردانه هومنیتی مدل L</div>
                                            <div class="price" style="text-align: left;">۱۰۰۰۰ریال</div>
                                        </figcaption>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>
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
                                    <!-- Repeatable product card -->
                                    <a href="#" class="carousel-products-card">
                                        <figure class="carousel-products-media" role="img" aria-label="کت سوییت">
                                            <img class="img ph" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-13.png" alt="دستکش چرمی قهوه‌ای">
                                        </figure>
                                        <figcaption class="carousel-products-caption">
                                            <div class="caption" style="text-align: right;">سویشرت مردانه هومنیتی مدل L</div>
                                            <div class="price" style="text-align: left;">۱۰۰۰۰۰۰۰ریال</div>
                                        </figcaption>
                                    </a>

                                    <a href="#" class="carousel-products-card">
                                        <figure class="carousel-products-media" role="img" aria-label="کت سوییت">
                                            <img class="img ph" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-12.png" alt="دستکش چرمی قهوه‌ای">
                                        </figure>
                                        <figcaption class="carousel-products-caption">
                                            <div class="caption" style="text-align: right;">سویشرت مردانه هومنیتی مدل L</div>
                                            <div class="price" style="text-align: left;">۱۰۰۰۰ریال</div>
                                        </figcaption>
                                    </a>
                                    <a href="#" class="carousel-products-card">
                                        <figure class="carousel-products-media" role="img" aria-label="کت سوییت">
                                            <img class="img ph" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-11.png" alt="دستکش چرمی قهوه‌ای">
                                        </figure>
                                        <figcaption class="carousel-products-caption">
                                            <div class="caption" style="text-align: right;">سویشرت مردانه هومنیتی مدل L</div>
                                            <div class="price" style="text-align: left;">۱۰۰۰۰ریال</div>
                                        </figcaption>
                                    </a>
                                    <a href="#" class="carousel-products-card">
                                        <figure class="carousel-products-media" role="img" aria-label="کت سوییت">
                                            <img class="img ph" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-10.png" alt="دستکش چرمی قهوه‌ای">
                                        </figure>
                                        <figcaption class="carousel-products-caption">
                                            <div class="caption" style="text-align: right;">سویشرت مردانه هومنیتی مدل L</div>
                                            <div class="price" style="text-align: left;">۱۰۰۰۰ریال</div>
                                        </figcaption>
                                    </a>
                                    <a href="#" class="carousel-products-card">
                                        <figure class="carousel-products-media" role="img" aria-label="کت سوییت">
                                            <img class="img ph" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-9.png" alt="دستکش چرمی قهوه‌ای">
                                        </figure>
                                        <figcaption class="carousel-products-caption">
                                            <div class="caption" style="text-align: right;">سویشرت مردانه هومنیتی مدل L</div>
                                            <div class="price" style="text-align: left;">۱۰۰۰۰ریال</div>
                                        </figcaption>
                                    </a>
                                    <a href="#" class="carousel-products-card">
                                        <figure class="carousel-products-media" role="img" aria-label="کت سوییت">
                                            <img class="img ph" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-8.png" alt="دستکش چرمی قهوه‌ای">
                                        </figure>
                                        <figcaption class="carousel-products-caption">
                                            <div class="caption" style="text-align: right;">سویشرت مردانه هومنیتی مدل L</div>
                                            <div class="price" style="text-align: left;">۱۰۰۰۰ریال</div>
                                        </figcaption>
                                    </a>
                                    <a href="#" class="carousel-products-card">
                                        <figure class="carousel-products-media" role="img" aria-label="کت سوییت">
                                            <img class="img ph" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-7.png" alt="دستکش چرمی قهوه‌ای">
                                        </figure>
                                        <figcaption class="carousel-products-caption">
                                            <div class="caption" style="text-align: right;">سویشرت مردانه هومنیتی مدل L</div>
                                            <div class="price" style="text-align: left;">۱۰۰۰۰ریال</div>
                                        </figcaption>
                                    </a>
                                    <a href="#" class="carousel-products-card">
                                        <figure class="carousel-products-media" role="img" aria-label="کت سوییت">
                                            <img class="img ph" src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-6.png" alt="دستکش چرمی قهوه‌ای">
                                        </figure>
                                        <figcaption class="carousel-products-caption">
                                            <div class="caption" style="text-align: right;">سویشرت مردانه هومنیتی مدل L</div>
                                            <div class="price" style="text-align: left;">۱۰۰۰۰ریال</div>
                                        </figcaption>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>
                    <div class="break">
                        <div class="break-container">
                            <hr />
                        </div>
                    </div>
                    <!-- Comments Section -->
                    <section class="comments-section">
                        <div class="container">
                            <h2>نظرات کاربران</h2>
                            <?php
                            global $post;
                            if ($post->post_type === 'page' && !comments_open($post->ID)) {
                                wp_update_post(array(
                                    'ID' => $post->ID,
                                    'comment_status' => 'open'
                                ));
                            }
                            if (comments_open($post->ID) || get_comments_number($post->ID)) {
                                comments_template();
                            }
                            ?>
                        </div>
                    </section>
                </div>
            </div>
    <?php endwhile;
    endif; ?>
</div>