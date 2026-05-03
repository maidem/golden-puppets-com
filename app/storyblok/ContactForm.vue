<template>
  <section v-editable="blok" class="py-16 px-4">
    <div class="max-w-2xl mx-auto">
      <header v-if="blok.headline || blok.intro" class="mb-8 text-center">
        <h2
          v-if="blok.headline"
          class="text-3xl md:text-4xl font-bold text-gray-900"
        >
          {{ blok.headline }}
        </h2>
        <p v-if="blok.intro" class="mt-3 text-gray-500 whitespace-pre-line">
          {{ blok.intro }}
        </p>
      </header>

      <form
        :action="blok.action_url || '/webhook/contact.php'"
        method="post"
        class="space-y-5"
        novalidate
        @submit="onSubmit"
      >
        <!-- Honeypot (für Bots) -->
        <div class="hidden" aria-hidden="true">
          <label
            >Website
            <input
              type="text"
              name="website"
              tabindex="-1"
              autocomplete="off"
            />
          </label>
        </div>

        <input
          type="hidden"
          name="recipient"
          :value="blok.recipient_email || ''"
        />

        <div>
          <label
            for="cf-name"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            Name <span class="text-red-500">*</span>
          </label>
          <input
            id="cf-name"
            type="text"
            name="name"
            required
            autocomplete="name"
            class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-900"
          />
        </div>

        <div>
          <label
            for="cf-email"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            E-Mail <span class="text-red-500">*</span>
          </label>
          <input
            id="cf-email"
            type="email"
            name="email"
            required
            autocomplete="email"
            class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-900"
          />
        </div>

        <div>
          <label
            for="cf-subject"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            Betreff <span class="text-red-500">*</span>
          </label>
          <input
            id="cf-subject"
            type="text"
            name="subject"
            required
            class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-900"
          />
        </div>

        <div>
          <label
            for="cf-message"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            Nachricht <span class="text-red-500">*</span>
          </label>
          <textarea
            id="cf-message"
            name="message"
            rows="5"
            required
            class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-900"
          />
        </div>

        <div class="flex items-start gap-2">
          <input
            id="cf-privacy"
            type="checkbox"
            name="privacy"
            value="1"
            required
            class="mt-1 h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900"
          />
          <label for="cf-privacy" class="text-sm text-gray-600">
            <template v-if="privacyHref">
              {{ privacyLabelStart }}
              <NuxtLink :to="privacyHref" class="underline hover:text-gray-900">
                Datenschutzerklärung
              </NuxtLink>
              {{ privacyLabelEnd }}
            </template>
            <template v-else>
              {{
                blok.privacy_label || "Ich akzeptiere die Datenschutzerklärung."
              }}
            </template>
            <span class="text-red-500">*</span>
          </label>
        </div>

        <button
          type="submit"
          class="w-full rounded bg-gray-900 text-white py-3 px-6 font-medium hover:bg-black transition"
        >
          {{ blok.submit_label || "Senden" }}
        </button>
      </form>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({ blok: Object });

// Privacy-Link auflösen (Storyblok multilink)
const privacyHref = computed(() => {
  const link = props.blok?.privacy_link;
  if (!link) return "";
  if (link.linktype === "url") return link.url || "";
  const url = link.cached_url || link.url || "";
  if (!url) return "";
  return url.startsWith("/") || url.startsWith("http") ? url : `/${url}`;
});

// Label-Aufteilung: ersetzt das Wort "Datenschutzerklärung" mit verlinkter Variante
const labelParts = computed(() => {
  const label =
    props.blok?.privacy_label || "Ich akzeptiere die Datenschutzerklärung.";
  const idx = label.toLowerCase().indexOf("datenschutzerklärung");
  if (idx === -1) return { start: label, end: "" };
  return {
    start: label.slice(0, idx),
    end: label.slice(idx + "datenschutzerklärung".length),
  };
});
const privacyLabelStart = computed(() => labelParts.value.start);
const privacyLabelEnd = computed(() => labelParts.value.end);

function onSubmit(e) {
  // Minimale Client-Validierung – Browser-Validierung übernimmt den Rest.
  const form = e.target;
  if (!form.checkValidity()) {
    e.preventDefault();
    form.reportValidity();
  }
}
</script>
