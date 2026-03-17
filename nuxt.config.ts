export default defineNuxtConfig({
  compatibilityDate: "2026-03-16",
  runtimeConfig: {
    public: {
      storyblokVersion:
        process.env.NODE_ENV === "production" ? "published" : "draft",
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
  modules: ["@storyblok/nuxt", "@nuxtjs/tailwindcss"],
  storyblok: {
    accessToken: process.env.STORYBLOK_DELIVERY_API_TOKEN,
    apiOptions: {
      region: "eu",
    },
    componentsDir: "~/storyblok",
  },
  components: {
    global: true,
    dirs: [
      { path: "~/components", pathPrefix: false },
      { path: "~/storyblok", pathPrefix: false, global: true },
    ],
  },
});
