<script setup>
import { ref, reactive } from 'vue'
import InvoiceEditable from '@/views/apps/invoice/InvoiceEditable.vue'
import InvoiceSendInvoiceDrawer from '@/views/apps/invoice/InvoiceSendInvoiceDrawer.vue'
import {themeConfig} from "@themeConfig";
import {VNodeRenderer} from "@layouts/components/VNodeRenderer.jsx";
import InvoiceProductEdit from "@/views/apps/invoice/InvoiceProductEdit.vue";
import AppDateTimePicker from "@core/components/app-form-elements/AppDateTimePicker.vue";
import axios from "axios";
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";

// 👉 Default Blank Data
const invoiceData = reactive({
  invoice: {
    company_id: '',
    client_id: '',
    invoice_num: 0,
    invoice_type: '',
    status: '',
    currency: '',
    invoice_date: '',
    invoice_due: '',
    na: '',
    can_pay_with_cc: false,
    sub_total: 0,
    taxes: 0,
    total: 0,
    total_paid: 0,
    balance: 0,
  },

  invoice_items: [{
    description: '',
    rate: 0,
    qty: 0,
    tax: '',
  }],

  invoice_payments: [{
    type: '',
    amount: '',
    date: '',
    note: '',
  }],

})

const isSendPaymentSidebarVisible = ref(false)

const addPayment = value => {
  invoiceData.value?.invoice_payments.push(value)
}

const removePayment = id => {
  invoiceData.value?.invoice_payments.splice(id, 1)
}

const addCharge = value => {
  invoiceData.value?.invoice_items.push(value)
}

const removeCharge = id => {
  invoiceData.value?.invoice_items.splice(id, 1)
}

const myCompanies = ref([])
const clients = ref([])

const getCompanies = () => {
  axios.get('/api/companies', {
    headers: {
      'Accept' : 'application/json',
    },
  }).then((response) => {
    if(response.data.success === true){
      myCompanies.value = response.data.companies;
    }

    if(import.meta.env.VITE_APP_ENV === 'local'){
      console.log("Get Companies", myCompanies.value);
    }

  }).catch((error) => {
    console.log(error);
  });
}

const getClients = () => {
  axios.get('/api/clients', {
    headers: {
      'Accept' : 'application/json',
    },
  }).then((response) => {
    if(response.data.success === true){
      myCompanies.value = response.data.companies;
    }

    if(import.meta.env.VITE_APP_ENV === 'local'){
      console.log("Get Companies", myCompanies.value);
    }

  }).catch((error) => {
    console.log(error);
  });
}

onBeforeMount(() => {
  getCompanies()
})
</script>

<template>

  <VForm>
    <!-- 👉 Invoice Editable -->
