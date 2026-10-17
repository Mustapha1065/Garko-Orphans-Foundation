<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;

class NewsController extends BaseController
{
    public function index()
    {
        $this->requirePermission('manage_news');

        $db = Database::get();

        $stmt = $db->query("
            SELECT
                n.*,
                CONCAT_WS(' ', u.first_name, u.last_name) AS author_name
            FROM news n
            LEFT JOIN users u ON n.author_id = u.id
            ORDER BY n.created_at DESC
        ");

        $articles = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->render('admin/news/index.php', compact('articles'));
    }

    public function create()
    {
        $this->requirePermission('manage_news');

        $this->render('admin/news/create.php');
    }

    public function store()
    {
        $this->requirePermission('manage_news');
        $this->validateCsrf();

        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? 'General');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $status = $_POST['status'] ?? 'draft';

        if (empty($title)) {
            $this->flash('error', 'Title is required.');
            $this->redirect('/admin/news/create');
            return;
        }

        // Generate slug
        $slug = strtolower(
            preg_replace('/[^a-z0-9]+/', '-', $title)
        );

        $slug = trim($slug, '-');

        $db = Database::get();

        // Check unique slug
        $stmtSlug = $db->prepare(
            "SELECT COUNT(*) FROM news WHERE slug = ?"
        );

        $stmtSlug->execute([$slug]);

        if ($stmtSlug->fetchColumn() > 0) {
            $slug .= '-' . time();
        }

        $currentUser = $this->currentUser();
        $author_id = $currentUser['id'] ?? null;

        $published_at = ($status === 'published')
            ? date('Y-m-d H:i:s')
            : null;

        try {
            $stmt = $db->prepare("
                INSERT INTO news
                (
                    title,
                    slug,
                    category,
                    excerpt,
                    content,
                    status,
                    author_id,
                    published_at,
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
                    NOW(),
                    NOW()
                )
            ");

            $stmt->execute([
                $title,
                $slug,
                $category,
                $excerpt,
                $content,
                $status,
                $author_id,
                $published_at
            ]);

            // Audit log
            if ($author_id) {
                (new \GOF\Services\AuditService())->log(
                    (int) $author_id,
                    'create_news',
                    'news',
                    'news',
                    (string) $db->lastInsertId(),
                    "Created news article: {$title}"
                );
            }

            $this->flash(
                'success',
                'Article created successfully.'
            );

            $this->redirect('/admin/news');

        } catch (\Exception $e) {

            $this->flash(
                'error',
                'Failed to create article.'
            );

            $this->redirect('/admin/news/create');
        }
    }

    public function edit($id)
    {
        $this->requirePermission('manage_news');

        $db = Database::get();

        $stmt = $db->prepare(
            "SELECT * FROM news WHERE id = ?"
        );

        $stmt->execute([$id]);

        $article = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$article) {
            $this->flash(
                'error',
                'Article not found'
            );

            $this->redirect('/admin/news');
            return;
        }

        $this->render(
            'admin/news/edit.php',
            compact('article')
        );
    }

    public function update($id)
    {
        $this->requirePermission('manage_news');
        $this->validateCsrf();

        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? 'General');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $status = $_POST['status'] ?? 'draft';

        if (empty($title)) {
            $this->flash(
                'error',
                'Title is required.'
            );

            $this->redirect(
                "/admin/news/edit/{$id}"
            );

            return;
        }

        $db = Database::get();

        // Get existing article
        $stmt = $db->prepare(
            "SELECT * FROM news WHERE id = ?"
        );

        $stmt->execute([$id]);

        $article = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$article) {
            $this->flash(
                'error',
                'Article not found.'
            );

            $this->redirect('/admin/news');
            return;
        }

        // Preserve existing published date
        $published_at = $article['published_at'];

        // Set published date when changing draft to published
        if (
            $status === 'published' &&
            $article['status'] !== 'published'
        ) {
            $published_at = date('Y-m-d H:i:s');
        }

        // Remove published date when changing back to draft
        if ($status === 'draft') {
            $published_at = null;
        }

        try {
            $stmt = $db->prepare("
                UPDATE news
                SET
                    title = ?,
                    category = ?,
                    excerpt = ?,
                    content = ?,
                    status = ?,
                    published_at = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                $title,
                $category,
                $excerpt,
                $content,
                $status,
                $published_at,
                $id
            ]);

            $currentUser = $this->currentUser();
            $author_id = $currentUser['id'] ?? null;

            // Audit log
            if ($author_id) {
                (new \GOF\Services\AuditService())->log(
                    (int) $author_id,
                    'update_news',
                    'news',
                    'news',
                    (string) $id,
                    "Updated news article: {$title}"
                );
            }

            $this->flash(
                'success',
                'Article updated successfully.'
            );

            $this->redirect('/admin/news');

        } catch (\Exception $e) {

            $this->flash(
                'error',
                'Failed to update article.'
            );

            $this->redirect(
                "/admin/news/edit/{$id}"
            );
        }
    }

    public function publish($id)
    {
        $this->requirePermission('manage_news');
        $this->validateCsrf();

        $db = Database::get();

        $stmt = $db->prepare("
            UPDATE news
            SET
                status = 'published',
                published_at = NOW(),
                updated_at = NOW()
            WHERE id = ?
            AND status = 'draft'
        ");

        $stmt->execute([$id]);

        $this->flash(
            'success',
            'Article published successfully.'
        );

        $this->redirect('/admin/news');
    }
}