/**
 * Professional Comment Component (IIFE)
 *
 * Enqueued by functions.php.
 * Receives PHP data from the 'commentData' object (localized script).
 */
(function () {
    // --- 1. Definitions & DOM Cache ---

    // Read localized data from WordPress (via commentData object)
    const itemId = commentData.itemId;
    // const ajaxUrl = commentData.ajaxUrl; // For future AJAX use

    // Form elements
    const commentForm = document.getElementById("comment-form");
    const submitButton = document.getElementById("submit-button");
    const formStatus = document.getElementById("form-status");

    // List elements
    const commentList = document.getElementById("comment-list");
    const commentListHeader = document.getElementById("comment-list-header");
    const commentListStatus = document.getElementById("comment-list-status");

    // Mock Data (matches design)
    // This array simulates a database.
    const mockApiData = [
        {
            id: 1,
            name: "علی فهمیده",
            date: "۱۳ مهر ۱۴۰۴",
            rating: 1,
            comment:
                "لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ...",
        },
        {
            id: 2,
            name: "علی فهمیده",
            date: "۱۳ مهر ۱۴۰۴",
            rating: 1,
            comment:
                "لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ...",
        },
        {
            id: 3,
            name: "علی فهمیده",
            date: "۱۳ مهر ۱۴۰۴",
            rating: 1,
            comment:
                "لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ...",
        },
    ];

    // --- 2. Helper Functions ---

    /**
     * Simulates network delay.
     * @param {number} ms - Milliseconds to wait
     */
    function sleep(ms) {
        return new Promise((resolve) => setTimeout(resolve, ms));
    }

    /**
     * Generates HTML for list rating stars (filled/empty).
     * @param {number} rating - The rating (1-5)
     */
    function renderListStars(rating) {
        let stars = "";
        for (let i = 1; i <= 5; i++) {
            stars += i <= rating ? '<span class="filled">★</span>' : "<span>☆</span>";
        }
        return stars;
    }

    /**
     * Creates a DOM element for a single comment.
     * @param {object} comment - The comment data object
     */
    function renderComment(comment) {
        const item = document.createElement("div");
        item.className = "comment-item";

        item.innerHTML = `
            <a href="#" class="comment-reply-link" onclick="replyToComment(${comment.id}); return false;">
                پاسخ &larr;
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

    // --- 3. Main Functions ---

    /**
     * Fetches (simulated) and displays all comments.
     */
    async function loadComments() {
        commentList.innerHTML = ""; // Clear list
        commentListStatus.textContent = "در حال بارگذاری دیدگاه‌ها...";
        commentListStatus.style.display = "block";

        try {
            // Simulate API call
            await sleep(500);
            
            // TODO: Replace 'mockApiData' with a real API call:
            // const response = await fetch('api/comments?item_id=' + itemId);
            // const comments = await response.json();
            const comments = mockApiData; 

            if (comments && comments.length > 0) {
                commentListStatus.style.display = "none";
                commentListHeader.textContent = `${comments.length} دیدگاه ثبت شده`;
                comments.forEach((comment) => {
                    commentList.appendChild(renderComment(comment));
                });
            } else {
                commentListHeader.textContent = "دیدگاه‌ها";
                commentListStatus.textContent = "هنوز دیدگاهی ثبت نشده است.";
            }
        } catch (error) {
            console.error("Error loading comments:", error);
            commentListStatus.textContent = "خطا در بارگذاری دیدگاه‌ها.";
            commentListStatus.style.color = "red";
        }
    }

    /**
     * Handles the form submission event.
     * @param {Event} e - The form submit event
     */
    async function handleFormSubmit(e) {
        e.preventDefault(); // Stop page reload
        
        // Disable form
        submitButton.disabled = true;
        submitButton.textContent = "در حال ارسال...";
        formStatus.textContent = "";

        // Get form data
        const formData = new FormData(commentForm);
        const data = {
            itemId: itemId, // Get ID from localized script
            name: formData.get("name"),
            email: formData.get("email"),
            comment: formData.get("comment"),
            rating: parseInt(formData.get("rating") || "0"),
            date: new Date().toLocaleDateString("fa-IR"), // Set current date
        };

        try {
            // Simulate API POST request
            await sleep(750);
            
            // TODO: Replace simulation with a real API call:
            // await fetch('api/comments', { 
            //   method: 'POST', 
            //   body: JSON.stringify(data),
            //   headers: { 'Content-Type': 'application/json' }
            // });

            // Add new comment to the top of the mock list
            mockApiData.unshift(data);
            loadComments(); // Reload the list
            
            formStatus.textContent = "دیدگاه شما با موفقیت ثبت شد.";
            formStatus.className = "form-status success";
            commentForm.reset(); // Clear the form

        } catch (error) {
            console.error("Error submitting comment:", error);
            formStatus.textContent = "خطا در ثبت دیدگاه.";
            formStatus.className = "form-status error";
        } finally {
            // Re-enable form
            submitButton.disabled = false;
            submitButton.textContent = "ثبت دیدگاه";
        }
    }

    /**
     * Placeholder for reply functionality.
     * (In a real app, this might open a threaded reply form)
     */
    window.replyToComment = function (commentId) {
        console.log("Replying to comment ID:", commentId);
        // Simple focus on the main comment box for now
        document.getElementById("comment").focus();
    };

    /**
     * Binds all event listeners.
     */
    function bindEvents() {
        // Ensure form exists before adding listener
        if (commentForm) {
            commentForm.addEventListener("submit", handleFormSubmit);
        }
    }

    /**
     * Initializes the component.
     */
    function init() {
        // Check if the component exists on this page
        if (commentListHeader) {
            bindEvents();
            loadComments();
        }
    }

    // --- 4. App Execution ---
    // Wait for the DOM to be fully loaded before running init
    document.addEventListener("DOMContentLoaded", init);
})();