<!--    <InvoiceEditable-->
<!--      v-model:invoice="invoiceData.invoice"-->
<!--      v-model:invoice_items="invoiceData.invoice_items"-->
<!--      v-model:invoice_payments="invoiceData.invoice_payments"-->
<!--      @add-payment="addPayment"-->
<!--      @remove-payment="removePayment"-->
<!--      @add-charge="addCharge"-->
<!--      @remove-charge="removeCharge"-->
<!--    />-->

    <VRow>

      <VCol cols="12" md="10">
        <VCard class="pa-6 pa-sm-12">
          <!-- SECTION Header -->
          <div class="d-flex flex-wrap justify-space-between flex-column rounded bg-var-theme-background flex-sm-row gap-6 pa-6 mb-6">
            <!-- 👉 Left Content -->
            <div>
              <div class="d-flex align-center app-logo mb-6">
                <!-- 👉 Logo -->
                <VNodeRenderer :nodes="themeConfig.app.logo" />

                <!-- 👉 Title -->
                <h6 class="app-logo-title">
                  {{ themeConfig.app.title }}
                </h6>
              </div>

              <!-- 👉 Address -->
              <p class="text-high-emphasis mb-0">
                203-125 Commerce Valley Dr. W.,
                Thornhill, ON, L3T 7W4
              </p>
              <p class="text-high-emphasis mb-0">
                info@oadsoft.com
              </p>
              <p class="text-high-emphasis mb-0">
                416-477-4763
              </p>
            </div>

            <!-- 👉 Right Content -->
            <div class="d-flex flex-column gap-2">
              <!-- 👉 Invoice Id -->
              <div class="d-flex align-start align-sm-center gap-x-4 font-weight-medium text-lg flex-column flex-sm-row">
              <span
                class="text-high-emphasis text-sm-end"
                style="inline-size: 5.625rem ;"
              >Invoice:</span>
                <span>
                <AppTextField
                  value="0000"
                  disabled
                  prefix="#"
                  style="inline-size: 9.5rem;"
                />
              </span>
              </div>

              <!-- 👉 Issue Date -->
              <div class="d-flex gap-x-4 align-start align-sm-center flex-column flex-sm-row">
            <span
              class="text-high-emphasis text-sm-end"
              style="inline-size: 5.625rem;"
            >Invoice Date:</span>

                <span style="inline-size: 9.5rem;">
                <AppDateTimePicker
                  v-model="invoiceData.invoice.invoice_date"
                  placeholder="YYYY-MM-DD"
                  :config="{ position: 'auto right' }"
                />
              </span>
              </div>

              <!-- 👉 Due Date -->
              <div class="d-flex gap-x-4 align-start align-sm-center flex-column flex-sm-row">
            <span
              class="text-high-emphasis text-sm-end"
              style="inline-size: 5.625rem;"
            >Payment Date:</span>
                <span style="min-inline-size: 9.5rem;">
              <AppDateTimePicker
                v-model="invoiceData.invoice.invoice_due"
                placeholder="YYYY-MM-DD"
                :config="{ position: 'auto right' }"
              />
            </span>
              </div>
            </div>
          </div>
          <!-- !SECTION -->

          <VRow>
            <VCol class="text-no-wrap">
              <h6 class="text-h6 mb-4">
                Invoice From:
              </h6>

              <VSelect
                v-model="invoiceData.invoice.company_id"
                :items="myCompanies"
                item-title="name"
                item-value="id"
                placeholder="Select Client"
                return-object
                class="mb-4"
                style="inline-size: 11.875rem;"
              />
            </VCol>

            <VCol class="text-no-wrap">
              <h6 class="text-h6 mb-4">
                Invoice To:
              </h6>

              <VSelect
                v-model="invoiceData.invoice.client_id"
                :items="clients"
                item-title="name"
                item-value="id"
                placeholder="Select Client"
                return-object
                class="mb-4"
                style="inline-size: 11.875rem;"
              />
            </VCol>

            <VCol class="text-no-wrap">
              <h6 class="text-h6 mb-4">
                Status:
              </h6>

              <VSelect
                v-model="invoiceData.invoice.status"
                :items="[
                { name: 'Draft' },
                { name: 'Approved' },
                { name: 'Sent' },
                { name: 'Charge Declined' },
                { name: 'Partially Paid' },
                { name: 'Paid' },
              ]"
                item-title="name"
                item-value="name"
                placeholder="Select Client"
                return-object
                class="mb-4"
                style="inline-size: 11.875rem;"
              />
            </VCol>

            <VCol class="text-no-wrap">
              <h6 class="text-h6 mb-4">
                Currency:
              </h6>

              <VSelect
                v-model="invoiceData.invoice.currency"
                :items="[
                { name: 'USD' },
                { name: 'CAD' },
              ]"
                item-title="name"
                item-value="name"
                placeholder="Select Client"
                return-object
                class="mb-4"
                style="inline-size: 11.875rem;"
              />
            </VCol>
          </VRow>

          <VDivider class="my-6 border-dashed" />

          <!-- 👉 Add purchased products -->
          <div class="add-products-form">
            <h6 class="text-h6 mb-4">
              Charges:
            </h6>
            <VRow v-for="(item, index) in invoiceData.invoice_items" :key="index">
              <VCol
                cols="12"
                md="3"
              >
                <AppTextField
                  v-model="item.description"
                  label="Item"
                  placeholder="Enter item name"
                />
              </VCol>

              <VCol
                cols="12"
                md="3"
              >
                <AppTextField
                  v-model="item.qty"
                  label="Item"
                  placeholder="Enter item name"
                />
              </VCol>

              <VCol
                cols="12"
                md="3"
              >
                <AppTextField
                  v-model="item.rate"
                  label="Item"
                  placeholder="Enter item name"
                />
              </VCol>

              <VCol
                cols="12"
                md="3"
              >
                <AppTextField
                  v-model="item.tax"
                  label="Item"
                  placeholder="Enter item name"
                />
              </VCol>

              <VCol
                cols="12"
                md="1"
              >
                <VBtn
                  size="small"
                  prepend-icon="tabler-trash"
                  @click="removeCharge(index)"
                >
                  Remove
                </VBtn>
              </VCol>

            </VRow>

            <VBtn
              size="small"
              prepend-icon="tabler-plus"
              @click="addItem"
            >
              Add Item
            </VBtn>
          </div>

          <VDivider class="my-6 border-dashed" />

          <div class="add-products-form">
            <h6 class="text-h6 mb-4">
              Payments:
            </h6>
            <VRow v-for="(payment, index) in invoiceData.invoice_payments" :key="index">
              <VCol
                cols="12"
                md="3"
              >
                <AppTextField
                  v-model="payment.item"
                  label="Item"
                  placeholder="Enter item name"
                />
              </VCol>

              <VCol
                cols="12"
                md="3"
              >
                <AppTextField
                  v-model="payment.amount"
                  label="Item"
                  placeholder="Enter item name"
                />
              </VCol>

              <VCol
                cols="12"
                md="3"
              >
                <AppTextField
                  v-model="payment.date"
                  label="Item"
                  placeholder="Enter item name"
                />
              </VCol>

              <VCol
                cols="12"
                md="3"
              >
                <AppTextField
                  v-model="payment.note"
                  label="Item"
                  placeholder="Enter item name"
                />
              </VCol>

              <VCol
                cols="12"
                md="1"
              >
                <VBtn
                  size="small"
                  prepend-icon="tabler-trash"
                  @click="removePayment(index)"
                >
                  Remove
                </VBtn>
              </VCol>

            </VRow>

            <VBtn
              size="small"
              prepend-icon="tabler-plus"
              @click="addPayment"
            >
              Add Payment
            </VBtn>
          </div>

          <VDivider class="my-6 border-dashed" />

          <!-- 👉 Total Amount -->
          <div class="d-flex justify-space-between flex-wrap flex-column flex-sm-row">
            <div>
              <table class="w-100">
                <tbody>
                <tr>
                  <td class="pe-16">
                    Subtotal:
                  </td>
                  <td :class="$vuetify.locale.isRtl ? 'text-start' : 'text-end'">
                    <h6 class="text-h6">
                      $1800
                    </h6>
                  </td>
                </tr>
                <tr>
                  <td class="pe-16">
                    Discount:
                  </td>
                  <td :class="$vuetify.locale.isRtl ? 'text-start' : 'text-end'">
                    <h6 class="text-h6">
                      $28
                    </h6>
                  </td>
                </tr>
                <tr>
                  <td class="pe-16">
                    Tax:
                  </td>
                  <td :class="$vuetify.locale.isRtl ? 'text-start' : 'text-end'">
                    <h6 class="text-h6">
                      21%
                    </h6>
                  </td>
                </tr>
                </tbody>
              </table>

              <VDivider class="mt-4 mb-3" />

              <table class="w-100">
                <tbody>
                <tr>
                  <td class="pe-16">
                    Total:
                  </td>
                  <td :class="$vuetify.locale.isRtl ? 'text-start' : 'text-end'">
                    <h6 class="text-h6">
                      $1690
                    </h6>
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>

        </VCard>
      </VCol>

      <VCol cols="12" md="2">
        <VCol
          cols="12"
          md="12"
        >
          <VCard class="mb-8">
            <VCardText>
              <!-- 👉 Send Invoice -->
              <VBtn
                block
                prepend-icon="tabler-send"
                class="mb-4"
              >
                All Invoices
              </VBtn>

              <!-- 👉 Save -->
              <VBtn
                block
                color="success"
                variant="tonal"
              >
                Save Invoice
              </VBtn>
            </VCardText>
          </VCard>
        </VCol>
      </VCol>


      <!-- 👉 InvoiceEditable -->
<!--      <VCol-->
<!--        cols="12"-->
<!--        md="9"-->
<!--      >-->
<!--        <InvoiceEditable-->
<!--          :data="invoiceData"-->
<!--          @push="addCharge"-->
<!--          @remove="removeCharge"-->
<!--        />-->

<!--      </VCol>-->

      <!-- 👉 Right Column: Invoice Action -->
    </VRow>


  </VForm>

  <!-- 👉 Send Invoice Sidebar -->
  <InvoiceSendInvoiceDrawer v-model:isDrawerOpen="isSendPaymentSidebarVisible" />
</template>
