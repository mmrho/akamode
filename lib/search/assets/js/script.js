jQuery(document).ready(function($) {

    // بررسی اولیه برای وجود متغیرهای وردپرس
    if (typeof wbs_data === 'undefined') {
        console.error('Error: wbs_data is missing. Check wp-enqueue.php');
        return;
    }

    // تابع کمکی تبدیل اعداد به فارسی
    function convertDigits(num) {
        return new Intl.NumberFormat('fa-IR', { useGrouping: false }).format(num);
    }
    
    // تابع کمکی فرمت قیمت
    function formatPrice(price) {
        return new Intl.NumberFormat('fa-IR').format(price) + ' تومان';
    }


    // ============================================================
    //  بخش اول: جستجوی زنده در هدر (Live Search Header)
    //  * این بخش فقط مسئول لیست کشویی نتایج زیر اینپوت است *
    // ============================================================
    
    const headerInputs = $('.search-input-wrapper input');
    let headerTypingTimer;
    
    headerInputs.on('input', function() {
        const input = $(this);
        const query = input.val().trim();
        
        // یافتن کانتینر نتایج مخصوص همین اینپوت (موبایل یا دسکتاپ)
        const container = input.closest('.search-container');
        const resultsBox = container.find('.search-results');
        const suggestionsBox = container.find('.search-suggestions');
        const listTarget = resultsBox.find('ul');

        clearTimeout(headerTypingTimer);

        // اگر کمتر از 2 کاراکتر بود، باکس نتایج را ببند و پیشنهادات را نشان بده
        if (query.length < 2) {
            resultsBox.addClass('hidden');
            suggestionsBox.removeClass('hidden'); 
            return;
        }

        // نمایش وضعیت "در حال جستجو"
        suggestionsBox.addClass('hidden');
        resultsBox.removeClass('hidden');
        listTarget.html('<li style="text-align:center; padding:10px; color:#666;">در حال جستجو...</li>');

        headerTypingTimer = setTimeout(function() {
            // ارسال درخواست به سرور
            $.ajax({
                url: wbs_data.ajax_url,
                type: 'POST',
                data: {
                    action: 'wbs_api_search', // اکشن ساده برای جستجوی زنده
                    term: query,
                    security: wbs_data.nonce
                },
                success: function(response) {
                    if (response.success) {
                        renderHeaderResults(response.data, listTarget, query);
                    } else {
                        listTarget.html('<li style="padding:10px;">خطایی رخ داده است.</li>');
                    }
                },
                error: function() {
                    listTarget.html('<li style="padding:10px;">خطای ارتباط.</li>');
                }
            });
        }, 600); // 600ms تاخیر
    });

    // تابع رندر HTML برای هدر (دقیقا همان کدی که قبلا درست کار میکرد)
    function renderHeaderResults(apiData, listTarget, originalQuery) {
        listTarget.empty();
        const products = apiData.data || [];

        if (products.length === 0) {
            listTarget.html('<li style="padding:10px;">محصولی یافت نشد.</li>');
            return;
        }

        products.forEach(item => {
            let imageUrl = '/wp-content/themes/your-theme/images/placeholder.png'; // عکس پیشفرض
            if (item.images && item.images.length > 0) {
                imageUrl = wbs_data.base_api_url + item.images[0].url;
            }

            let priceHtml = '';
            if (item.variants && item.variants.length > 0) {
                priceHtml = `<span style="font-size:12px; color:#666; margin-right:auto;">${formatPrice(item.variants[0].price)}</span>`;
            }

            const html = `
                <li>
                    <a href="/product/${item.slug}"> 
                        <div class="search-results-blur">
                            <div class="search-results-badge">
                                <img class="search-results-badge-inner" src="${imageUrl}" alt="${item.name}">
                            </div>
                            <div class="search-results-caption">
                                <span>${item.name}</span>
                                ${priceHtml}
                                <i class="icon-up-left-arrow"></i>
                            </div>
                        </div>
                    </a>
                </li>
            `;
            listTarget.append(html);
        });

        // لینک "مشاهده همه نتایج"
        const viewAllUrl = '/?s=' + encodeURIComponent(originalQuery);
        listTarget.append(`
            <li class="view-all-results" style="border-top: 1px solid #eee; margin-top: 8px; padding-top: 8px;">
                <a href="${viewAllUrl}" style="justify-content: center; font-weight: bold; color: #000;">
                    <span>مشاهده تمام نتایج برای "${originalQuery}"</span>
                    <i class="icon-left-arrow" style="display:block;"></i>
                </a>
            </li>
        `);
    }


    // ============================================================
    //  بخش دوم: فیلترهای پیشرفته در صفحه نتایج (Filter Page)
    //  * این بخش فقط در صفحه searchPage.php فعال میشود *
    // ============================================================

    // وضعیت فعلی فیلترها
    let searchState = {
        term: getUrlParameter('s') || '',
        page: 1,
        sort: 'newest',
        min_price: 0,
        max_price: 50000000,
        colors: [],
        sizes: [],
        categories: []
    };

    function getUrlParameter(name) {
        var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // فقط اگر در صفحه نتایج جستجو هستیم این توابع اجرا شوند
    if ($('.product-grid').length > 0) {

        // تابع اصلی دریافت محصولات با فیلتر
        function fetchFilteredProducts() {
            $('.product-grid').css('opacity', '0.5'); // افکت لودینگ
            
            $.ajax({
                url: wbs_data.ajax_url,
                type: 'POST',
                data: {
                    action: 'wbs_filter_search', // اکشن متفاوت برای فیلتر پیشرفته
                    ...searchState,
                    security: wbs_data.nonce
                },
                success: function(response) {
                    $('.product-grid').css('opacity', '1');
                    if (response.success) {
                        renderProductGrid(response.data.data);
                        renderPagination(response.data.meta);
                        updateResultCount(response.data.meta);
                    } else {
                        $('.product-grid').html('<div class="col-12 text-center">خطا در دریافت اطلاعات</div>');
                    }
                },
                error: function() {
                    $('.product-grid').css('opacity', '1').html('<div class="col-12 text-center">خطای ارتباط با سرور</div>');
                }
            });
        }

        // رندر گرید محصولات
        function renderProductGrid(products) {
            const grid = $('.product-grid');
            grid.empty();

            if (!products || products.length === 0) {
                grid.html('<div class="col-12 text-center" style="grid-column: 1/-1; padding: 40px;">محصولی با این مشخصات یافت نشد.</div>');
                return;
            }

            products.forEach(item => {
                let imageUrl = '/wp-content/themes/your-theme/images/placeholder.png';
                if (item.images && item.images.length > 0) {
                    imageUrl = wbs_data.base_api_url + item.images[0].url;
                }

                let priceHtml = 'ناموجود';
                if (item.variants && item.variants.length > 0) {
                    priceHtml = formatPrice(item.variants[0].price);
                }

                grid.append(`
                    <a href="/product/${item.slug}" class="product-card">
                        <div class="product-image-wrapper">
                            <img src="${imageUrl}" alt="${item.name}">
                        </div>
                        <h3 class="product-title">${item.name}</h3>
                        <div class="product-price">${priceHtml}</div>
                    </a>
                `);
            });
        }

        // رندر دکمه‌های صفحه بندی
        function renderPagination(meta) {
            const container = $('.pagination');
            container.empty();
            if (!meta || meta.last_page <= 1) return;

            const current = meta.current_page;
            const last = meta.last_page;

            if (current > 1) container.append(`<div class="page-btn" data-page="${current - 1}"><i class="icon-right-open"></i></div>`);

            for (let i = 1; i <= last; i++) {
                if (i === 1 || i === last || (i >= current - 1 && i <= current + 1)) {
                    let active = (i === current) ? 'active' : '';
                    container.append(`<div class="page-btn ${active}" data-page="${i}">${convertDigits(i)}</div>`);
                } else if (i === current - 2 || i === current + 2) {
                    container.append(`<div class="page-dots">...</div>`);
                }
            }

            if (current < last) container.append(`<div class="page-btn" data-page="${current + 1}"><i class="icon-left-open"></i></div>`);
        }
        
        // بروزرسانی تعداد نتایج در بالای صفحه
        function updateResultCount(meta) {
            const count = meta ? meta.total : 0;
            $('.page-description').text(`${convertDigits(count)} محصول یافت شد.`);
        }


        // --- رویدادهای فیلتر (Event Listeners) ---

        // 1. تغییر مرتب سازی
        $('.sort-option').on('click', function() {
            $('.sort-option').removeClass('active');
            $(this).addClass('active');
            $('#sortDropdown').removeClass('show');
            
            const txt = $(this).text().trim();
            $('#sortLabel').text('مرتب‌سازی: ' + txt);
            
            if (txt === 'جدیدترین') searchState.sort = 'newest';
            else if (txt === 'گران‌ترین') searchState.sort = 'price_desc';
            else if (txt === 'ارزان‌ترین') searchState.sort = 'price_asc';
            else if (txt === 'پر‌فروش‌ترین') searchState.sort = 'bestselling';
            
            searchState.page = 1;
            fetchFilteredProducts();
        });

        // 2. تغییر چک باکس دسته بندی
        $('.chk-row input').on('change', function() {
            let cats = [];
            $('.chk-row input:checked').each(function() {
                cats.push($(this).siblings('span').text().trim());
            });
            searchState.categories = cats;
            searchState.page = 1;
            fetchFilteredProducts();
        });

        // 3. تغییر رنگ و سایز
        $('.color-opt, .size-opt').on('click', function() {
            $(this).siblings().removeClass('selected');
            $(this).addClass('selected');
            
            const val = $(this).text().trim(); // یا استفاده از data-attribute برای دقت بیشتر
            
            if ($(this).hasClass('color-opt')) searchState.colors = [val]; 
            else searchState.sizes = [val];
            
            searchState.page = 1;
            fetchFilteredProducts();
        });

        // 4. دکمه صفحه بندی
        $(document).on('click', '.page-btn', function() {
            const p = $(this).data('page');
            if (p) {
                searchState.page = p;
                fetchFilteredProducts();
                $('html, body').animate({ scrollTop: $('.product-grid').offset().top - 100 }, 500);
            }
        });

        // 5. اسلایدر قیمت (لاجیک کامل)
        const sliderThumb = document.getElementById('sliderThumb');
        const sliderContainer = document.getElementById('sliderContainer');
        let isDragging = false;

        function updateSliderUI(clientX) {
            if (!sliderContainer) return;
            const rect = sliderContainer.getBoundingClientRect();
            let percent = ((rect.right - clientX) / rect.width) * 100; // محاسبه راست به چپ
            if (percent < 0) percent = 0; if (percent > 100) percent = 100;
            
            $('#sliderThumb').css('right', percent + '%');
            $('#sliderFill').css('width', percent + '%');
            
            const price = Math.round((50000000 * percent) / 100);
            $('#priceValue').text(formatPrice(price));
            searchState.max_price = price;
        }

        if (sliderThumb) {
            $(sliderThumb).on('mousedown touchstart', () => isDragging = true);
            $(window).on('mouseup touchend', () => {
                if (isDragging) {
                    isDragging = false;
                    searchState.page = 1;
                    fetchFilteredProducts(); // فقط موقع رها کردن درخواست بفرست
                }
            });
            $(window).on('mousemove touchmove', (e) => {
                if (isDragging) updateSliderUI(e.clientX || e.touches[0].clientX);
            });
            $(sliderContainer).on('click', (e) => {
                updateSliderUI(e.clientX);
                searchState.page = 1;
                fetchFilteredProducts();
            });
        }
    } // پایان شرط if (.product-grid)


    // ============================================================
    //  بخش سوم: تعاملات عمومی UI (منوها، تاگل‌ها و ...)
    // ============================================================

    // هدایت با دکمه اینتر
    headerInputs.on('keypress', function(e) {
        if (e.which == 13 && $(this).val().length > 0) {
            window.location.href = '/?s=' + encodeURIComponent($(this).val());
        }
    });
    
    // هدایت با آیکون سرچ
    $('.search-input-wrapper i').css('cursor', 'pointer').on('click', function() {
        const val = $(this).siblings('input').val();
        if(val && val.length > 0) window.location.href = '/?s=' + encodeURIComponent(val);
    });

    // تاگل سایدبار فیلتر (موبایل)
    $('#filterTrigger, #closeFilter, #overlay').on('click', function() {
        $('#sidebar, #overlay').toggleClass('active');
        $('body').toggleClass('no-scroll');
    });

    // تاگل منوی مرتب سازی
    $('#sortTrigger').on('click', (e) => { e.stopPropagation(); $('#sortDropdown').toggleClass('show'); });
    $(window).on('click', () => $('#sortDropdown').removeClass('show'));

    // آکاردئون
    $('.f-head').on('click', function() {
        const head = $(this);
        const content = head.next('.f-content');
        head.toggleClass('collapsed');
        if (head.hasClass('collapsed')) content.css('max-height', '0px');
        else content.css('max-height', content.prop('scrollHeight') + "px");
    });
    
    // باز کردن اولیه آکاردئون‌ها
    $('.f-content').each(function() {
        $(this).css('max-height', $(this).prop('scrollHeight') + "px");
    });

});