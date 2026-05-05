<template>
  <div v-editable="blok">
    <NavLink :to="resolvedTarget">
      {{ blok.label }}
    </NavLink>
  </div>
</template>

<script setup>
const props = defineProps({ blok: Object });

const normalizedSectionId = computed(() =>
  (props.blok?.section_id || "").trim(),
);

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
    team: "team",
    spenden: "spenden",
    aktuelles: "aktuelles",
    kontakt: "kontakt",
    faq: "faq",
  };
  return map[normalizedLabel.value] || "";
});

const resolvedTarget = computed(() => {
  if (normalizedSectionId.value) return `/#${normalizedSectionId.value}`;

  const link = props.blok?.link;
  if (!link) return labelToSectionId.value ? `/#${labelToSectionId.value}` : "/";

  const isRootLink =
    (link.linktype === "url" && (link.url || "").trim() === "/") ||
    (link.linktype !== "url" && (link.cached_url || link.url || "").trim() === "");

  if (isRootLink && labelToSectionId.value) {
    return `/#${labelToSectionId.value}`;
  }

  if (link.linktype === "url") return link.url || "/";

  const url = link.cached_url || link.url || "";
  if (!url) return "/";
  return url.startsWith("/") || url.startsWith("#") ? url : `/${url}`;
});
</script>
