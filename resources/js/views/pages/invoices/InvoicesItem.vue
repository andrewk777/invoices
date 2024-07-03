<script setup>
import { ref, defineProps, reactive } from 'vue'

const props = defineProps({
  invoice: {
    type: Object,
    required: true,
  },
});

const invoice = ref(props.invoice);
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

      <router-link
        :to="'/invoices/receipt/' + invoice.hash"
        class="btn btn-info btn-sm"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="icon icon-tabler icon-tabler-license"
          width="30" height="30"
          viewBox="0 0 24 24" stroke-width="1.5"
          stroke="currentColor" fill="none"
          stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M15 21h-9a3 3 0 0 1 -3 -3v-1h10v2a2 2 0 0 0 4 0v-14a2 2 0 1 1 2 2h-2m2 -4h-11a3 3 0 0 0 -3 3v11" />
            <path d="M9 7l4 0" />
            <path d="M9 11l4 0" />
        </svg>
      </router-link>
    </td>

  </tr>
</template>

<style scoped lang="scss">

</style>
