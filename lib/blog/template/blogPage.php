<?php
// detect what tab is active
$active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'dashboard';

// url of this page
$base_url = get_permalink(); 
?>

<div class="container">
    <div class="main">

        <div class="top">
            <div class="top-content">
                <nav aria-label="Breadcrumb">
                    <ol class="breadcrumb-list">
                        <li>
                            <a href="/">آکامد</a>
                        </li>
                        <li>
                            <span aria-current="page">وبلاگ آکامد</span>
                        </li>
                    </ol>
                </nav>
                <h1>وبلاگ آکامد</h1>
                <p>جدیدترین مطالب مد و فشن</p>
            </div>
            <div class="filter-container">
                <a href="#">جدیدترین</a>
                <a href="#" class="active">محبوب ترین</a>
                <a href="#">مرتبط ترین</a>
            </div>
        </div>


        <div class="blog-posts-container">
            <div class="filter-sort-container">
                <div class="sort">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <g clip-path="url(#clip0_860_34362)">
                            <path d="M4.5 12H11.25" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4.5 6H17.25" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4.5 18H9.75" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M13.5 15.75L17.25 19.5L21 15.75" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.25 19.5V10.5" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_860_34362">
                            <rect width="24" height="24" fill="white"/>
                            </clipPath>
                        </defs>
                        </svg>
                    </div>
                    <div class="text">
                        مرتب سازی بر اساس : جدید ترین
                    </div>
                </div>
                <div class="filter">
                    <div class="text">فیلتر</div>
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <g clip-path="url(#clip0_860_34350)">
                            <path d="M17.25 7.5H20.25" stroke="#111111" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3.75 7.5H14.25" stroke="#111111" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.25 16.5H20.25" stroke="#111111" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3.75 16.5H8.25" stroke="#111111" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14.25 5.25V9.75" stroke="#111111" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8.25 14.25V18.75" stroke="#111111" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_860_34350">
                            <rect width="24" height="24" fill="white"/>
                            </clipPath>
                        </defs>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="blog-posts">
                <?php
                for($i = 0; $i < 5; $i++)
                {
                ?>
                <article class="post">
                    <a href="#" class="img">
                        <img src="<?php echo THEME_IMG; ?>/temp/blog1.png" alt="">
                    </a>
                    <div class="post-info">
                        <div class="meta">
                            <div><a href="#" class="cat-link">فشن</a> - ۱۲ مهر ۱۴۰۲</div>
                        </div>
                        <div class="bottom">
                            <a href="" class="title">ترند امسال پاییز</a>
                            <div class="read">
                                <a href="#" class="main-link">مشاهده</a>
                            </div>
                        </div>
                    </div>
                </article>
                <?php
                }
                ?>
            </div>
        </div>

        <nav class="pagination-container" aria-label="Pagination">
            <ul class="pagination">
                <li class="active"><a href="/page/1">1</a></li>
                <li><a href="/page/3">2</a></li>
                <li><a href="/page/10">
                    <img src="<?php echo THEME_IMG; ?>/temp/left.png" alt="">
                </a></li>
            </ul>
        </nav>

        
    </div>
</div>