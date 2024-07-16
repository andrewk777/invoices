<script setup>
import AuthProvider from '@/views/pages/authentication/AuthProvider.vue'
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
import authV2LoginIllustrationBorderedDark from '@images/pages/auth-v2-login-illustration-bordered-dark.png'
import authV2LoginIllustrationBorderedLight from '@images/pages/auth-v2-login-illustration-bordered-light.png'
import authV2LoginIllustrationDark from '@images/pages/auth-v2-login-illustration-dark.png'
import authV2LoginIllustrationLight from '@images/pages/auth-v2-login-illustration-light.png'
import authV2MaskDark from '@images/pages/misc-mask-dark.png'
import authV2MaskLight from '@images/pages/misc-mask-light.png'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import { RouterLink } from 'vue-router'
import { ref, reactive, onBeforeMount } from 'vue';
definePage({ meta: { layout: 'blank' } });

const isPasswordVisible = ref(false)
const authThemeImg = useGenerateImageVariant(authV2LoginIllustrationLight, authV2LoginIllustrationDark, authV2LoginIllustrationBorderedLight, authV2LoginIllustrationBorderedDark, true)
const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)

import apiClient from '@/utils/apiClient';
import validationService from '@/utils/validation-service'
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";

const form = reactive({
  email: '',
  password: '',
  remember: false,
})

const errors = ref({});
const loading = ref(false);

const submitLogin = async () => {
  loading.value = true;
  localStorage.removeItem("invoice-client-token");

  validationService.deleteErrorsInObject(errors, null, true);

  try {
    const response = await apiClient.post('/login', form);

    if(response.data.success) {
      // Store relevant user details in local storage
      const user = {
        name: response.data.user.first_name + ' ' + response.data.user.last_name,
        email: response.data.user.email,
        token: response.data.token,
        authenticated: true,
      };
      // Store logged-in user in local storage
      localStorage.setItem('invoice-client-token', JSON.stringify(user));

      if(import.meta.env.VITE_APP_ENV === 'local') {
        console.log(response.data);
      }

      window.location.href = '/invoices';
    }

  } catch (error) {

    if (import.meta.env.VITE_APP_ENV === 'local') {
      console.log("Testing display on production", error);
    }

    if (error?.response?.data.success === false) {
      if(error.response.data.server_error) {
        errors.server_error = 'Oh oh, error occurred. please contact the admin';
      }

      if (error.response?.data?.errors) {
        // if errors exist in response, check if it's an object and convert to array
        errors.value = error.response?.data?.errors;
      }
    }

  }

  loading.value = false;
}

onBeforeMount(() => {

});
</script>

<template>
  <RouterLink to="/">
    <div class="auth-logo d-flex align-center gap-x-3">
        <img src="http://nginx.local/images/favicons/favicon-32x32.png" border="0">
        <h1 class="auth-title">
          {{ themeConfig.app.title }}
        </h1>
    </div>
  </RouterLink>

  <VRow
    no-gutters
    class="auth-wrapper bg-surface"
  >
    <VCol
      md="8"
      class="d-none d-md-flex"
    >
      <div class="position-relative bg-background w-100 me-0">
        <div
          class="d-flex align-center justify-center w-100 h-100"
          style="padding-inline: 6.25rem;"
        >
          <VImg
            max-width="613"
            :src="authThemeImg"
            class="auth-illustration mt-16 mb-2"
          />
        </div>

        <img
          class="auth-footer-mask"
          :src="authThemeMask"
          alt="auth-footer-mask"
          height="280"
          width="100"
        >
      </div>
    </VCol>

    <VCol
      cols="12"
      md="4"
      class="auth-card-v2 d-flex align-center justify-center"
    >
      <VCard
        flat
        :max-width="500"
        class="mt-12 mt-sm-0 pa-4"
      >
        <VCardText>
          <h4 class="text-h4 mb-1">
            Welcome to <span class="text-capitalize"> {{ themeConfig.app.title }} </span>! 👋🏻
          </h4>
          <p class="mb-0">
            Please sign-in to your account and start the adventure
          </p>
        </VCardText>

        <VCardText>
          <VForm @submit.prevent="submitLogin">
            <VRow>

              <VAlert
                v-if="errors.unauthorised"
                type="error"
                >
                {{ errors.unauthorised[0] }}
              </VAlert>

              <!-- loading -->
              <VCol cols="12" class="text-center">
                <VProgressCircular
                  v-if="loading"
                  indeterminate
                  color="primary"
                  size="24"
                />
              </VCol>

              <!-- email -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.email"
                  autofocus
                  label="Email"
                  type="email"
                />
                <p
                  v-if="errors.email"
                  class="mt-2 text-center text-error"
                >
                  {{ errors.email[0] }}
                </p>
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.password"
                  label="Password"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />
                <p
                  v-if="errors.password"
                  class="mt-2 text-center text-error"
                >
                  {{ errors.password[0] }}
                </p>

                <VBtn
                  class="mt-4"
                  block
                  type="submit"
                >
                  Login
                </VBtn>

              </VCol>

            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>
