<?php
/**
 * Kontaktformular-Handler
 *
 * URL auf dem Server: https://golden-puppets.com/webhook/contact.php
 *
 * Erwartete Felder (POST):
 *   - name      (string, required)
 *   - email     (string, required, gültige E-Mail)
 *   - subject   (string, required)
 *   - message   (string, required)
 *   - privacy   ("1", required – Datenschutz-Bestätigung)
 *   - recipient (optional – sonst Fallback auf .env)
 *   - website   (Honeypot, muss leer sein)
 *
 * Konfiguration via webhook/.env:
 *   CONTACT_RECIPIENT=info@golden-puppets.com
 *   CONTACT_FROM=no-reply@golden-puppets.com
 *   CONTACT_SUCCESS_URL=/danke
 *   CONTACT_ERROR_URL=/kontakt?error=1
 */

declare(strict_types=1);

header('X-Content-Type-Options: nosniff');

// 1. Methode prüfen
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method Not Allowed');
}

// 2. Konfig laden
$envFile = __DIR__ . '/.env';
$env = file_exists($envFile) ? parse_ini_file($envFile) : [];
$defaultRecipient = $env['CONTACT_RECIPIENT'] ?? (getenv('CONTACT_RECIPIENT') ?: '');
$fromAddress      = $env['CONTACT_FROM']      ?? (getenv('CONTACT_FROM')      ?: 'no-reply@golden-puppets.com');
$successUrl       = $env['CONTACT_SUCCESS_URL'] ?? '/?contact=success';
$errorUrl         = $env['CONTACT_ERROR_URL']   ?? '/?contact=error';

$mosparoEnabledRaw = $env['MOSPARO_ENABLED'] ?? (getenv('MOSPARO_ENABLED') ?: 'false');
$mosparoEnabled = filter_var($mosparoEnabledRaw, FILTER_VALIDATE_BOOLEAN);
$mosparoVerifyUrl = trim((string)($env['MOSPARO_VERIFY_URL'] ?? (getenv('MOSPARO_VERIFY_URL') ?: '')));
$mosparoApiKey = trim((string)($env['MOSPARO_API_KEY'] ?? (getenv('MOSPARO_API_KEY') ?: '')));
$mosparoApiSecret = trim((string)($env['MOSPARO_API_SECRET'] ?? (getenv('MOSPARO_API_SECRET') ?: '')));

// 3. Honeypot
if (!empty($_POST['website'] ?? '')) {
    // Bot erkannt – wir tun so, als wäre alles ok
    redirect($successUrl);
}

// 4. Rate-Limit pro IP (max 5 / 10 Min.)
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateFile = sys_get_temp_dir() . '/contact-rate-' . md5($ip);
$now = time();
$window = 600;
$max = 5;
$entries = [];
if (file_exists($rateFile)) {
    $raw = @file_get_contents($rateFile);
    $entries = $raw ? array_filter(array_map('intval', explode("\n", $raw)), fn ($t) => $t > $now - $window) : [];
}
if (count($entries) >= $max) {
    http_response_code(429);
    exit('Zu viele Anfragen. Bitte später erneut versuchen.');
}
$entries[] = $now;
@file_put_contents($rateFile, implode("\n", $entries));

