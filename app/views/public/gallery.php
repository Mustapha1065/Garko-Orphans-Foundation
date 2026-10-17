<?php
/**
 * GOF - Public Gallery
 *
 * Expected controller variable:
 *   $gallery
 */

$gallery = is_array($gallery ?? null) ? $gallery : [];

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

if (!function_exists('gallery_h')) {
    function gallery_h($value): string
    {
        return htmlspecialchars(
            (string)($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

if (!function_exists('gallery_image_url')) {
    function gallery_image_url($image): string
    {
        $image = trim((string)($image ?? ''));

        if ($image === '') {
            return '';
        }

        // Absolute URL
        if (preg_match('#^(https?:)?//#i', $image)) {
            return $image;
        }

        // Already starts from public root
        if (strpos($image, '/') === 0) {
            return url($image);
        }

        /*
         * Gallery uploads are normally stored under
         * public/uploads/gallery/.
         */
        if (
            str_starts_with($image, 'uploads/')
            || str_starts_with($image, 'storage/')
        ) {
            return url('/' . ltrim($image, '/'));
        }

        return url('/uploads/gallery/' . ltrim($image, '/'));
    }
}

if (!function_exists('gallery_date')) {
    function gallery_date($value): string
    {
        if (empty($value)) {
            return '';
        }

        $timestamp = strtotime((string)$value);

        if ($timestamp === false) {
            return '';
        }

        return date('F j, Y', $timestamp);
    }
}

if (!function_exists('gallery_category')) {
    function gallery_category(array $item): string
    {
        $category =
            $item['category']
            ?? $item['type']
            ?? $item['album']
            ?? '';

        return strtolower(trim((string)$category));
    }
}

if (!function_exists('gallery_category_label')) {
    function gallery_category_label($category): string
    {
        $category = trim((string)$category);

        if ($category === '') {
            return 'General';
        }

        return ucwords(
            str_replace(
                ['_', '-'],
                ' ',
                $category
            )
        );
    }
}

if (!function_exists('gallery_description')) {
    function gallery_description(array $item): string
    {
        $description =
            $item['description']
            ?? $item['caption']
            ?? $item['content']
            ?? '';

        $description = trim(
            strip_tags(
                html_entity_decode(
                    (string)$description,
                    ENT_QUOTES,
                    'UTF-8'
                )
            )
        );

        if ($description === '') {
            return 'A visual story from Garko Orphans Foundation.';
        }

        if (
            function_exists('mb_strlen')
            && mb_strlen($description) > 150
        ) {
            return rtrim(
                mb_substr($description, 0, 150)
            ) . '...';
        }

        if (strlen($description) > 150) {
            return rtrim(
                substr($description, 0, 150)
            ) . '...';
        }

        return $description;
    }
}

/*
|--------------------------------------------------------------------------
| Build dynamic categories
|--------------------------------------------------------------------------
*/

$categories = [];

foreach ($gallery as $item) {
    if (!is_array($item)) {
        continue;
    }

    $category = gallery_category($item);

    if ($category !== '') {
        $categories[$category] = gallery_category_label($category);
    }
}

ksort($categories);

/*
|--------------------------------------------------------------------------
| Count
|--------------------------------------------------------------------------
*/

$totalPhotos = count($gallery);
?>

<style>
/* =========================================================
   GOF PUBLIC GALLERY
   ========================================================= */

.gof-gallery-page {
    --gallery-primary: #c9a900;
    --gallery-primary-dark: #a98e00;
    --gallery-dark: #18202b;
    --gallery-muted: #68727d;
    --gallery-border: #e7eaee;
    --gallery-bg: #f7f8fa;
}

/* ---------------------------------------------------------
   Hero
   --------------------------------------------------------- */

.gof-gallery-page .gallery-page-hero {
    background:
        linear-gradient(
            135deg,
            var(--gallery-primary-dark),
            var(--gallery-primary)
        );
    padding: 54px 20px 48px;
}

.gof-gallery-page .gallery-page-hero .page-title {
    font-size: clamp(2rem, 5vw, 3rem);
    font-weight: 800;
    margin-bottom: 10px;
}

.gof-gallery-page .gallery-page-hero .page-subtitle {
    max-width: 700px;
    margin: 0 auto 18px;
    font-size: 1.05rem;
}

/* ---------------------------------------------------------
   Filter section
   --------------------------------------------------------- */

.gof-gallery-page .gallery-filter-section {
    padding: 30px 20px 10px;
    background: #fff;
}

.gof-gallery-page .gallery-filters {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 9px;
}

.gof-gallery-page .gallery-filter-btn {
    border: 1px solid #d8dde2;
    background: #fff;
    color: #505a64;
    border-radius: 50px;
    padding: 9px 18px;
    font-size: .9rem;
    font-weight: 600;
    cursor: pointer;
    transition:
        background .2s ease,
        color .2s ease,
        border-color .2s ease,
        transform .2s ease;
}

.gof-gallery-page .gallery-filter-btn:hover {
    border-color: var(--gallery-primary);
    color: var(--gallery-primary-dark);
    transform: translateY(-1px);
}

.gof-gallery-page .gallery-filter-btn.active {
    background: var(--gallery-primary);
    border-color: var(--gallery-primary);
    color: #fff;
}

/* ---------------------------------------------------------
   Gallery section
   --------------------------------------------------------- */

.gof-gallery-page .gallery-section {
    padding: 50px 20px 70px;
    background: var(--gallery-bg);
}

.gof-gallery-page .gallery-summary {
    color: var(--gallery-muted);
    margin-bottom: 25px;
}

/* ---------------------------------------------------------
   Grid
   --------------------------------------------------------- */

.gof-gallery-page .gallery-grid {
    display: grid;
    grid-template-columns:
        repeat(4, minmax(0, 1fr));
    gap: 22px;
}

.gof-gallery-page .gallery-item {
    min-width: 0;
    transition:
        opacity .2s ease,
        transform .2s ease;
}

.gof-gallery-page .gallery-item.is-hidden {
    display: none;
}

/* ---------------------------------------------------------
   Card
   --------------------------------------------------------- */

.gof-gallery-page .gallery-card {
    position: relative;
    background: #fff;
    border: 1px solid var(--gallery-border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 7px 25px rgba(0,0,0,.05);
    height: 100%;
    cursor: pointer;
    transition:
        transform .25s ease,
        box-shadow .25s ease;
}

.gof-gallery-page .gallery-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,.11);
}

/* ---------------------------------------------------------
   Image
   --------------------------------------------------------- */

.gof-gallery-page .gallery-image {
    position: relative;
    height: 250px;
    overflow: hidden;
    background:
        linear-gradient(
            135deg,
            #eef1f4,
            #dfe4e9
        );
}

.gof-gallery-page .gallery-image img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
    transition: transform .35s ease;
}

.gof-gallery-page .gallery-card:hover .gallery-image img {
    transform: scale(1.06);
}

.gof-gallery-page .gallery-placeholder {
    width: 100%;
    height: 100%;
    min-height: 250px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ba3aa;
}

.gof-gallery-page .gallery-placeholder i {
    font-size: 62px;
}

/* ---------------------------------------------------------
   Overlay
   --------------------------------------------------------- */

.gof-gallery-page .gallery-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: flex-end;
    padding: 18px;
    background:
        linear-gradient(
            to top,
            rgba(0,0,0,.82),
            rgba(0,0,0,.15) 65%,
            transparent
        );
    opacity: 0;
    transition: opacity .25s ease;
}

.gof-gallery-page .gallery-card:hover .gallery-overlay {
    opacity: 1;
}

.gof-gallery-page .gallery-overlay-content {
    width: 100%;
    color: #fff;
}

.gof-gallery-page .gallery-title {
    font-weight: 800;
    margin-bottom: 5px;
}

.gof-gallery-page .gallery-description {
    font-size: .86rem;
    line-height: 1.45;
    margin-bottom: 12px;
    color: rgba(255,255,255,.88);
}

/* ---------------------------------------------------------
   Card information below image
   --------------------------------------------------------- */

.gof-gallery-page .gallery-info {
    padding: 17px 18px;
}

.gof-gallery-page .gallery-info-title {
    color: var(--gallery-dark);
    font-size: 1rem;
    font-weight: 750;
    line-height: 1.4;
    margin-bottom: 7px;
}

.gof-gallery-page .gallery-info-meta {
    color: var(--gallery-muted);
    font-size: .8rem;
}

/* ---------------------------------------------------------
   Empty state
   --------------------------------------------------------- */

.gof-gallery-page .gallery-empty {
    background: #fff;
    border: 1px dashed #ced4da;
    border-radius: 18px;
    padding: 70px 25px;
    text-align: center;
}

.gof-gallery-page .gallery-empty i {
    color: #adb5bd;
    font-size: 65px;
    margin-bottom: 18px;
}

.gof-gallery-page .gallery-empty h3 {
    color: var(--gallery-dark);
    font-weight: 800;
}

/* ---------------------------------------------------------
   CTA
   --------------------------------------------------------- */

.gof-gallery-page .gallery-cta {
    padding: 65px 20px;
    background: #fff;
}

.gof-gallery-page .gallery-cta-card {
    background:
        linear-gradient(
            135deg,
            #fffdf0,
            #fff8cf
        );
    border: 1px solid #efdf8a;
    border-radius: 20px;
    padding: 45px 25px;
}

.gof-gallery-page .gallery-cta-title {
    color: var(--gallery-dark);
    font-weight: 800;
}

/* ---------------------------------------------------------
   Lightbox
   --------------------------------------------------------- */

.gof-gallery-lightbox {
    position: fixed;
    inset: 0;
    z-index: 99999;
    background: rgba(0,0,0,.92);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 25px;
}

.gof-gallery-lightbox.show {
    display: flex;
}

.gof-gallery-lightbox-content {
    position: relative;
    max-width: 1100px;
    width: 100%;
    max-height: 92vh;
    text-align: center;
}

.gof-gallery-lightbox-image {
    max-width: 100%;
    max-height: 75vh;
    object-fit: contain;
    border-radius: 10px;
}

.gof-gallery-lightbox-title {
    color: #fff;
    font-size: 1.15rem;
    font-weight: 700;
    margin-top: 14px;
}

.gof-gallery-lightbox-description {
    color: rgba(255,255,255,.75);
    margin-top: 5px;
}

.gof-gallery-lightbox-close {
    position: absolute;
    top: -15px;
    right: -5px;
    width: 42px;
    height: 42px;
    border: 0;
    border-radius: 50%;
    background: #fff;
    color: #222;
    font-size: 24px;
    cursor: pointer;
}

/* ---------------------------------------------------------
   Responsive
   --------------------------------------------------------- */

@media (max-width: 1199.98px) {
    .gof-gallery-page .gallery-grid {
        grid-template-columns:
            repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 767.98px) {

    .gof-gallery-page .gallery-page-hero {
        padding: 42px 16px 38px;
    }

    .gof-gallery-page .gallery-filter-section,
    .gof-gallery-page .gallery-section,
    .gof-gallery-page .gallery-cta {
        padding-left: 16px;
        padding-right: 16px;
    }

    .gof-gallery-page .gallery-grid {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
        gap: 15px;
    }

    .gof-gallery-page .gallery-image {
        height: 190px;
    }

    .gof-gallery-page .gallery-placeholder {
        min-height: 190px;
    }

    .gof-gallery-page .gallery-placeholder i {
        font-size: 48px;
    }

    .gof-gallery-page .gallery-overlay {
        display: none;
    }

    .gof-gallery-page .gallery-info {
        padding: 14px;
    }

    .gof-gallery-page .gallery-info-title {
        font-size: .92rem;
    }
}

@media (max-width: 480px) {

    .gof-gallery-page .gallery-grid {
        grid-template-columns: 1fr;
    }

    .gof-gallery-page .gallery-image {
        height: 240px;
    }

    .gof-gallery-page .gallery-placeholder {
        min-height: 240px;
    }

    .gof-gallery-page .gallery-filters {
        justify-content: flex-start;
        overflow-x: auto;
        flex-wrap: nowrap;
        padding-bottom: 8px;
    }

    .gof-gallery-page .gallery-filter-btn {
        white-space: nowrap;
    }
}
</style>


<div class="gof-gallery-page">

    <!-- =====================================================
         HERO
         ===================================================== -->

    <section class="gallery-page-hero">

        <div class="container">

            <div class="text-center">

                <h1 class="page-title text-white">
                    Photo Gallery
                </h1>

                <p class="page-subtitle text-white-50">
                    Visual stories of our work and impact
                </p>

                <nav aria-label="breadcrumb">

                    <ol class="breadcrumb justify-content-center mb-0">

                        <li class="breadcrumb-item">
                            <a
                                href="<?= gallery_h(url('/')) ?>"
                                class="text-white-50 text-decoration-none"
                            >
                                Home
                            </a>
                        </li>

                        <li
                            class="breadcrumb-item active text-white"
                            aria-current="page"
                        >
                            Gallery
                        </li>

                    </ol>

                </nav>

            </div>

        </div>

    </section>


    <!-- =====================================================
         FILTERS
         ===================================================== -->

    <?php if ($categories): ?>

        <section class="gallery-filter-section">

            <div class="container">

                <div class="text-center">

                    <div class="gallery-filters">

                        <button
                            type="button"
                            class="gallery-filter-btn active"
                            data-filter="all"
                        >
                            All
                        </button>

                        <?php foreach ($categories as $categoryKey => $categoryLabel): ?>

                            <button
                                type="button"
                                class="gallery-filter-btn"
                                data-filter="<?= gallery_h($categoryKey) ?>"
                            >
                                <?= gallery_h($categoryLabel) ?>
                            </button>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>

        </section>

    <?php endif; ?>


    <!-- =====================================================
         GALLERY
         ===================================================== -->

    <section class="gallery-section">

        <div class="container">

            <?php if ($totalPhotos > 0): ?>

                <div class="gallery-summary">

                    <i class="bi bi-images me-1"></i>

                    <span id="galleryCount">
                        <?= $totalPhotos ?>
                    </span>

                    <?= $totalPhotos === 1 ? 'photo' : 'photos' ?>

                </div>


                <div class="gallery-grid" id="galleryGrid">

                    <?php foreach ($gallery as $index => $item): ?>

                        <?php
                        if (!is_array($item)) {
                            continue;
                        }

                        $id =
                            (int)($item['id'] ?? 0);

                        $title =
                            trim(
                                (string)(
                                    $item['title']
                                    ?? $item['name']
                                    ?? 'Gallery Photo'
                                )
                            );

                        $description =
                            gallery_description($item);

                        $category =
                            gallery_category($item);

                        $categoryLabel =
                            gallery_category_label($category);

                        $image =
                            $item['image']
                            ?? $item['image_path']
                            ?? $item['file_path']
                            ?? $item['photo']
                            ?? $item['filename']
                            ?? '';

                        $imageUrl =
                            gallery_image_url($image);

                        $date =
                            $item['created_at']
                            ?? $item['uploaded_at']
                            ?? $item['date']
                            ?? null;

                        $filterCategory =
                            $category !== ''
                                ? $category
                                : 'general';
                        ?>

                        <div
                            class="gallery-item"
                            data-category="<?= gallery_h(
                                $filterCategory
                            ) ?>"
                        >

                            <div
                                class="gallery-card"
                                <?php if ($imageUrl): ?>
                                    data-image="<?= gallery_h(
                                        $imageUrl
                                    ) ?>"
                                    data-title="<?= gallery_h(
                                        $title
                                    ) ?>"
                                    data-description="<?= gallery_h(
                                        $description
                                    ) ?>"
                                <?php endif; ?>
                            >

                                <!-- Image -->

                                <div class="gallery-image">

                                    <?php if ($imageUrl): ?>

                                        <img
                                            src="<?= gallery_h(
                                                $imageUrl
                                            ) ?>"
                                            alt="<?= gallery_h(
                                                $title
                                            ) ?>"
                                            loading="lazy"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                        >

                                        <div
                                            class="gallery-placeholder"
                                            style="display:none;"
                                            aria-hidden="true"
                                        >
                                            <i class="bi bi-image"></i>
                                        </div>

                                    <?php else: ?>

                                        <div
                                            class="gallery-placeholder"
                                            aria-hidden="true"
                                        >
                                            <i class="bi bi-image"></i>
                                        </div>

                                    <?php endif; ?>

                                </div>


                                <!-- Hover overlay -->

                                <div class="gallery-overlay">

                                    <div class="gallery-overlay-content">

                                        <h5 class="gallery-title">
                                            <?= gallery_h($title) ?>
                                        </h5>

                                        <p class="gallery-description">
                                            <?= gallery_h(
                                                $description
                                            ) ?>
                                        </p>

                                        <?php if ($imageUrl): ?>

                                            <button
                                                type="button"
                                                class="btn btn-light btn-sm gallery-view-btn"
                                            >
                                                <i class="bi bi-eye me-1"></i>
                                                View
                                            </button>

                                        <?php endif; ?>

                                    </div>

                                </div>


                                <!-- Information -->

                                <div class="gallery-info">

                                    <div class="gallery-info-title">
                                        <?= gallery_h($title) ?>
                                    </div>

                                    <div class="gallery-info-meta">

                                        <?php if ($category !== ''): ?>

                                            <span>
                                                <i class="bi bi-tag me-1"></i>
                                                <?= gallery_h(
                                                    $categoryLabel
                                                ) ?>
                                            </span>

                                        <?php endif; ?>

                                        <?php if ($date): ?>

                                            <span class="ms-2">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                <?= gallery_h(
                                                    gallery_date($date)
                                                ) ?>
                                            </span>

                                        <?php endif; ?>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>


                <!-- No results after filtering -->

                <div
                    id="galleryFilterEmpty"
                    class="gallery-empty mt-4"
                    style="display:none;"
                >

                    <i class="bi bi-images"></i>

                    <h3>
                        No Photos Found
                    </h3>

                    <p class="text-muted mb-0">
                        There are no photos in this category yet.
                    </p>

                </div>

            <?php else: ?>

                <!-- No gallery records -->

                <div class="gallery-empty">

                    <i class="bi bi-images"></i>

                    <h3>
                        No Photos Available Yet
                    </h3>

                    <p class="text-muted mb-4">
                        Our gallery is being updated.
                        Please check back later to see photos
                        from our activities and programs.
                    </p>

                    <a
                        href="<?= gallery_h(url('/')) ?>"
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
         CTA
         ===================================================== -->

    <section class="gallery-cta">

        <div class="container">

            <div class="gallery-cta-card text-center">

                <i
                    class="bi bi-heart-fill"
                    style="font-size:42px;color:#c9a900;"
                ></i>

                <h2 class="gallery-cta-title mt-3">
                    Support Our Work
                </h2>

                <p class="text-muted mx-auto mb-4" style="max-width:650px;">
                    Your support helps us continue making a difference
                    in the lives of orphans and vulnerable children.
                </p>

                <a
                    href="<?= gallery_h(url('/donate')) ?>"
                    class="btn btn-primary btn-lg"
                >
                    <i class="bi bi-heart me-1"></i>
                    Donate Now
                </a>

            </div>

        </div>

    </section>

