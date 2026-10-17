<?php
namespace GOF\Controllers;

class ProfileController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        $db = \GOF\Helpers\Database::get();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $user['id']]);
        $profile = $stmt->fetch();
        
        $this->render('admin/profile/index.php', compact('profile'));
    }

    public function update(): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        
        $db = \GOF\Helpers\Database::get();
        $user = $this->currentUser();
        
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        $stmt = $db->prepare("UPDATE users SET first_name = :fn, last_name = :ln, phone = :phone WHERE id = :id");
        $stmt->execute([
            ':fn' => $firstName,
            ':ln' => $lastName,
            ':phone' => $phone,
            ':id' => $user['id']
        ]);
        
        // Update session
        $_SESSION['user']['first_name'] = $firstName;
        $_SESSION['user']['last_name'] = $lastName;
        
        // Handle password update optionally
        $currentPass = $_POST['current_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';
        
        if (!empty($newPass)) {
            $stmt = $db->prepare("SELECT password_hash FROM users WHERE id = :id");
            $stmt->execute([':id' => $user['id']]);
            $hash = $stmt->fetchColumn();
            
            if (password_verify($currentPass, $hash)) {
                if ($newPass === $confirmPass) {
                    $newHash = password_hash($newPass, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE users SET password_hash = :ph WHERE id = :id");
                    $stmt->execute([':ph' => $newHash, ':id' => $user['id']]);
                    $this->flash('success', 'Profile and password updated successfully.');
                } else {
                    $this->flash('error', 'New passwords do not match.');
                }
            } else {
                $this->flash('error', 'Current password is incorrect.');
            }
        } else {
            $this->flash('success', 'Profile updated successfully.');
        }
        
        $this->redirect('/admin/profile');
    }
}
