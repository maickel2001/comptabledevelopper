<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function send_mail(string $to, string $subject, string $html, string $fromEmail = null, string $fromName = null): bool {
    $fromEmail = $fromEmail ?: (getenv('MAIL_FROM') ?: 'no-reply@example.com');
    $fromName = $fromName ?: APP_NAME;
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . sprintf('%s <%s>', $fromName, $fromEmail);
    $headers[] = 'X-Mailer: PHP/' . phpversion();
    $success = @mail($to, $subject, $html, implode("\r\n", $headers));
    if (!$success) {
        // Fallback: write to local log so developers can inspect emails during development
        $logDir = ROOT_PATH . '/storage/logs';
        if (!is_dir($logDir)) @mkdir($logDir, 0777, true);
        $entry = "==== MAIL Fallback ====" . PHP_EOL .
                 'To: ' . $to . PHP_EOL .
                 'Subject: ' . $subject . PHP_EOL .
                 'Headers: ' . implode('; ', $headers) . PHP_EOL .
                 'Body:' . PHP_EOL . $html . PHP_EOL .
                 'Time: ' . date('c') . PHP_EOL . PHP_EOL;
        @file_put_contents($logDir . '/mail.log', $entry, FILE_APPEND);
    }
    return $success;
}

?>
