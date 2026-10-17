<?php
namespace GOF\Controllers;

class AdminController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        
        $db = \GOF\Helpers\Database::get();
        
        // Stats
        $stats = [];
        $stats['total_beneficiaries'] = $db->query("SELECT COUNT(*) FROM beneficiaries")->fetchColumn();
        $stats['active_beneficiaries'] = $db->query("SELECT COUNT(*) FROM beneficiaries WHERE status='active'")->fetchColumn();
        $stats['pending_beneficiaries'] = $db->query("SELECT COUNT(*) FROM beneficiaries WHERE status='pending'")->fetchColumn();
        $stats['total_staff'] = $db->query("SELECT COUNT(*) FROM staff")->fetchColumn();
        $stats['total_donors'] = $db->query("SELECT COUNT(*) FROM donors")->fetchColumn();
        $stats['total_donations'] = $db->query("SELECT COUNT(*) FROM donations WHERE status='confirmed'")->fetchColumn();
        $stats['total_donation_amount'] = $db->query("SELECT SUM(amount) FROM donations WHERE status='confirmed'")->fetchColumn() ?: 0;
        $stats['active_programs'] = $db->query("SELECT COUNT(*) FROM programs WHERE status='active'")->fetchColumn();
        $stats['upcoming_events'] = $db->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE() AND status='upcoming'")->fetchColumn();
        $stats['pending_volunteers'] = $db->query("SELECT COUNT(*) FROM volunteer_applications WHERE status='pending'")->fetchColumn();
        $stats['pending_contacts'] = $db->query("SELECT COUNT(*) FROM contact_messages WHERE status='unread'")->fetchColumn();
        $stats['active_id_cards'] = $db->query("SELECT COUNT(*) FROM id_cards WHERE status='active'")->fetchColumn();
        
        // Charts
        $beneficiaryByGender = $db->query("SELECT gender, COUNT(*) as c FROM beneficiaries WHERE status='active' GROUP BY gender")->fetchAll();
        $beneficiaryByStatus = $db->query("SELECT status, COUNT(*) as c FROM beneficiaries GROUP BY status")->fetchAll();
        $donationsByMonth = $db->query("SELECT DATE_FORMAT(donation_date,'%Y-%m') as month, SUM(amount) as total FROM donations WHERE status='confirmed' AND donation_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY month ORDER BY month")->fetchAll();
        
        // Recent Data
        $recentBeneficiaries = $db->query("SELECT * FROM beneficiaries ORDER BY created_at DESC LIMIT 5")->fetchAll();
        $recentDonations = $db->query("SELECT d.*, do.name as donor_name FROM donations d JOIN donors do ON d.donor_id = do.id ORDER BY d.created_at DESC LIMIT 5")->fetchAll();
        $pendingApprovals = $db->query("SELECT * FROM beneficiaries WHERE status='pending' LIMIT 5")->fetchAll();

        $this->render('admin/dashboard/index.php', compact(
            'stats', 
            'beneficiaryByGender', 
            'beneficiaryByStatus', 
            'donationsByMonth', 
            'recentBeneficiaries', 
            'recentDonations', 
            'pendingApprovals'
        ));
    }
}
