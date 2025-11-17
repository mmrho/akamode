<?php

/**
 * The template for displaying comments with API integration and custom styling
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get current post/app ID for API calls
$app_id = get_the_ID();

// Sample comments data (این داده‌ها بعداً از API خواهند آمد)
$sample_comments = [
    [
        'id' => 1,
        'author_name' => 'علی',
        'title' => '',
        'rating' => 5,
        'date' => '۱ روز پیش',
        'content' => 'فیلم فوق‌العاده‌ای بود! بازی‌ها عالی و داستان جذاب.',
        'likes' => 12,
        'dislikes' => 1,
        'user_liked' => false,
        'user_disliked' => false
    ],
    [
        'id' => 2,
        'author_name' => 'سارا',
        'title' => '',
        'rating' => 4,
        'date' => '۲ روز پیش',
        'content' => 'خوب بود اما پایانش می‌تونست بهتر باشه.',
        'likes' => 8,
        'dislikes' => 3,
        'user_liked' => false,
        'user_disliked' => false
    ],
    [
        'id' => 3,
        'author_name' => 'محمد',
        'title' => '',
        'rating' => 5,
        'date' => '۳ روز پیش',
        'content' => 'بهترین فیلمی که امسال دیدم!',
        'likes' => 15,
        'dislikes' => 0,
        'user_liked' => true,
        'user_disliked' => false
    ],
];

// Calculate average rating
$total_rating = 0;
$comment_count = count($sample_comments);
foreach ($sample_comments as $comment) {
    $total_rating += $comment['rating'];
}
$average_rating = $comment_count > 0 ? round($total_rating / $comment_count, 1) : 0;
?>

<div id="comments" class="comments-area">

    <!-- Review Title -->
    <h2 class="review-title">نقد فیلم</h2>
    <hr class="title-separator">

    <!-- Average Rating Section -->
    <div class="average-rating-section">
        <div class="average-title">امتیاز کاربران</div>
        <div class="average-stars">
            <?php
            $full_stars = floor($average_rating);
            $half_star = $average_rating - $full_stars >= 0.5 ? 1 : 0;
            $empty_stars = 5 - $full_stars - $half_star;

            for ($i = 1; $i <= $full_stars; $i++) {
                echo '<span class="star filled">★</span>';
            }
            if ($half_star) {
                echo '<span class="star half">★</span>';
            }
            for ($i = 1; $i <= $empty_stars; $i++) {
                echo '<span class="star empty">★</span>'; // در عکس ستاره خالی هم ★ است اما خاکستری
            }
            ?>
            <span class="average-value"><?php echo $average_rating; ?></span>
        </div>
    </div>

    <!-- Comment Form Title -->
    <div class="comment-form-section">
        <button class="add-comment-btn" id="add-comment-btn">
            <span>ثبت دیدگاه و امتیاز</span>
        </button>
    </div>

    <!-- Comments Display Section -->
    <div class="comments-display-section">
        <div class="comments-container" id="comments-container">
            <div class="comments-wrapper" id="comments-wrapper">
                <?php foreach ($sample_comments as $comment): ?>
                    <div class="comment-card">
                        <div class="comment-header">
                            <div class="comment-author"><?php echo esc_html($comment['author_name']); ?></div>
                            <div class="comment-rating">
                                <?php
                                for ($i = 1; $i <= 5; $i++):
                                    $star_class = $i <= $comment['rating'] ? 'star-filled' : 'star-empty';
                                ?>
                                    <span class="star <?php echo $star_class; ?>">★</span>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="comment-date"><?php echo esc_html($comment['date']); ?></div>
                        <div class="comment-content"><?php echo esc_html($comment['content']); ?></div>
                        <div class="comment-footer">
                            <div class="comment-actions">
                                <button class="dislike-btn <?php echo $comment['user_disliked'] ? 'active' : ''; ?>" data-comment-id="<?php echo $comment['id']; ?>">
                                    👎 <span><?php echo $comment['dislikes']; ?></span>
                                </button>
                                <button class="like-btn <?php echo $comment['user_liked'] ? 'active' : ''; ?>" data-comment-id="<?php echo $comment['id']; ?>">
                                    👍 <span><?php echo $comment['likes']; ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- More Comments Link -->
    <div class="more-comments-section">
        <a href="<?php echo esc_url(get_permalink() . 'comments/'); ?>" class="more-comments-link">
            مشاهده دیدگاه‌های بیشتر
        </a>
    </div>

</div>

<!-- Comment Popup Modal -->
<div id="comment-modal" class="comment-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>ثبت دیدگاه و امتیاز</h3>
            <button class="close-modal" id="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="comment-form">
                <div class="rating-section">
                    <label>امتیاز شما:</label>
                    <div class="star-rating" id="star-rating">
                        <span class="star" data-rating="1">★</span>
                        <span class="star" data-rating="2">★</span>
                        <span class="star" data-rating="3">★</span>
                        <span class="star" data-rating="4">★</span>
                        <span class="star" data-rating="5">★</span>
                    </div>
                </div>
                <div class="comment-input-section">
                    <label for="comment-text">دیدگاه شما:</label>
                    <textarea id="comment-text" name="comment" placeholder="دیدگاه خود را بنویسید..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="cancel-comment">انصراف</button>
                    <button type="submit" class="btn-submit">ثبت دیدگاه</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* استایل دقیق شبیه عکس: زمینه روشن، فونت فارسی، ستاره طلایی، خط سیاه زیر عنوان، چیدمان راست به چپ */
    * {
        box-sizing: border-box;
    }

    body {
        direction: rtl; /* راست به چپ برای فارسی */
        font-family: 'Tahoma', 'Arial', sans-serif; /* فونت شبیه عکس */
    }

    .comments-area {
        background: #fff; /* سفید */
        padding: 20px;
        max-width: 800px;
        margin: 0 auto;
    }

    .review-title {
        font-size: 22px;
        color: #000;
        margin: 0 0 10px;
        font-weight: bold;
    }

    .title-separator {
        border: 0;
        border-top: 2px solid #000;
        margin: 0 0 30px;
    }

    .average-rating-section {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
    }

    .average-title {
        font-size: 16px;
        color: #000;
        margin-left: 10px; /* فاصله از ستاره‌ها */
        font-weight: bold;
    }

    .average-stars {
        display: flex;
        align-items: center;
        font-size: 18px;
    }

    .average-stars .star {
        margin-left: 2px;
    }

    .average-stars .star.filled {
        color: #f5b50a; /* طلایی شبیه عکس */
    }

    .average-stars .star.half {
        position: relative;
        color: #f5b50a;
    }

    .average-stars .star.half::before {
        content: '★';
        position: absolute;
        left: 0;
        color: #ccc;
        overflow: hidden;
        width: 50%;
    }

    .average-stars .star.empty {
        color: #ccc;
    }

    .average-value {
        font-size: 16px;
        color: #000;
        margin-right: 5px; /* چپ برای rtl */
        font-weight: bold;
    }

    .comment-form-section {
        margin-bottom: 20px;
    }

    .add-comment-btn {
        background: #f0f0f0;
        border: 1px solid #ddd;
        padding: 10px 20px;
        cursor: pointer;
        font-size: 14px;
        color: #333;
    }

    .comment-card {
        margin-bottom: 20px;
        padding: 15px;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 4px;
    }

    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }

    .comment-author {
        font-size: 14px;
        font-weight: bold;
        color: #000;
    }

    .comment-rating {
        display: flex;
    }

    .comment-rating .star {
        font-size: 14px;
        margin-left: 2px;
    }

    .comment-rating .star-filled {
        color: #f5b50a;
    }

    .comment-rating .star-empty {
        color: #ccc;
    }

    .comment-date {
        font-size: 12px;
        color: #999;
        margin-bottom: 10px;
    }

    .comment-content {
        font-size: 14px;
        color: #333;
        line-height: 1.5;
    }

    .comment-footer {
        display: flex;
        justify-content: flex-end;
        margin-top: 10px;
    }

    .like-btn, .dislike-btn {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
        margin-left: 15px;
        color: #666;
    }

    .like-btn.active, .dislike-btn.active {
        color: #007bff;
    }

    .more-comments-section {
        text-align: center;
        margin-top: 20px;
    }

    .more-comments-link {
        color: #007bff;
        text-decoration: none;
        font-size: 14px;
    }

    /* مودال */
    .comment-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }

    .comment-modal.active {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
    }

    .rating-section {
        margin-bottom: 20px;
    }

    .star-rating {
        display: flex;
        justify-content: flex-end; /* برای rtl */
    }

    .star-rating .star {
        font-size: 24px;
        color: #ccc;
        cursor: pointer;
        margin-left: 5px;
    }

    .star-rating .star.active, .star-rating .star.hover {
        color: #f5b50a;
    }

    .comment-input-section {
        margin-bottom: 20px;
    }

    textarea {
        width: 100%;
        height: 100px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .modal-footer {
        display: flex;
        justify-content: space-between;
    }

    .btn-cancel, .btn-submit {
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        border-radius: 4px;
    }

    .btn-cancel {
        background: #f0f0f0;
        color: #333;
    }

    .btn-submit {
        background: #007bff;
        color: #fff;
    }

    /* تُست */
    .toast-message {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 15px;
        border-radius: 4px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .toast-message.show {
        opacity: 1;
    }
</style>

<script>
// اسکریپت همان قبلی، بدون تغییر چون استایل عوض شد 🔥
document.addEventListener('DOMContentLoaded', function() {
    const appId = <?php echo json_encode($app_id); ?>;
    let currentRating = 0;

    // Add comment button
    document.getElementById('add-comment-btn').addEventListener('click', function() {
        <?php if (is_user_logged_in()): ?>
            document.getElementById('comment-modal').classList.add('active');
        <?php else: ?>
            window.location.href = '<?php echo wp_login_url(get_permalink()); ?>';
        <?php endif; ?>
    });

    // Modal close buttons
    document.getElementById('close-modal').addEventListener('click', closeModal);
    document.getElementById('cancel-comment').addEventListener('click', closeModal);

    // Close modal when clicking outside
    document.getElementById('comment-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Star rating
    document.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', function() {
            currentRating = parseInt(this.dataset.rating);
            updateStarDisplay();
        });

        star.addEventListener('mouseover', function() {
            const rating = parseInt(this.dataset.rating);
            highlightStars(rating);
        });
    });

    document.getElementById('star-rating').addEventListener('mouseleave', function() {
        updateStarDisplay();
    });

    // Comment form submission
    document.getElementById('comment-form').addEventListener('submit', function(e) {
        e.preventDefault();
        submitComment();
    });

    // Like/Dislike functionality
    document.querySelectorAll('.like-btn, .dislike-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const type = this.classList.contains('like-btn') ? 'like' : 'dislike';
            toggleLike(commentId, type, this);
        });
    });

    function submitComment() {
        const commentText = document.getElementById('comment-text').value.trim();

        if (!commentText || currentRating === 0) {
            showMessage('لطفاً امتیاز و دیدگاه خود را وارد کنید.');
            return;
        }

        showMessage('دیدگاه شما با موفقیت ثبت شد!');
        closeModal();

        // API واقعی بعدا اضافه کن 😈
    }

    function toggleLike(commentId, type, buttonElement) {
        const isActive = buttonElement.classList.contains('active');
        const countSpan = buttonElement.querySelector('span');
        let currentCount = parseInt(countSpan.textContent);

        if (isActive) {
            buttonElement.classList.remove('active');
            countSpan.textContent = currentCount - 1;
        } else {
            buttonElement.classList.add('active');
            countSpan.textContent = currentCount + 1;

            const oppositeBtn = type === 'like' ?
                buttonElement.parentElement.querySelector('.dislike-btn') :
                buttonElement.parentElement.querySelector('.like-btn');

            if (oppositeBtn.classList.contains('active')) {
                oppositeBtn.classList.remove('active');
                const oppositeCount = oppositeBtn.querySelector('span');
                oppositeCount.textContent = parseInt(oppositeCount.textContent) - 1;
            }
        }

        // API واقعی بعدا اضافه کن 😈
    }

    function closeModal() {
        document.getElementById('comment-modal').classList.remove('active');
        document.getElementById('comment-text').value = '';
        currentRating = 0;
        updateStarDisplay();
    }

    function updateStarDisplay() {
        document.querySelectorAll('.star').forEach((star, index) => {
            if (index < currentRating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }

    function highlightStars(rating) {
        document.querySelectorAll('.star').forEach((star, index) => {
            if (index < rating) {
                star.classList.add('hover');
            } else {
                star.classList.remove('hover');
            }
        });
    }

    function showMessage(message) {
        const toast = document.createElement('div');
        toast.className = 'toast-message';
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('show');
        }, 100);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
});
</script>