</div>


<!-- =========================================================
     LIGHTBOX
     ========================================================= -->

<div
    class="gof-gallery-lightbox"
    id="galleryLightbox"
    aria-hidden="true"
>

    <div class="gof-gallery-lightbox-content">

        <button
            type="button"
            class="gof-gallery-lightbox-close"
            id="galleryLightboxClose"
            aria-label="Close image"
        >
            &times;
        </button>

        <img
            src=""
            alt=""
            class="gof-gallery-lightbox-image"
            id="galleryLightboxImage"
        >

        <div
            class="gof-gallery-lightbox-title"
            id="galleryLightboxTitle"
        ></div>

        <div
            class="gof-gallery-lightbox-description"
            id="galleryLightboxDescription"
        ></div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =====================================================
       FILTER
       ===================================================== */

    const filterButtons =
        document.querySelectorAll(
            '.gallery-filter-btn'
        );

    const galleryItems =
        document.querySelectorAll(
            '.gallery-item'
        );

    const galleryCount =
        document.getElementById(
            'galleryCount'
        );

    const filterEmpty =
        document.getElementById(
            'galleryFilterEmpty'
        );

    filterButtons.forEach(function (button) {

        button.addEventListener(
            'click',
            function () {

                filterButtons.forEach(
                    function (btn) {
                        btn.classList.remove(
                            'active'
                        );
                    }
                );

                this.classList.add('active');

                const filter =
                    this.getAttribute(
                        'data-filter'
                    );

                let visibleCount = 0;

                galleryItems.forEach(
                    function (item) {

                        const category =
                            item.getAttribute(
                                'data-category'
                            );

                        if (
                            filter === 'all'
                            || category === filter
                        ) {

                            item.classList.remove(
                                'is-hidden'
                            );

                            visibleCount++;

                        } else {

                            item.classList.add(
                                'is-hidden'
                            );
                        }
                    }
                );

                if (galleryCount) {
                    galleryCount.textContent =
                        visibleCount;
                }

                if (filterEmpty) {
                    filterEmpty.style.display =
                        visibleCount === 0
                            ? 'block'
                            : 'none';
                }
            }
        );
    });


    /* =====================================================
       LIGHTBOX
       ===================================================== */

    const lightbox =
        document.getElementById(
            'galleryLightbox'
        );

    const lightboxImage =
        document.getElementById(
            'galleryLightboxImage'
        );

    const lightboxTitle =
        document.getElementById(
            'galleryLightboxTitle'
        );

    const lightboxDescription =
        document.getElementById(
            'galleryLightboxDescription'
        );

    const lightboxClose =
        document.getElementById(
            'galleryLightboxClose'
        );

    const viewButtons =
        document.querySelectorAll(
            '.gallery-view-btn'
        );


    function openLightbox(card) {

        if (!card || !lightbox) {
            return;
        }

        const image =
            card.getAttribute(
                'data-image'
            );

        if (!image) {
            return;
        }

        const title =
            card.getAttribute(
                'data-title'
            ) || '';

        const description =
            card.getAttribute(
                'data-description'
            ) || '';

        lightboxImage.src = image;
        lightboxImage.alt = title;

        lightboxTitle.textContent =
            title;

        lightboxDescription.textContent =
            description;

        lightbox.classList.add('show');

        lightbox.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.style.overflow =
            'hidden';
    }


    function closeLightbox() {

        if (!lightbox) {
            return;
        }

        lightbox.classList.remove(
            'show'
        );

        lightbox.setAttribute(
            'aria-hidden',
            'true'
        );

        lightboxImage.src = '';

        document.body.style.overflow =
            '';
    }


    viewButtons.forEach(
        function (button) {

            button.addEventListener(
                'click',
                function (event) {

                    event.preventDefault();
                    event.stopPropagation();

                    const card =
                        this.closest(
                            '.gallery-card'
                        );

                    openLightbox(card);
                }
            );
        }
    );


    /*
     * Allow clicking the card itself
     * on mobile and desktop.
     */

    document
        .querySelectorAll('.gallery-card')
        .forEach(function (card) {

            card.addEventListener(
                'click',
                function () {

                    if (
                        this.getAttribute(
                            'data-image'
                        )
                    ) {
                        openLightbox(this);
                    }
                }
            );

        });


    if (lightboxClose) {

        lightboxClose.addEventListener(
            'click',
            closeLightbox
        );
    }


    if (lightbox) {

        lightbox.addEventListener(
            'click',
            function (event) {

                if (
                    event.target ===
                    lightbox
                ) {
                    closeLightbox();
                }
            }
        );
    }


    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape'
            ) {
                closeLightbox();
            }

        }
    );

});
</script>