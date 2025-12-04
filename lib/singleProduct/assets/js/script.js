// =========================================================
// 1. Global Helpers
// =========================================================
function selectColor(element) {
    document.querySelectorAll('.swatch').forEach(swatch => swatch.classList.remove('active'));
    element.classList.add('active');
    const label = document.getElementById('colorLabel');
    if(label) {
        label.innerText = 'رنگ : ' + element.getAttribute('data-name');
    }
}

function selectSize(element) {
    document.querySelectorAll('.size-box').forEach(box => box.classList.remove('selected'));
    element.classList.add('selected');
    const header = document.getElementById('sizeHeader');
    if(header) header.innerText = 'سایز : ' + element.innerText;
}

function toggleAccordion(element) {
    element.classList.toggle('open');
}

function addToCart() {
    const btn = document.getElementById('addToCartBtn');
    if(!btn) return;
    const originalText = btn.innerText;
    btn.innerText = "✓ افزوده شد";
    btn.style.backgroundColor = "#27ae60";
    setTimeout(() => {
        btn.innerText = originalText;
        btn.style.backgroundColor = "";
    }, 2000);
}

// video modal functions
function openVideoModal(element) {
    const overlay = document.getElementById('videoPopup');
    const titleEl = document.getElementById('vpTitle');
    const descEl = document.getElementById('vpDesc');
    const player = document.getElementById('vpPlayer');
    const source = player ? player.querySelector('source') : null;

    // دریافت اطلاعات
    const title = element.getAttribute('data-title');
    const desc = element.getAttribute('data-desc');
    const videoUrl = element.getAttribute('data-video');

    // ست کردن اطلاعات
    if(titleEl) titleEl.innerText = title;
    if(descEl) descEl.innerText = desc;
    
    if(player && source && videoUrl) {
        source.src = videoUrl;
        player.load(); // بارگذاری مجدد ویدیو
    }

    if(overlay) {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // قفل کردن اسکرول
    }
}

