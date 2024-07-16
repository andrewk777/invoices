<script setup>
import { computed, onBeforeMount, ref, watch } from 'vue'
import baseService from '@/utils/base-service.js'
import SubscriptionsListRow from "@/views/pages/subscriptions/SubscriptionsListRow.vue";
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";
import apiClientAuto from '@/utils/apiCLientAuto.js';
import handleErrors from "@/utils/handleErrors.js";

const token = computed(() => baseService.getTokenFromLocalStorage());
const loading = ref(false);

const subscriptions = ref([]);
const total = ref(0);
const searchTotal = ref(0);
const dataLoaded = ref(false);
const search = ref('');
const savedSearch = computed(() => localStorage.getItem('subscription-search'));

const headers = [
  {
    title: 'Name',
    key: 'name',
  },
  {
    title: 'From Company',
    key: 'company.name',
  },
  {
    title: 'Customer',
    key: 'client.company_name',
  },

  {
    title: 'Total',
    key: 'total',
  },

  {
    title: 'Frequency',
    key: 'frequency_month',
  },

  {
    title: 'Next Charge',
    key: 'next_charge_date',
  },

  {
    title: 'Expiration',
    key: 'due_in_days',
  },

  {
    title: 'Email Invoice',
    key: 'email_invoice',
    sortable: true,
  },

  {
    title: 'Charge Credit Card',
    sortable: false,
  },

  {
    title: 'Actions',
    sortable: false,
  },
]

const getSubscriptions = async (page = 1) => {
  dataLoaded.value = false;

  try{
    const response = await apiClientAuto.get('/subscriptions?page=' + page);
    if(response.data.success === true){
      subscriptions.value = response.data.subscriptions;
      total.value = response.data.total;
    }
    dataLoaded.value = true;

  } catch (error) {
    handleErrors(error);
  }

}

const searchSubscriptions = async () => {
  if (search.value.trim() === '') {
    localStorage.removeItem('subscription-search');
    await getSubscriptions();
    return;
  }

  // Store search result to be loaded on page refresh
  localStorage.setItem('subscription-search', search.value);
  dataLoaded.value = false;

  try{
    const response = await apiClientAuto.get('/subscriptions/search?query=' + search.value);

    if(response.data.success === true){
      subscriptions.value = response.data.subscriptions;
      searchTotal.value = response.data.total;
    }
    dataLoaded.value = true;

  } catch (error) {
    handleErrors(error);
  }

}

watch(search, (newValue) => {
  if (newValue === null) {
    search.value = '';
  }
  searchSubscriptions();
});

onBeforeMount(() => {
  if(savedSearch.value) {
    search.value = savedSearch.value;
    searchSubscriptions();
  }else{
    getSubscriptions();
  }
});
</script>

<template>
  <VRow class="mb-2">
    <VCol cols="6">
      <h3 class="card-header">Subscriptions</h3>
    </VCol>

    <VCol cols="6" class="text-right mt-4">
      <router-link
        class="btn btn-info waves-effect waves-light mr-2 btn-sm"
        exact
        :to="{name: 'SubscriptionsCreate'}"
      >
        <VBtn
          variant="flat"
          :size="'small'"
        >
          Create Subscription
        </VBtn>
      </router-link>
    </VCol>
  </VRow>

  <VRow>
    <VCol
      cols="12"
      md="4"
      class="d-flex"
    >
      <AppTextField
        v-model="search"
        @keyup.enter="searchSubscriptions"
        placeholder="Search ..."
        append-inner-icon="tabler-search"
        single-line
        hide-details
        dense
        outlined
        clearable
      />
      <VIcon
        @click="!search ? getSubscriptions() : searchSubscriptions()"
        class="ml-2"
        color="primary"
        size="30"
        icon="tabler-rotate-clockwise"
        title="reload"
      />
    </VCol>
  </VRow>

  <VDataTable
    :headers="headers"
    :items="subscriptions"
    :items-per-page="25"
    :loading="!dataLoaded"
  >
    <template v-slot:item="{ item: subscription }">
      <SubscriptionsListRow :subscription="subscription" />
    </template>
  </VDataTable>

  <div v-if="search && searchTotal > 0" class="text-center mt-4">
    <p>Showing {{ subscriptions.length }} of {{ searchTotal }} search results.</p>
  </div>

  <div v-if="!search && total > 0" class="text-center mt-4">
    <p>Showing {{ subscriptions.length }} of {{ total }} subscription.</p>
  </div>
</template>

<style scoped>
/* Add any necessary styles */
</style>
