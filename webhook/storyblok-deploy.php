<?php
/**
 * Storyblok → GitHub Actions Webhook-Bridge
 *
 * Dieses Script liegt auf dem Netcorp-Server und empfängt
 * den "published"-Webhook von Storyblok.
 * Es triggert dann das GitHub Actions Deployment via API.
 *
 * URL auf Netcorp: https://golden-puppets.com/webhook/storyblok-deploy.php
 * Diese URL in Storyblok unter: Settings → Webhooks → Story published
 */

// Sicherheit: Nur POST-Requests erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Sicherstellen, dass der Token vorhanden ist.
// Auf dem Server erstellen wir eine .env-Datei im gleichen Ordner.
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envVars = parse_ini_file($envFile);
    $GITHUB_TOKEN   = $envVars['GITHUB_DISPATCH_TOKEN'] ?? '';
    $WEBHOOK_SECRET = $envVars['STORYBLOK_WEBHOOK_SECRET'] ?? '';
} else {
    // Fallback auf Umgebungsvariablen
    $GITHUB_TOKEN   = getenv('GITHUB_DISPATCH_TOKEN');
    $WEBHOOK_SECRET = getenv('STORYBLOK_WEBHOOK_SECRET');
}

$GITHUB_REPO = 'maidem/golden-puppets-com';

// Debouncing: Nur einen Deploy auslösen, auch wenn mehrere Webhooks kurz hintereinander kommen.
// Wartezeit in Sekunden – in dieser Zeit werden weitere Webhooks ignoriert.
$DEBOUNCE_SECONDS = 30;
$lockFile = sys_get_temp_dir() . '/storyblok-deploy.lock';

if (file_exists($lockFile)) {
    $lastTrigger = (int) file_get_contents($lockFile);
    if (time() - $lastTrigger < $DEBOUNCE_SECONDS) {
        http_response_code(200);
        exit(json_encode(['status' => 'ok', 'message' => 'Debounced – deploy already scheduled']));
    }
}
file_put_contents($lockFile, time());

// Optional: Storyblok Webhook-Signatur prüfen
if (!empty($WEBHOOK_SECRET)) {
    $signature = $_SERVER['HTTP_WEBHOOK_SIGNATURE'] ?? '';
    $body      = file_get_contents('php://input');
    $expected  = hash_hmac('sha1', $body, $WEBHOOK_SECRET);
    if (!hash_equals($expected, $signature)) {
        http_response_code(401);
        exit('Invalid signature');
    }
}

// GitHub Actions via repository_dispatch triggern
$url     = "https://api.github.com/repos/{$GITHUB_REPO}/dispatches";
$payload = json_encode(['event_type' => 'storyblok_publish']);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => [
        'Accept: application/vnd.github.v3+json',
        'Authorization: Bearer ' . $GITHUB_TOKEN,
        'Content-Type: application/json',
        'User-Agent: Storyblok-Webhook',
    ],
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 204) {
    http_response_code(200);
    echo json_encode(['status' => 'ok', 'message' => 'Deployment triggered']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'code' => $httpCode, 'response' => $response]);
}
