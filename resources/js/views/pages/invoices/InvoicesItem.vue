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
        {{ invoice.client?.main_contact_first_name }} {{ invoice.client?.main_contact_last_name }}
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
        {{ invoice.total }}
      </p>
    </td>

    <td>
      <p>
        {{ invoice.currency }}
      </p>
    </td>

    <td>
      <p>
        {{ invoice.balance }}
      </p>
    </td>

    <td>
      <div class="d-flex">
        <router-link
          :to="{ name: 'InvoicesEdit', params: { hash: invoice.hash } }"
          class="btn btn-primary btn-sm mr-2"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="icon icon-tabler icon-tabler-pencil-plus"
            width="30" height="30" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
            <path d="M13.5 6.5l4 4" />
            <path d="M16 19h6" />
            <path d="M19 16v6" />
          </svg>
        </router-link>

        <a href="" @click.prevent="downloadInvoiceReceipt">
          <DocumentLicenseIcon />
        </a>
      </div>
    </td>

  </tr>
</template>

<style scoped lang="scss">

</style>
