import type { RouterConfig } from "@nuxt/schema";

export default <RouterConfig>{
  scrollBehavior(_to, _from, savedPosition) {
    if (savedPosition) return savedPosition;
    // instant verhindert den sichtbaren smooth-scroll durch css scroll-behavior
    return { top: 0, left: 0, behavior: "instant" };
  },
};
