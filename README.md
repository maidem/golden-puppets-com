# Nuxt 4 + Storyblok Starter

Ein schlankes Grundgerüst für Projekte mit **Nuxt 4**, **Storyblok** (Headless CMS) und **Tailwind CSS**.  
Ideal als Ausgangspunkt für neue Websites, die über Storyblok gepflegt werden sollen.

---

## Features

- **Nuxt 4** (Compatibility Version 4) mit `app/`-Verzeichnis-Struktur
- **Storyblok** Integration via `@storyblok/nuxt` inkl. Live-Preview (Storyblok Bridge)
- **Tailwind CSS** (`@nuxtjs/tailwindcss`)
- Automatisch globale Komponenten-Registrierung
- HTTPS-fähiger Dev-Server (lokale SSL-Zertifikate)
- Atomares Komponenten-System (Atoms → Molecules → Organisms → Templates)
- Dynamisches Routing über Storyblok Stories (`[...slug].vue`)

---

## Voraussetzungen

- [Node.js](https://nodejs.org/) ≥ 20
- Ein [Storyblok](https://www.storyblok.com/) Konto mit einem Space
- (Optional) Lokale SSL-Zertifikate für HTTPS im Dev-Modus (z. B. mit [mkcert](https://github.com/FiloSottile/mkcert))

---

## Setup

### 1. Repository klonen

```bash
git clone git@github.com:maidem/golden-puppets-com.git
cd golden-puppets-com
```

### 2. Abhängigkeiten installieren

```bash
npm install
```

### 3. Umgebungsvariablen konfigurieren

```bash
cp .env.example .env
```

Öffne `.env` und trage deinen Storyblok **Delivery API Token** ein:

```env
STORYBLOK_DELIVERY_API_TOKEN=dein_token_hier
```

Den Token findest du in deinem Storyblok Space unter **Settings → Access Tokens**.

### 4. (Optional) HTTPS für lokale Entwicklung einrichten

Der Dev-Server ist für HTTPS konfiguriert – das wird von Storyblok für den Visual Editor benötigt.  
Generiere lokale Zertifikate mit [mkcert](https://github.com/FiloSottile/mkcert):

```bash
brew install mkcert
mkcert -install
mkcert localhost
```

Die erzeugten Dateien `localhost.pem` und `localhost-key.pem` ins Projekt-Root kopieren.  
Sie sind in `.gitignore` gelistet und werden **nicht** eingecheckt.

---

## Entwicklung starten

```bash
npm run dev
```

→ `https://localhost:3000`

---

## Produktions-Build

```bash
npm run build
npm run preview
```

Oder als statische Seite generieren:

```bash
npm run generate
```

---

## Projektstruktur

```
├── app/
│   ├── app.vue                    # App-Root
│   ├── components/
│   │   ├── atoms/                 # Kleinste Bausteine (Button, Heading, Link …)
│   │   ├── molecules/             # Zusammengesetzte Elemente (Cards …)
│   │   ├── organisms/             # Komplexe Sektionen
│   │   ├── templates/             # Seiten-Layouts
│   │   ├── Feature.vue
│   │   ├── Grid.vue
│   │   ├── Header.vue
│   │   ├── NavItem.vue
│   │   ├── Page.vue
│   │   └── Teaser.vue
│   ├── pages/
│   │   └── [...slug].vue          # Dynamisches Routing via Storyblok
│   └── storyblok/                 # Storyblok-spezifische Komponenten
├── storyblok/                     # Storyblok-Konfigurationsdaten
├── .env.example                   # Vorlage für Umgebungsvariablen
├── nuxt.config.ts
├── tailwind.config.js
└── package.json
```

---

## Storyblok-Komponenten einbinden

Neue Storyblok-Blöcke werden als Vue-Komponenten in `app/components/` angelegt.  
Nuxt registriert alle Komponenten **global** – der Name der Datei entspricht dem Slug des Blöcks in Storyblok.

Beispiel für eine neue Komponente `TextBlock.vue`:

```vue
<template>
  <div v-editable="blok">
    <p>{{ blok.text }}</p>
  </div>
</template>

<script setup>
defineProps({ blok: Object });
</script>
```

In Storyblok den Block `text_block` (oder `TextBlock`) anlegen und das Feld `text` hinzufügen – fertig.

---

## Storyblok Region

Der Space ist auf die **EU-Region** konfiguriert (`apiOptions.region: 'eu'`).  
Für US-Spaces diesen Wert in `nuxt.config.ts` entfernen oder auf `'us'` setzen.

---

## Lizenz

MIT
