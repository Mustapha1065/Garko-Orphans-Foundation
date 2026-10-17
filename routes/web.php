<?php

return [

    // =========================================================
    // PUBLIC SITE
    // =========================================================
    ['GET', '/', 'PublicController@home'],
    ['GET', '/about', 'PublicController@about'],
    ['GET', '/programs', 'PublicController@programs'],
    ['GET', '/events', 'PublicController@events'],
    ['GET', '/news', 'PublicController@news'],
    ['GET', '/news/{slug}', 'PublicController@newsArticle'],
    ['GET', '/gallery', 'PublicController@gallery'],

    // Contact
    ['GET', '/contact', 'PublicController@contact'],
    ['POST', '/contact', 'PublicController@contactSubmit'],

    // Volunteer
    ['GET', '/volunteer', 'PublicController@volunteer'],
    ['POST', '/volunteer', 'PublicController@volunteerSubmit'],

    // Donation
    ['GET', '/donate', 'PublicController@donate'],
    ['POST', '/donate', 'PublicController@donateSubmit'],
    ['GET', '/donate/success', 'PublicController@donationSuccess'],

    // Public ID verification
    ['GET', '/verify', 'VerifyController@verify'],


    // =========================================================
    // AUTH
    // =========================================================
    ['GET', '/login', 'AuthController@showLogin'],
    ['POST', '/login', 'AuthController@login'],
    ['GET', '/logout', 'AuthController@logout'],
    ['GET', '/forgot-password', 'AuthController@showForgotPassword'],
    ['POST', '/forgot-password', 'AuthController@forgotPassword'],
    ['GET', '/reset-password', 'AuthController@showResetPassword'],
    ['POST', '/reset-password', 'AuthController@resetPassword'],


    // =========================================================
    // ADMIN DASHBOARD
    // =========================================================
    ['GET', '/admin', 'AdminController@index', [
        'middleware' => 'auth'
    ]],


    // =========================================================
    // USERS
    // =========================================================
    ['GET', '/admin/users', 'UsersController@index', [
        'middleware' => 'permission:manage_users'
    ]],
    ['GET', '/admin/users/create', 'UsersController@create', [
        'middleware' => 'permission:manage_users'
    ]],
    ['POST', '/admin/users', 'UsersController@store', [
        'middleware' => 'permission:manage_users'
    ]],
    ['GET', '/admin/users/{id}', 'UsersController@show', [
        'middleware' => 'permission:manage_users'
    ]],
    ['GET', '/admin/users/{id}/edit', 'UsersController@edit', [
        'middleware' => 'permission:manage_users'
    ]],
    ['POST', '/admin/users/{id}/update', 'UsersController@update', [
        'middleware' => 'permission:manage_users'
    ]],
    ['POST', '/admin/users/{id}/deactivate', 'UsersController@deactivate', [
        'middleware' => 'permission:manage_users'
    ]],
    ['POST', '/admin/users/{id}/activate', 'UsersController@activate', [
        'middleware' => 'permission:manage_users'
    ]],


    // =========================================================
    // STAFF
    // =========================================================
    ['GET', '/admin/staff', 'StaffController@index', [
        'middleware' => 'permission:manage_staff'
    ]],
    ['GET', '/admin/staff/create', 'StaffController@create', [
        'middleware' => 'permission:manage_staff'
    ]],
    ['POST', '/admin/staff', 'StaffController@store', [
        'middleware' => 'permission:manage_staff'
    ]],
    ['GET', '/admin/staff/{id}', 'StaffController@show', [
        'middleware' => 'permission:manage_staff'
    ]],
    ['GET', '/admin/staff/{id}/edit', 'StaffController@edit', [
        'middleware' => 'permission:manage_staff'
    ]],
    ['POST', '/admin/staff/{id}/update', 'StaffController@update', [
        'middleware' => 'permission:manage_staff'
    ]],


    // =========================================================
    // BENEFICIARIES
    // =========================================================
    ['GET', '/admin/beneficiaries', 'BeneficiariesController@index', [
        'middleware' => 'permission:view_beneficiaries'
    ]],
    ['GET', '/admin/beneficiaries/create', 'BeneficiariesController@create', [
        'middleware' => 'permission:create_beneficiary'
    ]],
    ['POST', '/admin/beneficiaries', 'BeneficiariesController@store', [
        'middleware' => 'permission:create_beneficiary'
    ]],
    ['GET', '/admin/beneficiaries/{id}', 'BeneficiariesController@show', [
        'middleware' => 'permission:view_beneficiaries'
    ]],
    ['GET', '/admin/beneficiaries/{id}/edit', 'BeneficiariesController@edit', [
        'middleware' => 'permission:edit_beneficiary'
    ]],
    ['POST', '/admin/beneficiaries/{id}/update', 'BeneficiariesController@update', [
        'middleware' => 'permission:edit_beneficiary'
    ]],
    ['POST', '/admin/beneficiaries/{id}/approve', 'BeneficiariesController@approve', [
        'middleware' => 'permission:approve_beneficiary'
    ]],
    ['POST', '/admin/beneficiaries/{id}/reject', 'BeneficiariesController@reject', [
        'middleware' => 'permission:approve_beneficiary'
    ]],
    ['POST', '/admin/beneficiaries/{id}/archive', 'BeneficiariesController@archive', [
        'middleware' => 'permission:archive_beneficiary'
    ]],


    // =========================================================
    // SUPPORT / EDUCATION
    // =========================================================
    ['GET', '/admin/beneficiaries/{id}/support', 'SupportController@index', [
        'middleware' => 'permission:manage_support'
    ]],
    ['POST', '/admin/beneficiaries/{id}/support', 'SupportController@store', [
        'middleware' => 'permission:manage_support'
    ]],
    ['GET', '/admin/beneficiaries/{id}/education', 'EducationController@index', [
        'middleware' => 'permission:manage_education'
    ]],
    ['POST', '/admin/beneficiaries/{id}/education', 'EducationController@store', [
        'middleware' => 'permission:manage_education'
    ]],


    // =========================================================
    // DONORS
    // =========================================================
    ['GET', '/admin/donors', 'DonorsController@index', [
        'middleware' => 'permission:view_donors'
    ]],
    ['GET', '/admin/donors/create', 'DonorsController@create', [
        'middleware' => 'permission:manage_donors'
    ]],
    ['POST', '/admin/donors', 'DonorsController@store', [
        'middleware' => 'permission:manage_donors'
    ]],
    ['GET', '/admin/donors/{id}', 'DonorsController@show', [
        'middleware' => 'permission:view_donors'
    ]],
    ['GET', '/admin/donors/{id}/edit', 'DonorsController@edit', [
        'middleware' => 'permission:manage_donors'
    ]],
    ['POST', '/admin/donors/{id}/update', 'DonorsController@update', [
        'middleware' => 'permission:manage_donors'
    ]],


    // =========================================================
    // DONATIONS
    // =========================================================
    ['GET', '/admin/donations', 'DonationsController@index', [
        'middleware' => 'permission:view_donations'
    ]],
    ['GET', '/admin/donations/create', 'DonationsController@create', [
        'middleware' => 'permission:manage_donations'
    ]],
    ['POST', '/admin/donations', 'DonationsController@store', [
        'middleware' => 'permission:manage_donations'
    ]],
    ['GET', '/admin/donations/{id}', 'DonationsController@show', [
        'middleware' => 'permission:view_donations'
    ]],
    ['POST', '/admin/donations/{id}/confirm', 'DonationsController@confirm', [
        'middleware' => 'permission:manage_donations'
    ]],
    ['POST', '/admin/donations/{id}/reject', 'DonationsController@reject', [
        'middleware' => 'permission:manage_donations'
    ]],
    ['GET', '/admin/donations/{id}/receipt', 'DonationsController@receipt', [
        'middleware' => 'permission:view_donations'
    ]],


    // =========================================================
    // PROGRAMS
    // =========================================================
    ['GET', '/admin/programs', 'ProgramsController@index', [
        'middleware' => 'permission:manage_programs'
    ]],
    ['GET', '/admin/programs/create', 'ProgramsController@create', [
        'middleware' => 'permission:manage_programs'
    ]],
    ['POST', '/admin/programs', 'ProgramsController@store', [
        'middleware' => 'permission:manage_programs'
    ]],
    ['GET', '/admin/programs/{id}', 'ProgramsController@show', [
        'middleware' => 'permission:manage_programs'
    ]],
    ['GET', '/admin/programs/{id}/edit', 'ProgramsController@edit', [
        'middleware' => 'permission:manage_programs'
    ]],
    ['POST', '/admin/programs/{id}/update', 'ProgramsController@update', [
        'middleware' => 'permission:manage_programs'
    ]],


    // =========================================================
    // EVENTS
    // =========================================================
    ['GET', '/admin/events', 'EventsController@index', [
        'middleware' => 'permission:manage_events'
    ]],
    ['GET', '/admin/events/create', 'EventsController@create', [
        'middleware' => 'permission:manage_events'
    ]],
    ['POST', '/admin/events', 'EventsController@store', [
        'middleware' => 'permission:manage_events'
    ]],
    ['GET', '/admin/events/{id}/edit', 'EventsController@edit', [
        'middleware' => 'permission:manage_events'
    ]],
    ['POST', '/admin/events/{id}/update', 'EventsController@update', [
        'middleware' => 'permission:manage_events'
    ]],


    // =========================================================
    // NEWS
    // =========================================================
    ['GET', '/admin/news', 'NewsController@index', [
        'middleware' => 'permission:manage_news'
    ]],
    ['GET', '/admin/news/create', 'NewsController@create', [
        'middleware' => 'permission:manage_news'
    ]],
    ['POST', '/admin/news', 'NewsController@store', [
        'middleware' => 'permission:manage_news'
    ]],
    ['GET', '/admin/news/{id}/edit', 'NewsController@edit', [
        'middleware' => 'permission:manage_news'
    ]],
    ['POST', '/admin/news/{id}/update', 'NewsController@update', [
        'middleware' => 'permission:manage_news'
    ]],
    ['POST', '/admin/news/{id}/publish', 'NewsController@publish', [
        'middleware' => 'permission:manage_news'
    ]],


    // =========================================================
    // GALLERY
    // =========================================================
    ['GET', '/admin/gallery', 'GalleryController@index', [
        'middleware' => 'permission:manage_gallery'
    ]],
    ['GET', '/admin/gallery/create', 'GalleryController@create', [
        'middleware' => 'permission:manage_gallery'
    ]],
    ['POST', '/admin/gallery', 'GalleryController@store', [
        'middleware' => 'permission:manage_gallery'
    ]],
    ['POST', '/admin/gallery/{id}/upload', 'GalleryController@upload', [
        'middleware' => 'permission:manage_gallery'
    ]],


    // =========================================================
    // ID CARDS
    // =========================================================
    ['GET', '/admin/idcards', 'IdCardsController@index', [
        'middleware' => 'permission:view_id_cards'
    ]],
    ['POST', '/admin/idcards/generate', 'IdCardsController@generate', [
        'middleware' => 'permission:generate_id_cards'
    ]],
    ['GET', '/admin/idcards/{id}', 'IdCardsController@show', [
        'middleware' => 'permission:view_id_cards'
    ]],
    ['GET', '/admin/idcards/{id}/pdf', 'IdCardsController@downloadPdf', [
        'middleware' => 'permission:view_id_cards'
    ]],
    ['GET', '/admin/idcards/{id}/print', 'IdCardsController@printCard', [
        'middleware' => 'permission:view_id_cards'
    ]],
    ['POST', '/admin/idcards/{id}/revoke', 'IdCardsController@revoke', [
        'middleware' => 'permission:revoke_id_cards'
    ]],
    ['POST', '/admin/idcards/{id}/reissue', 'IdCardsController@reissue', [
        'middleware' => 'permission:generate_id_cards'
    ]],
    ['GET', '/admin/idcards/tokens', 'IdCardsController@tokens', [
        'middleware' => 'permission:view_id_cards'
    ]],
    ['POST', '/admin/idcards/tokens/revoke', 'IdCardsController@revokeToken', [
        'middleware' => 'permission:revoke_id_cards'
    ]],
    ['GET', '/admin/idcards/attempts', 'IdCardsController@attempts', [
        'middleware' => 'permission:view_id_cards'
    ]],
    ['POST', '/admin/idcards/block', 'IdCardsController@blockIp', [
        'middleware' => 'permission:revoke_id_cards'
    ]],
    ['POST', '/admin/idcards/unblock', 'IdCardsController@unblockIp', [
        'middleware' => 'permission:revoke_id_cards'
    ]],
    ['GET', '/admin/idcards/analytics', 'IdCardsController@analytics', [
        'middleware' => 'permission:view_id_cards'
    ]],


    // =========================================================
    // DOCUMENTS
    // =========================================================
    ['POST', '/admin/documents/upload', 'DocumentsController@upload', [
        'middleware' => 'permission:manage_documents'
    ]],
    ['GET', '/admin/documents/{id}/download', 'DocumentsController@download', [
        'middleware' => 'auth'
    ]],
    ['POST', '/admin/documents/{id}/delete', 'DocumentsController@delete', [
        'middleware' => 'permission:manage_documents'
    ]],


    // =========================================================
    // REPORTS
    // =========================================================
    ['GET', '/admin/reports', 'ReportsController@index', [
        'middleware' => 'permission:view_reports'
    ]],
    ['GET', '/admin/reports/beneficiaries', 'ReportsController@beneficiaries', [
        'middleware' => 'permission:view_reports'
    ]],
    ['GET', '/admin/reports/donations', 'ReportsController@donations', [
        'middleware' => 'permission:view_reports'
    ]],
    ['GET', '/admin/reports/donors', 'ReportsController@donors', [
        'middleware' => 'permission:view_reports'
    ]],
    ['GET', '/admin/reports/staff', 'ReportsController@staff', [
        'middleware' => 'permission:view_reports'
    ]],
    ['GET', '/admin/reports/idcards', 'ReportsController@idcards', [
        'middleware' => 'permission:view_reports'
    ]],


    // =========================================================
    // CONTACTS
    // =========================================================
    ['GET', '/admin/contacts', 'ContactsController@index', [
        'middleware' => 'permission:manage_contacts'
    ]],
    ['POST', '/admin/contacts/{id}/mark-read', 'ContactsController@markRead', [
        'middleware' => 'permission:manage_contacts'
    ]],


    // =========================================================
    // VOLUNTEERS
    // =========================================================
    ['GET', '/admin/volunteers', 'VolunteersController@index', [
        'middleware' => 'permission:manage_volunteers'
    ]],
    ['POST', '/admin/volunteers/{id}/review', 'VolunteersController@review', [
        'middleware' => 'permission:manage_volunteers'
    ]],


    // =========================================================
    // AUDIT
    // =========================================================
    ['GET', '/admin/audit', 'AuditController@index', [
        'middleware' => 'permission:view_audit_logs'
    ]],


    // =========================================================
    // NOTIFICATIONS
    // =========================================================
    ['GET', '/admin/notifications', 'NotificationsController@index', [
        'middleware' => 'auth'
    ]],
    ['POST', '/admin/notifications/mark-all-read', 'NotificationsController@markAllRead', [
        'middleware' => 'auth'
    ]],
    ['POST', '/admin/notifications/{id}/read', 'NotificationsController@markRead', [
        'middleware' => 'auth'
    ]],


    // =========================================================
    // SETTINGS
    // =========================================================
    ['GET', '/admin/settings', 'SettingsController@index', [
        'middleware' => 'permission:manage_settings'
    ]],
    ['POST', '/admin/settings', 'SettingsController@update', [
        'middleware' => 'permission:manage_settings'
    ]],


    // =========================================================
    // RBAC
    // =========================================================
    ['GET', '/admin/roles', 'RolesController@index', [
        'middleware' => 'permission:manage_roles'
    ]],
    ['GET', '/admin/roles/create', 'RolesController@create', [
        'middleware' => 'permission:manage_roles'
    ]],
    ['POST', '/admin/roles', 'RolesController@store', [
        'middleware' => 'permission:manage_roles'
    ]],
    ['GET', '/admin/roles/{id}/edit', 'RolesController@edit', [
        'middleware' => 'permission:manage_roles'
    ]],
    ['POST', '/admin/roles/{id}/update', 'RolesController@update', [
        'middleware' => 'permission:manage_roles'
    ]],


    // =========================================================
    // PROFILE
    // =========================================================
    ['GET', '/admin/profile', 'ProfileController@index', [
        'middleware' => 'auth'
    ]],
    ['POST', '/admin/profile/update', 'ProfileController@update', [
        'middleware' => 'auth'
    ]],


    // =========================================================
    // HEALTH CHECK
    // =========================================================
    ['GET', '/health', 'HomeController@health'],

];