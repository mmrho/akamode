<?php
// اطمینان از وجود item_id (مثلاً از ID پست وردپرس)
if (!isset($item_id)) {
    $item_id = get_the_ID() ? get_the_ID() : 1; // 1 به عنوان پیش‌فرض
}
?>

<style>
    /* CSS بازنویسی شده برای تطابق کامل با تصاویر (Pixel-Perfect)
      - فیلدهای فرم فقط خط زیرین دارند
      - ستاره‌های فرم توخالی هستند
      - لیست کامنت‌ها هیچ خط جداکننده‌ای ندارد
      - لینک "پاسخ" با position: absolute در سمت چپ قرار گرفته
    */
    .comment-section {
        direction: rtl;
        background: #f9f5f0;
        padding: 25px;
        border-radius: 8px;
        max-width: 800px;
        margin: 0 auto;
    }

    /* --- بخش فرم (عکس ۱) --- */
    .comment-header {
        text-align: right;
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 5px;
        color: #333;
    }

    .comment-form-subtitle {
        text-align: right;
        font-size: 14px;
        color: #555;
        margin-bottom: 25px;
    }

    .comment-form {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        align-content: center;
        flex-wrap: wrap;
        width: 100%;
        margin-bottom: 30px;
    }

    .comment-form input[type="text"],
    .comment-form input[type="email"],
    .comment-form textarea {
        width: 100%;
        margin-bottom: 20px;
        padding: 8px 0;
        border: none;
        border-bottom: 1px solid #333;
        background: transparent;
        font-size: 14px;
        box-sizing: border-box;
    }

    .comment-form input:focus,
    .comment-form textarea:focus {
        outline: none;
        border-bottom-color: #000;
    }

    .comment-form textarea {
        min-height: 40px;
        resize: vertical;
    }

    .comment-form-checkbox {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        width: 100%;
        margin: 15px 0;
        direction: rtl;
    }

    .comment-form-checkbox label {
        font-size: 13px;
        color: #555;
        margin-right: 8px;
        cursor: pointer;
        width: 100%;
    }

    .comment-form-checkbox input[type="checkbox"] {
        width: auto;
        margin: 0;
    }

    .rating-label {
        width: 100%;
        font-size: 14px;
        color: #333;
        margin: 15px 0 10px;
        text-align: right;
    }

    .stars {
        display: flex;
        direction: rtl;
        justify-content: flex-start;
        width: 100%;
        margin: 10px 0 20px;
    }

    .stars input {
        display: none;
    }

    .stars label {
        font-size: 22px;
        color: #ccc;
        /* رنگ ستاره‌های خالی فرم */
        cursor: pointer;
        margin-left: 5px;
    }

    .stars label:before {
        content: '\2606';
    }

    /* ستاره توخالی (Outline) */
    .stars label:hover,
    .stars label:hover~label {
        color: #000;
        content: '\2605';
        /* ستاره توپر */
    }

    .stars label:hover:before,
    .stars label:hover~label:before {
        content: '\2605';
    }

    .stars input:checked~label {
        color: #000;
    }

    .stars input:checked~label:before {
        content: '\2605';
    }
    .submit-button{
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        align-content: center;
        flex-wrap: wrap;
        width: 100%;
    }

    .comment-form button {
       
        background: #000;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        display: block;
        margin: 20px 0 0 auto;
        transition: background-color 0.2s;
    }

    .comment-form button:hover {
        background: #333;
    }

    .comment-form button:disabled {
        background: #888;
        cursor: not-allowed;
    }

    /* --- بخش لیست (عکس ۲) --- */
    .comment-list-header {
        text-align: right;
        font-size: 20px;
        font-weight: bold;
        margin: 30px 0 20px;
        color: #333;
    }

    .comment-item {
        position: relative;
        padding: 15px 0;
        padding-left: 60px;
        border-bottom: none;
        margin-bottom: 15px;
    }

    .comment-header-info {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
        width: 100%;
    }

    .comment-author-info {
        display: flex;
        align-items: center;
    }

    .comment-name {
        font-weight: bold;
        color: #333;
        font-size: 14px;
    }

    .comment-date {
        color: #888;
        font-size: 12px;
        margin-right: 10px;
    }

    .comment-rating {
        direction: ltr;
        unicode-bidi: bidi-override;
    }

    .comment-rating span {
        font-size: 16px;
        color: #ccc;
    }

    .comment-rating span.filled {
        color: #000;
    }

    .comment-text {
        margin: 5px 0;
        color: #555;
        font-size: 14px;
        line-height: 1.6;
    }

    .comment-reply-link {
        position: absolute;
        left: 0;
        top: 15px;
        font-size: 13px;
        color: #555;
        text-decoration: none;
        direction: ltr;
    }

    .comment-reply-link:hover {
        text-decoration: underline;
    }


    .form-status,
    .comment-list-status {
        text-align: right;
        margin: 10px 0;
        font-size: 13px;
    }

    .form-status.success {
        color: green;
    }

    .form-status.error {
        color: red;
    }

    .comment-list-status {
        color: #888;
    }
