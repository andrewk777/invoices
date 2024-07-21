<script setup>
import {onBeforeMount, reactive, ref, watch, onMounted} from 'vue'
import InvoiceSendInvoiceDrawer from '@/views/apps/invoice/InvoiceSendInvoiceDrawer.vue'
import AppDateTimePicker from "@core/components/app-form-elements/AppDateTimePicker.vue";
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";
import AppAutocomplete from "@core/components/app-form-elements/AppAutocomplete.vue";
import AppSelect from "@core/components/app-form-elements/AppSelect.vue";
import {useRoute} from 'vue-router';
import AppTextarea from "@core/components/app-form-elements/AppTextarea.vue";
import PdfIcon from "@/components/icons/PdfIcon.vue";

import apiClientAuto from '@/utils/apiCLientAuto.js';
import handleErrors from "@/utils/handleErrors.js";
import axios from "axios";

const route = useRoute();
const hash = ref(route.params.hash);
watch(() => hash.value, async () => {
  if (hash.value) {
    await getSubscription(hash.value);
  }
});

const token = computed(() => baseService.getTokenFromLocalStorage());
const loading = ref(false);
const submitted = ref(false);
const errors = ref({});

const invoice_due = computed(() => {
  const selectedDate = new Date();
  selectedDate.setDate(selectedDate.getDate() + 10);
  return selectedDate.toISOString().split('T')[0];
});

const invoiceData = reactive({
  invoice: {
    my_company_id: '',
    client_id: '',
    invoice_num: 0,
    invoice_type: '',
    status: 'draft',
    currency: 'CAD',
    invoice_date: new Date().toISOString().split('T')[0],
    invoice_due: invoice_due.value,
    na: false,
    can_pay_with_cc: false,
    subtotal: 0,
    taxes: 0,
    total: 0,
    total_paid: 0,
    balance: 0,
  },

  invoice_items: [

  ],

  invoice_payments: [

  ],
});

const statuses = ref([
  {value: 'draft', text: 'Draft'},
  {value: 'paid', text: 'Paid'},
  {value: 'approved', text: 'Approved'},
  {value: 'sent', text: 'Sent'},
  {value: 'partially_paid', text: 'Partially Paid'},
])

const submitInvoice = async (event, action = null) => {

  updateBalanceAndStatusOnSave();

  // Delete all errors
  Object.keys(errors.value).forEach(function (key) {
    delete errors.value[key];
  });

  submitted.value = false;
  loading.value = true;

  try {
    let response;
    if (hash.value) {
      response = await apiClientAuto.post('/invoices/update/' + hash.value, invoiceData, {
        headers: {
          'Accept': 'application/json',
          "Authorization": "Bearer " + token.value,
        }
      });
    } else {
      response = await apiClientAuto.post('/invoices/store', invoiceData, {
        headers: {
          'Accept': 'application/json',
          "Authorization": "Bearer " + token.value,
        }
      });
    }

    if (response.data.success){
      submitted.value = true;
      hash.value = response.data.invoice.hash;
      invoiceData.invoice.invoice_num = response.data.invoice.invoice_num;

      if(action === 'close') {
        window.location.href = '/invoices';
      }

    }

  } catch (error) {
    handleErrors(error, errors.value);
  }

  loading.value = false;
}

const populateInvoice = (invoice) => {
  Object.keys(invoice).forEach(function (key) {
    if (invoice[key] !== null && invoice[key] !== '') {
      invoiceData.invoice[key] = invoice[key];
    }
  });
}

const populateInvoiceItem = (invoice_items) => {
  invoice_items.forEach(function (item, index) {

    // Initialize the item at index if it does not exist
    if (!invoiceData.invoice_items[index]) {
      invoiceData.invoice_items[index] = {
        description: '',
        rate: 0,
        qty: 0,
        tax: '',
      };
    }

    Object.keys(item).forEach(function (key) {
      if (item[key] !== null && item[key] !== '') {
        if(key === 'tax'){
          if(item[key] === 1){
            invoiceData.invoice_items[index][key] = 'HST';
          }else{
            invoiceData.invoice_items[index][key] = 'None';
          }
        }else{
          invoiceData.invoice_items[index][key] = item[key];
        }
      }
    });

  });
};

