<?php
namespace GOF\Models;

use PDO;

class Donation extends BaseModel {
    protected string $table = 'donations';

    public function allWithDonor(int $limit = 20, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT d.*, CONCAT(dnr.first_name, ' ', dnr.last_name) as donor_name
            FROM donations d
            LEFT JOIN donors dnr ON d.donor_id = dnr.id
            ORDER BY d.created_at DESC
            LIMIT :lim OFFSET :off
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function byDonor(int $donorId, int $limit = 50): array {
        $stmt = $this->db->prepare("SELECT * FROM donations WHERE donor_id = :did ORDER BY created_at DESC LIMIT :lim");
        $stmt->bindValue(':did', $donorId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int {
        return parent::count();
    }

    public function totalConfirmed(): float {
        return (float)$this->db->query("SELECT SUM(amount) FROM donations WHERE status = 'confirmed'")->fetchColumn();
    }

    public function monthlyTotals(int $months = 12): array {
        $stmt = $this->db->prepare("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total
            FROM donations
            WHERE status = 'confirmed' AND created_at >= DATE_SUB(NOW(), INTERVAL :months MONTH)
            GROUP BY month
            ORDER BY month ASC
        ");
        $stmt->bindValue(':months', $months, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function search(array $filters, int $limit = 20, int $offset = 0): array {
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "d.status = :status";
            $params[':status'] = $filters['status'];
        }

        $whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT d.*, CONCAT(dnr.first_name, ' ', dnr.last_name) as donor_name
            FROM donations d
            LEFT JOIN donors dnr ON d.donor_id = dnr.id
            $whereClause
            ORDER BY d.created_at DESC
            LIMIT :lim OFFSET :off
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
