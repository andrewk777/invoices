<script setup>
import InvoiceProductEdit from './InvoiceProductEdit.vue'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import axios from "axios";
import AppDateTimePicker from "@core/components/app-form-elements/AppDateTimePicker.vue";
import { defineProps, defineEmits, ref, onBeforeMount } from 'vue'

const props = defineProps({
  invoice: {
    type: null,
    required: true,
  },
  invoice_items: {
    type: Array,
    required: true,
    default: () => [],
  },
  invoice_payments: {
    type: Array,
    required: true,
    default: () => [],
  },
})

const emit = defineEmits([
  'add_payment',
  'remove_payment',
  'add_charge',
  'remove_charge',
])

const invoice = ref(props.invoice)
const invoice_items = ref(props.invoice_items)
const invoice_payments = ref(props.data.invoice_payments)
const myCompanies = ref([])
const clients = ref([])

// 👉 Add item function
const addPayment = () => {
  emit('add_payment', {
    amount: '',
    date: '',
    type: '',
    note: '',
  })
}

const removePayment = id => {
  emit('remove', id)
}

const addCharge = () => {
  emit('add_charge', {
    description: '',
    qty: 0,
    rate: 0,
    tax: '',
  })
}

const removeCharge = id => {
  emit('remove', id)
}

</script>

<template>

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
              v-model="invoice.id"
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
              v-model="invoice.invoice_date"
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
              v-model="invoice.invoice_due"
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
          v-model="invoice.my_company_id"
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
          v-model="invoice.client_id"
          :items="clients"
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
          Status:
        </h6>

        <VSelect
          v-model="invoice.client"
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
          v-model="invoice.currency"
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
      <div
        v-for="(product, index) in props.data.purchasedProducts"
        :key="product.title"
        class="mb-4"
      >
        <InvoiceProductEdit
          :id="index"
          :data="product"
          @remove-product="removeProduct"
        />
      </div>

      <VBtn
        size="small"
        prepend-icon="tabler-plus"
        @click="addItem"
      >
        Add Item
      </VBtn>
    </div>

    <VDivider class="my-6 border-dashed" />

    <!-- 👉 Total Amount -->
    <div class="d-flex justify-space-between flex-wrap flex-column flex-sm-row">
      <div class="mb-6 mb-sm-0">
        <div class="d-flex align-center mb-4">
          <h6 class="text-h6 me-2">
            Salesperson:
          </h6>
          <AppTextField
            v-model="salesperson"
            style="inline-size: 8rem;"
            placeholder="John Doe"
          />
        </div>

        <AppTextField
          v-model="thanksNote"
          placeholder="Thanks for your business"
        />
      </div>

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

    <VDivider class="my-6 border-dashed" />

    <div>
      <h6 class="text-h6 mb-2">
        Note:
      </h6>
      <VTextarea
        v-model="note"
        placeholder="Write note here..."
        :rows="2"
      />
    </div>
  </VCard>

</template>
