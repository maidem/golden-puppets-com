<template>
  <section
    v-editable="blok"
    :id="blok.section_id || 'kontakt'"
    class="py-10 md:py-16 bg-[#f5f4f2] px-5 md:px-16"
  >
    <div class="max-w-[1400px] mx-auto">
      <!-- Überschrift — gleiche Größe wie FAQ -->
      <h2 class="text-3xl md:text-5xl font-black tracking-tight mb-8 md:mb-10">
        {{ blok.contact_title || "Kontakt"
        }}<span class="text-[#D0992A]">.</span>
      </h2>

      <!-- 50/50 Karten-Layout -->
      <div class="flex flex-col md:flex-row gap-5 md:gap-6 items-start">
        <!-- Links: Kontaktinfos als weiße Karte -->
        <div class="w-full md:w-[38%] bg-white rounded-2xl p-6 md:p-8">
          <div v-if="blok.logo?.filename" class="mb-6">
            <img
              :src="blok.logo.filename"
              :alt="blok.logo.alt || 'Golden Puppets Logo'"
              class="w-[clamp(8rem,20vw,12rem)] h-auto object-contain"
              loading="lazy"
              decoding="async"
            />
          </div>

          <p
            v-if="blok.headline"
            class="text-base font-semibold mb-5 text-gray-700"
          >
            {{ blok.headline }}
          </p>

          <div class="space-y-5 text-sm">
            <div v-if="blok.address" class="flex flex-col gap-1">
              <span
                class="text-[0.7rem] font-bold tracking-widest uppercase text-gray-400"
                >Adresse</span
              >
              <address
                class="not-italic font-medium whitespace-pre-line leading-relaxed text-gray-800"
              >
                {{ blok.address }}
              </address>
            </div>

            <div v-if="blok.contact_email" class="flex flex-col gap-1">
              <span
                class="text-[0.7rem] font-bold tracking-widest uppercase text-gray-400"
                >E-Mail</span
              >
              <a
                :href="`mailto:${blok.contact_email}`"
                class="font-medium text-gray-800 hover:text-[#D0992A] transition-colors"
              >
                {{ blok.contact_email }}
              </a>
            </div>

            <div v-if="blok.phone" class="flex flex-col gap-1">
              <span
                class="text-[0.7rem] font-bold tracking-widest uppercase text-gray-400"
                >Telefon</span
              >
              <a
                :href="`tel:${blok.phone.replace(/\s/g, '')}`"
                class="font-medium text-gray-800 hover:text-[#D0992A] transition-colors"
              >
                {{ blok.phone }}
              </a>
            </div>

            <div v-if="blok.maps_url" class="pt-1">
              <a
                :href="blok.maps_url"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 bg-[#231F21] text-white font-bold text-sm px-5 py-2.5 rounded-full hover:bg-black transition-colors duration-200"
              >
                <svg
                  class="w-3.5 h-3.5 shrink-0"
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

        <!-- Rechts: Formular als weiße Karte -->
        <div class="w-full md:w-[62%] bg-white rounded-2xl p-6 md:p-8">
          <form
            :action="blok.action_url || '/webhook/contact.php'"
            method="post"
            class="space-y-4"
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
            <div class="flex flex-col gap-1">
              <label
                for="cf-name"
                class="text-[0.7rem] font-bold tracking-widest uppercase text-gray-400"
              >
                Name <span class="text-red-500">*</span>
              </label>
              <input
                id="cf-name"
                type="text"
                name="name"
                required
                autocomplete="name"
                class="w-full bg-[#f5f4f2] rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#D0992A]/40 placeholder:text-gray-400"
                placeholder="Dein Name"
              />
            </div>

            <!-- E-Mail -->
            <div class="flex flex-col gap-1">
              <label
                for="cf-email"
                class="text-[0.7rem] font-bold tracking-widest uppercase text-gray-400"
              >
                E-Mail <span class="text-red-500">*</span>
              </label>
              <input
                id="cf-email"
                type="email"
                name="email"
                required
                autocomplete="email"
                class="w-full bg-[#f5f4f2] rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#D0992A]/40 placeholder:text-gray-400"
                placeholder="deine@email.de"
              />
            </div>

            <!-- Betreff -->
            <div class="flex flex-col gap-1">
              <label
                for="cf-subject"
                class="text-[0.7rem] font-bold tracking-widest uppercase text-gray-400"
              >
                Betreff <span class="text-red-500">*</span>
              </label>
              <input
                id="cf-subject"
                type="text"
                name="subject"
                required
                class="w-full bg-[#f5f4f2] rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#D0992A]/40 placeholder:text-gray-400"
                placeholder="Worum geht's?"
              />
            </div>

            <!-- Nachricht -->
            <div class="flex flex-col gap-1">
              <label
                for="cf-message"
                class="text-[0.7rem] font-bold tracking-widest uppercase text-gray-400"
              >
                Nachricht <span class="text-red-500">*</span>
              </label>
              <textarea
                id="cf-message"
                name="message"
                rows="5"
                required
                class="w-full bg-[#f5f4f2] rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#D0992A]/40 resize-none placeholder:text-gray-400"
                placeholder="Deine Nachricht…"
              />
            </div>

            <!-- Datenschutz -->
            <div class="flex items-start gap-3 pt-1">
              <input
                id="cf-privacy"
                type="checkbox"
                name="privacy"
                value="1"
                required
                class="mt-0.5 h-4 w-4 shrink-0 rounded accent-[#D0992A]"
              />
              <label
                for="cf-privacy"
                class="text-xs text-gray-500 leading-snug"
              >
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
            <div class="pt-2 flex justify-end">
              <button
                type="submit"
                class="inline-flex items-center gap-2 bg-[#231F21] text-white font-bold text-sm px-7 py-3 rounded-full hover:bg-black transition-colors duration-200"
              >
                {{ blok.submit_label || "Nachricht senden" }}
              </button>
            </div>
          </form>
        </div>
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
