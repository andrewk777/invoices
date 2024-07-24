<script setup>
import {onBeforeMount, reactive, ref, watch} from 'vue'
import AppDateTimePicker from "@core/components/app-form-elements/AppDateTimePicker.vue";
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";
import AppAutocomplete from "@core/components/app-form-elements/AppAutocomplete.vue";
import AppSelect from "@core/components/app-form-elements/AppSelect.vue";
import {useRoute} from 'vue-router';
import AppTextarea from "@core/components/app-form-elements/AppTextarea.vue";
import apiClientAuto from '@/utils/apiCLientAuto.js';
import handleErrors from "@/utils/handleErrors.js";

const route = useRoute();
const hash = ref(route.params.hash || '');

const token = computed(() => baseService.getTokenFromLocalStorage());

const loading = ref(false);
const submitted = ref(false);
const errors = ref({});

const subscription = ref({});
const myCompanies = ref([]);
const fromMyCompany = ref({});
const toClient = ref({});
const clients = ref([]);

const subscriptionTagsInput = ref('');
const subscriptionTags = ref([]);

const clientCreditCards = ref([]);

const form = reactive({
  subscription: {
    name: '',
    my_company_id: '',
    client_id: '',
    tags: '',
    currency: '',
    credit_card_id: '',
    next_charge_date: new Date().toISOString().split('T')[0],
    due_in_days: 10,
    frequency_day: '',
    frequency_month: '',
    can_pay_with_cc: false,
    starting_date: '',
    expiration_date: '',
    charge_cc: false,
    email_invoice: false,
    email_template_id: '',
    subtotal: 0,
    taxes: 0,
    total: 0,
  },

  charges: [],

});

const createTag = () => {
  if (subscriptionTagsInput.value.trim() !== '') {
    subscriptionTags.value.push(subscriptionTagsInput.value.trim());
    subscriptionTagsInput.value = '';
    form.subscription.tags = subscriptionTags.value.join(',');
  }
};

const removeTag = (index) => {
  subscriptionTags.value.splice(index, 1);
};

const frequencies = ref([
  {value: 1, text: 'Monthly'},
  {value: 2, text: 'Every 2 Months'},
  {value: 3, text: 'Every 3 Months'},
  {value: 4, text: 'Every 4 Months'},
  {value: 5, text: 'Every 5 Months'},
  {value: 6, text: 'Every 6 Months'},
  {value: 7, text: 'Every 7 Months'},
  {value: 8, text: 'Every 8 Months'},
  {value: 9, text: 'Every 9 Months'},
  {value: 10, text: 'Every 10 Months'},
  {value: 11, text: 'Every 11 Months'},
  {value: 12, text: 'Yearly'},
]);

const currencies = ref([
  {value: 'USD', text: 'USD'},
  {value: 'CAD', text: 'CAD'},
]);

const submitSubscription = async (event, action = null) => {

  if(form.subscription.charge_cc === true && !form.subscription.credit_card_id){
    errors.value.credit_card = ['Please select a credit card'];
    return;
  }

  // Delete all errors
  Object.keys(errors.value).forEach(function (key) {
    delete errors.value[key];
  });

  submitted.value = false;
  loading.value = true;

  try {
    let response;
    if (hash.value) {
      response = await apiClientAuto.post('/subscriptions/update/' + hash.value, form);

    } else {
      response = await apiClientAuto.post('/subscriptions/store', form);

    }

    if (response.data.success){
      submitted.value = true;
      if(!hash.value){
        hash.value = response.data.subscription.hash;
      }
      if(action === 'close') {
        window.location.href = '/subscriptions';
      }

    }

  } catch (error) {
    handleErrors(error, errors);
  }

  loading.value = false;
}

const populateSubscription = (subscription) => {
  Object.keys(subscription).forEach(function (key) {
    if (subscription[key] !== null && subscription[key] !== '') {
      form.subscription[key] = subscription[key];
    }
  });
}

const populateStringTags = (tags) => {
  if (tags) {
    subscriptionTags.value = tags.split(',');
  }
};

const populateCharges = (charges) => {
  charges.forEach(function (item, index) {
    // Initialize the item at index if it does not exist
    if (!form.charges[index]) {
      form.charges[index] = {
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
            form.charges[index][key] = 'HST';
          }else{
            form.charges[index][key] = 'None';
          }
        }else{
          form.charges[index][key] = item[key];
        }
      }
    });

  });
};

