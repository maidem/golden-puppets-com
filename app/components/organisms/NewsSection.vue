<template>
  <section class="py-16 px-4 bg-gray-50">
    <div class="max-w-6xl mx-auto">
      <BaseHeading tag="h2" size="lg" class="text-center mb-12 text-gray-900">
        {{ headline }}
      </BaseHeading>

      <div
        v-if="articles.length"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8"
      >
        <NewsCard
          v-for="article in articles"
          :key="article.slug"
          :image="article.image"
          :headline="article.headline"
          :teaser-text="article.teaserText"
          :link="article.slug"
        />
      </div>

      <p v-else class="text-center text-gray-500 text-lg">
        Keine Beiträge vorhanden.
      </p>
    </div>
  </section>
</template>

<script setup>
defineProps({
  headline: { type: String, default: 'Aktuelles' },
  articles: { type: Array, default: () => [] },
})
</script>
