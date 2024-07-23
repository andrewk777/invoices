<script setup>
import { ref, defineProps, reactive } from 'vue'
import DocumentLicenseIcon from "@/components/icons/DocumentLicenseIcon.vue";
import axios from "axios";
import apiClientAuto from "@/utils/apiCLientAuto.js";

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
  apiClientAuto.get(`/invoices/receipt/${invoice.value.hash}/download`, {
    responseType: 'blob',
  })
    .then(response => {
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', 'invoice_receipt.pdf');
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    })
    .catch(error => {
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
      <VChip
        class="justify-center"
        style="width: 100%;"
        v-if="invoice.status === 'Paid'"
        color="success"
      >
        {{ invoice.status }}
      </VChip>

      <VChip
        class="justify-center"
        style="width: 100%;"
        v-else-if="invoice.status === 'Partially Paid'"
        color="warning"
      >
        {{ invoice.status }}
      </VChip>

      <VChip
        class="justify-center"
        style="width: 100%;"
        v-else-if="invoice.status === 'Approved'"
        color="success"
      >
        {{ invoice.status }}
      </VChip>

      <VChip
        class="justify-center"
        style="width: 100%;"
        v-else
        color="error"
      >
        {{ invoice.status }}
      </VChip>
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
            class="pa-0 pl-0 pr-0 h-auto rounded-1"
          >
            <router-link
              :to="{ name: 'InvoicesEdit', params: { hash: invoice.hash } }"
              class="btn btn-primary btn-sm mr-2 pl-2"
            >
              <v-btn class="ma-0 rounded-0 pl-3">View</v-btn>
            </router-link>

            <v-btn v-bind="props" >
              <VIcon icon="tabler-dots-vertical"/>
            </v-btn>
          </v-btn-toggle>
        </template>

        <v-list class="border-label-info">
          <v-list-item >
            <a href="" @click.prevent="downloadInvoiceReceipt">
              <v-list-item-title>Invoice pdf</v-list-item-title>
            </a>
          </v-list-item>
        </v-list>

      </v-menu>

    </td>

  </tr>
</template>

<style scoped lang="scss">
.v-btn--size-default {
  --v-btn-size: 0.9375rem;
  --v-btn-height: 38px;
  min-width: 20px;
  padding: 0 20px;
  font-size: 13px;
}
</style>
