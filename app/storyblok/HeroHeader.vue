<template>
  <div v-editable="blok">
    <HeroHeader
      :headline="blok.headline"
      :subline="blok.subline"
      :cta-label="blok.cta_label"
      :resolved-link="resolvedLink"
      :cta-variant="blok.cta_variant || 'primary'"
    />
  </div>
</template>

<script setup>
const props = defineProps({ blok: Object })

const resolvedLink = computed(() => {
  const link = props.blok?.cta_link
  if (!link || !link.linktype) return ''
  if (link.linktype === 'email') return `mailto:${link.email}`
  if (link.linktype === 'url') return link.url || ''
  // story or asset: use cached_url
  const url = link.cached_url || ''
  return url ? `/${url}` : ''
})
</script>
