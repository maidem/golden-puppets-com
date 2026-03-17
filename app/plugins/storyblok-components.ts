import Feature from '~/storyblok/Feature.vue'
import Grid from '~/storyblok/Grid.vue'
import Header from '~/storyblok/Header.vue'
import HeroHeader from '~/storyblok/HeroHeader.vue'
import NavItem from '~/storyblok/NavItem.vue'
import NewsArticle from '~/storyblok/NewsArticle.vue'
import NewsOverview from '~/storyblok/NewsOverview.vue'
import Page from '~/storyblok/Page.vue'
import Teaser from '~/storyblok/Teaser.vue'

export default defineNuxtPlugin((nuxtApp) => {
  nuxtApp.vueApp.component('Feature', Feature)
  nuxtApp.vueApp.component('Grid', Grid)
  nuxtApp.vueApp.component('Header', Header)
  nuxtApp.vueApp.component('HeroHeader', HeroHeader)
  nuxtApp.vueApp.component('NavItem', NavItem)
  nuxtApp.vueApp.component('NewsArticle', NewsArticle)
  nuxtApp.vueApp.component('NewsOverview', NewsOverview)
  nuxtApp.vueApp.component('Page', Page)
  nuxtApp.vueApp.component('Teaser', Teaser)
})
