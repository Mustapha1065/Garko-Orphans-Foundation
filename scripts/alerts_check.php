<?php
// CLI script to check verification spikes and send alerts.
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Helpers/Database.php';

use GOF\Helpers\Database;
use GOF\Services\AlertService;

// load config
$cfg = require __DIR__ . '/../config/config.php';
$db = Database::get();
$alert = new AlertService();

$thresholds = $cfg['alerts']['thresholds'] ?? ['last_5_min'=>50, 'last_60_min'=>400];

// count last 5 minutes
$stmt = $db->prepare('SELECT COUNT(*) as c FROM verification_attempts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)');
$stmt->execute(); $r = $stmt->fetch(); $c5 = (int)($r['c'] ?? 0);
// count last 60 minutes
$stmt = $db->prepare('SELECT COUNT(*) as c FROM verification_attempts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 60 MINUTE)');
$stmt->execute(); $r = $stmt->fetch(); $c60 = (int)($r['c'] ?? 0);

$notify = false; $reason = [];
if ($c5 >= ($thresholds['last_5_min'] ?? 50)) { $notify = true; $reason[] = "High 5m attempts: $c5"; }
if ($c60 >= ($thresholds['last_60_min'] ?? 400)) { $notify = true; $reason[] = "High 60m attempts: $c60"; }

if ($notify) {
    $title = 'GOF: Verification Spike Alert';
    $msg = implode("\n", $reason) . "\n\nDetails:\n- last_5m: $c5\n- last_60m: $c60\n";
    $alert->notify($title, $msg, ['c5'=>$c5,'c60'=>$c60]);
    echo "Alert sent: " . $msg . PHP_EOL;
} else {
    echo "No alert. counts: 5m=$c5, 60m=$c60" . PHP_EOL;
}

