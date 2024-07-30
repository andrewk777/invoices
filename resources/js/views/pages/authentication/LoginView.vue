<script setup>
import apiClientAuto from "@/utils/apiCLientAuto.js";

definePage({
  meta: {
    layout: 'blank',
  },
});

import { ref, reactive, onBeforeMount } from 'vue';
import axios from 'axios';
import validationService from '@/utils/validation-service'
import handleErrors from "@/utils/handleErrors.js";

let errors = reactive({});
const loading = ref(false);

const form = reactive({
  email: '',
  password: '',
});

const submitLogin = async () => {
  loading.value = true;
  localStorage.removeItem("invoice-client-token");

  validationService.deleteErrorsInObject(errors, null, true);

  try {
    const response = await apiClientAuto.post('/login', form);

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
    handleErrors(error, errors);
  }

  loading.value = false;
}

onBeforeMount(() => {

});
</script>

<template>

  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner py-6">
        <!-- Login -->
        <div class="card">
          <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center mb-6">
              <a href="" class="app-brand-link">
              <span class="app-brand-logo demo">
                <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z" fill="#7367F0"></path>
                  <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616"></path>
                  <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616"></path>
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z" fill="#7367F0"></path>
                </svg>
              </span>
                <span class="app-brand-text demo text-heading fw-bold">OAD SOFT</span>
              </a>
            </div>
            <!-- /Logo -->
<!--            <h4 class="mb-1">Welcome to Vuexy! 👋</h4>-->
<!--            <p class="mb-6">Please sign-in to your account and start the adventure</p>-->

            <form
              @submit.prevent="submitLogin"
              class="mb-4 fv-plugins-bootstrap5 fv-plugins-framework"
            >

              <p v-if="loading" class="text-center">
                <i class="fa fa-circle-notch fa-spin fa-2x"></i>
              </p>

              <p v-if="errors.unauthorised" class="text-center text-danger">
                {{ errors.unauthorised }}
              </p>

              <div class="mb-6 fv-plugins-icon-container">
                <label for="email" class="form-label">Email or Username</label>
                <input
                  v-model="form.email"
                  type="email"
                  class="form-control"
                  autofocus=""
                >

                <p v-if="errors.email" class="text-danger text-center">
                  {{ errors.email[0] }}
                </p>
              </div>

              <div class="mb-6 form-password-toggle fv-plugins-icon-container">
                <label class="form-label" for="password">Password</label>
                <div class="input-group input-group-merge has-validation">
                  <input
                    v-model="form.password"
                    type="password"
                    class="form-control"
                    aria-describedby="password"
                  >
                  <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                </div>
                <p v-if="errors.password" class="text-center text-danger">
                  {{ errors.password[0] }}
                </p>
              </div>

              <div class="my-8">
                <div class="d-flex justify-content-between">
                  <div class="form-check mb-0 ms-2">
                    <input class="form-check-input" type="checkbox" id="remember-me">
                    <label class="form-check-label" for="remember-me">
                      Remember Me
                    </label>
                  </div>
                  <a href="">
                    <p class="mb-0">Forgot Password?</p>
                  </a>
                </div>
              </div>

              <div class="mb-6">
                <button class="btn btn-primary d-grid w-100 waves-effect waves-light" type="submit">Login</button>
              </div>
            </form>

          </div>
        </div>
        <!-- /Register -->
      </div>
    </div>
  </div>

</template>

<style src="@assets/vendor/css/rtl/core.css"></style>

<style src="@assets/vendor/css/rtl/theme-default.css"></style>

<style src="@assets/css/demo.css"></style>

<style src="@assets/vendor/libs/node-waves/node-waves.css"></style>

<style src="@assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"></style>

<style src="@assets/vendor/libs/typeahead-js/typeahead.css"></style>

<style src="@assets/vendor/libs/@form-validation/form-validation.css"></style>

<style src="@assets/vendor/css/pages/page-auth.css"></style>
