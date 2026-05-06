<template>
  <NavLink :to="resolvedTarget">
    {{ blok.label }}
  </NavLink>
</template>

<script setup>
const props = defineProps({ blok: Object });

const normalizedLabel = computed(() =>
  (props.blok?.label || "")
    .trim()
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, ""),
);

const labelToSectionId = computed(() => {
  const map = {
    home: "home",
    "uber uns": "ueber-uns",
    "über uns": "ueber-uns",
    team: "team",
    spenden: "spenden",
    aktuelles: "aktuelles",
    kontakt: "kontakt",
    faq: "faq",
  };
  return map[normalizedLabel.value] || null;
});

const resolvedTarget = computed(() => {
  // 1. Explizite section_id hat Vorrang
  if (props.blok?.section_id) {
    return `/#${props.blok.section_id}`;
  }

  // 2. Label zu Section ID mappen
  if (labelToSectionId.value) {
    return `/#${labelToSectionId.value}`;
  }

  // 3. Expliziter Link
  const link = props.blok?.link;
  if (link) {
    if (link.linktype === "url") return link.url || "/";
    const url = link.cached_url || link.url || "";
    return url.startsWith("/") || url.startsWith("#") ? url : `/${url}`;
  }

  return "/";
});
</script>