</style>

<div class="comment-section">
    <h2 class="comment-header">ثبت دیدگاه</h2>
    <p class="comment-form-subtitle">دیدگاه خود را در مورد این محصول ثبت کنید</p>

    <form id="comment-form" class="comment-form">
        <input type="text" id="name" name="name" placeholder="نام" required>
        <input type="email" id="email" name="email" placeholder="ایمیل" required>

        <div class="comment-form-checkbox">
            <input type="checkbox" id="lorem-ipsum-check">
            <label for="lorem-ipsum-check">لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است</label>
        </div>

        <p class="rating-label">امتیاز شما برای این محصول*</p>
        <div class="stars">
            <input type="radio" id="star5" name="rating" value="5" required><label for="star5"></label>
            <input type="radio" id="star4" name="rating" value="4"><label for="star4"></label>
            <input type="radio" id="star3" name="rating" value="3"><label for="star3"></label>
            <input type="radio" id="star2" name="rating" value="2"><label for="star2"></label>
            <input type="radio" id="star1" name="rating" value="1"><label for="star1"></label>
        </div>

        <textarea id="comment" name="comment" placeholder="متن نظر" required></textarea>
        <div class="submit-button">
            <button class="sbutton" type="submit" id="submit-button">ثبت دیدگاه</button>
        </div>
        <div id="form-status" class="form-status"></div>
    </form>

    <h2 id="comment-list-header" class="comment-list-header">دیدگاه‌ها</h2>
    <div id="comment-list" class="comment-list">
        <div id="comment-list-status" class="comment-list-status">در حال بارگذاری دیدگاه‌ها...</div>
    </div>
</div>