const getSubscription = async (hash) => {
  try {
    const params = {
      hash: hash
    }
    const response = await apiClientAuto.get('/subscriptions/show/' + hash, {params});

    if (response.data.success) {
      subscription.value = response.data.subscription;

      populateSubscription(subscription.value);
      populateStringTags(subscription.value.tags);
      selectClient(subscription.value.client_id);
      selectMyCompany(subscription.value.my_company_id);

      if(subscription.value.charges.length > 0){
        populateCharges(subscription.value.charges);
      }

    }
  } catch (error) {
    handleErrors(error, errors);
  }

  loading.value = false;
}

const addCharge = value => {
  form.charges.push({
    description: '',
    rate: '',
    qty: '',
    tax: 'HST',
  });
}

const removeCharge = index => {
  form.charges.splice(index, 1)
}

const getCompanies = async () => {
  try {
    const response = await apiClientAuto.get('/companies');

    if (response.data.success === true) {
      myCompanies.value = response.data.companies;
      fromMyCompany.value = response.data.companies[1];
      form.subscription.my_company_id = response.data.companies[1].id
    }
  } catch (error) {

  }
}

const selectMyCompany = (event) => {
  fromMyCompany.value = myCompanies.value.find(company => company.id === event);
}

const selectClient = (event) => {
  toClient.value = clients.value.find(client => client.id === event);
  clientCreditCards.value = toClient.value.credit_cards;
  form.subscription.credit_card_id = toClient.value.default_credit_card_id;
}

const getClients = async () => {
  try {
    const response = await apiClientAuto.get('/clients/min');
      if (response.data.success === true) {
        clients.value = response.data.clients;
      }
  } catch (error) {
    handleErrors(error, errors);
  }
}

const calculateSubTotal = () => {
  let subTotal = 0;
  let tax = 0;
  form.charges.forEach((item) => {
    if (item.qty && item.rate) {
      subTotal += (item.qty * item.rate);
      if (item.tax === 'HST') {
        tax += (item.qty * item.rate) * 0.13;
      }
    }
  });

  form.subscription.subtotal = subTotal;
  form.subscription.taxes = tax;
  form.subscription.total = subTotal + tax;
}

watch(() => form.charges, () => {
  calculateSubTotal();
}, { deep: true });

watch(() => hash.value, async () => {
  if (hash.value) {
    await getSubscription(hash.value);
  }
});

onBeforeMount(async () => {
  await getCompanies();
  await getClients();

  if (hash.value) {
    await getSubscription(hash.value);
  }

})
</script>