const populateInvoicePayment = (invoice_payments) => {
  invoice_payments.forEach(function (payment, index) {

    // Initialize the item at index if it does not exist
    if (!invoiceData.invoice_payments[index]) {
      invoiceData.invoice_payments[index] = {
        type: '',
        amount: 0,
        date: '',
        note: '',
      };
    }

    Object.keys(payment).forEach(function (key) {
      if (payment[key] !== null && payment[key] !== '') {
        invoiceData.invoice_payments[index][key] = payment[key];
      }
    });

  });
}

const getInvoice = async (hash) => {
  try {
    const params = {
      hash: hash
    }
    const response = await apiClientAuto.get('/invoices/show/' + hash, {params});

    if (response.data.success) {
      invoice.value = response.data.invoice;

      populateInvoice(invoice.value);
      selectInvoiceTo(invoice.value.client_id);
      if(invoice.value.items.length > 0){
        populateInvoiceItem(invoice.value.items);
      }
      if(invoice.value.payments.length > 0){
        populateInvoicePayment(invoice.value.payments);
      }
    }
  } catch (error) {

  }

  loading.value = false;
}

const isSendPaymentSidebarVisible = ref(false)

const addPayment = value => {
  calculateBalance();
  const paymentToday = new Date().toISOString().split('T')[0];

  invoiceData?.invoice_payments.push({
    type: 'Cheque',
    amount: invoiceData.invoice.balance,
    date: paymentToday,
    note: '',
  });
}

const removePayment = index => {
  invoiceData?.invoice_payments.splice(index, 1)
}

const addCharge = value => {
  invoiceData?.invoice_items.push({
    description: '',
    rate: '',
    qty: '',
    tax: 'HST',
  });
}

const removeCharge = index => {
  invoiceData?.invoice_items.splice(index, 1)
}

const updateInvoiceDue = (event) => {
  const selectedDate = new Date(event.target.value);
  selectedDate.setDate(selectedDate.getDate() + 10);
  invoiceData.invoice.invoice_due = selectedDate.toISOString().split('T')[0];
}

const myCompanies = ref([]);
const invoiceFrom = ref({});
const invoiceTo = ref({});
const clients = ref([]);
const invoice = ref({});

const getCompanies = async () => {
  try {
    const response = await apiClientAuto.get('/companies');

    if (response.data.success === true) {
        myCompanies.value = response.data.companies;
        invoiceFrom.value = response.data.companies[1];
        invoiceData.invoice.my_company_id = response.data.companies[1].id
    }

  } catch (error) {

  }
}

const selectInvoiceFrom = (event) => {
  invoiceFrom.value = myCompanies.value.find(company => company.id === event);
}

const selectInvoiceTo = (event) => {
  invoiceTo.value = clients.value.find(client => client.id === event);
}

const getClients = async () => {
  try {
    const response = await apiClientAuto.get('/clients');
    if (response.data.success === true) {
      clients.value = response.data.clients;
    }
  } catch (error) {

  }
}

const calculateSubTotal = () => {
  let subTotal = 0;
  let tax = 0;
  invoiceData.invoice_items.forEach((item) => {
    if (item.qty && item.rate) {
      subTotal += (item.qty * item.rate);
      if (item.tax === 'HST') {
        tax += (item.qty * item.rate) * 0.13;
      }
    }
  });

  invoiceData.invoice.subtotal = subTotal;
  invoiceData.invoice.taxes = tax;
  invoiceData.invoice.total = subTotal + tax;
  calculateBalance();
}

const calculateBalance = () => {
  let totalPaid = 0;
  if(invoiceData.invoice_payments.length > 0){
    invoiceData.invoice_payments.forEach((payment) => {
      if (payment.amount) {
        totalPaid += parseFloat(payment.amount);
      }
    });
  }

  invoiceData.invoice.total_paid = totalPaid;
  invoiceData.invoice.balance = invoiceData.invoice.total - totalPaid;
}