<script>
    /**
     * کامپوننت حرفه‌ای دیدگاه
     * از IIFE برای کپسوله‌سازی و جلوگیری از آلودگی Global Scope استفاده شده است.
     */
    (function() {
        // --- 1. تعریف متغیرها و گرفتن عناصر DOM ---
        const itemId = <?php echo json_encode($item_id); ?>;

        // عناصر فرم
        const commentForm = document.getElementById('comment-form');
        const submitButton = document.getElementById('submit-button');
        const formStatus = document.getElementById('form-status');

        // عناصر لیست
        const commentList = document.getElementById('comment-list');
        const commentListHeader = document.getElementById('comment-list-header');
        const commentListStatus = document.getElementById('comment-list-status');

        // داده‌های آزمایشی (Mock Data) - دقیقاً مطابق عکس‌ها
        const mockApiData = [{
                id: 1,
                name: 'علی فهمیده',
                date: '۱۳ مهر ۱۴۰۴',
                rating: 1,
                comment: 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است که لازم در ستون و سطرآنچنان که لازم است',
            },
            {
                id: 2,
                name: 'علی فهمیده',
                date: '۱۳ مهر ۱۴۰۴',
                rating: 1,
                comment: 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است که لازم در ستون و سطرآنچنان که لازم است',
            },
            {
                id: 3,
                name: 'علی فهمیده',
                date: '۱۳ مهر ۱۴۰۴',
                rating: 1,
                comment: 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است که لازم در ستون و سطرآنچنان که لازم است',
            }
        ];

        // --- 2. توابع کمکی ---

        /**
         * شبیه‌سازی تاخیر شبکه
         */
        function sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        /**
         * رندر کردن ستاره‌های لیست (پر و خالی)
         */
        function renderListStars(rating) {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                // ستاره پر ★ (U+2605) / ستاره خالی ☆ (U+2606)
                // ستاره‌های عکس کمی متفاوت هستند، از ستاره‌های پیش‌فرض استفاده می‌کنیم
                stars += (i <= rating) ? '<span class="filled">★</span>' : '<span>☆</span>';
            }
            return stars;
        }

        /**
         * رندر کردن یک آیتم کامنت
         */
        function renderComment(comment) {
            const item = document.createElement('div');
            item.className = 'comment-item';

            item.innerHTML = `
                <a href="#" class="comment-reply-link" onclick="replyToComment(${comment.id}); return false;">
                    <i class="icon-up-left-arrow"></i> پاسخ 
                </a>
                <div class="comment-header-info">
                    <div class="comment-author-info">
                        <span class="comment-name">${comment.name}</span>
                        <span class="comment-date">${comment.date}</span>
                    </div>
                    <div class="comment-rating">${renderListStars(comment.rating)}</div>
                </div>
                <p class="comment-text">${comment.comment}</p>
            `;
            return item;
        }

        // --- 3. توابع اصلی ---

        /**
         * بارگذاری و نمایش دیدگاه‌ها
         */
        async function loadComments() {
            commentList.innerHTML = ''; // پاک کردن لیست قبلی
            commentListStatus.textContent = 'در حال بارگذاری دیدگاه‌ها...';
            commentListStatus.style.display = 'block';

            try {
                // شبیه‌سازی فراخوانی API
                await sleep(500);
                // TODO: در اینجا `mockApiData` را با `fetch('api/comments?item_id=' + itemId)` جایگزین کنید
                const comments = mockApiData;

                if (comments && comments.length > 0) {
                    commentListStatus.style.display = 'none';
                    commentListHeader.textContent = `${comments.length} دیدگاه ثبت شده`;
                    comments.forEach(comment => {
                        commentList.appendChild(renderComment(comment));
                    });
                } else {
                    commentListHeader.textContent = 'دیدگاه‌ها';
                    commentListStatus.textContent = 'هنوز دیدگاهی ثبت نشده است.';
                }

            } catch (error) {
                console.error('خطا در بارگذاری دیدگاه‌ها:', error);
                commentListStatus.textContent = 'خطا در بارگذاری دیدگاه‌ها. لطفاً دوباره تلاش کنید.';
                commentListStatus.style.color = 'red';
            }
        }

        /**
         * هندل کردن ثبت فرم
         */
        async function handleFormSubmit(e) {
            e.preventDefault();

            // قفل کردن فرم
            submitButton.disabled = true;
            submitButton.textContent = 'در حال ارسال...';
            formStatus.textContent = '';

            // استخراج داده‌ها
            const formData = new FormData(commentForm);
            const data = {
                itemId: itemId,
                name: formData.get('name'),
                email: formData.get('email'),
                comment: formData.get('comment'),
                rating: parseInt(formData.get('rating') || '0'),
                date: new Date().toLocaleDateString('fa-IR'), // تاریخ همان لحظه
            };

            try {
                // شبیه‌سازی ارسال به API
                await sleep(750);
                // TODO: در اینجا `fetch('api/comments', { method: 'POST', body: JSON.stringify(data) })` را جایگزین کنید

                // چون از API واقعی استفاده نمی‌کنیم، فقط به لیست Mock اضافه می‌کنیم
                mockApiData.unshift(data); // اضافه کردن به ابتدای لیست
                loadComments(); // بارگذاری مجدد کل لیست

                formStatus.textContent = 'دیدگاه شما با موفقیت ثبت شد.';
                formStatus.className = 'form-status success';
                commentForm.reset(); // پاک کردن فرم

            } catch (error) {
                console.error('خطا در ثبت دیدگاه:', error);
                formStatus.textContent = 'خطا در ثبت دیدگاه. لطفاً دوباره تلاش کنید.';
                formStatus.className = 'form-status error';
            } finally {
                // باز کردن قفل فرم
                submitButton.disabled = false;
                submitButton.textContent = 'ثبت دیدگاه';
            }
        }

        /**
         * تابع نمونه برای "پاسخ"
         * (در نسخه حرفه‌ای این تابع فرم پاسخ را باز می‌کند)
         */
        window.replyToComment = function(commentId) {
            console.log('Replying to comment ID:', commentId);
            // به عنوان مثال، کاربر را به فرم اصلی هدایت می‌کنیم
            document.getElementById('comment').focus();
        }

        /**
         * اتصال Event Listeners
         */
        function bindEvents() {
            commentForm.addEventListener('submit', handleFormSubmit);
        }

        /**
         * تابع راه‌انداز
         */
        function init() {
            bindEvents();
            loadComments();
        }

        // --- 4. اجرای برنامه ---
        // منتظر می‌مانیم تا DOM به صورت کامل بارگذاری شود
        document.addEventListener('DOMContentLoaded', init);

    })(); // پایان IIFE
</script>