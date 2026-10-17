<?php
/**
 * GOF - Public News Page
 *
 * Expected controller data:
 *   $news
 */

$news = is_array($news ?? null) ? $news : [];

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

if (!function_exists('news_h')) {
    function news_h($value): string
    {
        return htmlspecialchars(
            (string)($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

if (!function_exists('news_date')) {
    function news_date($value, string $fallback = 'Date unavailable'): string
    {
        if (empty($value)) {
            return $fallback;
        }

        $timestamp = strtotime((string)$value);

        if ($timestamp === false) {
            return news_h($value);
        }

        return date('F j, Y', $timestamp);
    }
}

if (!function_exists('news_excerpt')) {
    function news_excerpt($value, int $length = 150): string
    {
        $text = trim(
            strip_tags(
                html_entity_decode(
                    (string)($value ?? ''),
                    ENT_QUOTES,
                    'UTF-8'
                )
            )
        );

        if ($text === '') {
            return 'Read the latest update from Garko Orphans Foundation.';
        }

        if (function_exists('mb_strlen') && mb_strlen($text) > $length) {
            return rtrim(
                mb_substr($text, 0, $length)
            ) . '...';
        }

        if (strlen($text) > $length) {
            return rtrim(
                substr($text, 0, $length)
            ) . '...';
        }

        return $text;
    }
}

if (!function_exists('news_image_url')) {
    function news_image_url($image): string
    {
        $image = trim((string)($image ?? ''));

        if ($image === '') {
            return '';
        }

        /*
         * Already an absolute URL.
         */
        if (
            preg_match(
                '#^(https?:)?//#i',
                $image
            )
        ) {
            return $image;
        }

        /*
         * Already an application/public path.
         */
        if (strpos($image, '/') === 0) {
            return url($image);
        }

        /*
         * Common uploaded image locations.
         */
        $possiblePaths = [
            '/uploads/news/' . ltrim($image, '/'),
            '/uploads/' . ltrim($image, '/'),
            '/' . ltrim($image, '/'),
        ];

        foreach ($possiblePaths as $path) {
            return url($path);
        }

        return '';
    }
}

if (!function_exists('news_category')) {
    function news_category($article): string
    {
        $category =
            $article['category']
            ?? $article['type']
            ?? '';

        $category = trim((string)$category);

        if ($category === '') {
            return 'News';
        }

        return $category;
    }
}

if (!function_exists('news_category_class')) {
    function news_category_class($category): string
    {
        $category = strtolower(trim((string)$category));

        return match (true) {
            str_contains($category, 'achievement') => 'success',
            str_contains($category, 'award')       => 'success',
            str_contains($category, 'donation')    => 'danger',
            str_contains($category, 'community')   => 'warning',
            str_contains($category, 'health')      => 'info',
            str_contains($category, 'education')   => 'primary',
            str_contains($category, 'event')       => 'secondary',
            str_contains($category, 'volunteer')   => 'purple',
            default                                => 'primary',
        };
    }
}

/*
|--------------------------------------------------------------------------
| Prepare articles
|--------------------------------------------------------------------------
*/

$featuredNews = $news[0] ?? null;

$recentNews = [];

if (count($news) > 1) {
    $recentNews = array_slice($news, 1, 5);
}

$gridNews = [];

if (count($news) > 1) {
    $gridNews = array_slice($news, 1);
}

/*
|--------------------------------------------------------------------------
| Base path
|--------------------------------------------------------------------------
*/

$newsBaseUrl = url('/news');
?>

<style>
/* =========================================================
   GOF PUBLIC NEWS PAGE
   ========================================================= */

.gof-news-page {
    --news-primary: #c9a900;
    --news-primary-dark: #a98e00;
    --news-dark: #18202b;
    --news-muted: #6c757d;
    --news-border: #e9ecef;
    --news-bg: #f7f8fa;
}

/* ---------------------------------------------------------
   Hero
   --------------------------------------------------------- */

.gof-news-page .news-page-hero {
    background:
        linear-gradient(
            135deg,
            var(--news-primary-dark) 0%,
            var(--news-primary) 100%
        );
    padding: 54px 20px 48px;
}

.gof-news-page .news-page-hero .page-title {
    font-size: clamp(2rem, 5vw, 3rem);
    font-weight: 800;
    margin-bottom: 10px;
}

.gof-news-page .news-page-hero .page-subtitle {
    max-width: 720px;
    margin: 0 auto 18px;
    font-size: 1.05rem;
}

/* ---------------------------------------------------------
   Main section
   --------------------------------------------------------- */

.gof-news-page .news-main-section {
    padding: 64px 20px;
}

/* ---------------------------------------------------------
   Featured article
   --------------------------------------------------------- */

.gof-news-page .featured-news-card {
    background: #fff;
    border: 1px solid var(--news-border);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 10px 35px rgba(0, 0, 0, .06);
    height: 100%;
}

.gof-news-page .featured-news-image {
    position: relative;
    min-height: 360px;
    overflow: hidden;
    background:
        linear-gradient(
            135deg,
            #eef1f4,
            #dfe4e9
        );
}

.gof-news-page .featured-news-image img {
    width: 100%;
    height: 100%;
    min-height: 360px;
    object-fit: cover;
    display: block;
}

.gof-news-page .news-image-placeholder {
    width: 100%;
    min-height: 260px;
    display: flex;
    align-items: center;
    justify-content: center;
    background:
        linear-gradient(
            135deg,
            #f1f3f5,
            #dee2e6
        );
    color: #8b949e;
}

.gof-news-page .featured-news-image .news-image-placeholder {
    min-height: 360px;
}

.gof-news-page .news-image-placeholder i {
    font-size: 72px;
}

.gof-news-page .featured-news-body {
    padding: 30px;
}

.gof-news-page .featured-news-title {
    color: var(--news-dark);
    font-size: clamp(1.55rem, 3vw, 2.2rem);
    line-height: 1.25;
    font-weight: 800;
    margin-bottom: 14px;
}

.gof-news-page .featured-news-excerpt {
    color: #59636e;
    font-size: 1rem;
    line-height: 1.75;
    margin-bottom: 22px;
}

/* ---------------------------------------------------------
   Meta
   --------------------------------------------------------- */

.gof-news-page .news-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    color: var(--news-muted);
    font-size: .9rem;
}

.gof-news-page .news-meta-item {
    display: inline-flex;
    align-items: center;
}

/* ---------------------------------------------------------
   Sidebar
   --------------------------------------------------------- */

.gof-news-page .news-sidebar {
    background: #fff;
    border: 1px solid var(--news-border);
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 8px 28px rgba(0, 0, 0, .05);
}

.gof-news-page .sidebar-title {
    color: var(--news-dark);
    font-weight: 800;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 2px solid var(--news-primary);
}

.gof-news-page .recent-news-list {
    display: flex;
    flex-direction: column;
}

.gof-news-page .recent-news-item {
    display: block;
    padding: 16px 0;
    border-bottom: 1px solid var(--news-border);
    text-decoration: none;
    transition: transform .2s ease;
}

.gof-news-page .recent-news-item:last-child {
    border-bottom: 0;
}

.gof-news-page .recent-news-item:hover {
    transform: translateX(4px);
}

.gof-news-page .recent-news-date {
    color: var(--news-primary-dark);
    font-size: .78rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.gof-news-page .recent-news-title {
    color: var(--news-dark);
    font-weight: 700;
    line-height: 1.45;
}

/* ---------------------------------------------------------
   News grid
   --------------------------------------------------------- */

.gof-news-page .latest-news-section {
    background: var(--news-bg);
    padding: 64px 20px;
}

.gof-news-page .section-heading {
    max-width: 700px;
    margin: 0 auto 42px;
    text-align: center;
}

.gof-news-page .section-title {
    color: var(--news-dark);
    font-weight: 800;
    margin-bottom: 8px;
}

.gof-news-page .section-description {
    color: var(--news-muted);
    margin-bottom: 0;
}

.gof-news-page .news-card {
    background: #fff;
    border: 1px solid var(--news-border);
    border-radius: 16px;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 7px 25px rgba(0, 0, 0, .045);
    transition:
        transform .2s ease,
        box-shadow .2s ease;
}

.gof-news-page .news-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, .09);
}

.gof-news-page .news-card-image {
    height: 210px;
    overflow: hidden;
}

.gof-news-page .news-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .35s ease;
}

.gof-news-page .news-card:hover .news-card-image img {
    transform: scale(1.04);
}

.gof-news-page .news-card-body {
    padding: 24px;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.gof-news-page .news-card-title {
    color: var(--news-dark);
    font-size: 1.2rem;
    line-height: 1.4;
    font-weight: 750;
    margin-bottom: 10px;
}

.gof-news-page .news-card-excerpt {
    color: #66707b;
    line-height: 1.65;
    margin-bottom: 20px;
    flex: 1;
}

.gof-news-page .news-card .btn {
    align-self: flex-start;
}

/* ---------------------------------------------------------
   Empty state
   --------------------------------------------------------- */

.gof-news-page .news-empty-state {
    background: #fff;
    border: 1px dashed #ced4da;
    border-radius: 18px;
    padding: 65px 25px;
    text-align: center;
}

.gof-news-page .news-empty-state i {
    font-size: 58px;
    color: #adb5bd;
    margin-bottom: 18px;
}

.gof-news-page .news-empty-state h3 {
    color: var(--news-dark);
    font-weight: 800;
}

/* ---------------------------------------------------------
   Newsletter
   --------------------------------------------------------- */

.gof-news-page .newsletter-section {
    padding: 64px 20px;
}

.gof-news-page .newsletter-card {
    background:
        linear-gradient(
            135deg,
            #fffdf0,
            #fff8cf
        );
    border: 1px solid #f0df8a;
    border-radius: 20px;
    padding: 38px;
}

.gof-news-page .newsletter-title {
    color: var(--news-dark);
    font-weight: 800;
}

.gof-news-page .newsletter-description {
    color: var(--news-muted);
    max-width: 620px;
    margin: 0 auto 22px;
}

.gof-news-page .newsletter-form {
    max-width: 650px;
    margin: 0 auto;
}

/* ---------------------------------------------------------
   Purple fallback
   --------------------------------------------------------- */

.gof-news-page .bg-purple {
    background-color: #6f42c1 !important;
}

.gof-news-page .btn-outline-purple {
    color: #6f42c1;
    border-color: #6f42c1;
}

.gof-news-page .btn-outline-purple:hover {
    color: #fff;
    background-color: #6f42c1;
}

/* ---------------------------------------------------------
   Mobile
   --------------------------------------------------------- */

@media (max-width: 767.98px) {

    .gof-news-page .news-page-hero {
        padding: 42px 16px 38px;
    }

    .gof-news-page .news-main-section,
    .gof-news-page .latest-news-section,
    .gof-news-page .newsletter-section {
        padding: 45px 16px;
    }

    .gof-news-page .featured-news-image,
    .gof-news-page .featured-news-image img {
        min-height: 250px;
    }

    .gof-news-page .featured-news-body {
        padding: 22px;
    }

    .gof-news-page .featured-news-title {
        font-size: 1.55rem;
    }

    .gof-news-page .news-sidebar {
        margin-top: 25px;
    }

    .gof-news-page .newsletter-card {
        padding: 28px 20px;
    }

    .gof-news-page .newsletter-form .input-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .gof-news-page .newsletter-form .form-control,
    .gof-news-page .newsletter-form .btn {
        width: 100%;
        border-radius: 8px !important;
    }
}
</style>

<div class="gof-news-page">

    <!-- =====================================================
         HERO
         ===================================================== -->

    <section class="news-page-hero">
        <div class="container">
            <div class="text-center">

                <h1 class="page-title text-white">
                    News &amp; Updates
                </h1>

                <p class="page-subtitle text-white-50">
                    Stay informed about our latest activities,
                    achievements, and community impact.
                </p>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">

                        <li class="breadcrumb-item">
                            <a
                                href="<?= news_h(url('/')) ?>"
                                class="text-white-50 text-decoration-none"
                            >
                                Home
                            </a>
                        </li>

                        <li
                            class="breadcrumb-item active text-white"
                            aria-current="page"
                        >
                            News
                        </li>

                    </ol>
                </nav>

            </div>
        </div>
    </section>


    <!-- =====================================================
         FEATURED + RECENT NEWS
         ===================================================== -->

    <section class="news-main-section">
        <div class="container">

            <?php if ($featuredNews): ?>

                <div class="row g-4 align-items-stretch">

                    <!-- Featured article -->
                    <div class="col-lg-8">

                        <?php
                        $featuredImage =
                            $featuredNews['image']
                            ?? $featuredNews['featured_image']
                            ?? null;

                        $featuredImageUrl =
                            news_image_url($featuredImage);

                        $featuredCategory =
                            news_category($featuredNews);

                        $featuredCategoryClass =
                            news_category_class(
                                $featuredCategory
                            );

                        $featuredId =
                            (int)($featuredNews['id'] ?? 0);

                        $featuredTitle =
                            trim(
                                (string)(
                                    $featuredNews['title']
                                    ?? 'Latest News'
                                )
                            );

                        $featuredContent =
                            $featuredNews['excerpt']
                            ?? $featuredNews['summary']
                            ?? $featuredNews['content']
                            ?? $featuredNews['body']
                            ?? '';

                        $featuredAuthor =
                            trim(
                                (string)(
                                    $featuredNews['author_name']
                                    ?? 'Garko Orphans Foundation'
                                )
                            );

                        $featuredDate =
                            $featuredNews['published_at']
                            ?? $featuredNews['created_at']
                            ?? null;
                        ?>

                        <article class="featured-news-card">

                            <div class="featured-news-image">

                                <?php if ($featuredImageUrl): ?>

                                    <img
                                        src="<?= news_h($featuredImageUrl) ?>"
                                        alt="<?= news_h($featuredTitle) ?>"
                                        loading="eager"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                    >

                                    <div
                                        class="news-image-placeholder"
                                        style="display:none;"
                                        aria-hidden="true"
                                    >
                                        <i class="bi bi-newspaper"></i>
                                    </div>

                                <?php else: ?>

                                    <div
                                        class="news-image-placeholder"
                                        aria-hidden="true"
                                    >
                                        <i class="bi bi-newspaper"></i>
                                    </div>

                                <?php endif; ?>

                            </div>

                            <div class="featured-news-body">

                                <span
                                    class="badge bg-<?= news_h($featuredCategoryClass) ?> mb-3"
                                >
                                    <?= news_h($featuredCategory) ?>
                                </span>

                                <h2 class="featured-news-title">
                                    <?= news_h($featuredTitle) ?>
                                </h2>

                                <div class="news-meta mb-3">

                                    <span class="news-meta-item">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?= news_date($featuredDate) ?>
                                    </span>

                                    <?php if ($featuredAuthor !== ''): ?>

                                        <span class="news-meta-item">
                                            <i class="bi bi-person me-1"></i>
                                            <?= news_h($featuredAuthor) ?>
                                        </span>

                                    <?php endif; ?>

                                </div>

                                <p class="featured-news-excerpt">
                                    <?= news_h(
                                        news_excerpt(
                                            $featuredContent,
                                            260
                                        )
                                    ) ?>
                                </p>

                                <?php if ($featuredId > 0): ?>

                                    <a
                                        href="<?= news_h(
                                            url('/news/' . $featuredId)
                                        ) ?>"
                                        class="btn btn-primary"
                                    >
                                        Read Full Article
                                        <i class="bi bi-arrow-right ms-1"></i>
                                    </a>

                                <?php endif; ?>

                            </div>

                        </article>

                    </div>


                    <!-- Recent news -->
                    <div class="col-lg-4">

                        <aside class="news-sidebar">

                            <h3 class="sidebar-title">
                                Recent News
                            </h3>

                            <?php if ($recentNews): ?>

                                <div class="recent-news-list">

                                    <?php foreach ($recentNews as $recent): ?>

                                        <?php
                                        $recentId =
                                            (int)(
                                                $recent['id'] ?? 0
                                            );

                                        $recentTitle =
                                            trim(
                                                (string)(
                                                    $recent['title']
                                                    ?? 'News Update'
                                                )
                                            );

                                        $recentDate =
                                            $recent['published_at']
                                            ?? $recent['created_at']
                                            ?? null;
                                        ?>

                                        <?php if ($recentId > 0): ?>

                                            <a
                                                href="<?= news_h(
                                                    url(
                                                        '/news/' .
                                                        $recentId
                                                    )
                                                ) ?>"
                                                class="recent-news-item"
                                            >

                                                <div class="recent-news-date">
                                                    <?= news_date(
                                                        $recentDate
                                                    ) ?>
                                                </div>

                                                <div class="recent-news-title">
                                                    <?= news_h(
                                                        $recentTitle
                                                    ) ?>
                                                </div>

                                            </a>

                                        <?php endif; ?>

                                    <?php endforeach; ?>

                                </div>

                            <?php else: ?>

                                <p class="text-muted mb-0">
                                    No other published news is available yet.
                                </p>

                            <?php endif; ?>

                        </aside>

                    </div>

                </div>

            <?php else: ?>

                <!-- No published news -->
                <div class="news-empty-state">

                    <i class="bi bi-newspaper"></i>

                    <h3>
                        No News Published Yet
                    </h3>

                    <p class="text-muted mb-4">
                        There are currently no published news articles.
                        Please check back later for updates from
                        Garko Orphans Foundation.
                    </p>

                    <a
                        href="<?= news_h(url('/')) ?>"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-house me-1"></i>
                        Return Home
                    </a>

                </div>

            <?php endif; ?>

        </div>
    </section>


    <!-- =====================================================
         LATEST NEWS GRID
         ===================================================== -->

    <?php if (count($gridNews) > 0): ?>

        <section class="latest-news-section">

            <div class="container">

                <div class="section-heading">

                    <h2 class="section-title">
                        Latest News
                    </h2>

                    <p class="section-description">
                        Browse our recent updates, activities,
                        achievements, and community stories.
                    </p>

                </div>

                <div class="row g-4">

                    <?php foreach ($gridNews as $article): ?>

                        <?php
                        $articleId =
                            (int)($article['id'] ?? 0);

                        $articleTitle =
                            trim(
                                (string)(
                                    $article['title']
                                    ?? 'News Update'
                                )
                            );

                        $articleCategory =
                            news_category($article);

                        $articleCategoryClass =
                            news_category_class(
                                $articleCategory
                            );

                        $articleImage =
                            $article['image']
                            ?? $article['featured_image']
                            ?? null;

                        $articleImageUrl =
                            news_image_url($articleImage);

                        $articleContent =
                            $article['excerpt']
                            ?? $article['summary']
                            ?? $article['content']
                            ?? $article['body']
                            ?? '';

                        $articleDate =
                            $article['published_at']
                            ?? $article['created_at']
                            ?? null;

                        $articleAuthor =
                            trim(
                                (string)(
                                    $article['author_name']
                                    ?? ''
                                )
                            );
                        ?>

                        <div class="col-md-6 col-lg-4">

                            <article class="news-card">

                                <div class="news-card-image">

                                    <?php if ($articleImageUrl): ?>

                                        <img
                                            src="<?= news_h(
                                                $articleImageUrl
                                            ) ?>"
                                            alt="<?= news_h(
                                                $articleTitle
                                            ) ?>"
                                            loading="lazy"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                        >

                                        <div
                                            class="news-image-placeholder"
                                            style="display:none;"
                                            aria-hidden="true"
                                        >
                                            <i class="bi bi-newspaper"></i>
                                        </div>

                                    <?php else: ?>

                                        <div
                                            class="news-image-placeholder"
                                            aria-hidden="true"
                                        >
                                            <i class="bi bi-newspaper"></i>
                                        </div>

                                    <?php endif; ?>

                                </div>


                                <div class="news-card-body">

                                    <span
                                        class="badge bg-<?= news_h(
                                            $articleCategoryClass
                                        ) ?> mb-3 align-self-start"
                                    >
                                        <?= news_h(
                                            $articleCategory
                                        ) ?>
                                    </span>

                                    <h3 class="news-card-title">
                                        <?= news_h(
                                            $articleTitle
                                        ) ?>
                                    </h3>

                                    <div class="news-meta mb-3">

                                        <span class="news-meta-item">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?= news_date(
                                                $articleDate
                                            ) ?>
                                        </span>

                                        <?php if ($articleAuthor !== ''): ?>

                                            <span class="news-meta-item">
                                                <i class="bi bi-person me-1"></i>
                                                <?= news_h(
                                                    $articleAuthor
                                                ) ?>
                                            </span>

                                        <?php endif; ?>

                                    </div>

                                    <p class="news-card-excerpt">
                                        <?= news_h(
                                            news_excerpt(
                                                $articleContent,
                                                135
                                            )
                                        ) ?>
                                    </p>

                                    <?php if ($articleId > 0): ?>

                                        <a
                                            href="<?= news_h(
                                                url(
                                                    '/news/' .
                                                    $articleId
                                                )
                                            ) ?>"
                                            class="btn btn-outline-primary btn-sm"
                                        >
                                            Read More
                                            <i class="bi bi-arrow-right ms-1"></i>
                                        </a>

                                    <?php endif; ?>

                                </div>

                            </article>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

        </section>

    <?php endif; ?>


    <!-- =====================================================
         NEWSLETTER
         ===================================================== -->

    <section class="newsletter-section">

        <div class="container">

            <div class="row justify-content-center">

                <div class="col-lg-8">

                    <div class="newsletter-card text-center">

                        <i
                            class="bi bi-envelope-paper"
                            style="font-size: 42px; color:#a98e00;"
                        ></i>

                        <h3 class="newsletter-title mt-3">
                            Stay Connected
                        </h3>

                        <p class="newsletter-description">
                            Stay updated with our latest news,
                            events, activities, and community stories.
                        </p>

                        <!--
                            Newsletter backend is not connected yet.
                            This button intentionally does not submit
                            fake information.
                        -->

                        <div class="d-flex justify-content-center">

                            <a
                                href="<?= news_h(url('/contact')) ?>"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-envelope me-1"></i>
                                Contact Us for Updates
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</div>