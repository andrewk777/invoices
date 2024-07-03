<script setup>
import { ref, reactive, onBeforeMount } from 'vue'
import InvoiceSendInvoiceDrawer from '@/views/apps/invoice/InvoiceSendInvoiceDrawer.vue'
import {themeConfig} from "@themeConfig";
import {VNodeRenderer} from "@layouts/components/VNodeRenderer.jsx";
import AppDateTimePicker from "@core/components/app-form-elements/AppDateTimePicker.vue";
import axios from "axios";
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";
import TrashIcon from "@/components/icons/TrashIcon.vue";
import AppAutocomplete from "@core/components/app-form-elements/AppAutocomplete.vue";
import AppSelect from "@core/components/app-form-elements/AppSelect.vue";

// For routing with params
const hash = ref('');
const route = useRoute();
hash.value = route.params.hash;
watch(
  () => route.params.hash,
  () => {
    hash.value = route.params.hash;
    if (hash.value) {
      getInvoice(hash.value);
    } else {
      resetInvoiceForm();
    }
  },
);

const token = computed(() => baseService.getTokenFromLocalStorage());

const loading = ref(false);
const submitted = ref(false);
const errors = ref({});

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

});

const submitInvoice = async () => {
  // Delete all errors
  Object.keys(errors.value).forEach(function (key) {
    delete errors.value[key];
  });

  submitted.value = false;
  loading.value = true;

  const formData = new FormData();
  // iterate and add form data
  Object.keys(invoiceData).forEach(function (form_key) {
    console.log(form_key); // key

    if (form_key === 'invoice' ) {
      Object.keys(invoiceData[form_key]).forEach(function (key) {
        console.log(key); // key
        if (invoiceData[form_key][key] !== null && invoiceData[form_key][key] !== '') {
          formData.append(key, invoiceData[form_key][key]);
        }
      });
    }

    if (form_key === 'invoice_items' ) {
      invoiceData[form_key].forEach(function (item, index) {
        Object.keys(item).forEach(function (key) {
          console.log(key); // key
          if (item[key] !== null && item[key] !== '') {
            formData.append(key + '[' + index + ']', item[key]);
          }
        });
      });
    }

    if (form_key === 'invoice_payments' ) {
      invoiceData[form_key].forEach(function (payment, index) {
        Object.keys(payment).forEach(function (key) {
          console.log(key); // key
          if (payment[key] !== null && payment[key] !== '') {
            formData.append(key + '[' + index + ']', payment[key]);
          }
        });
      });
    }

  });

  await axios.post('/api/invoices/store', formData, {
    headers: {
      'Accept': 'application/json',
      "Authorization": "Bearer " + token.value,
    }
  }).then((response) => {
    if (response.data.success) {
      submitted.value = true;
    }
  }).catch((error) => {
    if ([401, 402, 422].includes(error.response.status)) {
      console.log(error.response);

      if (Object.keys(error.response?.data?.errors).length > 0) {
        errors.value = error.response?.data?.errors;
        if (import.meta.env.VITE_APP_ENV === 'local') {
          console.log("Validation errors", errors.value);
        }
      }

      if (error.response?.data?.server_error) {
        errors.value.server_error = 'Server error. Please try again later or contact your admin.';
      }
    }

    console.log(error);
  });
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
    Object.keys(item).forEach(function (key) {
      if (item[key] !== null && item[key] !== '') {
        invoiceData.invoice_items[index][key] = item[key];
      }
    });
  });
}

const populateInvoicePayment = (invoice_payments) => {
  invoice_payments.forEach(function (payment, index) {
    Object.keys(payment).forEach(function (key) {
      if (payment[key] !== null && payment[key] !== '') {
        invoiceData.invoice_payments[index][key] = payment[key];
      }
    });
  });
}

