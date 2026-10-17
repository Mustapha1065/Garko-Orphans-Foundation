<?php
namespace GOF\Services;

use GOF\Helpers\Database;
use PDO;

class IdGenerator {
    public function next(string $type): int {
        $db = Database::get();
        // Support being called both standalone and inside an existing
        // transaction (e.g. from a controller that wraps a full create
        // workflow in its own transaction). PDO does not support nested
        // transactions, so only manage the transaction when we own it.
        $ownsTransaction = !$db->inTransaction();
        if ($ownsTransaction) {
            $db->beginTransaction();
        }

        try {
            $stmt = $db->prepare("SELECT last_val FROM sequences WHERE name = :name FOR UPDATE");
            $stmt->execute([':name' => $type]);
            $val = $stmt->fetchColumn();
            
            if ($val === false) {
                $db->prepare("INSERT INTO sequences (name, last_val) VALUES (:name, 0)")
                   ->execute([':name' => $type]);
                $val = 0;
            }
            
            $db->prepare("UPDATE sequences SET last_val = last_val + 1 WHERE name = :name")
               ->execute([':name' => $type]);
               
            $stmt->execute([':name' => $type]);
            $newVal = (int)$stmt->fetchColumn();
            
            if ($ownsTransaction) {
                $db->commit();
            }
            return $newVal;
        } catch (\Exception $e) {
            if ($ownsTransaction) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public function formatGOFId(string $type, int $seq): string {
        $prefix = match($type) {
            'beneficiary' => 'GOF-ORP',
            'staff' => 'GOF-STF',
            'donor' => 'GOF-DON',
            'volunteer' => 'GOF-VOL',
            default => 'GOF-UNK'
        };
        
        return $prefix . '-' . str_pad((string)$seq, 5, '0', STR_PAD_LEFT);
    }
}
