<template>
  <section
    v-editable="blok"
    :id="blok.section_id || 'events'"
    class="py-16 px-4"
  >
    <div class="max-w-3xl mx-auto">
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

      <ClientOnly>
        <div v-if="eventUrl" class="pretix-wrapper">
          <component
            :is="'pretix-widget'"
            :key="widgetKey"
            :event="eventUrl"
            :list-type="blok.list_type || undefined"
            :filter="blok.filter || undefined"
            :voucher="blok.voucher || undefined"
            :disable-vouchers="blok.disable_vouchers ? '' : undefined"
            :disable-iframe="blok.disable_iframe ? '' : undefined"
            :display-event-info="
              blok.display_event_info && blok.display_event_info !== 'auto'
                ? blok.display_event_info
                : undefined
            "
          />
          <noscript>
            <div class="pretix-widget">
              <div class="pretix-widget-info-message">
                JavaScript ist deaktiviert. Bitte
                <a :href="eventUrl" target="_blank" rel="noopener"
                  >hier klicken</a
                >, um direkt zum Ticketshop zu gelangen.
              </div>
            </div>
          </noscript>
        </div>
        <p v-else class="text-sm text-red-500 text-center">
          Bitte im Storyblok einen Veranstalter-Slug hinterlegen.
        </p>

        <template #fallback>
          <div
            class="h-96 flex items-center justify-center text-gray-400 text-sm"
          >
            Veranstaltungen werden geladen…
          </div>
        </template>
      </ClientOnly>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({ blok: Object });

const language = computed(() => (props.blok?.language === "en" ? "en" : "de"));

const baseUrl = computed(() =>
  (props.blok?.base_url || "https://pretix.eu").replace(/\/+$/, ""),
);

const eventUrl = computed(() => {
  const org = (props.blok?.organizer_slug || "")
    .trim()
    .replace(/^\/+|\/+$/g, "");
  if (!org) return "";
  const evt = (props.blok?.event_slug || "").trim().replace(/^\/+|\/+$/g, "");
  return evt ? `${baseUrl.value}/${org}/${evt}/` : `${baseUrl.value}/${org}/`;
});

const widgetKey = computed(() =>
  [
    eventUrl.value,
    props.blok?.list_type,
    props.blok?.filter,
    props.blok?.voucher,
    props.blok?.disable_vouchers,
    props.blok?.disable_iframe,
    props.blok?.display_event_info,
  ].join("|"),
);

const styleId = "pretix-widget-style";
const scriptId = computed(() => `pretix-widget-script-${language.value}`);

function ensureAssets() {
  if (typeof document === "undefined") return;
  if (!document.getElementById(styleId)) {
    const link = document.createElement("link");
    link.id = styleId;
    link.rel = "stylesheet";
    link.type = "text/css";
    link.href = `${baseUrl.value}/widget/v2.css`;
    document.head.appendChild(link);
  }
  if (!document.getElementById(scriptId.value)) {
    const script = document.createElement("script");
    script.id = scriptId.value;
    script.type = "text/javascript";
    script.async = true;
    script.src = `${baseUrl.value}/widget/v2.${language.value}.js`;
    document.body.appendChild(script);
  } else if (
    window.PretixWidget &&
    typeof window.PretixWidget.buildWidgets === "function"
  ) {
    window.PretixWidget.buildWidgets();
  }
}

onMounted(ensureAssets);
watch([eventUrl, language, widgetKey], () => {
  nextTick(ensureAssets);
});
</script>

<style scoped>
.pretix-wrapper {
  min-height: 200px;
}
</style>
