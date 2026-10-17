<?php
namespace GOF\Models;

use PDO;

class Donor extends BaseModel {
    protected string $table = 'donors';

    public function search(array $filters, int $limit = 20, int $offset = 0): array {
        $where = [];
        $params = [];

        if (!empty($filters['name'])) {
            $where[] = "(first_name LIKE :name OR last_name LIKE :name)";
            $params[':name'] = '%' . $filters['name'] . '%';
        }

        $whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT * FROM {$this->table} $whereClause ORDER BY created_at DESC LIMIT :lim OFFSET :off";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int {
        return parent::count(); // Basic count for now
    }

    public function findWithDonations(int $id): ?array {
        $donor = $this->find($id);
        if (!$donor) return null;

        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count, SUM(amount) as total 
            FROM donations 
            WHERE donor_id = :id AND status = 'confirmed'
        ");
        $stmt->execute([':id' => $id]);
        $stats = $stmt->fetch();
        
        $donor['donation_count'] = (int)($stats['count'] ?? 0);
        $donor['total_donated'] = (float)($stats['total'] ?? 0);
        
        return $donor;
    }

    public function withDonationTotals(int $limit = 50): array {
        $stmt = $this->db->prepare("
            SELECT d.*, SUM(dn.amount) as total_donated
            FROM donors d
            LEFT JOIN donations dn ON dn.donor_id = d.id AND dn.status = 'confirmed'
            GROUP BY d.id
            ORDER BY total_donated DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