const resetInvoiceForm = () => {
  Object.keys(invoiceData).forEach(function (master_key) {
    if(master_key === 'invoice'){
      Object.keys(invoiceData[master_key]).forEach(function (master_key) {
        invoiceData[master_key] = '';
      });

    }else if(master_key === 'invoice_items'){
      invoiceData[master_key].forEach(function (item, index) {
        Object.keys(item).forEach(function (key) {
          invoiceData[master_key][index][key] = '';
        });
      });

    }else if (master_key === 'invoice_payments'){
      invoiceData[master_key].forEach(function (payment, index) {
        Object.keys(payment).forEach(function (key) {
          invoiceData[master_key][index][key] = '';
        });
      });
    }

});

const updateInvoice = async (hash) => {
  // Delete all errors
  Object.keys(errors.value).forEach(function (key) {
    delete errors.value[key];
  });

  submitted.value = false;
  loading.value = true;

  const formData = new FormData();

  // iterate and add form data
  Object.keys(invoiceData).forEach(function (form_key) {
    console.log(form_key); // key

    if (form_key === 'invoice' ) {
      Object.keys(invoiceData[form_key]).forEach(function (key) {
        console.log(key); // key
        if (invoiceData[form_key][key] !== null && invoiceData[form_key][key] !== '') {
          formData.append(key, invoiceData[form_key][key]);
        }
      });
    }

    if (form_key === 'invoice_items' ) {
      invoiceData[form_key].forEach(function (item, index) {
        Object.keys(item).forEach(function (key) {
          console.log(key); // key
          if (item[key] !== null && item[key] !== '') {
            formData.append(key + '[' + index + ']', item[key]);
          }
        });
      });
    }

    if (form_key === 'invoice_payments' ) {
      invoiceData[form_key].forEach(function (payment, index) {
        Object.keys(payment).forEach(function (key) {
          console.log(key); // key
          if (payment[key] !== null && payment[key] !== '') {
            formData.append(key + '[' + index + ']', payment[key]);
          }
        });
      });
    }

  });

  await axios.post('/api/invoices/update/' + hash, formData, {
    headers: {
      'Accept': 'application/json',
      "Authorization": "Bearer " + token.value,
    }
  }).then((response) => {
    if (response.data.success) {
      submitted.value = true;
    }
  }).catch((error) => {
    if (error.response && [401, 402, 422].includes(error.response.status)) {
      console.log(error.response);

      if (Object.keys(error.response?.data?.errors).length > 0) {
        errors.value = error.response?.data?.errors;
      }

      if (error.response?.data?.server_error) {
        errors.value.server_error = 'Server error. Please try again later or contact your admin.';
      }
    }

    console.log(error);
  });
  loading.value = false;
}

const getInvoice = async (hash) => {
  // Get token from local storage
  await axios.get('/api/invoice/show/' + hash, {
    headers: {
      "Authorization": "Bearer " + token.value,
      'Accept': 'application/json',
    },
    params: {
      hash: hash.value
    }
  }).then((response) => {
    if (response.data.success) {
      invoice.value = response.data.invoice;
      populateInvoice(invoice.value);
      populateInvoiceItem(invoice.value.items);
      populateInvoicePayment(invoice.value.payments);

      if (import.meta.env.VITE_APP_ENV === 'local') {
        console.log("Invoice Show", invoice.value);
      }
    }
  }).catch((error) => {
    console.log(error);
  });
  loading.value = false;
}

const isSendPaymentSidebarVisible = ref(false)

const addPayment = value => {
  invoiceData?.invoice_payments.push(value)
}

const removePayment = index => {
  invoiceData?.invoice_payments.splice(index, 1)
}

const addCharge = value => {
  invoiceData?.invoice_items.push(value)
}

const removeCharge = index => {
  invoiceData?.invoice_items.splice(index, 1)
}

const myCompanies = ref([]);
const clients = ref([]);
const invoice = ref({});

const getCompanies = () => {
  axios.get('/api/companies', {
    headers: {
      "Authorization" : "Bearer " + token.value,
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
  axios.get('/api/clients/min', {
    headers: {
      "Authorization" : "Bearer " + token.value,
      'Accept' : 'application/json',
    },
  }).then((response) => {
    if(response.data.success === true){
      clients.value = response.data.clients;
    }

    if(import.meta.env.VITE_APP_ENV === 'local'){
      console.log("Get Companies", myCompanies.value);
    }

  }).catch((error) => {
    console.log(error);
  });
}

const calculateSubTotal = () => {
  let subTotal = 0;
  let tax = 0;
  invoiceData.invoice_items.forEach((item) => {
    if(item.qty && item.rate){
      subTotal += (item.qty * item.rate);
      if(item.tax === 'HST'){
        tax += (item.qty * item.rate) * 0.13;
      }
    }

  });

  invoiceData.invoice.sub_total = subTotal;
  invoiceData.invoice.taxes = tax;
  invoiceData.invoice.total = subTotal + tax;
}

watch(invoiceData.invoice_items, () => {
  calculateSubTotal();
}, { deep: true });

onBeforeMount(async () => {
  getCompanies();
  getClients();

  if (hash.value) {
    await getInvoice(hash.value);
  } else {
    resetInvoiceForm();
  }

  console.log(myCompanies.value);
  console.log("Token", token.value);

})
</script>

<template>

  <VForm>

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
            <VCol cols="3" md="3" class="text-no-wrap">
              <h6 class="text-h6 mb-4">
                Invoice From:
              </h6>

              <AppAutocomplete
                v-model="invoiceData.invoice.company_id"
                :items="myCompanies"
                item-title="name"
                item-value="id"
                placeholder="Select Client"
                return-object
                class="mb-4"
                style="inline-size: 11.875rem;"
              />

<!--              <VSelect-->
<!--                v-model="invoiceData.invoice.company_id"-->
<!--                :items="myCompanies"-->
<!--                item-title="name"-->
<!--                item-value="id"-->
<!--                placeholder="Select Client"-->
<!--                return-object-->
<!--                class="mb-4"-->
<!--                style="inline-size: 11.875rem;"-->
<!--              />-->
            </VCol>

            <VCol cols="3" md="3" class="text-no-wrap">
              <h6 class="text-h6 mb-4">
                Invoice To:
              </h6>

              <AppAutocomplete
                v-model="invoiceData.invoice.client_id"
                :items="clients"
                item-title="company_name"
                item-value="id"
                placeholder="Select Client"
                return-object
                class="mb-4"
                style="inline-size: 11.875rem;"
              />
            </VCol>

            <VCol cols="3" md="3" class="text-no-wrap">
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

            <VCol cols="3" md="3" class="text-no-wrap">
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

          <VDivider class="my-6 border-dashed" thickness="4" />

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
                  label="Description"
                  placeholder="Description"
                />
              </VCol>

              <VCol
                cols="12"
                md="3"
              >
                <AppTextField
                  @input="calculateSubTotal"
                  v-model="item.qty"
                  label="Quantity"
                  placeholder="Quantity"
                />
              </VCol>

              <VCol
                cols="12"
                md="2"
              >
                <AppTextField
                  @input="calculateSubTotal"
                  v-model="item.rate"
                  label="Rate"
                  placeholder="Rate"
                />
              </VCol>

              <VCol
                cols="12"
                md="2"
              >
                <AppSelect
                  @change="calculateSubTotal"
                  v-model="item.tax"
                  label="Tax"
                  :items="[
                   'HST',
                   'None',
                  ]"
                />
              </VCol>

              <VCol
                cols="12"
                md="2"
              >
                <a href="">
                  <TrashIcon
                    @click.prevent="removeCharge(index)"
                    :width="25" :height="25"
                    :color="'#f84444'"
                    class="mt-md-5"
                  />
                </a>
              </VCol>

            </VRow>

            <VBtn
              class="mt-2"
              size="small"
              prepend-icon="tabler-plus"
              @click="addCharge"
            >
              Add Item
            </VBtn>
          </div>

          <VDivider class="my-6 border-dashed" thickness="4" />

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
                  placeholder="Item"
                />
              </VCol>

              <VCol
                cols="12"
                md="3"
              >
                <AppTextField
                  v-model="payment.amount"
                  label="Amount"
                  placeholder="Amount"
                />
              </VCol>

              <VCol
                cols="12"
                md="2"
              >
                <AppTextField
                  v-model="payment.date"
                  label="Date"
                  placeholder="Date"
                />
              </VCol>

              <VCol
                cols="12"
                md="2"
              >
                <AppTextField
                  v-model="payment.note"
                  label="Note"
                  placeholder="Note"
                />
              </VCol>

              <VCol
                cols="12"
                md="2"
              >
                <a href="">
                  <TrashIcon
                    @click.prevent="removePayment(index)"
                    :width="25" :height="25"
                    :color="'#f84444'"
                    class="mt-md-5"
                  />
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
                      {{ invoiceData.invoice.sub_total.toFixed(2) }}
                    </h6>
                  </td>
                </tr>

<!--                <tr>-->
<!--                  <td class="pe-16">-->
<!--                    Discount:-->
<!--                  </td>-->
<!--                  <td :class="$vuetify.locale.isRtl ? 'text-start' : 'text-end'">-->
<!--                    <h6 class="text-h6">-->
<!--                      $28-->
<!--                    </h6>-->
<!--                  </td>-->
<!--                </tr>-->

                <tr>
                  <td class="pe-16">
                    Tax:
                  </td>
                  <td :class="$vuetify.locale.isRtl ? 'text-start' : 'text-end'">
                    <h6 class="text-h6">
                      {{ invoiceData.invoice.taxes.toFixed(2) }}
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
                      {{ invoiceData.invoice.total.toFixed(2) }}
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
                      {{ invoiceData.invoice.balance.toFixed(2) }}
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

        <VBtn
          block
          color="success"
          variant="tonal"
          class="mb-2"
          @click="submitInvoice"
        >
          Submit Invoice
        </VBtn>

        <router-link
          :to="{ name: 'InvoicesView'}"
        >
          <VBtn
            block
            color="primary"
            variant="tonal"
            @click=""
          >
            All Invoices
          </VBtn>
        </router-link>

      </VCol>

      <!-- 👉 Right Column: Invoice Action -->
    </VRow>


  </VForm>

  <!-- 👉 Send Invoice Sidebar -->
  <InvoiceSendInvoiceDrawer v-model:isDrawerOpen="isSendPaymentSidebarVisible" />
</template>
