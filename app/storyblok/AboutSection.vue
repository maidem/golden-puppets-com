<template>
  <section
    v-editable="blok"
    :id="blok.section_id || 'ueber-uns'"
    class="py-16 md:py-24 px-5 md:px-16"
  >
    <div class="max-w-[1400px] mx-auto">
      <div
        class="relative flex flex-col gap-10 md:gap-16"
        :class="imageRight ? 'md:flex-row' : 'md:flex-row-reverse'"
      >
        <!-- Text-Spalte -->
        <div class="w-full md:w-[54%] flex flex-col justify-center">
          <p
            v-if="blok.subline"
            class="text-xs font-bold tracking-[0.18em] uppercase text-[#231F21]/70 mb-4"
          >
            {{ blok.subline }}
          </p>

          <h2
            v-if="displayHeadline"
            class="text-2xl md:text-4xl font-bold leading-[1.1] tracking-tight text-black mb-7"
          >
            {{ displayHeadline }}<span class="text-[#D0992A]">.</span>
          </h2>

          <div
            v-if="blok.text"
            class="max-w-[62ch] text-[1rem] md:text-[1.04rem] leading-8 text-gray-700 [&>p]:mb-5 [&>p:first-child]:text-black [&>p:first-child]:text-[1.08rem] [&>p:first-child]:font-medium"
            v-html="renderedText"
          />

          <div v-if="blok.cta_label && resolvedLink" class="mt-8">
            <NuxtLink
              :to="resolvedLink"
              :target="isExternal ? '_blank' : undefined"
              :rel="isExternal ? 'noopener noreferrer' : undefined"
              class="inline-flex items-center gap-2 bg-[#231F21] text-white font-bold text-base px-7 py-3 rounded-full border-2 border-[#231F21] hover:bg-black hover:border-black transition-colors duration-200"
            >
              {{ blok.cta_label }}
            </NuxtLink>
          </div>
        </div>

        <!-- Bild-Spalte -->
        <div
          v-if="blok.image?.filename"
          class="w-full md:w-[46%] flex items-start relative"
          :class="imageRight ? 'md:justify-end' : 'md:justify-start'"
        >
          <div class="w-full md:max-w-[34rem] relative">
            <div
              class="hidden md:block absolute -inset-3 rounded-[2rem] border border-black/10 bg-white/35"
              aria-hidden="true"
            ></div>

            <img
              :src="optimizedSrc"
              :alt="blok.image_alt || blok.image.alt || ''"
              class="relative w-full aspect-[5/4] max-h-[30rem] rounded-[1.75rem] object-cover shadow-[0_30px_70px_rgba(0,0,0,0.22)]"
              loading="lazy"
              decoding="async"
            />

            <!-- Info card removed -->
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { renderRichText } from "@storyblok/vue";
import { computed } from "vue";

const props = defineProps({ blok: Object });

const imageRight = computed(
  () => !props.blok.image_position || props.blok.image_position === "right",
);

const renderedText = computed(() =>
  props.blok.text ? renderRichText(props.blok.text) : "",
);

const displayHeadline = computed(() => {
  const raw = (props.blok?.headline || "").trim();
  if (!raw) return "Seniorenteam";
  const normalized = raw.toLowerCase();
  return normalized === "über uns" || normalized === "ueber uns"
    ? "Seniorenteam"
    : raw;
});

const optimizedSrc = computed(() => {
  const url = props.blok.image?.filename;
  if (!url) return "";
  return url + "/m/760x0/filters:format(webp):quality(85)";
});

const resolvedLink = computed(() => {
  const link = props.blok.cta_link;
  if (!link) return null;
  if (link.linktype === "url" || link.linktype === "asset") return link.url;
  if (link.linktype === "story")
    return "/" + (link.story?.full_slug || link.cached_url || "");
  return null;
});

const isExternal = computed(() => {
  const link = props.blok.cta_link;
  return link?.linktype === "url" || link?.linktype === "asset";
});
</script>
