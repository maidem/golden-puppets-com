export default defineNuxtConfig({
  compatibilityDate: "2026-03-16",
  runtimeConfig: {
    public: {
      storyblokVersion:
        process.env.STORYBLOK_VERSION ||
        (process.env.NODE_ENV === "production" ? "published" : "draft"),
    },
  },
  future: {
    compatibilityVersion: 4,
  },
  devServer: {
    port: 3000,
    https: {
      key: "./localhost-key.pem",
      cert: "./localhost.pem",
    },
  },
  devtools: { enabled: true },
  vue: {
    compilerOptions: {
      isCustomElement: (tag) =>
        tag.startsWith("pretix-") ||
        tag === "pretix-widget" ||
        tag === "pretix-button",
    },
  },
  css: ["~/assets/css/main.css"],
  modules: ["@storyblok/nuxt", "nuxt-security"],
  vite: {
    plugins: [(await import("@tailwindcss/vite")).default()],
  },
  storyblok: {
    accessToken: process.env.STORYBLOK_DELIVERY_API_TOKEN,
    apiOptions: {
      region: "eu",
    },
  },
  components: {
    global: true,
    dirs: [{ path: "~/components", pathPrefix: false }],
  },
  security: {
    // Im Visual Editor / Dev erst mal nur Report-Mode, damit nichts blockiert wird
    headers: {
      contentSecurityPolicy: {
        "base-uri": ["'self'"],
        "object-src": ["'none'"],
        "script-src": [
          "'self'",
          "'unsafe-inline'", // Nuxt Hydration-Inline-Scripts
          "'unsafe-eval'", // von pretix-widget benötigt
          "https://app.storyblok.com",
          "https://app.storyblok.io",
          "https://a.storyblok.com",
          "https://pretix.eu",
          "https://spenden.twingle.de",
          "https://*.twingle.de",
        ],
        "script-src-attr": ["'unsafe-inline'"],
        "style-src": [
          "'self'",
          "'unsafe-inline'", // Tailwind / Nuxt Inline-Styles
          "https://pretix.eu",
          "https://app.storyblok.com",
          "https://*.twingle.de",
        ],
        "img-src": [
          "'self'",
          "data:",
          "blob:",
          "https://a.storyblok.com",
          "https://img2.storyblok.com",
          "https://app.storyblok.com",
          "https://pretix.eu",
          "https://cdn.pretix.space",
          "https://*.twingle.de",
        ],
        "font-src": ["'self'", "data:", "https://pretix.eu"],
        "connect-src": [
          "'self'",
          "https://api.storyblok.com",
          "https://api-eu.storyblok.com",
          "https://api-us.storyblok.com",
          "https://app.storyblok.com",
          "https://mapi.storyblok.com",
          "https://pretix.eu",
          "https://spenden.twingle.de",
          "https://*.twingle.de",
        ],
        "frame-src": [
          "'self'",
          "https://app.storyblok.com",
          "https://pretix.eu",
          "https://spenden.twingle.de",
          "https://*.twingle.de",
        ],
        "frame-ancestors": [
          "'self'",
          "https://app.storyblok.com",
          "https://app.storyblok.io",
        ],
        "form-action": ["'self'", "https://pretix.eu"],
        "upgrade-insecure-requests": true,
      },
      crossOriginEmbedderPolicy: "unsafe-none", // pretix-widget braucht das
      crossOriginOpenerPolicy: "same-origin-allow-popups", // PayPal-Popup im Pretix-Checkout
      crossOriginResourcePolicy: "cross-origin",
      strictTransportSecurity: {
        maxAge: 15552000, // 180 Tage
        includeSubdomains: true,
      },
      xFrameOptions: false, // wird durch frame-ancestors abgedeckt
      referrerPolicy: "strict-origin-when-cross-origin",
      permissionsPolicy: {
        camera: [],
        microphone: [],
        geolocation: [],
      },
    },
    // Storyblok Visual Editor lädt unsere Seite in einem iframe
    rateLimiter: false,
    requestSizeLimiter: false,
    xssValidator: false,
    corsHandler: false,
  },
});
