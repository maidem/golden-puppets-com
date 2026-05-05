<template>
  <section
    v-editable="blok"
    :id="blok.section_id || 'kontakt'"
    class="py-12 md:py-16 px-5 md:px-16"
  >
    <!-- 50/50 Layout -->
    <div class="flex flex-col md:flex-row gap-0">
      <!-- Links: Kontaktinfos -->
      <div class="w-full md:w-1/2 pr-0 md:pr-16 pb-10 md:pb-0">
        <p class="text-3xl md:text-5xl font-black pt-6 mb-3">
          {{ blok.contact_title || "Kontakt" }}
        </p>

        <p v-if="blok.headline" class="text-xl md:text-2xl font-semibold mb-8">
          {{ blok.headline }}
        </p>

        <div class="space-y-5 text-base">
          <div v-if="blok.address" class="flex flex-col gap-0.5">
            <span
              class="text-xs font-bold tracking-widest uppercase text-gray-400"
              >Adresse</span
            >
            <address
              class="not-italic font-semibold whitespace-pre-line leading-relaxed"
            >
              {{ blok.address }}
            </address>
          </div>

          <div v-if="blok.contact_email" class="flex flex-col gap-0.5">
            <span
              class="text-xs font-bold tracking-widest uppercase text-gray-400"
              >E-Mail</span
            >
            <a
              :href="`mailto:${blok.contact_email}`"
              class="font-semibold hover:text-[#D0992A] transition-colors"
            >
              {{ blok.contact_email }}
            </a>
          </div>

          <div v-if="blok.phone" class="flex flex-col gap-0.5">
            <span
              class="text-xs font-bold tracking-widest uppercase text-gray-400"
              >Telefon</span
            >
            <a
              :href="`tel:${blok.phone.replace(/\s/g, '')}`"
              class="font-semibold hover:text-[#D0992A] transition-colors"
            >
              {{ blok.phone }}
            </a>
          </div>

          <div v-if="blok.maps_url">
            <a
              :href="blok.maps_url"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center gap-2 bg-[#D0992A] text-[#231F21] font-bold text-sm px-5 py-2.5 rounded-full border-2 border-[#D0992A] hover:bg-transparent hover:text-[#D0992A] transition-colors duration-200 mt-3"
            >
              <svg
                class="w-4 h-4 shrink-0"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                <circle cx="12" cy="10" r="3" />
              </svg>
              {{ blok.maps_label || "Route anzeigen" }}
            </a>
          </div>
        </div>
      </div>

      <!-- Rechts: Formular -->
      <div class="w-full md:w-1/2 pl-0 md:pl-16 pt-6 md:pt-20">
        <form
          :action="blok.action_url || '/webhook/contact.php'"
          method="post"
          class="space-y-0"
          novalidate
          @submit="onSubmit"
        >
          <!-- Honeypot -->
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

          <!-- Name -->
          <div class="border-b-2 border-black py-4">
            <label
              for="cf-name"
              class="block text-xs font-bold tracking-widest uppercase text-gray-400 mb-1"
            >
              Name <span class="text-red-500">*</span>
            </label>
            <input
              id="cf-name"
              type="text"
              name="name"
              required
              autocomplete="name"
              class="w-full bg-transparent text-base font-medium focus:outline-none placeholder:text-gray-300"
              placeholder="Dein Name"
            />
          </div>

          <!-- E-Mail -->
          <div class="border-b-2 border-black py-4">
            <label
              for="cf-email"
              class="block text-xs font-bold tracking-widest uppercase text-gray-400 mb-1"
            >
              E-Mail <span class="text-red-500">*</span>
            </label>
            <input
              id="cf-email"
              type="email"
              name="email"
              required
              autocomplete="email"
              class="w-full bg-transparent text-base font-medium focus:outline-none placeholder:text-gray-300"
              placeholder="deine@email.de"
            />
          </div>

          <!-- Betreff -->
          <div class="border-b-2 border-black py-4">
            <label
              for="cf-subject"
              class="block text-xs font-bold tracking-widest uppercase text-gray-400 mb-1"
            >
              Betreff <span class="text-red-500">*</span>
            </label>
            <input
              id="cf-subject"
              type="text"
              name="subject"
              required
              class="w-full bg-transparent text-base font-medium focus:outline-none placeholder:text-gray-300"
              placeholder="Worum geht's?"
            />
          </div>

          <!-- Nachricht -->
          <div class="border-b-2 border-black py-4">
            <label
              for="cf-message"
              class="block text-xs font-bold tracking-widest uppercase text-gray-400 mb-1"
            >
              Nachricht <span class="text-red-500">*</span>
            </label>
            <textarea
              id="cf-message"
              name="message"
              rows="4"
              required
              class="w-full bg-transparent text-base font-medium focus:outline-none resize-none placeholder:text-gray-300"
              placeholder="Deine Nachricht…"
            />
          </div>

          <!-- Datenschutz -->
          <div class="flex items-start gap-3 pt-5">
            <input
              id="cf-privacy"
              type="checkbox"
              name="privacy"
              value="1"
              required
              class="mt-0.5 h-4 w-4 shrink-0 rounded border-2 border-black accent-[#D0992A]"
            />
            <label for="cf-privacy" class="text-sm text-gray-500 leading-snug">
              <template v-if="privacyHref">
                {{ privacyLabelStart }}
                <NuxtLink :to="privacyHref" class="underline hover:text-black"
                  >Datenschutzerklärung</NuxtLink
                >
                {{ privacyLabelEnd }}
              </template>
              <template v-else>
                {{
                  blok.privacy_label ||
                  "Ich akzeptiere die Datenschutzerklärung."
                }}
              </template>
              <span class="text-red-500">*</span>
            </label>
          </div>

          <!-- Submit -->
          <div class="pt-6">
            <button
              type="submit"
              class="inline-flex items-center gap-2 bg-[#D0992A] text-[#231F21] font-bold text-base px-7 py-3.5 rounded-full border-2 border-[#D0992A] hover:bg-transparent hover:text-[#D0992A] transition-colors duration-200"
            >
              {{ blok.submit_label || "Nachricht senden" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({ blok: Object });

const privacyHref = computed(() => {
  const link = props.blok?.privacy_link;
  if (!link) return "";
  if (link.linktype === "url") return link.url || "";
  const url = link.cached_url || link.url || "";
  if (!url) return "";
  return url.startsWith("/") || url.startsWith("http") ? url : `/${url}`;
});

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
  const form = e.target;
  if (!form.checkValidity()) {
    e.preventDefault();
    form.reportValidity();
  }
}
</script>
