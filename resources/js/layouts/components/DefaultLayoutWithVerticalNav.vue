<script setup>
import navItems from '@/navigation/vertical'
import { themeConfig } from '@themeConfig'
import { ref, watch, onBeforeMount, onMounted } from 'vue'
import { useRouter } from 'vue-router'

// Components
import Footer from '@/layouts/components/Footer.vue'
import NavbarThemeSwitcher from '@/layouts/components/NavbarThemeSwitcher.vue'
import UserProfile from '@/layouts/components/UserProfile.vue'
import NavBarI18n from '@core/components/I18n.vue'

// @layouts plugin
// import { VerticalNavLayout } from '@layouts'
import AppLoadingIndicator from "@/components/AppLoadingIndicator.vue";
import CustomVerticalNavLayout from "@layouts/components/CustomVerticalNavLayout.vue";
import RouteService from "@/utils/route-service.js";
import VerticalNavLayout from "@layouts/components/VerticalNavLayout.vue";

// SECTION: Loading Indicator
const isFallbackStateActive = ref(false)
const refLoadingIndicator = ref(null)

watch([
  isFallbackStateActive,
  refLoadingIndicator,
], () => {
  if (isFallbackStateActive.value && refLoadingIndicator.value)
    refLoadingIndicator.value.fallbackHandle()
  if (!isFallbackStateActive.value && refLoadingIndicator.value)
    refLoadingIndicator.value.resolveHandle()
}, { immediate: true })
// !SECTION

// Custom loading indicator
const router = useRouter()
const showProgressIndicator = ref(true)

// Show loader before each router component load
router.beforeEach((to, from, next) => {
  showProgressIndicator.value = true
  next()
})

// Hide loader after each router component load
router.afterEach(() => {
  setTimeout(() => {
    showProgressIndicator.value = false
  }, 1000)
})

onBeforeMount(() => {
// show loader before each page load
  setTimeout(() => {
    showProgressIndicator.value = false
  }, 1000);

  RouteService.authenticateUser(
    '/api/authenticate',
    null,
    '/login',
  )
})
</script>

<template>

  <div class="app-container">
    <VProgressLinear
      v-if="showProgressIndicator"
      indeterminate
      color="primary"
      class="progress-linear"
    />

    <div class="app-content">
      <VerticalNavLayout :nav-items="navItems">
        <!-- 👉 navbar -->
        <template #navbar="{ toggleVerticalOverlayNavActive }">
          <div class="d-flex h-100 align-center">
            <IconBtn
              id="vertical-nav-toggle-btn"
              class="ms-n3 d-lg-none"
              @click="toggleVerticalOverlayNavActive(true)"
            >
              <VIcon
                size="26"
                icon="tabler-menu-2"
              />
            </IconBtn>

            <VSpacer />

            <NavBarI18n
              v-if="themeConfig.app.i18n.enable && themeConfig.app.i18n.langConfig?.length"
              :languages="themeConfig.app.i18n.langConfig"
            />

            <NavbarThemeSwitcher />

            <UserProfile />
          </div>
        </template>

        <template #content-loading>
            <div>
            <h1 class="text-h1">
                Loading...
            </h1>
            </div>
        </template>

        <!--    <AppLoadingIndicator ref="refLoadingIndicator" />-->

        <!-- 👉 Pages -->
        <RouterView v-slot="{ Component }">
          <Suspense
            :timeout="0"
            @fallback="isFallbackStateActive = true"
            @resolve="isFallbackStateActive = false"
          >
            <Component :is="Component" />
          </Suspense>
        </RouterView>

        <!-- 👉 Footer -->
        <template #footer>
          <Footer />
        </template>

        <!-- 👉 Customizer -->
        <!-- <TheCustomizer /> -->
      </VerticalNavLayout>
    </div>
  </div>

</template>

<style scoped>
.app-container {
  position: relative;
}

.progress-linear {
  position: absolute;
  top: -4px !important;
  left: 0;
  right: 0;
  z-index: 9999;
}

.app-content {
  position: relative;
  z-index: 1;
}
</style>
