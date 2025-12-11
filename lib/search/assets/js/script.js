jQuery(document).ready(function ($) {
  // Base URL for API images (Hardcoded for reliability)
  const API_BASE_URL = "https://api.akamode.com";

  if (typeof wbs_data === "undefined") {
    console.error("Error: wbs_data is missing.");
    return;
  }

  function convertDigits(num) {
    return new Intl.NumberFormat("fa-IR", { useGrouping: false }).format(num);
  }

  function formatPrice(price) {
    return new Intl.NumberFormat("fa-IR").format(price) + " تومان";
  }

  // Helper to fix image URL
  function getFullImageUrl(url) {
    if (!url) return "/wp-content/themes/your-theme/images/placeholder.png";
    if (url.startsWith("http")) return url;
    return API_BASE_URL + url;
  }

  // ============================================================
  //  1. Live Search Header (OPTIMIZED)
  // ============================================================

  const headerInputs = $(".search-input-wrapper input");
  let headerTypingTimer;
  let currentRequest = null; // متغیری برای ذخیره درخواست فعال

  headerInputs.on("input", function () {
    const input = $(this);
    const query = input.val().trim();
    const container = input.closest(".search-container");
    const resultsBox = container.find(".search-results");
    const suggestionsBox = container.find(".search-suggestions");
    const listTarget = resultsBox.find("ul");

    // 1. اگر تایمر قبلی هست پاکش کن
    clearTimeout(headerTypingTimer);

    // 2. اگر درخواست فعالی در حال انجام است، آن را کنسل کن (خیلی مهم برای API کند)
    if (currentRequest != null) {
      currentRequest.abort();
      currentRequest = null;
    }

    if (query.length < 2) {
      resultsBox.addClass("hidden");
      suggestionsBox.removeClass("hidden");
      return;
    }

    suggestionsBox.addClass("hidden");
    resultsBox.removeClass("hidden");

    // اینجا "در حال جستجو" را نشان می‌دهیم و منتظر می‌مانیم
    listTarget.html(
      '<li style="text-align:center; padding:10px; color:#666;">در حال جستجو... <span class="spinner-border" style="width: 10px; height:10px;"></span></li>'
    );

    // زمان انتظار را کمی بیشتر کردیم (800ms) تا کاربر کامل تایپ کند بعد درخواست برود
    headerTypingTimer = setTimeout(function () {
      currentRequest = $.ajax({
        url: wbs_data.ajax_url,
        type: "POST",
        timeout: 30000, // 30 ثانیه صبر کن قبل از اینکه ارور بدهد
        data: {
          action: "wbs_api_search",
          term: query,
          security: wbs_data.nonce,
        },
        success: function (response) {
          // درخواست موفق بود، پس متغیر درخواست را خالی می‌کنیم
          currentRequest = null;

          if (response.success) {
            renderHeaderResults(response.data, listTarget, query);
          } else {
            // اگر سرور پاسخ داد اما موفق نبود (مثلا ارور داخلی)
            listTarget.html(
              '<li style="padding:10px;">خطایی رخ داده است.</li>'
            );
          }
        },
        error: function (jqXHR, textStatus) {
          // اگر درخواست توسط خودمان کنسل شده (چون کاربر دوباره تایپ کرده) ارور نشان نده
          if (textStatus === "abort") {
            return;
          }
          currentRequest = null;
          if (textStatus === "timeout") {
            listTarget.html(
              '<li style="padding:10px;">پاسخ سرور طول کشید. دوباره تلاش کنید.</li>'
            );
          } else {
            listTarget.html('<li style="padding:10px;">خطای ارتباط.</li>');
          }
        },
      });
    }, 800); // افزایش زمان تاخیر تایپ
  });

  function renderHeaderResults(apiData, listTarget, originalQuery) {
    listTarget.empty();
    const products = apiData.data || [];

    if (products.length === 0) {
      // حالا وقتی به اینجا می‌رسیم که مطمئنیم سرور جواب داده و واقعا محصولی نیست
      listTarget.html('<li style="padding:10px;">محصولی یافت نشد.</li>');
      return;
    }

    products.forEach((item) => {
      // FIX: Use helper function for image
      let imageUrl =
        item.images && item.images.length > 0
          ? getFullImageUrl(item.images[0].url)
          : getFullImageUrl(null);

      let priceHtml = "";
      if (item.variants && item.variants.length > 0) {
        priceHtml = `<span style="font-size:12px; color:#666; margin-right:auto;">${formatPrice(
          item.variants[0].price
        )}</span>`;
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

    const viewAllUrl = "/?s=" + encodeURIComponent(originalQuery);
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
  //  2. Advanced Filter Page
  // ============================================================

  let searchState = {
    term: getUrlParameter("s") || "",
    page: 1,
    sort: "newest",
    min_price: 0,
    max_price: 50000000,
    colors: [],
    sizes: [],
    categories: [],
  };

  function getUrlParameter(name) {
    var results = new RegExp("[\\?&]" + name + "=([^&#]*)").exec(
      location.search
    );
    return results === null
      ? ""
      : decodeURIComponent(results[1].replace(/\+/g, " "));
  }

  if ($(".product-grid").length > 0) {
    function fetchFilteredProducts() {
      $(".product-grid").css("opacity", "0.5");

      $.ajax({
        url: wbs_data.ajax_url,
        type: "POST",
        data: {
          action: "wbs_filter_search",
          ...searchState,
          security: wbs_data.nonce,
        },
        success: function (response) {
          $(".product-grid").css("opacity", "1");
          if (response.success) {
            renderProductGrid(response.data.data);
            renderPagination(response.data.meta);
            updateResultCount(response.data.meta);
          } else {
            $(".product-grid").html(
              '<div class="col-12 text-center">خطا در دریافت اطلاعات</div>'
            );
          }
        },
        error: function () {
          $(".product-grid")
            .css("opacity", "1")
            .html('<div class="col-12 text-center">خطای ارتباط با سرور</div>');
        },
      });
    }

    function renderProductGrid(products) {
      const grid = $(".product-grid");
      grid.empty();

      if (!products || products.length === 0) {
        grid.html(
          '<div class="col-12 text-center" style="grid-column: 1/-1; padding: 40px;">محصولی با این مشخصات یافت نشد.</div>'
        );
        return;
      }

      products.forEach((item) => {
        // FIX: Use helper function for image
        let imageUrl =
          item.images && item.images.length > 0
            ? getFullImageUrl(item.images[0].url)
            : getFullImageUrl(null);

        let priceHtml = "ناموجود";
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

    function renderPagination(meta) {
      const container = $(".pagination");
      container.empty();
      if (!meta || meta.last_page <= 1) return;

      const current = meta.current_page;
      const last = meta.last_page;

      if (current > 1)
        container.append(
          `<div class="page-btn" data-page="${
            current - 1
          }"><i class="icon-right-open"></i></div>`
        );

      for (let i = 1; i <= last; i++) {
        if (i === 1 || i === last || (i >= current - 1 && i <= current + 1)) {
          let active = i === current ? "active" : "";
          container.append(
            `<div class="page-btn ${active}" data-page="${i}">${convertDigits(
              i
            )}</div>`
          );
        } else if (i === current - 2 || i === current + 2) {
          container.append(`<div class="page-dots">...</div>`);
        }
      }

      if (current < last)
        container.append(
          `<div class="page-btn" data-page="${
            current + 1
          }"><i class="icon-left-open"></i></div>`
        );
    }

    function updateResultCount(meta) {
      const count = meta ? meta.total : 0;
      $(".page-description").text(`${convertDigits(count)} محصول یافت شد.`);
    }

    $(".sort-option").on("click", function () {
      $(".sort-option").removeClass("active");
      $(this).addClass("active");
      $("#sortDropdown").removeClass("show");

      const txt = $(this).text().trim();
      $("#sortLabel").text("مرتب‌سازی: " + txt);

      if (txt === "جدیدترین") searchState.sort = "newest";
      else if (txt === "گران‌ترین") searchState.sort = "price_desc";
      else if (txt === "ارزان‌ترین") searchState.sort = "price_asc";
      else if (txt === "پر‌فروش‌ترین") searchState.sort = "bestselling";

      searchState.page = 1;
      fetchFilteredProducts();
    });

    $(".chk-row input").on("change", function () {
      let cats = [];
      $(".chk-row input:checked").each(function () {
        cats.push($(this).siblings("span").text().trim());
      });
      searchState.categories = cats;
      searchState.page = 1;
      fetchFilteredProducts();
    });

    $(".color-opt, .size-opt").on("click", function () {
      $(this).siblings().removeClass("selected");
      $(this).addClass("selected");

      const val = $(this).text().trim();

      if ($(this).hasClass("color-opt")) searchState.colors = [val];
      else searchState.sizes = [val];

      searchState.page = 1;
      fetchFilteredProducts();
    });

    $(document).on("click", ".page-btn", function () {
      const p = $(this).data("page");
      if (p) {
        searchState.page = p;
        fetchFilteredProducts();
        $("html, body").animate(
          { scrollTop: $(".product-grid").offset().top - 100 },
          500
        );
      }
    });

    const sliderThumb = document.getElementById("sliderThumb");
    const sliderContainer = document.getElementById("sliderContainer");
    let isDragging = false;

    function updateSliderUI(clientX) {
      if (!sliderContainer) return;
      const rect = sliderContainer.getBoundingClientRect();
      let percent = ((rect.right - clientX) / rect.width) * 100;
      if (percent < 0) percent = 0;
      if (percent > 100) percent = 100;

      $("#sliderThumb").css("right", percent + "%");
      $("#sliderFill").css("width", percent + "%");

      const price = Math.round((50000000 * percent) / 100);
      $("#priceValue").text(formatPrice(price));
      searchState.max_price = price;
    }

    if (sliderThumb) {
      $(sliderThumb).on("mousedown touchstart", () => (isDragging = true));
      $(window).on("mouseup touchend", () => {
        if (isDragging) {
          isDragging = false;
          searchState.page = 1;
          fetchFilteredProducts();
        }
      });
      $(window).on("mousemove touchmove", (e) => {
        if (isDragging) updateSliderUI(e.clientX || e.touches[0].clientX);
      });
      $(sliderContainer).on("click", (e) => {
        updateSliderUI(e.clientX);
        searchState.page = 1;
        fetchFilteredProducts();
      });
    }
  }

  // ============================================================
  //  3. General UI
  // ============================================================
  headerInputs.on("keypress", function (e) {
    if (e.which == 13 && $(this).val().length > 0) {
      window.location.href = "/?s=" + encodeURIComponent($(this).val());
    }
  });

  $(".search-input-wrapper i")
    .css("cursor", "pointer")
    .on("click", function () {
      const val = $(this).siblings("input").val();
      if (val && val.length > 0)
        window.location.href = "/?s=" + encodeURIComponent(val);
    });

  $("#filterTrigger, #closeFilter, #overlay").on("click", function () {
    $("#sidebar, #overlay").toggleClass("active");
    $("body").toggleClass("no-scroll");
  });

  $("#sortTrigger").on("click", (e) => {
    e.stopPropagation();
    $("#sortDropdown").toggleClass("show");
  });
  $(window).on("click", () => $("#sortDropdown").removeClass("show"));

  $(".f-head").on("click", function () {
    const head = $(this);
    const content = head.next(".f-content");
    head.toggleClass("collapsed");
    if (head.hasClass("collapsed")) content.css("max-height", "0px");
    else content.css("max-height", content.prop("scrollHeight") + "px");
  });

  $(".f-content").each(function () {
    $(this).css("max-height", $(this).prop("scrollHeight") + "px");
  });
});
