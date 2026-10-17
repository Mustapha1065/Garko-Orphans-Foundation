<?php
/**
 * GOF - Public News Article Page
 *
 * Expected controller data:
 *   $article
 */

$article = is_array($article ?? null) ? $article : [];

if (!function_exists('article_h')) {
    function article_h($value): string
    {
        return htmlspecialchars(
            (string)($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

if (!function_exists('article_date')) {
    function article_date($value): string
    {
        if (empty($value)) {
            return 'Date unavailable';
        }

        $timestamp = strtotime((string)$value);

        if ($timestamp === false) {
            return article_h($value);
        }

        return date('F j, Y', $timestamp);
    }
}

if (!function_exists('article_image_url')) {
    function article_image_url($image): string
    {
        $image = trim((string)($image ?? ''));

        if ($image === '') {
            return '';
        }

        if (preg_match('#^(https?:)?//#i', $image)) {
            return $image;
        }

        if (strpos($image, '/') === 0) {
            return url($image);
        }

        return url('/uploads/news/' . ltrim($image, '/'));
    }
}

if (!function_exists('article_category')) {
    function article_category(array $article): string
    {
        $category =
            $article['category']
            ?? $article['type']
            ?? '';

        return trim((string)$category) !== ''
            ? trim((string)$category)
            : 'News';
    }
}

$title = trim(
    (string)(
        $article['title']
        ?? 'News Article'
    )
);

$content =
    $article['content']
    ?? $article['body']
    ?? $article['description']
    ?? '';

$excerpt =
    $article['excerpt']
    ?? $article['summary']
    ?? '';

$date =
    $article['published_at']
    ?? $article['created_at']
    ?? null;

$author = trim(
    (string)(
        $article['author_name']
        ?? 'Garko Orphans Foundation'
    )
);

$category = article_category($article);

$image =
    $article['image']
    ?? $article['featured_image']
    ?? null;

$imageUrl = article_image_url($image);

$articleId = (int)($article['id'] ?? 0);
?>

<style>
/* =========================================================
   GOF PUBLIC NEWS ARTICLE
   ========================================================= */

.gof-news-article {
    --article-primary: #c9a900;
    --article-primary-dark: #a98e00;
    --article-dark: #18202b;
    --article-muted: #68727d;
    --article-border: #e7eaee;
    --article-bg: #f7f8fa;
}

/* ---------------------------------------------------------
   Hero
   --------------------------------------------------------- */

.gof-news-article .article-hero {
    background:
        linear-gradient(
            135deg,
            var(--article-primary-dark),
            var(--article-primary)
        );
    padding: 48px 20px;
}

.gof-news-article .article-hero-inner {
    max-width: 900px;
    margin: 0 auto;
}

.gof-news-article .article-category {
    display: inline-block;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff;
    border-radius: 50px;
    padding: 7px 15px;
    font-size: .8rem;
    font-weight: 700;
    margin-bottom: 16px;
}

.gof-news-article .article-title {
    color: #fff;
    font-size: clamp(2rem, 5vw, 3.3rem);
    line-height: 1.18;
    font-weight: 800;
    margin-bottom: 20px;
}

.gof-news-article .article-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
    color: rgba(255,255,255,.85);
    font-size: .92rem;
}

/* ---------------------------------------------------------
   Article area
   --------------------------------------------------------- */

.gof-news-article .article-section {
    padding: 65px 20px;
    background: var(--article-bg);
}

.gof-news-article .article-wrapper {
    max-width: 900px;
    margin: 0 auto;
}

.gof-news-article .article-card {
    background: #fff;
    border: 1px solid var(--article-border);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 12px 40px rgba(0,0,0,.06);
}

/* ---------------------------------------------------------
   Featured image
   --------------------------------------------------------- */

.gof-news-article .article-image {
    width: 100%;
    max-height: 520px;
    overflow: hidden;
    background:
        linear-gradient(
            135deg,
            #eef1f4,
            #dfe4e9
        );
}

.gof-news-article .article-image img {
    width: 100%;
    max-height: 520px;
    object-fit: cover;
    display: block;
}

.gof-news-article .article-image-placeholder {
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9aa2aa;
}

.gof-news-article .article-image-placeholder i {
    font-size: 80px;
}

/* ---------------------------------------------------------
   Content
   --------------------------------------------------------- */

.gof-news-article .article-content {
    padding: 42px;
}

.gof-news-article .article-excerpt {
    color: #59636e;
    font-size: 1.08rem;
    line-height: 1.8;
    padding-bottom: 24px;
    margin-bottom: 28px;
    border-bottom: 1px solid var(--article-border);
}

.gof-news-article .article-body {
    color: #343a40;
    font-size: 1.05rem;
    line-height: 1.9;
    overflow-wrap: break-word;
}

.gof-news-article .article-body p {
    margin-bottom: 1.35rem;
}

.gof-news-article .article-body h2,
.gof-news-article .article-body h3,
.gof-news-article .article-body h4 {
    color: var(--article-dark);
    font-weight: 800;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.gof-news-article .article-body ul,
.gof-news-article .article-body ol {
    padding-left: 1.5rem;
    margin-bottom: 1.5rem;
}

.gof-news-article .article-body img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
}

.gof-news-article .article-body blockquote {
    border-left: 4px solid var(--article-primary);
    padding: 15px 20px;
    margin: 25px 0;
    background: #fffbea;
    color: #59636e;
}

/* ---------------------------------------------------------
   Bottom actions
   --------------------------------------------------------- */

.gof-news-article .article-actions {
    margin-top: 35px;
    padding-top: 25px;
    border-top: 1px solid var(--article-border);
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

/* ---------------------------------------------------------
   Share area
   --------------------------------------------------------- */

.gof-news-article .article-share {
    margin-top: 30px;
    padding: 22px;
    background: #f8f9fa;
    border-radius: 14px;
}

.gof-news-article .article-share-title {
    color: var(--article-dark);
    font-weight: 700;
    margin-bottom: 12px;
}

/* ---------------------------------------------------------
   Mobile
   --------------------------------------------------------- */

@media (max-width: 767.98px) {

    .gof-news-article .article-hero {
        padding: 40px 16px;
    }

    .gof-news-article .article-section {
        padding: 40px 16px;
    }

    .gof-news-article .article-content {
        padding: 25px 20px;
    }

    .gof-news-article .article-title {
        font-size: 2rem;
    }

    .gof-news-article .article-body {
        font-size: 1rem;
        line-height: 1.8;
    }

    .gof-news-article .article-meta {
        gap: 10px;
    }

    .gof-news-article .article-actions .btn {
        width: 100%;
    }
}
</style>

<div class="gof-news-article">

    <!-- =====================================================
         ARTICLE HERO
         ===================================================== -->

    <section class="article-hero">

        <div class="article-hero-inner">

            <span class="article-category">
                <?= article_h($category) ?>
            </span>

            <h1 class="article-title">
                <?= article_h($title) ?>
            </h1>

            <div class="article-meta">

                <span>
                    <i class="bi bi-calendar3 me-1"></i>
                    <?= article_date($date) ?>
                </span>

                <?php if ($author !== ''): ?>

                    <span>
                        <i class="bi bi-person me-1"></i>
                        <?= article_h($author) ?>
                    </span>

                <?php endif; ?>

            </div>

        </div>

    </section>


    <!-- =====================================================
         ARTICLE
         ===================================================== -->

    <section class="article-section">

        <div class="article-wrapper">

            <article class="article-card">

                <!-- Featured image -->

                <?php if ($imageUrl): ?>

                    <div class="article-image">

                        <img
                            src="<?= article_h($imageUrl) ?>"
                            alt="<?= article_h($title) ?>"
                            loading="eager"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        >

                        <div
                            class="article-image-placeholder"
                            style="display:none;"
                            aria-hidden="true"
                        >
                            <i class="bi bi-newspaper"></i>
                        </div>

                    </div>

                <?php endif; ?>


                <div class="article-content">

                    <!-- Excerpt -->

                    <?php if (trim(strip_tags((string)$excerpt)) !== ''): ?>

                        <div class="article-excerpt">
                            <?= nl2br(
                                article_h($excerpt)
                            ) ?>
                        </div>

                    <?php endif; ?>


                    <!-- Article body -->

                    <div class="article-body">

                        <?php if (trim((string)$content) !== ''): ?>

                            <?php
                            /*
                             * News content may contain formatted HTML
                             * from the admin editor.
                             *
                             * Do not htmlspecialchars() the complete
                             * content here, otherwise paragraphs,
                             * headings and lists would appear as text.
                             */
                            ?>

                            <?= $content ?>

                        <?php else: ?>

                            <p class="text-muted">
                                The full article content is not available.
                            </p>

                        <?php endif; ?>

                    </div>


                    <!-- Share -->

                    <div class="article-share">

                        <div class="article-share-title">
                            <i class="bi bi-share me-1"></i>
                            Share this article
                        </div>

                        <div class="d-flex flex-wrap gap-2">

                            <button
                                type="button"
                                class="btn btn-outline-secondary btn-sm"
                                onclick="copyNewsArticleLink()"
                            >
                                <i class="bi bi-link-45deg me-1"></i>
                                Copy Link
                            </button>

                        </div>

                    </div>


                    <!-- Actions -->

                    <div class="article-actions">

                        <a
                            href="<?= article_h(
                                url('/news')
                            ) ?>"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-arrow-left me-1"></i>
                            Back to News
                        </a>

                        <a
                            href="<?= article_h(
                                url('/')
                            ) ?>"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-house me-1"></i>
                            Home
                        </a>

                    </div>

                </div>

            </article>

        </div>

    </section>

</div>


<script>
function copyNewsArticleLink() {

    const articleUrl = window.location.href;

    if (navigator.clipboard && window.isSecureContext) {

        navigator.clipboard.writeText(articleUrl)
            .then(function () {
                alert('Article link copied.');
            })
            .catch(function () {
                fallbackCopyArticleLink(articleUrl);
            });

        return;
    }

    fallbackCopyArticleLink(articleUrl);
}

function fallbackCopyArticleLink(text) {

    const textarea = document.createElement('textarea');

    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.left = '-9999px';

    document.body.appendChild(textarea);

    textarea.focus();
    textarea.select();

    try {
        document.execCommand('copy');
        alert('Article link copied.');
    } catch (error) {
        alert('Unable to copy the link. Please copy it from the browser address bar.');
    }

    document.body.removeChild(textarea);
}
</script>