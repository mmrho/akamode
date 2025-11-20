// product-gallery.js

// =========================================================
// 1. توابع کمکی عمومی (Global Helpers)
// =========================================================
function selectColor(element) {
    document.querySelectorAll('.swatch').forEach(swatch => swatch.classList.remove('active'));
    element.classList.add('active');
    const label = document.getElementById('colorLabel');
    if(label) {
        label.innerText = 'رنگ : ' + element.getAttribute('data-name');
        label.style.color = '#000';
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

function playVideo(videoName) {
    alert('درحال بارگذاری ' + videoName + ' ...');
}

// تابع بستن مودال (باید گلوبال باشد)
window.closeFullscreen = function() {
    const modal = document.getElementById('fsModal');
    if(modal) modal.classList.remove('active');
};

// =========================================================
// 2. منطق اصلی بعد از لود صفحه
// =========================================================
window.addEventListener('load', function() {

    // الف) تعریف متغیرهای گالری اصلی
    const track = document.getElementById('galleryTrack');
    const thumbBox = document.querySelector('.thumbnails-glass-box');
    const thumbs = document.querySelectorAll('.thumb');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const allSlides = document.querySelectorAll('.slide');

    // ب) تعریف متغیرهای مودال (Fullscreen)
    const fsModal = document.getElementById('fsModal');
    const fsMainImg = document.getElementById('fsMainImage');
    const fsThumbsTrack = document.getElementById('fsThumbsTrack');
    const fsPrevBtn = document.getElementById('fsPrevBtn');
    const fsNextBtn = document.getElementById('fsNextBtn');

    if (track && thumbs.length > 0 && allSlides.length > 0) {
        
        let isClicking = false; 
        let clickTimeout;
        let scrollTimeout;
        let currentIndex = 0;

        // --- توابع گالری اصلی ---

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
            setActiveThumb(index); // آپدیت ایندکس

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

        // --- توابع مودال (Fullscreen) ---

        // کپی کردن تامنیل‌ها به داخل مودال
        if(fsThumbsTrack) {
            thumbs.forEach((t, i) => {
                const clone = t.cloneNode(true);
                clone.className = 'thumb-clone'; // پاک کردن کلاس‌های قبلی
                clone.removeAttribute('id');
                clone.onclick = () => updateFullscreenView(i);
                fsThumbsTrack.appendChild(clone);
            });
        }
        const fsThumbClones = document.querySelectorAll('.thumb-clone');

        function updateFullscreenView(index) {
            // مدیریت چرخش
            if (index < 0) index = allSlides.length - 1;
            if (index >= allSlides.length) index = 0;

            // 1. نمایش عکس بزرگ
            const src = allSlides[index].querySelector('img').src;
            if(fsMainImg) fsMainImg.src = src;

            // 2. آپدیت تامنیل‌های مودال
            fsThumbClones.forEach((t, i) => {
                if(i === index) t.classList.add('active');
                else t.classList.remove('active');
            });

            // 3. همگام‌سازی با گالری اصلی (اختیاری)
            goToSlide(index);
        }

        function openFullscreen(index) {
            if(fsModal) {
                fsModal.classList.add('active');
                updateFullscreenView(index);
            }
        }

        // --- اتصال رویدادها (Event Listeners) ---

        // 1. گالری اصلی
        thumbs.forEach((thumb, index) => {
            thumb.addEventListener('click', (e) => {
                e.preventDefault();
                goToSlide(index);
            });
        });
        if(nextBtn) nextBtn.addEventListener('click', () => goToSlide(currentIndex + 1));
        if(prevBtn) prevBtn.addEventListener('click', () => goToSlide(currentIndex - 1));
        setTimeout(() => { track.addEventListener('scroll', onScroll); }, 200);

        // 2. فعال‌سازی دابل کلیک برای مودال
        allSlides.forEach((slide, index) => {
            slide.addEventListener('dblclick', () => {
                openFullscreen(index);
            });
        });

        // 3. دکمه‌های داخل مودال
        if(fsNextBtn) fsNextBtn.addEventListener('click', () => updateFullscreenView(currentIndex + 1));
        if(fsPrevBtn) fsPrevBtn.addEventListener('click', () => updateFullscreenView(currentIndex - 1));


        // --- رفع باگ لود اولیه ---
        track.style.scrollBehavior = 'auto';
        track.scrollLeft = 0; 
        setActiveThumb(0);
        setTimeout(() => {
            track.style.scrollBehavior = 'smooth';
            track.style.scrollSnapType = 'x mandatory';
        }, 150);
    }

    // =========================================================
    // 3. مدیریت ریسپانسیو (جابجایی پنل)
    // =========================================================
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