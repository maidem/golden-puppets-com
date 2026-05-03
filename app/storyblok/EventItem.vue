<template>
  <NuxtLink
    v-if="href"
    v-editable="blok"
    :to="href"
    :target="isExternal ? '_blank' : undefined"
    :rel="isExternal ? 'noopener noreferrer' : undefined"
    class="inline-flex items-center gap-2 hover:underline"
  >
    <span v-if="formattedDate" class="opacity-70 tabular-nums">{{
      formattedDate
    }}</span>
    <span>{{ blok.label }}</span>
  </NuxtLink>
  <span v-else v-editable="blok" class="inline-flex items-center gap-2">
    <span v-if="formattedDate" class="opacity-70 tabular-nums">{{
      formattedDate
    }}</span>
    <span>{{ blok.label }}</span>
  </span>
</template>

<script setup>
const props = defineProps({ blok: Object });

const href = computed(() => {
  const link = props.blok?.link;
  if (!link) return "";
  if (link.linktype === "email")
    return link.email ? `mailto:${link.email}` : "";
  if (link.linktype === "url") return link.url || "";
  const url = link.cached_url || link.url || "";
  if (!url) return "";
  return url.startsWith("/") || url.startsWith("http") ? url : `/${url}`;
});

const isExternal = computed(
  () => href.value.startsWith("http") || href.value.startsWith("mailto:"),
);

const formattedDate = computed(() => {
  if (!props.blok?.date) return "";
  const d = new Date(props.blok.date);
  if (Number.isNaN(d.getTime())) return "";
  return d.toLocaleDateString("de-DE", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
});
</script>
