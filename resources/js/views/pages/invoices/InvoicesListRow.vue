<script setup>
import { ref, defineProps, reactive } from 'vue'
import DocumentLicenseIcon from "@/components/icons/DocumentLicenseIcon.vue";
import axios from "axios";

const props = defineProps({
  invoice: {
    type: Object,
    required: true,
  },
});

const token = computed(() => baseService.getTokenFromLocalStorage());
const invoice = ref(props.invoice);
const actionItems = [
  { title: 'Download Receipt' },
];

const downloadInvoiceReceipt = () => {

  axios.get(`/api/invoices/receipt/${invoice.value.hash}/download`, {

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

</script>

<template>
  <tr>

    <td>
      <p>
        {{ invoice.invoice_num }}
      </p>
    </td>

    <td>
      <p>
        {{ invoice.company?.name }}
      </p>
    </td>

    <td>
      <p>
        {{ invoice.status }}
      </p>
    </td>

    <td>
      <p>
        {{ invoice.client?.company_name }}
      </p>
    </td>

    <td>
      <p>
        {{ invoice.invoice_date }}
      </p>
    </td>

    <td>
      <p>
        {{ invoice.invoice_due }}
      </p>
    </td>

    <td>
      <p>
        ${{ parseFloat(invoice.total).toFixed(2) }}
      </p>
    </td>

    <td>
      <p>
        {{ invoice.currency }}
      </p>
    </td>

    <td>
      <p>
        ${{ parseFloat(invoice.balance).toFixed(2) }}
      </p>
    </td>

    <td>

      <v-menu>

        <template v-slot:activator="{ props }">
          <v-btn-toggle
            variant="text"
            density="compact"
            class="pa-0 h-auto rounded-1"
          >
            <router-link
              :to="{ name: 'InvoicesEdit', params: { hash: invoice.hash } }"
              class="btn btn-primary btn-sm mr-2"
            >
              <v-btn class="ma-0 rounded-0">View</v-btn>
            </router-link>

            <v-btn
              color="primary"
              v-bind="props"
              density="compact"
              class="rounded-0"
              variant="tonal"
            >
              <VIcon
                icon="tabler-dots-vertical"
              />
            </v-btn>
          </v-btn-toggle>
        </template>

        <v-list class="border-label-info">
          <v-list-item >
            <a href="" @click.prevent="downloadInvoiceReceipt">
              <v-list-item-title>Download Receipt</v-list-item-title>
            </a>
          </v-list-item>
        </v-list>

      </v-menu>

    </td>

  </tr>
</template>

<style scoped lang="scss">

</style>
