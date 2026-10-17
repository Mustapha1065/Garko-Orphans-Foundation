<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;
use GOF\Services\AuditService;

class PublicController extends BaseController
{
    /* =========================================================
       PUBLIC PAGES
       ========================================================= */

    public function home(): void
    {
        $this->renderPublic('public/home.php', [
            'pageTitle' => 'Home - Garko Orphans Foundation'
        ]);
    }


    public function about(): void
    {
        $this->renderPublic('public/about.php', [
            'pageTitle' => 'About Us - Garko Orphans Foundation'
        ]);
    }


    public function programs(): void
    {
        $programs = [];

        try {
            $db = Database::get();

            $stmt = $db->query("
                SELECT *
                FROM programs
                WHERE is_active = 1
                ORDER BY created_at DESC
            ");

            $programs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Throwable $e) {
            error_log(
                'GOF Public Programs Error: '
                . $e->getMessage()
            );

            $programs = [];
        }

        $this->renderPublic('public/programs.php', [
            'pageTitle' => 'Programs - Garko Orphans Foundation',
            'programs' => $programs
        ]);
    }


    public function events(): void
    {
        $events = [];

        try {
            $db = Database::get();

            $stmt = $db->query("
                SELECT
                    id,
                    title,
                    description,
                    event_date,
                    end_date,
                    event_time,
                    location,
                    status,
                    is_public,
                    image,
                    created_at,
                    updated_at
                FROM events
                WHERE is_public = 1
                ORDER BY
                    CASE status
                        WHEN 'ongoing' THEN 1
                        WHEN 'upcoming' THEN 2
                        WHEN 'completed' THEN 3
                        WHEN 'cancelled' THEN 4
                        ELSE 5
                    END,
                    event_date ASC,
                    event_time ASC
            ");

            $events = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Throwable $e) {
            error_log(
                'GOF Public Events Error: '
                . $e->getMessage()
            );

            $events = [];
        }

        $this->renderPublic('public/events.php', [
            'pageTitle' => 'Events - Garko Orphans Foundation',
            'events' => $events
        ]);
    }


    public function news(): void
    {
        $news = [];

        try {
            $db = Database::get();

            $stmt = $db->query("
                SELECT
                    n.*,
                    CONCAT_WS(
                        ' ',
                        u.first_name,
                        u.last_name
                    ) AS author_name
                FROM news n
                LEFT JOIN users u
                    ON u.id = n.author_id
                WHERE n.is_published = 1
                ORDER BY
                    n.published_at DESC,
                    n.created_at DESC
            ");

            $news = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Throwable $e) {
            error_log(
                'GOF Public News Error: '
                . $e->getMessage()
            );

            $news = [];
        }

        $this->renderPublic('public/news.php', [
            'pageTitle' => 'News - Garko Orphans Foundation',
            'news' => $news
        ]);
    }


    public function newsArticle($id): void
    {
        $article = null;

        try {
            $db = Database::get();

            $stmt = $db->prepare("
                SELECT
                    n.*,
                    CONCAT_WS(
                        ' ',
                        u.first_name,
                        u.last_name
                    ) AS author_name
                FROM news n
                LEFT JOIN users u
                    ON u.id = n.author_id
                WHERE n.id = ?
                  AND n.is_published = 1
                LIMIT 1
            ");

            $stmt->execute([(int)$id]);

            $article = $stmt->fetch(
                \PDO::FETCH_ASSOC
            );

        } catch (\Throwable $e) {
            error_log(
                'GOF Public News Article Error: '
                . $e->getMessage()
            );
        }

        if (!$article) {
            $this->renderPublic('errors/404.php', [
                'pageTitle' =>
                    'News Not Found - Garko Orphans Foundation'
            ]);

            return;
        }

        $this->renderPublic('public/news_article.php', [
            'pageTitle' =>
                $article['title']
                . ' - Garko Orphans Foundation',

            'article' => $article
        ]);
    }


    public function gallery(): void
    {
        $gallery = [];

        try {
            $db = Database::get();

            $stmt = $db->query("
                SELECT *
                FROM gallery
                WHERE is_public = 1
                ORDER BY created_at DESC
            ");

            $gallery = $stmt->fetchAll(
                \PDO::FETCH_ASSOC
            );

        } catch (\Throwable $e) {
            error_log(
                'GOF Public Gallery Error: '
                . $e->getMessage()
            );

            $gallery = [];
        }

        $this->renderPublic('public/gallery.php', [
            'pageTitle' =>
                'Gallery - Garko Orphans Foundation',

            'gallery' => $gallery
        ]);
    }


    /* =========================================================
       CONTACT
       ========================================================= */

    public function contact(): void
    {
        $this->renderPublic('public/contact.php', [
            'pageTitle' =>
                'Contact Us - Garko Orphans Foundation'
        ]);
    }


    public function contactSubmit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/contact');
            return;
        }

        if (!$this->checkPublicCsrf()) {
            $this->publicFormError(
                'Invalid form submission.'
            );
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        $errors = [];

        if ($name === '') {
            $errors[] = 'Name is required.';
        }

        if (
            $email === ''
            || !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $errors[] =
                'A valid email address is required.';
        }

        if ($message === '') {
            $errors[] = 'Message is required.';
        }

        if ($errors) {
            $this->publicFormError(
                implode(' ', $errors)
            );
            return;
        }

        try {
            $db = Database::get();

            $stmt = $db->prepare("
                INSERT INTO contact_messages
                    (
                        name,
                        email,
                        phone,
                        subject,
                        message,
                        status,
                        created_at
                    )
                VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        'unread',
                        NOW()
                    )
            ");

            $stmt->execute([
                $name,
                $email,
                $phone ?: null,
                $subject ?: 'Website Contact',
                $message
            ]);

            $_SESSION['contact_success'] =
                'Thank you. Your message has been sent successfully.';

            $this->redirect('/contact');

        } catch (\Throwable $e) {

            error_log(
                'GOF Contact Submission Error: '
                . $e->getMessage()
            );

            $this->publicFormError(
                'Unable to send your message at the moment. Please try again.'
            );
        }
    }


    /* =========================================================
       VOLUNTEER
       ========================================================= */

    public function volunteer(): void
    {
        $this->renderPublic('public/volunteer.php', [
            'pageTitle' =>
                'Volunteer - Garko Orphans Foundation',

            'volunteerSuccess' =>
                $_SESSION['volunteer_success'] ?? null
        ]);

        /*
         * Remove one-time success message after
         * the page has received it.
         */
        unset($_SESSION['volunteer_success']);
    }


    /**
     * Submit public volunteer application.
     *
     * Generates a unique application reference such as:
     *
     * VOL-2026-00001
     */
    public function volunteerSubmit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/volunteer');
            return;
        }

        if (!$this->checkPublicCsrf()) {
            $this->publicFormError(
                'Invalid form submission.'
            );
            return;
        }

        $firstName = trim(
            $_POST['first_name'] ?? ''
        );

        $lastName = trim(
            $_POST['last_name'] ?? ''
        );

        $email = trim(
            $_POST['email'] ?? ''
        );

        $phone = trim(
            $_POST['phone'] ?? ''
        );

        $address = trim(
            $_POST['address'] ?? ''
        );

        $age = trim(
            $_POST['age'] ?? ''
        );

        $occupation = trim(
            $_POST['occupation'] ?? ''
        );

        $skills = trim(
            $_POST['skills'] ?? ''
        );

        $interest = trim(
            $_POST['interest'] ?? ''
        );

        $availability = trim(
            $_POST['availability'] ?? ''
        );

        $motivation = trim(
            $_POST['motivation'] ?? ''
        );

        $experience = trim(
            $_POST['experience'] ?? ''
        );

        $errors = [];

        if ($firstName === '') {
            $errors[] = 'First name is required.';
        }

        if ($lastName === '') {
            $errors[] = 'Last name is required.';
        }

        if (
            $email === ''
            || !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $errors[] =
                'A valid email address is required.';
        }

        if ($phone === '') {
            $errors[] =
                'Phone number is required.';
        }

        if ($motivation === '') {
            $errors[] =
                'Please tell us why you want to volunteer.';
        }

        if ($age !== '') {
            $ageNumber = filter_var(
                $age,
                FILTER_VALIDATE_INT
            );

            if (
                $ageNumber === false
                || $ageNumber < 13
                || $ageNumber > 100
            ) {
                $errors[] =
                    'Please provide a valid age.';
            }
        }

        if ($errors) {
            $this->publicFormError(
                implode(' ', $errors)
            );
            return;
        }

        try {

            $db = Database::get();

            /*
             * Prevent duplicate active applications
             * from the same email address.
             */
            $duplicate = $db->prepare("
                SELECT
                    id,
                    application_ref,
                    status
                FROM volunteer_applications
                WHERE LOWER(email) = LOWER(?)
                  AND status IN (
                      'pending',
                      'reviewed',
                      'contacted',
                      'approved'
                  )
                LIMIT 1
            ");

            $duplicate->execute([
                $email
            ]);

            $existingApplication =
                $duplicate->fetch(
                    \PDO::FETCH_ASSOC
                );

            if ($existingApplication) {

                $existingRef =
                    $existingApplication['application_ref']
                    ?? '';

                if ($existingRef !== '') {

                    $this->publicFormError(
                        'You already have an active volunteer application. '
                        . 'Your application reference is '
                        . $existingRef
                        . '.'
                    );

                } else {

                    $this->publicFormError(
                        'You already have an active volunteer application with this email address.'
                    );
                }

                return;
            }

            /*
             * Generate unique application reference.
             *
             * Example:
             * VOL-2026-00001
             */
            $year = date('Y');

            do {

                $randomNumber = random_int(
                    1,
                    99999
                );

                $applicationRef =
                    'VOL-'
                    . $year
                    . '-'
                    . str_pad(
                        (string)$randomNumber,
                        5,
                        '0',
                        STR_PAD_LEFT
                    );

                $refCheck = $db->prepare("
                    SELECT id
                    FROM volunteer_applications
                    WHERE application_ref = ?
                    LIMIT 1
                ");

                $refCheck->execute([
                    $applicationRef
                ]);

                $referenceExists =
                    $refCheck->fetchColumn();

            } while ($referenceExists);

            /*
             * Insert application.
             */
            $stmt = $db->prepare("
                INSERT INTO volunteer_applications
                (
                    application_ref,
                    first_name,
                    last_name,
                    email,
                    phone,
                    address,
                    age,
                    occupation,
                    skills,
                    interest,
                    availability,
                    motivation,
                    experience,
                    status,
                    decision_message,
                    created_at,
                    updated_at
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    'pending',
                    NULL,
                    NOW(),
                    NOW()
                )
            ");

            $stmt->execute([
                $applicationRef,
                $firstName,
                $lastName,
                $email,
                $phone ?: null,
                $address ?: null,
                $age !== ''
                    ? (int)$age
                    : null,
                $occupation ?: null,
                $skills ?: null,
                $interest ?: null,
                $availability ?: null,
                $motivation,
                $experience ?: null
            ]);

            $applicationId =
                (int)$db->lastInsertId();

            /*
             * Audit trail.
             */
            try {

                $userId =
                    $_SESSION['user']['id']
                    ?? $_SESSION['user_id']
                    ?? null;

                if ($userId) {

                    (new AuditService())->log(
                        (int)$userId,
                        'create',
                        'volunteer',
                        'volunteer_application',
                        (string)$applicationId,
                        'Public volunteer application submitted: '
                        . $applicationRef
                    );
                }

            } catch (\Throwable $auditError) {

                error_log(
                    'GOF Volunteer Audit Error: '
                    . $auditError->getMessage()
                );
            }

            /*
             * Store successful submission information
             * for the volunteer confirmation message.
             */
            $_SESSION['volunteer_success'] = [
                'message' =>
                    'Thank you for your interest in volunteering with Garko Orphans Foundation. Your application has been submitted successfully.',

                'application_ref' =>
                    $applicationRef,

                'email' =>
                    $email
            ];

            /*
             * Redirect back to volunteer page.
             */
            $this->redirect('/volunteer');

        } catch (\Throwable $e) {

            error_log(
                'GOF Volunteer Submission Error: '
                . $e->getMessage()
            );

            $this->publicFormError(
                'Unable to submit your volunteer application at the moment. Please try again.'
            );
        }
    }


    /**
     * Public volunteer application status page.
     *
     * Volunteer provides:
     *
     * Application Reference
     * Email Address
     */
    public function volunteerStatus(): void
    {
        $application = null;
        $searched = false;
        $error = '';

        $reference =
            trim($_GET['reference'] ?? '');

        $email =
            trim($_GET['email'] ?? '');

        /*
         * Also support POST in case the status page
         * form submits using POST.
         */
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST'
        ) {

            $reference =
                trim(
                    $_POST['reference']
                    ?? $_POST['application_ref']
                    ?? ''
                );

            $email =
                trim(
                    $_POST['email']
                    ?? ''
                );
        }

        if (
            $reference !== ''
            || $email !== ''
        ) {

            $searched = true;

            if ($reference === '') {
                $error =
                    'Please enter your application reference.';
            } elseif ($email === '') {
                $error =
                    'Please enter the email address used during registration.';
            } elseif (
                !filter_var(
                    $email,
                    FILTER_VALIDATE_EMAIL
                )
            ) {
                $error =
                    'Please enter a valid email address.';
            } else {

                try {

                    $db = Database::get();

                    /*
                     * Require BOTH reference and email.
                     *
                     * This prevents someone who only knows
                     * a reference number from viewing another
                     * volunteer's application.
                     */
                    $stmt = $db->prepare("
                        SELECT
                            id,
                            application_ref,
                            first_name,
                            last_name,
                            email,
                            phone,
                            interest,
                            availability,
                            status,
                            decision_message,
                            reviewed_at,
                            created_at,
                            updated_at
                        FROM volunteer_applications
                        WHERE application_ref = ?
                          AND LOWER(email) = LOWER(?)
                        LIMIT 1
                    ");

                    $stmt->execute([
                        $reference,
                        $email
                    ]);

                    $application =
                        $stmt->fetch(
                            \PDO::FETCH_ASSOC
                        );

                    if (!$application) {
                        $error =
                            'No volunteer application was found with that reference and email address.';
                    }

                } catch (\Throwable $e) {

                    error_log(
                        'GOF Volunteer Status Error: '
                        . $e->getMessage()
                    );

                    $error =
                        'Unable to check your application status at the moment.';
                }
            }
        }

        $this->renderPublic(
            'public/volunteer_status.php',
            [
                'pageTitle' =>
                    'Volunteer Application Status - Garko Orphans Foundation',

                'application' =>
                    $application,

                'searched' =>
                    $searched,

                'error' =>
                    $error,

                'reference' =>
                    $reference,

                'email' =>
                    $email
            ]
        );
    }


    /* =========================================================
       PUBLIC DONATION
       ========================================================= */

    public function donate(): void
    {
        $programs = [];

        try {

            $db = Database::get();

            $stmt = $db->query("
                SELECT
                    id,
                    name
                FROM programs
                WHERE is_active = 1
                ORDER BY name ASC
            ");

            $programs =
                $stmt->fetchAll(
                    \PDO::FETCH_ASSOC
                );

        } catch (\Throwable $e) {

            error_log(
                'GOF Public Donation Programs Error: '
                . $e->getMessage()
            );

            $programs = [];
        }

        $this->renderPublic('public/donate.php', [
            'pageTitle' =>
                'Donate - Garko Orphans Foundation',

            'programs' =>
                $programs
        ]);
    }


    public function donateSubmit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/donate');
            return;
        }

        if (!$this->checkPublicCsrf()) {
            $this->publicFormError(
                'Invalid form submission.'
            );
            return;
        }

        $name = trim(
            $_POST['name'] ?? ''
        );

        $email = trim(
            $_POST['email'] ?? ''
        );

        $phone = trim(
            $_POST['phone'] ?? ''
        );

        $amountRaw = trim(
            $_POST['amount'] ?? ''
        );

        $purpose = trim(
            $_POST['purpose'] ?? 'general'
        );

        $paymentMethod = trim(
            $_POST['payment_method']
            ?? $_POST['paymentMethod']
            ?? 'bank'
        );

        $message = trim(
            $_POST['message'] ?? ''
        );

        $anonymous =
            !empty($_POST['anonymous'])
            ? 1
            : 0;

        $programId =
            !empty($_POST['program_id'])
            ? (int)$_POST['program_id']
            : null;

        $errors = [];

        if ($name === '') {
            $errors[] =
                'Name is required.';
        }

        if (
            $email !== ''
            && !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $errors[] =
                'Please provide a valid email address.';
        }

        $amount =
            filter_var(
                $amountRaw,
                FILTER_VALIDATE_FLOAT
            );

        if (
            $amount === false
            || $amount <= 0
        ) {
            $errors[] =
                'Please enter a valid donation amount.';
        }

        $allowedPurposes = [
            'general',
            'education',
            'healthcare',
            'shelter',
            'skills',
            'other'
        ];

        if (
            !in_array(
                $purpose,
                $allowedPurposes,
                true
            )
        ) {
            $purpose = 'general';
        }

        $allowedPaymentMethods = [
            'bank',
            'card',
            'transfer',
            'cash',
            'other'
        ];

        if (
            !in_array(
                $paymentMethod,
                $allowedPaymentMethods,
                true
            )
        ) {
            $paymentMethod = 'bank';
        }

        if ($errors) {
            $this->publicFormError(
                implode(' ', $errors)
            );
            return;
        }

        try {

            $db = Database::get();

            /*
             * Confirm selected program exists.
             */
            if ($programId !== null) {

                $programStmt = $db->prepare("
                    SELECT id
                    FROM programs
                    WHERE id = ?
                      AND is_active = 1
                    LIMIT 1
                ");

                $programStmt->execute([
                    $programId
                ]);

                if (
                    !$programStmt->fetchColumn()
                ) {
                    $programId = null;
                }
            }

            /*
             * Determine actual donor columns.
             */
            $donorColumns = [];

            try {

                $columnsStmt =
                    $db->query(
                        "DESCRIBE donors"
                    );

                foreach (
                    $columnsStmt->fetchAll(
                        \PDO::FETCH_ASSOC
                    ) as $column
                ) {
                    if (
                        isset($column['Field'])
                    ) {
                        $donorColumns[] =
                            $column['Field'];
                    }
                }

            } catch (\Throwable $e) {

                error_log(
                    'GOF Donor Schema Check Error: '
                    . $e->getMessage()
                );
            }

            /*
             * Find existing donor.
             */
            $donorId = null;

            if (
                $email !== ''
                && in_array(
                    'email',
                    $donorColumns,
                    true
                )
            ) {

                $donorStmt = $db->prepare("
                    SELECT id
                    FROM donors
                    WHERE email = ?
                    ORDER BY id ASC
                    LIMIT 1
                ");

                $donorStmt->execute([
                    $email
                ]);

                $existingDonor =
                    $donorStmt->fetch(
                        \PDO::FETCH_ASSOC
                    );

                if ($existingDonor) {
                    $donorId =
                        (int)$existingDonor['id'];
                }
            }

            /*
             * Create donor if necessary.
             */
            if ($donorId === null) {

                $insertColumns = [];
                $insertValues = [];
                $placeholders = [];

                if (
                    in_array(
                        'name',
                        $donorColumns,
                        true
                    )
                ) {
                    $insertColumns[] = 'name';
                    $insertValues[] = $name;
                    $placeholders[] = '?';
                }

                if (
                    in_array(
                        'email',
                        $donorColumns,
                        true
                    )
                ) {
                    $insertColumns[] = 'email';
                    $insertValues[] =
                        $email !== ''
                            ? $email
                            : null;
                    $placeholders[] = '?';
                }

                if (
                    in_array(
                        'phone',
                        $donorColumns,
                        true
                    )
                ) {
                    $insertColumns[] = 'phone';
                    $insertValues[] =
                        $phone !== ''
                            ? $phone
                            : null;
                    $placeholders[] = '?';
                }

                if (
                    in_array(
                        'status',
                        $donorColumns,
                        true
                    )
                ) {
                    $insertColumns[] = 'status';
                    $insertValues[] = 'active';
                    $placeholders[] = '?';
                }

                if (
                    in_array(
                        'notes',
                        $donorColumns,
                        true
                    )
                ) {
                    $insertColumns[] = 'notes';

                    $insertValues[] =
                        $message !== ''
                            ? 'Public website donor. '
                                . $message
                            : 'Public website donor.';

                    $placeholders[] = '?';
                }

                if (
                    in_array(
                        'created_at',
                        $donorColumns,
                        true
                    )
                ) {
                    $insertColumns[] =
                        'created_at';

                    $insertValues[] =
                        date('Y-m-d H:i:s');

                    $placeholders[] = '?';
                }

                if (
                    in_array(
                        'updated_at',
                        $donorColumns,
                        true
                    )
                ) {
                    $insertColumns[] =
                        'updated_at';

                    $insertValues[] =
                        date('Y-m-d H:i:s');

                    $placeholders[] = '?';
                }

                if (
                    empty($insertColumns)
                ) {
                    throw new \RuntimeException(
                        'No compatible donor columns were found.'
                    );
                }

                $sql = "
                    INSERT INTO donors
                    (
                        "
                        . implode(
                            ', ',
                            $insertColumns
                        )
                        . "
                    )
                    VALUES
                    (
                        "
                        . implode(
                            ', ',
                            $placeholders
                        )
                        . "
                    )
                ";

                $insertDonor =
                    $db->prepare($sql);

                $insertDonor->execute(
                    $insertValues
                );

                $donorId =
                    (int)$db->lastInsertId();
            }

            /*
             * Donation schema.
             */
            $donationColumns = [];

            try {

                $columnsStmt =
                    $db->query(
                        "DESCRIBE donations"
                    );

                foreach (
                    $columnsStmt->fetchAll(
                        \PDO::FETCH_ASSOC
                    ) as $column
                ) {
                    if (
                        isset($column['Field'])
                    ) {
                        $donationColumns[] =
                            $column['Field'];
                    }
                }

            } catch (\Throwable $e) {

                error_log(
                    'GOF Donation Schema Check Error: '
                    . $e->getMessage()
                );
            }

            /*
             * Unique donation reference.
             */
            $year = date('Y');

            do {

                $donationRef =
                    'DON-'
                    . $year
                    . '-'
                    . str_pad(
                        (string)random_int(
                            1,
                            99999
                        ),
                        5,
                        '0',
                        STR_PAD_LEFT
                    );

                $refStmt = $db->prepare("
                    SELECT id
                    FROM donations
                    WHERE donation_ref = ?
                    LIMIT 1
                ");

                $refStmt->execute([
                    $donationRef
                ]);

            } while (
                $refStmt->fetchColumn()
            );

            /*
             * Build donation INSERT.
             */
            $columns = [];
            $values = [];
            $placeholders = [];

            $addColumn =
                static function (
                    string $column,
                    $value
                ) use (
                    &$columns,
                    &$values,
                    &$placeholders,
                    $donationColumns
                ): void {

                    if (
                        in_array(
                            $column,
                            $donationColumns,
                            true
                        )
                    ) {
                        $columns[] = $column;
                        $values[] = $value;
                        $placeholders[] = '?';
                    }
                };

            $addColumn(
                'donation_ref',
                $donationRef
            );

            $addColumn(
                'donor_id',
                $donorId
            );

            $addColumn(
                'amount',
                $amount
            );

            $addColumn(
                'currency',
                'NGN'
            );

            $addColumn(
                'donation_date',
                date('Y-m-d')
            );

            $addColumn(
                'payment_method',
                $paymentMethod
            );

            $addColumn(
                'purpose',
                $purpose
            );

            $addColumn(
                'program_id',
                $programId
            );

            $addColumn(
                'notes',
                $message !== ''
                    ? $message
                    : null
            );

            $addColumn(
                'status',
                'pending'
            );

            if (
                in_array(
                    'is_anonymous',
                    $donationColumns,
                    true
                )
            ) {
                $addColumn(
                    'is_anonymous',
                    $anonymous
                );
            }

            if (
                in_array(
                    'anonymous',
                    $donationColumns,
                    true
                )
            ) {
                $addColumn(
                    'anonymous',
                    $anonymous
                );
            }

            if (
                in_array(
                    'created_at',
                    $donationColumns,
                    true
                )
            ) {
                $addColumn(
                    'created_at',
                    date('Y-m-d H:i:s')
                );
            }

            if (
                in_array(
                    'updated_at',
                    $donationColumns,
                    true
                )
            ) {
                $addColumn(
                    'updated_at',
                    date('Y-m-d H:i:s')
                );
            }

            if (empty($columns)) {
                throw new \RuntimeException(
                    'No compatible donation columns were found.'
                );
            }

            $sql = "
                INSERT INTO donations
                (
                    "
                    . implode(
                        ', ',
                        $columns
                    )
                    . "
                )
                VALUES
                (
                    "
                    . implode(
                        ', ',
                        $placeholders
                    )
                    . "
                )
            ";

            $insertDonation =
                $db->prepare($sql);

            $insertDonation->execute(
                $values
            );

            $donationId =
                (int)$db->lastInsertId();

            /*
             * Audit.
             */
            try {

                $userId =
                    $_SESSION['user']['id']
                    ?? $_SESSION['user_id']
                    ?? null;

                if ($userId) {

                    (new AuditService())->log(
                        (int)$userId,
                        'create',
                        'donation',
                        'donation',
                        (string)$donationId,
                        'Public donation submitted: '
                        . $donationRef
                    );
                }

            } catch (\Throwable $auditError) {

                error_log(
                    'GOF Public Donation Audit Error: '
                    . $auditError->getMessage()
                );
            }

            /*
             * Donation success data.
             */
            $_SESSION['donation_success'] = [
                'donation_id' =>
                    $donationId,

                'donation_ref' =>
                    $donationRef,

                'name' =>
                    $name,

                'amount' =>
                    $amount,

                'currency' =>
                    'NGN',

                'payment_method' =>
                    $paymentMethod,

                'purpose' =>
                    $purpose
            ];

            $this->redirect(
                '/donate/success'
            );

        } catch (\Throwable $e) {

            error_log(
                'GOF Public Donation Error: '
                . $e->getMessage()
            );

            $this->publicFormError(
                'Unable to submit your donation at the moment. Please try again.'
            );
        }
    }


    public function donationSuccess(): void
    {
        $donation =
            $_SESSION['donation_success']
            ?? null;

        if (!$donation) {
            $this->redirect('/donate');
            return;
        }

        unset(
            $_SESSION['donation_success']
        );

        $this->renderPublic(
            'public/donation_success.php',
            [
                'pageTitle' =>
                    'Donation Submitted - Garko Orphans Foundation',

                'donation' =>
                    $donation
            ]
        );
    }


    /* =========================================================
       CSRF
       ========================================================= */

    private function checkPublicCsrf(): bool
    {
        if (
            session_status()
            !== PHP_SESSION_ACTIVE
        ) {
            @session_start();
        }

        $submittedToken =
            $_POST['_token']
            ?? $_POST['_csrf']
            ?? $_POST['csrf_token']
            ?? '';

        $sessionTokens = [];

        if (
            !empty(
                $_SESSION['csrf_token']
            )
        ) {
            $sessionTokens[] =
                (string)$_SESSION['csrf_token'];
        }

        if (
            !empty(
                $_SESSION['_csrf']
            )
        ) {
            $sessionTokens[] =
                (string)$_SESSION['_csrf'];
        }

        if (
            !empty(
                $_SESSION['csrf']
            )
        ) {
            $sessionTokens[] =
                (string)$_SESSION['csrf'];
        }

        /*
         * Official CSRF helper.
         */
        try {

            if (
                class_exists(
                    \GOF\Helpers\Csrf::class
                )
            ) {

                $helperToken =
                    \GOF\Helpers\Csrf::token();

                if (
                    !empty($helperToken)
                ) {
                    $sessionTokens[] =
                        (string)$helperToken;
                }
            }

        } catch (\Throwable $e) {

            error_log(
                'GOF CSRF Helper Error: '
                . $e->getMessage()
            );
        }

        if (
            $submittedToken === ''
            || empty($sessionTokens)
        ) {
            return false;
        }

        foreach (
            $sessionTokens as $token
        ) {

            if (
                $token !== ''
                && hash_equals(
                    $token,
                    (string)$submittedToken
                )
            ) {
                return true;
            }
        }

        return false;
    }


    /* =========================================================
       PUBLIC FORM ERROR
       ========================================================= */

    private function publicFormError(
        string $message
    ): void {

        if (
            session_status()
            !== PHP_SESSION_ACTIVE
        ) {
            @session_start();
        }

        $_SESSION['public_form_error'] =
            $message;

        $back =
            $_SERVER['HTTP_REFERER']
            ?? '';

        if ($back !== '') {

            $parsed =
                parse_url($back);

            if (
                isset($parsed['path'])
                && is_string(
                    $parsed['path']
                )
                && $parsed['path'] !== ''
            ) {

                $this->redirect(
                    $parsed['path']
                );

                return;
            }
        }

        $this->redirect('/');
    }
}