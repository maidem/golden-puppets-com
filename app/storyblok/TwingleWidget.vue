<template>
  <section
    v-editable="blok"
    :id="blok.section_id || 'spenden'"
    class="py-16 px-4"
  >
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

      <ClientOnly>
        <div
          v-if="finalUrl"
          :id="containerId"
          ref="container"
          class="twingle-widget"
        />
        <p v-else class="text-sm text-red-500 text-center">
          Bitte im Storyblok eine Twingle Embed-URL hinterlegen.
        </p>

        <template #fallback>
          <div
            class="h-96 flex items-center justify-center text-gray-400 text-sm"
          >
            Spendenformular wird geladen…
          </div>
        </template>
      </ClientOnly>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({ blok: Object });

// Eindeutige ID je Instanz (für mehrere Widgets pro Seite)
const uid = Math.random().toString(36).slice(2, 11);
const containerId = `twingle-public-embed-_${uid}`;
const scriptId = `tw-_${uid}`;

const container = ref(null);

const finalUrl = computed(() => {
  const base = (props.blok?.embed_url || "").trim();
  if (!base) return "";

  const params = new URLSearchParams();
  if (props.blok.preset_amount)
    params.set("tw_amount", String(props.blok.preset_amount));
  if (props.blok.preset_rhythm)
    params.set("tw_rhythm", props.blok.preset_rhythm);
  if (props.blok.preset_target) {
    params.set(
      props.blok.preset_target_fix ? "tw_target_fix" : "tw_target",
      props.blok.preset_target,
    );
  }

  // Extra-Params aus dem freien Feld zusammenführen
  const extra = (props.blok.extra_params || "").replace(/^[?&]+/, "");
  if (extra) {
    for (const pair of extra.split("&")) {
      const [k, v = ""] = pair.split("=");
      if (k) params.set(k, v);
    }
  }

  const query = params.toString();
  if (!query) return base;
  return base + (base.includes("?") ? "&" : "?") + query;
});

function loadWidget() {
  if (typeof document === "undefined") return;
  if (!finalUrl.value) return;
  // Bereits geladenes Script entfernen (z. B. nach Live-Edit im Visual Editor)
  const existing = document.getElementById(scriptId);
  if (existing) existing.remove();
  if (container.value) container.value.innerHTML = "";

  const script = document.createElement("script");
  script.type = "text/javascript";
  script.async = true;
  script.defer = true;
  script.id = scriptId;
  script.src = finalUrl.value;
  // Twingle-Embed sucht den Container per ID `twingle-public-embed-<id>`
  document.body.appendChild(script);
}

onMounted(loadWidget);
onBeforeUnmount(() => {
  const existing = document.getElementById(scriptId);
  if (existing) existing.remove();
});

// Visual-Editor-Updates: bei URL-Änderung neu laden
watch(finalUrl, () => loadWidget());
</script>

<style scoped>
.twingle-widget {
  min-height: 450px;
}
</style>
