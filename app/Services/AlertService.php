<?php
namespace GOF\Services;

class AlertService
{
    protected array $cfg;

    public function __construct()
    {
        $this->cfg = require __DIR__ . '/../../config/config.php';
    }

    public function sendEmail(string $to, string $subject, string $body): bool
    {
        // prefer PHPMailer if installed
        if (class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mailCfg = $this->cfg['mail'] ?? [];
                if (!empty($mailCfg['smtp_host'])) {
                    $mail->isSMTP();
                    $mail->Host = $mailCfg['smtp_host'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $mailCfg['smtp_user'] ?? '';
                    $mail->Password = $mailCfg['smtp_pass'] ?? '';
                    if (!empty($mailCfg['smtp_port'])) $mail->Port = (int)$mailCfg['smtp_port'];
                    if (!empty($mailCfg['smtp_secure'])) $mail->SMTPSecure = $mailCfg['smtp_secure'];
                }
                $mail->setFrom($mailCfg['from_email'] ?? 'no-reply@localhost', $mailCfg['from_name'] ?? 'GOF');
                $mail->addAddress($to);
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->isHTML(true);
                $mail->send();
                return true;
            } catch (\Throwable $e) {
                return false;
            }
        }

        // fallback to PHP mail()
        $headers = "From: " . ($this->cfg['mail']['from_email'] ?? 'no-reply@localhost') . "\r\n" . "Content-type: text/html; charset=utf-8\r\n";
        return (bool)@mail($to, $subject, $body, $headers);
    }

    public function sendSlack(string $webhookUrl, string $message): bool
    {
        if (empty($webhookUrl)) return false;
        $payload = json_encode(['text'=>$message]);
        if (function_exists('curl_version')) {
            $ch = curl_init($webhookUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $res = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return $code >= 200 && $code < 300;
        }
        // fallback to file_get_contents
        $opts = ['http' => ['method' => 'POST','header' => "Content-Type: application/json\r\n",'content' => $payload,'timeout' => 5]];
        $context = stream_context_create($opts);
        $result = @file_get_contents($webhookUrl, false, $context);
        return $result !== false;
    }

    public function notify(string $title, string $message, array $context = []): void
    {
        $mailCfg = $this->cfg['alerts']['emails'] ?? [];
        foreach ($mailCfg as $to) {
            $this->sendEmail($to, $title, nl2br(htmlentities($message)));
        }
        $slack = $this->cfg['alerts']['slack_webhook'] ?? '';
        if ($slack) {
            $this->sendSlack($slack, "*" . $title . "*\n" . $message);
        }
    }
}
