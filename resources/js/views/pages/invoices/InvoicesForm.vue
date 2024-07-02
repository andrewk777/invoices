<script setup>
import { ref, reactive } from 'vue'
import InvoiceEditable from '@/views/apps/invoice/InvoiceEditable.vue'
import InvoiceSendInvoiceDrawer from '@/views/apps/invoice/InvoiceSendInvoiceDrawer.vue'

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
  invoice_payments: [{
      amount: '',
      date: '',
      type: '',
      note: '',
  }],
  invoice_items: [{
    description: '',
    qty: 0,
    rate: 0,
    tax: '',
  }]
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
</script>

<template>

  <VForm>
    <!-- 👉 Invoice Editable -->
    <InvoiceEditable
      v-model:invoice="invoiceData.invoice"
      v-model:invoice_items="invoiceData.invoice_items"
      v-model:invoice_payments="invoiceData.invoice_payments"
      @add-payment="addPayment"
      @remove-payment="removePayment"
      @add-charge="addCharge"
      @remove-charge="removeCharge"
    />

    <VRow>

      <!-- 👉 InvoiceEditable -->
      <VCol
        cols="12"
        md="9"
      >
        <InvoiceEditable
          :data="invoiceData"
          @push="addCharge"
          @remove="removeCharge"
        />

      </VCol>

      <!-- 👉 Right Column: Invoice Action -->
      <VCol
        cols="12"
        md="3"
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

    </VRow>


  </VForm>

  <!-- 👉 Send Invoice Sidebar -->
  <InvoiceSendInvoiceDrawer v-model:isDrawerOpen="isSendPaymentSidebarVisible" />
</template>
