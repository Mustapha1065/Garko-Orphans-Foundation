<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;

class PublicVolunteerController extends BaseController
{
    /**
     * Show public volunteer registration form.
     */
    public function create(): void
    {
        $this->renderPublic('public/volunteer.php');
    }

    /**
     * Submit volunteer application.
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/volunteer');
            return;
        }

        // CSRF protection
        $this->validateCsrf();

        $firstName   = trim($_POST['first_name'] ?? '');
        $lastName    = trim($_POST['last_name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $phone       = trim($_POST['phone'] ?? '');
        $address     = trim($_POST['address'] ?? '');
        $age         = trim($_POST['age'] ?? '');
        $occupation  = trim($_POST['occupation'] ?? '');
        $skills      = trim($_POST['skills'] ?? '');
        $interest    = trim($_POST['interest'] ?? '');
        $availability = trim($_POST['availability'] ?? '');
        $motivation  = trim($_POST['motivation'] ?? '');
        $experience  = trim($_POST['experience'] ?? '');

        $errors = [];

        // Required fields
        if ($firstName === '') {
            $errors[] = 'First name is required.';
        }

        if ($lastName === '') {
            $errors[] = 'Last name is required.';
        }

        if ($email === '') {
            $errors[] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        // Age validation
        if ($age !== '') {
            if (!ctype_digit($age)) {
                $errors[] = 'Age must be a valid number.';
            } else {
                $ageValue = (int) $age;

                if ($ageValue < 13 || $ageValue > 100) {
                    $errors[] = 'Please enter a valid age.';
                }
            }
        }

        // Length protection
        if (mb_strlen($firstName) > 100) {
            $errors[] = 'First name is too long.';
        }

        if (mb_strlen($lastName) > 100) {
            $errors[] = 'Last name is too long.';
        }

        if (mb_strlen($email) > 150) {
            $errors[] = 'Email address is too long.';
        }

        if (mb_strlen($phone) > 20) {
            $errors[] = 'Phone number is too long.';
        }

        if (mb_strlen($occupation) > 100) {
            $errors[] = 'Occupation is too long.';
        }

        if (mb_strlen($interest) > 100) {
            $errors[] = 'Area of interest is too long.';
        }

        if (mb_strlen($availability) > 50) {
            $errors[] = 'Availability value is too long.';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->flash('error', $error);
            }

            $_SESSION['old_volunteer'] = $_POST;

            $this->redirect('/volunteer');
            return;
        }

        $db = Database::get();

        try {
            /*
             * Prevent accidental duplicate applications.
             *
             * An applicant can apply again if their previous application
             * was rejected. Pending/reviewed/approved/contacted applications
             * are considered active.
             */
            $duplicate = $db->prepare("
                SELECT id, status
                FROM volunteer_applications
                WHERE LOWER(email) = LOWER(?)
                AND status IN ('pending', 'reviewed', 'approved', 'contacted')
                LIMIT 1
            ");

            $duplicate->execute([$email]);

            $existing = $duplicate->fetch(\PDO::FETCH_ASSOC);

            if ($existing) {
                $this->flash(
                    'error',
                    'A volunteer application already exists for this email address.'
                );

                $_SESSION['old_volunteer'] = $_POST;

                $this->redirect('/volunteer');
                return;
            }

            /*
             * Insert application.
             *
             * Status is deliberately set to pending.
             */
            $stmt = $db->prepare("
                INSERT INTO volunteer_applications (
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
                    status
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending'
                )
            ");

            $stmt->execute([
                $firstName,
                $lastName,
                $email,
                $phone !== '' ? $phone : null,
                $address !== '' ? $address : null,
                $age !== '' ? (int) $age : null,
                $occupation !== '' ? $occupation : null,
                $skills !== '' ? $skills : null,
                $interest !== '' ? $interest : null,
                $availability !== '' ? $availability : null,
                $motivation !== '' ? $motivation : null,
                $experience !== '' ? $experience : null
            ]);

            // Remove old form data
            unset($_SESSION['old_volunteer']);

            // Store application ID for confirmation page
            $_SESSION['volunteer_application_id'] = $db->lastInsertId();

            $this->redirect('/volunteer/success');
            return;

        } catch (\PDOException $e) {

            error_log(
                'Volunteer application error: ' . $e->getMessage()
            );

            $this->flash(
                'error',
                'Unable to submit your application right now. Please try again.'
            );

            $_SESSION['old_volunteer'] = $_POST;

            $this->redirect('/volunteer');
            return;
        }
    }

    /**
     * Volunteer application success page.
     */
    public function success(): void
    {
        $applicationId = $_SESSION['volunteer_application_id'] ?? null;

        if (!$applicationId) {
            $this->redirect('/volunteer');
            return;
        }

        unset($_SESSION['volunteer_application_id']);

        $this->renderPublic(
            'public/volunteer-success.php',
            [
                'applicationId' => $applicationId
            ]
        );
    }
}