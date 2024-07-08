<script setup>
import { computed, onBeforeMount, ref, watch } from 'vue'
import axios from 'axios'

import baseService from '@/utils/base-service.js'
import SubscriptionsListRow from "@/views/pages/subscriptions/SubscriptionsListRow.vue";
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";

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
    title: 'Charge Credit Card',
    sortable: false,
  },
  {
    title: 'Actions',
    sortable: false,
  },
]

const getSubscriptions = (page = 1) => {
  dataLoaded.value = false;
  axios.get('/api/subscriptions?page=' + page, {
    headers: {
      "Authorization" : "Bearer " + token.value,
      'Accept' : 'application/json',
    },
  }).then((response) => {
    if(response.data.success === true){
      subscriptions.value = response.data.subscriptions;
      total.value = response.data.total;
    }
    dataLoaded.value = true;
    console.log(subscriptions.value);
  }).catch((error) => {
    console.log(error);
  });
}

const searchSubscriptions = () => {
  if (search.value.trim() === '') {
    localStorage.removeItem('subscription-search');
    getSubscriptions();
    return;
  }

  // Store search result to be loaded on page refresh
  localStorage.setItem('subscription-search', search.value);

  dataLoaded.value = false;
  axios.get('/api/subscriptions/search?query=' + search.value, {
    headers: {
      "Authorization" : "Bearer " + token.value,
      'Accept' : 'application/json',
    },
  }).then((response) => {
    if(response.data.success === true){
      subscriptions.value = response.data.subscriptions;
      searchTotal.value = response.data.total;
    }
    dataLoaded.value = true;
  }).catch((error) => {
    console.log(error);
  });
}

watch(search, () => {
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

  <VCardText>
    <VRow>
      <VCol
        cols="12"
        offset-md="8"
        md="4"
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
        />
      </VCol>
    </VRow>
  </VCardText>

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