const updateBalanceFromStatus = (event) => {
  if (event.target.value === 'draft') {
    invoiceData.invoice.balance = invoiceData.invoice.total;

  }else if(event.target.value === 'paid'){
    invoiceData.invoice.balance = 0;
  }
}

const updateBalanceAndStatusOnSave = () => {
  if (invoiceData.invoice.balance === 0) {
    invoiceData.invoice.status = 'paid';
  } else if(invoiceData.invoice.balance === invoiceData.invoice.total) {
    invoiceData.invoice.status = 'draft';
  }else{
    invoiceData.invoice.status = 'partially_paid';
  }
}

const downloadInvoiceReceipt = (hash) => {

  axios.get(`/api/invoices/receipt/${hash}/download`, {
    responseType: 'blob',
    headers: {
      'Accept': 'application/json',
      "Authorization": "Bearer " + token.value,
    }

  }).then(response => {

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'invoice_receipt.pdf');
    document.body.appendChild(link);
    link.click();

  }).catch(error => {
    console.error('Error downloading invoice receipt:', error);
  });
};

watch(() => invoiceData.invoice_items, () => {
  calculateSubTotal();
}, { deep: true });



onBeforeMount(async () => {
  await getCompanies();
  await getClients();

  if (hash.value) {
    await getInvoice(hash.value);
  }
});

</script>

