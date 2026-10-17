<?php
namespace GOF\Models;

use PDO;

class Beneficiary extends BaseModel {
    protected string $table = 'beneficiaries';

    public function search(array $filters, int $limit = 20, int $offset = 0): array {
        $where = [];
        $params = [];

        if (!empty($filters['gof_id'])) {
            $where[] = "b.gof_id = :gof_id";
            $params[':gof_id'] = $filters['gof_id'];
        }
        if (!empty($filters['name'])) {
            $where[] = "b.full_name LIKE :name";
            $params[':name'] = '%' . $filters['name'] . '%';
        }
        if (!empty($filters['gender'])) {
            $where[] = "b.gender = :gender";
            $params[':gender'] = $filters['gender'];
        }
        if (!empty($filters['status'])) {
            $where[] = "b.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['state'])) {
            $where[] = "b.state = :state";
            $params[':state'] = $filters['state'];
        }
        if (!empty($filters['created_by'])) {
            $where[] = "b.created_by = :created_by";
            $params[':created_by'] = $filters['created_by'];
        }

        $whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT b.*, CONCAT(u.first_name, ' ', u.last_name) as creator_name
            FROM beneficiaries b
            LEFT JOIN users u ON b.created_by = u.id
            $whereClause
            ORDER BY b.created_at DESC
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

    public function count(array $filters = []): int {
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params[':status'] = $filters['status'];
        }

        $whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} $whereClause");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function countByStatus(): array {
        $stmt = $this->db->query("SELECT status, COUNT(*) as count FROM {$this->table} GROUP BY status");
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[$row['status']] = (int)$row['count'];
        }
        return $results;
    }

    public function getPending(int $limit = 50): array {
        return $this->search(['status' => 'pending'], $limit, 0);
    }

    public function findWithDetails(int $id): ?array {
        $beneficiary = $this->find($id);
        if (!$beneficiary) return null;

        $beneficiary['guardians'] = $this->getGuardians($id);
        $beneficiary['education'] = $this->getEducationRecords($id);
        $beneficiary['support'] = $this->getSupportHistory($id);
        return $beneficiary;
    }

    public function getGuardians(int $beneficiaryId): array {
        $s = $this->db->prepare("SELECT * FROM guardians WHERE beneficiary_id = :id");
        $s->execute([':id' => $beneficiaryId]);
        return $s->fetchAll();
    }

    public function getEducationRecords(int $beneficiaryId): array {
        $s = $this->db->prepare("SELECT * FROM education_records WHERE beneficiary_id = :id ORDER BY start_date DESC");
        $s->execute([':id' => $beneficiaryId]);
        return $s->fetchAll();
    }

    public function getSupportHistory(int $beneficiaryId): array {
        $s = $this->db->prepare("SELECT * FROM beneficiary_support WHERE beneficiary_id = :id ORDER BY support_date DESC");
        $s->execute([':id' => $beneficiaryId]);
        return $s->fetchAll();
    }
}
