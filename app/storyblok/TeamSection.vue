<template>
  <section
    v-editable="blok"
    :id="blok.section_id || 'team'"
    class="py-10 md:py-16 px-5 md:px-16"
  >
    <div class="max-w-[1400px] mx-auto">
      <!-- Headline links-ausgerichtet wie FAQ/Kontakt -->
      <div v-if="blok.headline || blok.subline" class="mb-7">
        <h2
          v-if="blok.headline"
          class="text-2xl md:text-4xl font-bold leading-[1.1] tracking-tight text-black"
        >
          {{ blok.headline }}<span class="text-[#D0992A]">.</span>
        </h2>
        <p v-if="blok.subline" class="mt-2 text-gray-500 text-base">
          {{ blok.subline }}
        </p>
      </div>

      <!-- Mobile: simple slider with nav; Desktop: grid -->
      <div>
        <!-- Mobile Swiper -->
        <div class="md:hidden">
          <!-- Navigation oben rechts -->
          <div class="flex justify-end items-center gap-2 mb-3">
            <button
              @click="teamMobileSwiper?.slidePrev()"
              class="w-9 h-9 flex items-center justify-center rounded-full bg-[#231F21] text-white font-bold text-lg hover:bg-black transition-colors"
              aria-label="Vorheriges Teammitglied"
            >‹</button>
            <button
              @click="teamMobileSwiper?.slideNext()"
              class="w-9 h-9 flex items-center justify-center rounded-full bg-[#231F21] text-white font-bold text-lg hover:bg-black transition-colors"
              aria-label="Nächstes Teammitglied"
            >›</button>
          </div>
          <Swiper
            v-if="(blok.members?.length || 0) > 0"
            :loop="(blok.members?.length || 0) > 1"
            :grab-cursor="true"
            :slides-per-view="1"
            @swiper="onTeamMobileSwiper"
          >
            <SwiperSlide v-for="member in blok.members" :key="member._uid">
              <div
                class="bg-white rounded-2xl overflow-hidden shadow-sm flex flex-col"
              >
                <!-- Member Image -->
                <div
                  v-if="member.image?.filename"
                  class="aspect-[4/3] overflow-hidden bg-gray-200"
                >
                  <img
                    :src="optimizeImage(member.image.filename, 400)"
                    :alt="member.name || 'Team Member'"
                    class="w-full h-full object-cover"
                  />
                </div>
                <div
                  v-else
                  class="aspect-[4/3] bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center"
                >
                  <span class="text-gray-300 text-4xl">👤</span>
                </div>

                <!-- Member Info -->
                <div class="p-4 flex flex-col flex-grow">
                  <h3 class="text-base font-bold text-black mb-1">
                    {{ member.name || "Team Member" }}
                  </h3>
                  <p
                    v-if="member.role"
                    class="text-xs text-[#D0992A] font-semibold mb-2"
                  >
                    {{ member.role }}
                  </p>
                  <p
                    v-if="member.description"
                    class="text-gray-600 text-xs leading-relaxed line-clamp-2"
                  >
                    {{ member.description }}
                  </p>
                </div>
              </div>
            </SwiperSlide>
          </Swiper>
        </div>

        <!-- Desktop grid with card styling -->
        <div class="hidden md:grid grid-cols-4 gap-4">
          <div
            v-for="member in blok.members"
            :key="member._uid"
            class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 flex flex-col"
          >
            <!-- Member Image -->
            <div
              v-if="member.image?.filename"
              class="aspect-square overflow-hidden bg-gray-200"
            >
              <img
                :src="optimizeImage(member.image.filename, 400)"
                :alt="member.name || 'Team Member'"
                class="w-full h-full object-cover"
              />
            </div>
            <div
              v-else
              class="aspect-square bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center"
            >
              <span class="text-gray-300 text-4xl">👤</span>
            </div>

            <!-- Member Info -->
            <div class="p-4 flex flex-col flex-grow">
              <h3 class="text-base font-bold text-black mb-1">
                {{ member.name || "Team Member" }}
              </h3>
              <p
                v-if="member.role"
                class="text-xs text-[#D0992A] font-semibold mb-2"
              >
                {{ member.role }}
              </p>
              <p
                v-if="member.description"
                class="text-gray-600 text-xs leading-relaxed line-clamp-2"
              >
                {{ member.description }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";

const props = defineProps({ blok: Object });

const teamMobileSwiper = ref(null);

function onTeamMobileSwiper(swiper) {
  teamMobileSwiper.value = swiper;
}

function optimizeImage(url, width) {
  if (!url) return "";
  if (!url.includes("a.storyblok.com") && !url.includes("img2.storyblok.com")) {
    return url;
  }
  return `${url}/m/${width}x0/filters:quality(85):format(webp)`;
}
</script>

<style scoped></style>