function closeVideoModal() {
    const overlay = document.getElementById('videoPopup');
    const player = document.getElementById('vpPlayer');

    if(overlay) {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if(player) {
        player.pause();
        player.currentTime = 0;
    }
}

// Image modal close function (full screen)
window.closeFullscreen = function() {
    const modal = document.getElementById('fsModal');
    if(modal) modal.classList.remove('active');
    document.body.style.overflow = '';
};


// =========================================================
// 2. Main logic after page load
// =========================================================
window.addEventListener('load', function() {

    // --- a) Defining the main gallery variables ---
    const track = document.getElementById('galleryTrack');
    const thumbBox = document.querySelector('.thumbnails-glass-box');
    const thumbs = document.querySelectorAll('.thumb');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const allSlides = document.querySelectorAll('.slide');

   // --- b) Defining image modal variables ---
    const fsModal = document.getElementById('fsModal');
    const fsMainImg = document.getElementById('fsMainImage');
    const fsThumbsTrack = document.getElementById('fsThumbsTrack');
    const fsPrevBtn = document.getElementById('fsPrevBtn');
    const fsNextBtn = document.getElementById('fsNextBtn');

   // --- c) Handling clicks out of the video modal ---
    const videoOverlay = document.getElementById('videoPopup');
    if(videoOverlay) {
        videoOverlay.addEventListener('click', function(e) {
            if (e.target === videoOverlay) {
                closeVideoModal();
            }
        });
    }

    if (track && thumbs.length > 0 && allSlides.length > 0) {
        
        let isClicking = false; 
        let clickTimeout;
        let scrollTimeout;
        let currentIndex = 0;

       // Gallery internal functions
        function setActiveThumb(index) {
            currentIndex = index; 
            thumbs.forEach((t, i) => {
                if (i === index) {
                    t.classList.add('active');
                    if (thumbBox) {
                        const scrollLeft = t.offsetLeft - (thumbBox.clientWidth / 2) + (t.clientWidth / 2);
                        thumbBox.scrollTo({ left: scrollLeft, behavior: 'smooth' });
                    }
                } else {
                    t.classList.remove('active');
                }
            });
        }

        function goToSlide(index) {
            if (index < 0) index = allSlides.length - 1;
            if (index >= allSlides.length) index = 0;

            isClicking = true;
            clearTimeout(clickTimeout);
            setActiveThumb(index);

            track.style.scrollSnapType = 'none';
            const targetLeft = allSlides[index].offsetLeft;
            track.scrollTo({ left: targetLeft, behavior: 'smooth' });

            clickTimeout = setTimeout(() => {
                isClicking = false;
                track.style.scrollSnapType = 'x mandatory';
            }, 800);
        }

        function onScroll() {
            if (isClicking) return;
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                const trackCenter = track.getBoundingClientRect().left + track.clientWidth / 2;
                let closestIndex = 0;
                let minDistance = Number.MAX_VALUE;
                allSlides.forEach((slide, index) => {
                    const slideCenter = slide.getBoundingClientRect().left + slide.clientWidth / 2;
                    const distance = Math.abs(trackCenter - slideCenter);
                    if (distance < minDistance) {
                        minDistance = distance;
                        closestIndex = index;
                    }
                });
                if (currentIndex !== closestIndex) {
                    setActiveThumb(closestIndex);
                }
            }, 60);
        }

       // --- Image Modal Logic (Fullscreen) ---
        if(fsThumbsTrack) {
            thumbs.forEach((t, i) => {
                const clone = t.cloneNode(true);
                clone.className = 'thumb-clone';
                clone.removeAttribute('id');
                clone.onclick = () => updateFullscreenView(i);
                fsThumbsTrack.appendChild(clone);
            });
        }
        const fsThumbClones = document.querySelectorAll('.thumb-clone');

        function updateFullscreenView(index) {
            if (index < 0) index = allSlides.length - 1;
            if (index >= allSlides.length) index = 0;

            const src = allSlides[index].querySelector('img').src;
            if(fsMainImg) fsMainImg.src = src;

            fsThumbClones.forEach((t, i) => {
                if(i === index) t.classList.add('active');
                else t.classList.remove('active');
            });

            goToSlide(index);
        }

        function openFullscreen(index) {
            if(fsModal) {
                fsModal.classList.add('active');
                document.body.style.overflow = 'hidden';
                updateFullscreenView(index);
            }
        }

       // --- Event Listeners ---
        thumbs.forEach((thumb, index) => {
            thumb.addEventListener('click', (e) => {
                e.preventDefault();
                goToSlide(index);
            });
        });
        if(nextBtn) nextBtn.addEventListener('click', () => goToSlide(currentIndex + 1));
        if(prevBtn) prevBtn.addEventListener('click', () => goToSlide(currentIndex - 1));
        setTimeout(() => { track.addEventListener('scroll', onScroll); }, 200);

       // Open the image modal on double click
        allSlides.forEach((slide, index) => {
            slide.addEventListener('dblclick', () => {
                openFullscreen(index);
            });
        });

        if(fsNextBtn) fsNextBtn.addEventListener('click', () => updateFullscreenView(currentIndex + 1));
        if(fsPrevBtn) fsPrevBtn.addEventListener('click', () => updateFullscreenView(currentIndex - 1));


       // Fix initial load bug
        track.style.scrollBehavior = 'auto';
        track.scrollLeft = 0; 
        setActiveThumb(0);
        setTimeout(() => {
            track.style.scrollBehavior = 'smooth';
            track.style.scrollSnapType = 'x mandatory';
        }, 150);
    }

    // =======================================================================
    // 3. Responsive management (moving the panel below the gallery on mobile)
    // =======================================================================
    const panel = document.querySelector('.selector-panel');
    const gallery = document.querySelector('.product-gallery-layout');
    const mainContainer = document.querySelector('.singleProduct-container');
    const contentLayout = document.querySelector('.product-Content-layout');

    function reorderLayout() {
        if (!panel || !gallery || !mainContainer) return;
        if (window.innerWidth <= 849.98) {
            if (panel.parentNode !== contentLayout && gallery.parentNode) {
                gallery.parentNode.insertBefore(panel, gallery.nextSibling);
            }
        } else {
            if (panel.parentNode !== mainContainer) {
                mainContainer.appendChild(panel);
            }
        }
    }
    reorderLayout();
    window.addEventListener('resize', reorderLayout);
});



