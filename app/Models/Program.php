<?php
namespace GOF\Models;

use PDO;

class Program extends BaseModel {
    protected string $table = 'programs';

    public function allWithManager(): array {
        $stmt = $this->db->query("
            SELECT p.*, CONCAT(u.first_name, ' ', u.last_name) as manager_name
            FROM programs p
            LEFT JOIN users u ON p.manager_id = u.id
            ORDER BY p.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function getActive(): array {
        $stmt = $this->db->query("SELECT * FROM programs WHERE status = 'active' ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function getBeneficiaries(int $programId): array {
        $stmt = $this->db->prepare("
            SELECT b.* 
            FROM beneficiaries b
            JOIN program_beneficiaries pb ON pb.beneficiary_id = b.id
            WHERE pb.program_id = :id
        ");
        $stmt->execute([':id' => $programId]);
        return $stmt->fetchAll();
    }

    public function count(): int {
        return parent::count();
    }
}
