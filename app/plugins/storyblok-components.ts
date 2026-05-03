import { defineAsyncComponent } from "vue";

export default defineNuxtPlugin((nuxtApp) => {
  const components: Record<string, ReturnType<typeof defineAsyncComponent>> = {
    ContactForm: defineAsyncComponent(
      () => import("~/storyblok/ContactForm.vue"),
    ),
    contact_form: defineAsyncComponent(
      () => import("~/storyblok/ContactForm.vue"),
    ),
    EventItem: defineAsyncComponent(() => import("~/storyblok/EventItem.vue")),
    event_item: defineAsyncComponent(() => import("~/storyblok/EventItem.vue")),
    Feature: defineAsyncComponent(() => import("~/storyblok/Feature.vue")),
    feature: defineAsyncComponent(() => import("~/storyblok/Feature.vue")),
    Footer: defineAsyncComponent(() => import("~/storyblok/Footer.vue")),
    footer: defineAsyncComponent(() => import("~/storyblok/Footer.vue")),
    Grid: defineAsyncComponent(() => import("~/storyblok/Grid.vue")),
    grid: defineAsyncComponent(() => import("~/storyblok/Grid.vue")),
    Header: defineAsyncComponent(() => import("~/storyblok/Header.vue")),
    header: defineAsyncComponent(() => import("~/storyblok/Header.vue")),
    HeroHeader: defineAsyncComponent(
      () => import("~/storyblok/HeroHeader.vue"),
    ),
    hero_header: defineAsyncComponent(
      () => import("~/storyblok/HeroHeader.vue"),
    ),
    ImageGrid: defineAsyncComponent(() => import("~/storyblok/ImageGrid.vue")),
    image_grid: defineAsyncComponent(() => import("~/storyblok/ImageGrid.vue")),
    NavItem: defineAsyncComponent(() => import("~/storyblok/NavItem.vue")),
    nav_item: defineAsyncComponent(() => import("~/storyblok/NavItem.vue")),
    NewsArticle: defineAsyncComponent(
      () => import("~/storyblok/NewsArticle.vue"),
    ),
    news_article: defineAsyncComponent(
      () => import("~/storyblok/NewsArticle.vue"),
    ),
    NewsOverview: defineAsyncComponent(
      () => import("~/storyblok/NewsOverview.vue"),
    ),
    news_overview: defineAsyncComponent(
      () => import("~/storyblok/NewsOverview.vue"),
    ),
    Page: defineAsyncComponent(() => import("~/storyblok/Page.vue")),
    page: defineAsyncComponent(() => import("~/storyblok/Page.vue")),
    PretixEvents: defineAsyncComponent(
      () => import("~/storyblok/PretixEvents.vue"),
    ),
    pretix_events: defineAsyncComponent(
      () => import("~/storyblok/PretixEvents.vue"),
    ),
    Teaser: defineAsyncComponent(() => import("~/storyblok/Teaser.vue")),
    teaser: defineAsyncComponent(() => import("~/storyblok/Teaser.vue")),
    TeamMember: defineAsyncComponent(
      () => import("~/storyblok/TeamMember.vue"),
    ),
    team_member: defineAsyncComponent(
      () => import("~/storyblok/TeamMember.vue"),
    ),
    TeamSection: defineAsyncComponent(
      () => import("~/storyblok/TeamSection.vue"),
    ),
    team_section: defineAsyncComponent(
      () => import("~/storyblok/TeamSection.vue"),
    ),
    TwingleWidget: defineAsyncComponent(
      () => import("~/storyblok/TwingleWidget.vue"),
    ),
    twingle_widget: defineAsyncComponent(
      () => import("~/storyblok/TwingleWidget.vue"),
    ),
  };

  for (const [name, component] of Object.entries(components)) {
    nuxtApp.vueApp.component(name, component);
  }
});
