<template>
  <figure
    v-editable="blok"
    class="flex flex-col items-center bg-white rounded-2xl px-5 py-6"
  >
    <!-- Photo -->
    <div
      class="w-28 h-28 md:w-32 md:h-32 rounded-full overflow-hidden shrink-0 ring-4 ring-[#F5F4F2]"
    >
      <img
        v-if="blok.image?.filename"
        :src="optimizedSrc"
        :alt="blok.image.alt || blok.name"
        class="w-full h-full object-cover"
        loading="lazy"
        decoding="async"
      />
      <!-- Placeholder if no image -->
      <div
        v-else
        class="w-full h-full bg-[#f5f4f2] flex items-center justify-center"
      >
        <svg
          class="w-1/2 h-1/2 text-gray-300"
          viewBox="0 0 24 24"
          fill="currentColor"
        >
          <path
            d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"
          />
        </svg>
      </div>
    </div>

    <!-- Info -->
    <figcaption class="mt-4 text-center">
      <p class="font-bold text-[#231F21] text-sm leading-tight">
        {{ blok.name }}
      </p>
      <p
        v-if="blok.role"
        class="text-xs text-[#D0992A] font-semibold mt-1 uppercase tracking-wide"
      >
        {{ blok.role }}
      </p>
      <p v-if="blok.bio" class="text-xs text-gray-500 mt-2 leading-relaxed">
        {{ blok.bio }}
      </p>
    </figcaption>
  </figure>
</template>

<script setup>
const props = defineProps({ blok: Object });
const optimizedSrc = computed(() => {
  const url = props.blok?.image?.filename;
  if (!url) return "";
  return url + "/m/400x400/filters:format(webp):quality(85)";
});
</script>
