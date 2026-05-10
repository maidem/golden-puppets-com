# SMTP E-Mail Konfiguration für Golden Puppets

## Übersicht

Das Kontaktformular verwendet derzeit PHPs `mail()` Funktion, die einen lokalen Mailserver benötigt. Um zuverlässige E-Mails zu versenden, wird empfohlen, einen externen SMTP-Dienst zu verwenden.

## 1. SMTP Variablen hinzufügen

### webhook/.env Datei erweitern

Öffne `webhook/.env.example` oder `webhook/.env` und füge folgende Variablen hinzu:

```bash
# E-Mail Konfiguration
CONTACT_RECIPIENT=info@golden-puppets.com
CONTACT_FROM=no-reply@golden-puppets.com

# SMTP-Einstellungen
SMTP_ENABLED=true
SMTP_HOST=smtp.mailtrap.io
SMTP_PORT=2525
SMTP_USERNAME=dein-username
SMTP_PASSWORD=dein-passwort
SMTP_ENCRYPTION=tls
```

## 2. SMTP-Anbieter Optionen

### Option A: Mailtrap (kostenlos, zum Testen)

- Website: [mailtrap.io](https://mailtrap.io)
- Port: 2525 (TLS) oder 465 (SSL)
- Kostenlos: 500 E-Mails/Monat
- Einrichtung: Nach Registrierung findest du Credentials im Dashboard

### Option B: Gmail/Google Workspace

- SMTP_HOST: `smtp.gmail.com`
- SMTP_PORT: `587` (TLS) oder `465` (SSL)
- SMTP_USERNAME: Deine Gmail-Adresse
- SMTP_PASSWORD: App-Password (nicht das normale Passwort!)
- [App Passwords generieren](https://myaccount.google.com/apppasswords)

### Option C: Postmark (für Production)

- Website: [postmarkapp.com](https://postmarkapp.com)
- SMTP_HOST: `smtp.postmarkapp.com`
- SMTP_PORT: `587`
- SMTP_USERNAME: `postmark_account_token`
- SMTP_PASSWORD: `postmark_account_token`
- Zuverlässig, gutes Dashboard, kostenlos bis 100 E-Mails/Tag

### Option D: SendGrid

- Website: [sendgrid.com](https://sendgrid.com)
- SMTP_HOST: `smtp.sendgrid.net`
- SMTP_PORT: `587`
- SMTP_USERNAME: `apikey`
- SMTP_PASSWORD: Dein SendGrid API Key
- Kostenlos bis 100 E-Mails/Tag

### Option E: Dein Host-Provider

Frag deinen Hosting-Provider nach:

- SMTP-Server (z.B. `mail.deinedomain.com`)
- SMTP-Port (meist 587 oder 465)
- SMTP-Benutzername und Passwort

## 3. webhook/contact.php aktualisieren

Ersetze den `mail()`-Aufruf mit einer SMTP-Lösung. Hier sind zwei Optionen:

### Option 1: PHPMailer verwenden (empfohlen)

1. Installiere PHPMailer via Composer:

```bash
cd webhook
composer require phpmailer/phpmailer
```

1. Ersetze in `contact.php` den Mail-Versand (ca. Zeile 155):

```php
// Alten mail()-Aufruf durch diesen ersetzen:
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$smtpEnabled = filter_var($env['SMTP_ENABLED'] ?? 'false', FILTER_VALIDATE_BOOLEAN);

if ($smtpEnabled) {
    $mail = new PHPMailer(true);

    try {
        // SMTP konfigurieren
        $mail->isSMTP();
        $mail->Host       = $env['SMTP_HOST'] ?? '';
        $mail->SMTPAuth   = true;
        $mail->Username   = $env['SMTP_USERNAME'] ?? '';
        $mail->Password   = $env['SMTP_PASSWORD'] ?? '';
        $mail->SMTPSecure = $env['SMTP_ENCRYPTION'] ?? 'tls';
        $mail->Port       = (int)($env['SMTP_PORT'] ?? 587);
        $mail->Timeout    = 10;

        // Absender und Empfänger
        $mail->setFrom($fromAddress, 'Golden Puppets');
        $mail->addAddress($recipient);
        $mail->addReplyTo($email, $name);

        // Inhalt
        $mail->isHTML(false);
        $mail->Subject = $mailSubject;
        $mail->Body    = $body;

        // Versenden
        $mail->send();
    } catch (Exception $e) {
        respondError(['E-Mail konnte nicht versendet werden: ' . $e->getMessage()], $errorUrl);
    }
} else {
    // Fallback auf PHP mail()
    $ok = @mail(
        $recipient,
        '=?UTF-8?B?' . base64_encode($mailSubject) . '?=',
        $body,
        implode("\r\n", $headers)
    );

    if (!$ok) {
        respondError(['Versand fehlgeschlagen. Bitte später erneut versuchen.'], $errorUrl);
    }
}
```

### Option 2: Swift Mailer verwenden

```bash
cd webhook
composer require swiftmailer/swiftmailer
```

Dann ähnlich wie PHPMailer konfigurieren.

## 4. .env.example aktualisieren

Füge die SMTP-Variablen auch zu `.env.example` hinzu:

```bash
# webhook/.env.example

# SMTP Configuration
SMTP_ENABLED=false
SMTP_HOST=smtp.mailtrap.io
SMTP_PORT=2525
SMTP_USERNAME=your-username
SMTP_PASSWORD=your-password
SMTP_ENCRYPTION=tls
```

## 5. Sicherheit: SMTP Credentials schützen

- **Niemals** commit `.env` in Git
- Nutze Environment Variables auf dem Server (nicht Dateien)
- Bei Hosting-Providern: Nutze die bereitgestellten Umgebungsvariablen-Tools
- Sensitive Daten verschlüsseln, wenn möglich

## 6. Testen

### Test 1: Mailtrap

1. Gehe zu [mailtrap.io](https://mailtrap.io)
1. Nutze kostenlos Mailtrap-Credentials in `.env`
1. Sende ein Test-E-Mail über das Formular
1. Überprüfe in Mailtrap Dashboard, ob es ankommt
1. Wenn ja: PHPMailer funktioniert ✓

### Test 2: Real-World

1. Wechsle zu echtem SMTP (Gmail, SendGrid, etc.)
1. Sende ein Test-E-Mail
1. Überprüfe dein E-Mail Postfach

## 7. Troubleshooting

### E-Mail kommt nicht an

- SMTP_ENABLED = true in `.env` gesetzt?
- SMTP-Credentials korrekt?
- SMTP_HOST und SMTP_PORT korrekt?
- Firewall blockiert SMTP-Port?

### "SMTP Connect failed"

- Port prüfen: meist 587 (TLS) oder 465 (SSL)
- Encryption prüfen: 'tls' oder 'ssl'
- Host erreichbar? `telnet smtp.host.com 587`

### PHPMailer nicht gefunden

```bash
cd webhook
composer install
# oder
php -r "require 'vendor/autoload.php';"
```

### "Username and Password not accepted"

- App-Password statt normalem Passwort nutzen (Gmail)
- SMTP-Credentials in Anbieter-Dashboard überprüfen
- Sonderzeichen in Passwort? URL-encode nutzen

## 8. Production-Empfehlungen

Für produktive Nutzung:

1. **Postmark** oder **SendGrid** verwenden (zuverlässig)
1. **SPF/DKIM/DMARC** Records eintragen für Domain
1. **Bounce/Complaint Handling** implementieren
1. **Rate Limiting** nutzen
1. **Logging** aktivieren für Debugging

## Weitere Ressourcen

- PHPMailer Docs: [github.com/PHPMailer/PHPMailer/wiki](https://github.com/PHPMailer/PHPMailer/wiki)
- Swift Mailer Docs: [swiftmailer.symfony.com](https://swiftmailer.symfony.com)
- SMTP Ports: [mailserve.nl/sending-mails/port](https://www.mailserve.nl/sending-mails/port)
