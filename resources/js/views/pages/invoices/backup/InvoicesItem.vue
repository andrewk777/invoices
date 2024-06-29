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

    </td>

  </tr>
</template>

<style scoped lang="scss">

</style>