// =========================================================
// 4. Size Guide Modal Logic
// =========================================================
document.addEventListener('DOMContentLoaded', () => {
    // Select elements with specific IDs
    const sgModal = document.getElementById('modal-size-guide');
    const sgOpenBtn = document.getElementById('btn-open-size-guide');
    const sgCloseBtn = document.getElementById('btn-close-size-guide');

    // Open modal function
    const openSizeGuide = () => {
        if (!sgModal) return;
        sgModal.classList.add('is-visible'); 
        document.body.style.overflow = 'hidden'; // Lock body scroll
    };

    // Close modal function
    const closeSizeGuide = () => {
        if (!sgModal) return;
        sgModal.classList.remove('is-visible');
        document.body.style.overflow = ''; // Unlock body scroll
    };

    // Click event to open modal
    if (sgOpenBtn) {
        sgOpenBtn.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent default anchor behavior
            openSizeGuide();
        });
    }

    // Click event to close modal
    if (sgCloseBtn) {
        sgCloseBtn.addEventListener('click', closeSizeGuide);
    }

    // Close modal when clicking outside the content (on overlay)
    if (sgModal) {
        sgModal.addEventListener('click', (e) => {
            if (e.target === sgModal) {
                closeSizeGuide();
            }
        });
    }
});







// جایگزین کردن تابع addToCart قبلی در بخش <script> انتهای فایل singleProductPage.php

function addToCart() {
    // 1. بررسی اینکه کاربر رنگ و سایز را انتخاب کرده است یا خیر
    // (فرض بر این است که متغیر productVariants از PHP آمده است)
    
    let selectedVariant = null;
    let finalPrice = productBaseData.price; // قیمت پیش‌فرض
    let variantLabel = "";

    // اگر محصول واریانت دارد (مثل رنگ و سایز)
    if (typeof productVariants !== 'undefined' && productVariants.length > 0) {
        const activeColor = document.querySelector('.swatch.active');
        const activeSize = document.querySelector('.size-box.selected');
        
        // اگر HTML انتخاب رنگ هست ولی انتخاب نشده
        if (document.querySelector('.swatch') && !activeColor) {
            alert("لطفا رنگ را انتخاب کنید");
            return;
        }
        // اگر HTML انتخاب سایز هست ولی انتخاب نشده
        if (document.querySelector('.size-box') && !activeSize) {
            alert("لطفا سایز را انتخاب کنید");
            return;
        }

        const colorName = activeColor ? activeColor.getAttribute('data-name') : null;
        const sizeName = activeSize ? activeSize.innerText.trim() : null;

        // پیدا کردن واریانت منطبق در آرایه
        selectedVariant = productVariants.find(v => {
            const cMatch = !colorName || v.color === colorName;
            const sMatch = !sizeName || v.size === sizeName;
            return cMatch && sMatch;
        });

        if (!selectedVariant) {
            alert("این محصول با مشخصات انتخاب شده موجود نیست.");
            return;
        }

        finalPrice = selectedVariant.price; // قیمت واریانت
        variantLabel = [colorName, sizeName].filter(Boolean).join(' - ');
    }

    // 2. ساخت آبجکت محصول برای ذخیره
    const product = {
        id: productBaseData.id,            // ID محصول والد
        variant_id: selectedVariant ? selectedVariant.id : null, // ID واریانت (مهم برای API)
        name: productBaseData.name,
        price: finalPrice,
        image: productBaseData.image,      // عکس محصول
        variant_title: variantLabel,       // متن نمایشی مثل "قرمز - XL"
        quantity: 1
    };

    // 3. فراخوانی تابع سراسری سبد خرید
    if (window.cartManager) {
        window.cartManager.addItem(product);
        
        // تغییر ظاهر دکمه برای فیدبک
        const btn = document.getElementById('addToCartBtn');
        const oldText = btn.innerText;
        btn.innerText = "✓ افزوده شد";
        btn.style.background = "#4CAF50";
        setTimeout(() => {
            btn.innerText = oldText;
            btn.style.background = "";
        }, 2000);
    } else {
        console.error("Cart Manager Loaded Nashode Ast!");
    }
}