// 5. Inputs einsammeln + säubern
$name    = trim((string)($_POST['name']    ?? ''));
$email   = trim((string)($_POST['email']   ?? ''));
$subject = trim((string)($_POST['subject'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));
$privacy = (string)($_POST['privacy'] ?? '');
$recipientRaw = trim((string)($_POST['recipient'] ?? ''));
$mosparoSubmitToken = trim((string)($_POST['mosparo_submitToken'] ?? ''));
$mosparoValidationSignature = trim((string)($_POST['mosparo_validationSignature'] ?? ''));
$mosparoFormData = trim((string)($_POST['mosparo_formData'] ?? ''));

// 6. Validierung
$errors = [];
if ($name === '' || mb_strlen($name) > 200) {
    $errors[] = 'Bitte einen gültigen Namen angeben.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Bitte eine gültige E-Mail angeben.';
}
if ($subject === '' || mb_strlen($subject) > 300) {
    $errors[] = 'Bitte einen gültigen Betreff angeben.';
}
if ($message === '' || mb_strlen($message) > 10000) {
    $errors[] = 'Bitte eine Nachricht eingeben.';
}
if ($privacy !== '1') {
    $errors[] = 'Bitte die Datenschutzerklärung bestätigen.';
}

// Header-Injection verhindern
foreach ([$name, $email, $subject] as $field) {
    if (preg_match('/[\r\n]/', $field)) {
        $errors[] = 'Ungültige Zeichen erkannt.';
        break;
    }
}

// Empfänger validieren – Whitelist auf den default oder eine erlaubte Domain
$recipient = $defaultRecipient;
if ($recipientRaw !== '' && filter_var($recipientRaw, FILTER_VALIDATE_EMAIL)) {
    if ($defaultRecipient && str_ends_with($recipientRaw, substr(strrchr($defaultRecipient, '@') ?: '', 1))) {
        $recipient = $recipientRaw;
    }
}
if (!$recipient) {
    $errors[] = 'Konfigurationsfehler: Kein Empfänger hinterlegt.';
}

if ($mosparoEnabled) {
    if (!$mosparoVerifyUrl || !$mosparoApiKey || !$mosparoApiSecret) {
        $errors[] = 'Konfigurationsfehler: Mosparo ist nicht korrekt konfiguriert.';
    } elseif (!$mosparoSubmitToken || !$mosparoValidationSignature || !$mosparoFormData) {
        $errors[] = 'Spam-Schutz unvollständig. Bitte Formular neu laden.';
    }
}

if ($errors) {
    respondError($errors, $errorUrl);
}

if ($mosparoEnabled) {
    $mosparoResult = verifyMosparo(
        $mosparoVerifyUrl,
        $mosparoApiKey,
        $mosparoApiSecret,
        $mosparoSubmitToken,
        $mosparoValidationSignature,
        $mosparoFormData
    );
    if (!$mosparoResult['ok']) {
        respondError(['Spam-Schutz Prüfung fehlgeschlagen. Bitte erneut versuchen.'], $errorUrl);
    }
}

// 7. Mail bauen
$siteHost = $_SERVER['HTTP_HOST'] ?? 'golden-puppets.com';
$mailSubject = '[Kontakt] ' . mb_substr($subject, 0, 200);

$body  = "Neue Nachricht über das Kontaktformular ($siteHost)\n\n";
$body .= "Name:    $name\n";
$body .= "E-Mail:  $email\n";
$body .= "Betreff: $subject\n";
$body .= "Datum:   " . date('Y-m-d H:i:s') . "\n";
$body .= "IP:      $ip\n";
$body .= str_repeat('-', 60) . "\n\n";
$body .= $message . "\n";

$headers = [];
$headers[] = 'From: ' . $fromAddress;
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'X-Mailer: golden-puppets-contact';
$headers[] = 'Content-Type: text/plain; charset=UTF-8';

$ok = @mail(
    $recipient,
    '=?UTF-8?B?' . base64_encode($mailSubject) . '?=',
    $body,
    implode("\r\n", $headers)
);

if (!$ok) {
    respondError(['Versand fehlgeschlagen. Bitte später erneut versuchen.'], $errorUrl);
}

// 8. Antwort
if (wantsJson()) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'ok']);
    exit;
}
redirect($successUrl);


// ---- Helpers ----

function wantsJson(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $xrw = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    return str_contains($accept, 'application/json') || strtolower($xrw) === 'xmlhttprequest';
}

function redirect(string $url): void
{
    header('Location: ' . $url, true, 303);
    exit;
}

function respondError(array $errors, string $errorUrl): void
{
    if (wantsJson()) {
        http_response_code(422);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        exit;
    }
    $sep = str_contains($errorUrl, '?') ? '&' : '?';
    redirect($errorUrl . $sep . 'msg=' . urlencode(implode(' ', $errors)));
}

function verifyMosparo(
    string $verifyUrl,
    string $apiKey,
    string $apiSecret,
    string $submitToken,
    string $validationSignature,
    string $formData
): array {
    $payload = http_build_query([
        'submitToken' => $submitToken,
        'validationSignature' => $validationSignature,
        'formData' => $formData,
    ]);

    $auth = 'Authorization: Basic ' . base64_encode($apiKey . ':' . $apiSecret);
    $headers = [
        $auth,
        'Content-Type: application/x-www-form-urlencoded',
    ];

    $response = false;

    if (function_exists('curl_init')) {
        $ch = curl_init($verifyUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
    } else {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $payload,
                'timeout' => 8,
            ],
        ]);
        $response = @file_get_contents($verifyUrl, false, $context);
    }

    if (!$response) {
        return ['ok' => false];
    }

    $json = json_decode($response, true);
    if (!is_array($json)) {
        return ['ok' => false];
    }

    if (array_key_exists('valid', $json)) {
        return ['ok' => (bool)$json['valid']];
    }

    if (isset($json['verificationResult']['isValid'])) {
        return ['ok' => (bool)$json['verificationResult']['isValid']];
    }

    return ['ok' => false];
}