<template>

  <VForm>

    <VRow class="position-relative">

      <VCol cols="12" md="10">

        <VCard class="pa-6 pa-sm-12">

          <VAlert
            v-if="submitted"
            class="text-center mb-2"
            type="success"
          >
            {{ hash ? 'Invoice updated successfully' : 'Invoice created successfully'}}
          </VAlert>

          <VAlert
            v-if="Object.keys(errors).length > 0"
            class="text-center mb-2"
            type="error"
          >
            <p v-for="(error, index) in Object.keys(errors)" :key="index" class="mb-0">
              {{ errors[error][0] }}
            </p>
          </VAlert>

          <!-- SECTION Header -->
          <div class="d-flex flex-wrap justify-space-between flex-column rounded bg-var-theme-background flex-sm-row gap-6 pa-6 mb-6">
            <!-- 👉 Left Content -->
            <div>
              <div class="d-block align-center app-logo mb-6">
                <!-- 👉 Logo -->
                <img :src="invoiceFrom.logo" width="200"/>
              </div>

              <!-- 👉 Address -->
              <p class="text-high-emphasis mb-0">
                {{ invoiceFrom.address }}
              </p>

              <p class="text-high-emphasis mb-0">
                {{ invoiceFrom.email }}
              </p>

              <p class="text-high-emphasis mb-0">
                {{ invoiceFrom.mobile }}
              </p>
            </div>

            <!-- 👉 Right Content -->
            <div class="d-flex flex-column gap-2">

              <div class="d-flex align-start align-sm-center gap-x-4 font-weight-medium text-lg flex-column flex-sm-row">
                <span
                  class="text-high-emphasis text-sm-end"
                  style="inline-size: 5.625rem ;"
                >Invoice From:</span>

                <AppAutocomplete
                  v-if="invoiceFrom"
                  @change="selectInvoiceFrom"
                  v-model="invoiceData.invoice.my_company_id"
                  :items="myCompanies"
                  item-title="name"
                  item-value="id"
                  placeholder="Select Client"
                  style="inline-size: 9.5rem;"
                />
              </div>


              <!-- 👉 Invoice Id -->
              <div class="d-flex align-start align-sm-center gap-x-4 font-weight-medium text-lg flex-column flex-sm-row">
                <span
                  class="text-high-emphasis text-sm-end"
                  style="inline-size: 5.625rem ;"
                >Invoice:</span>
                  <span>
                  <AppTextField
                    :value="hash ? invoiceData.invoice.invoice_num : ''"
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
                    @change="updateInvoiceDue"
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
                >Invoice Due:</span>
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
            <VCol md="3">
              <AppAutocomplete
                @change="selectInvoiceTo"
                v-model="invoiceData.invoice.client_id"
                :items="clients"
                label="Client"
                item-title="company_name"
                item-value="id"
                placeholder="Select Client"
                class="mb-4"
              />
            </VCol>

            <VCol md="2" >
              <AppAutocomplete
                v-model="invoiceData.invoice.status"
                @change="updateBalanceFromStatus"
                label="Status"
                :items="statuses"
                item-title="text"
                item-value="value"
                placeholder="Select Status"
                class="mb-4"
              />
            </VCol>

            <VCol md="2">
              <AppSelect
                label="Currency"
                v-model="invoiceData.invoice.currency"
                :items="[
                  'USD',
                  'CAD',
                ]"
                placeholder="Select Client"
                class="mb-4"
              />
            </VCol>

            <VCol md="3">
                <VCheckbox
                    v-model="invoiceData.invoice.can_pay_with_cc"
                    label="Can Pay with Credit Card"
                  class="mt-6"
                />
            </VCol>

            <VCol md="2">
                <VCheckbox
                v-model="invoiceData.invoice.na"
                label="N/A"
                class="mt-6"
                />
            </VCol>
          </VRow>
          
          <VRow v-if="invoiceData.invoice.client_id">
            <VCol md="12">
                <div class="d-block">
                <p><strong>Company:</strong> {{ invoiceTo.company_name }}</p>
                <p><strong>Email:</strong> {{ invoiceTo.company_email }}</p>
                <p><strong>Mobile:</strong> {{ invoiceTo.company_phone }}</p>
                <p><strong>Address:</strong> {{ invoiceTo.company_address }}</p>
              </div>
            </VCol>
          </VRow>

          <VDivider class="my-6 border-dashed" thickness="4" />

          <!-- Add Charges -->
          <div class="add-products-form">
            <div class="d-flex justify-space-between mb-4">
                <h3 class="pt-2">
                    Charges:
                </h3>

                <VBtn
                    class="mt-2"
                    size="small"
                    prepend-icon="tabler-plus"
                    @click="addCharge"
                    >
                    Add Charge
                </VBtn>
            </div>

            <VRow v-for="(item, index) in invoiceData.invoice_items" :key="index">

              <VCol md="6" >                
                <AppTextarea
                    v-model="item.description"
                    rows="2"
                    label="Description"
                    placeholder="Item description"
                    persistent-placeholder
                    />
              </VCol>
              <VCol md="2" >
                <AppTextField
                  @input="calculateSubTotal"
                  v-model="item.qty"
                  type="number"
                  label="Quantity"
                  placeholder="Quantity"
                  class="mb-6"
                />
              </VCol>
              <VCol md="2" >
                <AppTextField
                  @input="calculateSubTotal"
                  v-model="item.rate"
                  type="number"
                  label="Rate"
                  placeholder="Rate"
                  class="mb-6"
                />
              </VCol>
              <VCol class="d-flex" md="2" >
                <AppSelect
                  @change="calculateSubTotal"
                  v-model="item.tax"
                  :items="[
                   'HST',
                   'None',
                  ]"
                  label="Tax"
                  placeholder="Select Tax"
                  class="mb-6"
                />
                <a href="" class="mt-6 ml-4">
                  <IconBtn
                    size="36"
                    @click.prevent="removeCharge(index)"
                  >
                    <VIcon
                      :size="24"
                      icon="tabler-x"
                    />
                  </IconBtn>
                </a>
              </VCol>

            </VRow>
          </div>

          <VDivider class="my-6 border-dashed" thickness="4" />

          <div class="add-products-form">
            <h6 class="text-h6 mb-4">
              Payments:
            </h6>

            <VRow v-for="(payment, index) in invoiceData.invoice_payments" :key="index">

              <VCol cols="12" md="6">
                <AppSelect
                  v-model="payment.type"
                  :items="[
                    'Card',
                    'Cheque',
                    'Cash',
                    'EFT'
                  ]"
                  placeholder="Payment Type"
                  class="mb-4"
                />

                <AppTextarea
                  v-model="payment.note"
                  rows="2"
                  placeholder="Note"
                  persistent-placeholder
                />
              </VCol>

              <VCol cols="12" md="2" sm="2">
                <AppDateTimePicker
                  v-model="payment.date"
                  placeholder="Date"
                  class="mb-6"
                />
              </VCol>

              <VCol cols="12" md="2" sm="2">
                <AppTextField
                  @input="calculateBalance"
                  v-model="payment.amount"
                  type="number"
                  placeholder="Amount"
                  class="mb-6"
                />
              </VCol>

              <VCol cols="12" md="2">
                <a href="">
                  <IconBtn
                    size="36"
                    @click.prevent="removePayment(index)"
                  >
                    <VIcon
                      :size="24"
                      icon="tabler-x"
                    />
                  </IconBtn>
                </a>
              </VCol>

            </VRow>

            <VBtn
              class="mt-2"
              size="small"
              prepend-icon="tabler-plus"
              @click="addPayment"
            >
              Add Payment
            </VBtn>

          </div>

          <VDivider class="my-6 border-dashed" thickness="4" />

          <!-- 👉 Total Amount -->
          <div class="d-flex justify-end flex-wrap flex-column flex-sm-row">
            <div>
              <table class="w-100">
                <tbody>

                <tr>
                  <td class="pe-16">
                    Subtotal:
                  </td>
                  <td :class="$vuetify.locale.isRtl ? 'text-start' : 'text-end'">
                    <h6 class="text-h6">
                      {{ parseFloat(invoiceData.invoice.subtotal).toFixed(2) }}
                    </h6>
                  </td>
                </tr>

                <tr>
                  <td class="pe-16">
                    Tax:
                  </td>
                  <td :class="$vuetify.locale.isRtl ? 'text-start' : 'text-end'">
                    <h6 class="text-h6">
                      {{ parseFloat(invoiceData.invoice.taxes).toFixed(2) }}
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
                      {{ parseFloat(invoiceData.invoice.total).toFixed(2) }}
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
                    Balance:
                  </td>
                  <td :class="$vuetify.locale.isRtl ? 'text-start' : 'text-end'">
                    <h6 class="text-h6">
                      {{ invoiceData.invoice.balance }}
                    </h6>
                  </td>
                </tr>
                </tbody>
              </table>

            </div>
          </div>

        </VCard>
      </VCol>

      <!--Right Buttons-->
      <VCol cols="12" md="2">
        <VBtn
          :loading="loading"
          block
          color="success"
          variant="tonal"
          class="mb-2"
          @click="submitInvoice"
        >
          Save
        </VBtn>

        <VBtn
          :loading="loading"
          block
          color="success"
          variant="tonal"
          class="mb-2"
          @click="submitInvoice($event, 'close')"
        >
          Save & Close
        </VBtn>

        <VBtn
            :loading="loading"
          v-if="hash"
          @click.prevent="downloadInvoiceReceipt(hash)"
          class="mt-2"
          block
          color="info"
          variant="tonal"
          @click=""
        >
           <PdfIcon :width="'18px'" class="mr-1"/> View Invoice
        </VBtn>

        <VBtn
          v-if="hash"
          :loading="loading"
          class="mt-2 mb-2"
          block
          color="warning"
          variant="tonal"
        >
          <VIcon
            left
            class="mr-1"
            icon="tabler-credit-card"
          />
          Charge Credit Card
        </VBtn>

        <router-link
        :to="{ name: 'InvoicesView'}"
        >
        <VBtn
            :loading="loading"
            block
            color="primary"
            variant="tonal"
            class="mb-2"
            @click=""
        >
            List All
        </VBtn>
        </router-link>

        <VBtn
        :loading="loading"
        block
        color="error"
        variant="tonal"
        class="mb-2"
        @click.prevent="$router.go(-1)"
        >
        Close
        </VBtn>



      </VCol>

    </VRow>


  </VForm>

  <!-- 👉 Send Invoice Sidebar -->
  <InvoiceSendInvoiceDrawer v-model:isDrawerOpen="isSendPaymentSidebarVisible" />
</template>

<style scoped>
.position-relative {
  position: relative;
}

.static-column {
  position: absolute;
  top: 0;
  right: 0;
  height: 100%;
}
</style>