<template>

  <VForm>

    <VRow class="position-relative">

      <VCol cols="12" md="10">

        <VCol cols="12" md="10">
          <h3>Subscriptions</h3>
        </VCol>

        <VCard class="pa-6 pa-sm-12">

          <VAlert
            v-if="submitted"
            class="text-center mb-2"
            type="success"
          >
            {{ hash ? 'Subscription updated successfully' : 'Subscription created successfully'}}
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
                <img :src="fromMyCompany.logo" width="200"/>
              </div>

              <!-- 👉 Address -->
              <p class="text-high-emphasis mb-0">
                {{ fromMyCompany.address }}
              </p>

              <p class="text-high-emphasis mb-0">
                {{ fromMyCompany.email }}
              </p>

              <p class="text-high-emphasis mb-0">
                {{ fromMyCompany.mobile }}
              </p>
            </div>

            <!-- 👉 Right Content -->
            <div class="d-flex flex-column gap-2">

              <div class="d-flex align-start align-sm-center gap-x-4 font-weight-medium text-lg flex-column flex-sm-row">
                <span
                  class="text-high-emphasis text-sm-end"
                  style="inline-size: 5.625rem;"
                >From Company:</span>

                <AppAutocomplete
                  v-if="fromMyCompany"
                  @change="selectMyCompany"
                  v-model="form.subscription.my_company_id"
                  :items="myCompanies"
                  item-title="name"
                  item-value="id"
                  placeholder="Select Client"
                  style="inline-size: 9.5rem;"
                />
              </div>

              <!-- 👉 Issue Date -->
              <div class="d-flex gap-x-4 align-start align-sm-center flex-column flex-sm-row">
                <span
                  class="text-high-emphasis text-sm-end"
                  style="inline-size: 5.625rem;"
                >Next Charge:</span>

                <span style="inline-size: 9.5rem;">
                  <AppDateTimePicker
                    v-model="form.subscription.next_charge_date"
                    placeholder="YYYY-MM-DD"
                    :config="{ position: 'auto right' }"
                  />
                </span>
              </div>

              <div class="d-flex gap-x-4 align-start align-sm-center flex-column flex-sm-row">
                <span
                  class="text-high-emphasis text-sm-end"
                  style="inline-size: 5.625rem;"
                >Due in days:</span>

                <span style="inline-size: 9.5rem;">
                  <AppTextField
                    v-model="form.subscription.due_in_days"
                    type="number"
                    placeholder="Due in days"
                  />
                </span>
              </div>

              <div class="d-flex gap-x-4 align-start align-sm-center flex-column flex-sm-row">
                <span
                  class="text-high-emphasis text-sm-end"
                  style="inline-size: 5.625rem;"
                >Expiration:</span>
                <span style="min-inline-size: 9.5rem;">
                  <AppDateTimePicker
                    v-model="form.subscription.expiration_date"
                    placeholder="YYYY-MM-DD"
                    :config="{ position: 'auto right' }"
                  />
                </span>
              </div>
            </div>
          </div>
          <!-- !SECTION -->

          <VRow>
            <VCol cols="12" md="6" class="text-no-wrap">
              <h6 class="text-h6 mb-4">Name:</h6>

              <AppTextField
                v-model="form.subscription.name"
                placeholder="Subscription Name"
                class="mb-4"
              />
            </VCol>

            <VCol cols="12" md="6" class="text-no-wrap">
              <h6 class="text-h6 mb-4">Tags:</h6>

              <div class="tag-input">
                  <div class="tag-container">
                  <span v-for="(tag, index) in subscriptionTags" :key="index" class="tag">
                    {{ tag }}
                    <span class="remove-tag" @click="removeTag(index)">&times;</span>
                  </span>
                  <input
                    type="text"
                    v-model="subscriptionTagsInput"
                    @keydown.enter.prevent="createTag"
                    @keydown.space.prevent="createTag"
                    @keydown.188.prevent="createTag"
                    placeholder="Enter tags..."
                  />
                </div>
              </div>
            </VCol>

            <VCol cols="12" md="3" class="text-no-wrap">
              <h6 class="text-h6 mb-4">
                Client:
              </h6>

              <AppAutocomplete
                @change="selectClient"
                v-model="form.subscription.client_id"
                :items="clients"
                item-title="company_name"
                item-value="id"
                placeholder="Select Client"
                class="mb-4"
                style="inline-size: 11.875rem;"
              />

              <div v-if="form.subscription.client_id" class="d-block">
                <p><strong>Company:</strong> {{ toClient.company_name }}</p>
                <p><strong>Email:</strong> {{ toClient.company_email }}</p>
                <p><strong>Mobile:</strong> {{ toClient.company_phone }}</p>
                <p><strong>Address:</strong> {{ toClient.company_address }}</p>
                <router-link
                  :to="{ name: 'ClientsEdit', params: { hash: toClient.hash }}"
                  target="_blank"
                >
                  <VBtn
                    color="primary"
                    variant="text"
                  >
                    View Client
                  </VBtn>
                </router-link>
              </div>
            </VCol>

            <VCol cols="12" md="3" class="text-no-wrap">
              <h6 class="text-h6 mb-4">
                Client CC:
              </h6>

              <AppAutocomplete
                v-model="form.subscription.credit_card_id"
                :items="clientCreditCards || []"
                item-title="cc_last_4_digits"
                item-value="id"
                placeholder="Select Credit Card"
                class="mb-4"
                style="inline-size: 11.875rem;"
              />
            </VCol>

            <VCol cols="12" md="3" class="text-no-wrap">
              <h6 class="text-h6 mb-4">
                Frequency Month:
              </h6>
              <AppAutocomplete
                v-model="form.subscription.frequency_month"
                :items="frequencies"
                item-title="text"
                item-value="value"
                placeholder="Select Frequency"
                class="mb-4"
                style="inline-size: 11.875rem;"
              />
            </VCol>

            <VCol cols="12" md="3" class="text-no-wrap">
              <h6 class="text-h6 mb-4">Frequency Day:</h6>

              <AppTextField
                v-model="form.subscription.frequency_day"
                placeholder="Frequency Day"
                class="mb-4"
                type="number"
              />
            </VCol>

            <VCol cols="12" md="3" class="text-no-wrap">
              <h6 class="text-h6 mb-4">
                Currency:
              </h6>

              <VSelect
                v-model="form.subscription.currency"
                :items="[
                  'USD',
                  'CAD',
                ]"
                placeholder="Select Currency"
                class="mb-4"
                style="inline-size: 11.875rem;"
              />
            </VCol>

            <VCol cols="12" md="3" class="text-no-wrap d-flex">
              <VCheckbox
                v-model="form.subscription.charge_cc"
                label="Charge Credit Card"
                class="mb-4"
              />
            </VCol>

            <VCol cols="12" md="3" class="text-no-wrap d-flex">
              <VCheckbox
                v-model="form.subscription.can_pay_with_cc"
                label="Customers can pay with CC"
                class="mb-4"
              />
            </VCol>

            <VCol cols="12" md="3" class="text-no-wrap d-flex">
              <VCheckbox
                v-model="form.subscription.email_invoice"
                label="Email Invoice"
                class="mb-4"
              />
            </VCol>

          </VRow>

          <VDivider class="my-6 border-dashed" thickness="4" />

          <!-- Add Charges -->
          <div class="add-products-form">
            <div class="d-flex justify-space-between mb-4">
              <h3 class="pt-6">
                Charges:
              </h3>

              <VBtn
                class="mt-2"
                size="small"
                prepend-icon="tabler-plus"
                @click="addCharge"
              >
                Add Item
              </VBtn>
            </div>

            <VRow v-for="(item, index) in form.charges" :key="index">

              <VCol
                cols="12"
                md="5"
              >
                <AppTextarea
                  v-model="item.description"
                  rows="2"
                  placeholder="Item description"
                  persistent-placeholder
                />
              </VCol>

              <VCol
                cols="12"
                md="2"
                sm="4"
              >
                <AppTextField
                  @input="calculateSubTotal"
                  v-model="item.qty"
                  type="number"
                  placeholder="Quantity"
                  class="mb-6"
                />
              </VCol>

              <VCol
                cols="12"
                md="2"
                sm="4"
              >
                <AppTextField
                  @input="calculateSubTotal"
                  v-model="item.rate"
                  type="number"
                  placeholder="Rate"
                  class="mb-6"
                />
              </VCol>

              <VCol
                cols="12"
                md="2"
              >
                <AppSelect
                  @change="calculateSubTotal"
                  v-model="item.tax"
                  :items="[
                   'HST',
                   'None',
                  ]"
                  placeholder="Select Tax"
                  class="mb-6"
                />
              </VCol>

              <VCol
                cols="12"
                md="1"
              >
                <a href="">
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
                      {{ parseFloat(form.subscription.subtotal).toFixed(2) }}
                    </h6>
                  </td>
                </tr>

                <tr>
                  <td class="pe-16">
                    Tax:
                  </td>
                  <td :class="$vuetify.locale.isRtl ? 'text-start' : 'text-end'">
                    <h6 class="text-h6">
                      {{ parseFloat(form.subscription.taxes).toFixed(2) }}
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
                      {{ parseFloat(form.subscription.total).toFixed(2) }}
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
          v-if="!loading"
          block
          color="success"
          variant="tonal"
          class="mb-2"
          @click="submitSubscription"
        >
          Save Subscription
        </VBtn>

        <VBtn
          v-if="!loading"
          block
          color="info"
          variant="tonal"
          class="mb-2"
          @click="submitSubscription($event, 'close')"
        >
          Save and Close
        </VBtn>

        <VBtn
          v-else
          block
          color="success"
          variant="tonal"
          class="mb-2"
          disabled
        >
          <i class="fa fa-circle-notch fa-spin fa-2x"></i>
        </VBtn>

        <VBtn
          block
          color="error"
          variant="tonal"
          class="mb-2"
          @click.prevent="$router.go(-1)"
        >
          Close
        </VBtn>

        <router-link
          :to="{ name: 'SubscriptionsView'}"
        >
          <VBtn
            block
            color="primary"
            variant="tonal"
            @click=""
          >
            View all
          </VBtn>
        </router-link>

      </VCol>

    </VRow>


  </VForm>

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

.tag-input {
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 4px;
  display: flex;
  align-items: center;
}

.tag-container {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
}

.tag {
  display: inline-flex;
  align-items: center;
  background-color: #f0f0f0;
  padding: 4px 8px;
  margin-right: 4px;
  margin-bottom: 4px;
  border-radius: 3px;
}

.remove-tag {
  margin-left: 4px;
  cursor: pointer;
}

input {
  flex: 1;
  border: none;
  outline: none;
  padding: 4px;
  font-size: 14px;
}
</style>
