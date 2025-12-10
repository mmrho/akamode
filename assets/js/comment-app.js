/**
 * Professional Comment Component - Connected to API
 */
(function () {
    // --- 1. Variables & DOM ---
    
    // دریافت داده‌های لوکالیز شده از وردپرس
    if (typeof commentData === 'undefined') {
        console.error('CommentData is missing.');
        return;
    }

    const { itemId, ajaxUrl, nonce, isLoggedIn } = commentData;

    const commentForm = document.getElementById("comment-form");
    const submitButton = document.getElementById("submit-button");
    const formStatus = document.getElementById("form-status");
    const commentList = document.getElementById("comment-list");
    const commentListHeader = document.getElementById("comment-list-header");
    const commentListStatus = document.getElementById("comment-list-status");

    // --- 2. Helper Functions ---

    function renderListStars(rating) {
        let stars = "";
        for (let i = 1; i <= 5; i++) {
            stars += i <= rating ? '<span class="filled">★</span>' : "<span>☆</span>";
        }
        return stars;
    }

    function renderComment(comment) {
        const item = document.createElement("div");
        item.className = "comment-item";
        
        // هندل کردن فیلدهای مختلف که ممکن است از API بیاید
        const name = comment.user ? comment.user.name : (comment.name || 'کاربر ناشناس');
        // تبدیل تاریخ میلادی به شمسی (در صورت نیاز) یا استفاده از رشته تاریخ API
        const date = comment.created_at || comment.date || ''; 
        const body = comment.comment || comment.body || '';
        const rating = comment.rating || 0;

        item.innerHTML = `
            <div class="comment-header-info">
                <div class="comment-author-info">
                    <span class="comment-name">${name}</span>
                    <span class="comment-date" style="font-size:11px; color:#999; margin-right:10px;">${date}</span>
                </div>
                <div class="comment-rating">${renderListStars(rating)}</div>
            </div>
            <p class="comment-text">${body}</p>
        `;
        return item;
    }

    // --- 3. Main Logic ---

    /**
     * دریافت لیست نظرات از سرور
     */
    async function loadComments() {
        commentList.innerHTML = "";
        commentListStatus.textContent = "در حال دریافت دیدگاه‌ها...";
        commentListStatus.style.display = "block";

        try {
            const formData = new FormData();
            formData.append('action', 'wbs_get_comments');
            formData.append('item_id', itemId);

            const response = await fetch(ajaxUrl + '?action=wbs_get_comments&item_id=' + itemId);
            const result = await response.json();

            if (result.success) {
                const comments = result.data;
                
                if (Array.isArray(comments) && comments.length > 0) {
                    commentListStatus.style.display = "none";
                    commentListHeader.textContent = `${comments.length} دیدگاه ثبت شده`;
                    comments.forEach((comment) => {
                        commentList.appendChild(renderComment(comment));
                    });
                } else {
                    commentListHeader.textContent = "دیدگاه‌ها";
                    commentListStatus.textContent = "اولین نفری باشید که نظر می‌دهید!";
                }
            } else {
                throw new Error(result.data.message || 'خطا در دریافت');
            }

        } catch (error) {
            console.error("Error loading comments:", error);
            commentListStatus.textContent = "خطا در بارگذاری دیدگاه‌ها.";
            commentListStatus.style.color = "red";
        }
    }

    /**
     * ارسال نظر جدید
     */
    async function handleFormSubmit(e) {
        e.preventDefault();

        // بررسی لاگین بودن در سمت کلاینت (اختیاری ولی بهتر است)
        if (isLoggedIn !== '1') {
             formStatus.textContent = "برای ثبت نظر لطفاً ابتدا وارد حساب کاربری شوید.";
             formStatus.className = "form-status error";
             return;
        }

        submitButton.disabled = true;
        submitButton.textContent = "در حال ارسال...";
        formStatus.textContent = "";

        const formData = new FormData(commentForm);
        formData.append('action', 'wbs_submit_comment');
        formData.append('item_id', itemId);
        formData.append('security', nonce); // Nonce امنیتی

        try {
            const response = await fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                formStatus.textContent = "دیدگاه شما با موفقیت ثبت شد.";
                formStatus.className = "form-status success";
                commentForm.reset();
                loadComments(); // رفرش کردن لیست
            } else {
                throw new Error(result.data.message || 'خطا در ثبت');
            }

        } catch (error) {
            console.error("Error submitting comment:", error);
            formStatus.textContent = error.message || "خطا در برقراری ارتباط با سرور.";
            formStatus.className = "form-status error";
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = "ثبت دیدگاه";
        }
    }

    // --- 4. Init ---
    function init() {
        if (commentForm) commentForm.addEventListener("submit", handleFormSubmit);
        if (commentList) loadComments();
    }

    document.addEventListener("DOMContentLoaded", init);
})();