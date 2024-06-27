<script setup>
import { ref, defineProps, reactive } from 'vue'
// import ClientForm from "@/views/pages/clients/ClienstForm.vue";

const props = defineProps({
  client: {
    type: Object,
    required: true,
  },
});

const client = ref(props.client);

const emittedClient = (event) => {
  client.value = event;
}
</script>

<template>
  <tr>

    <td>
      <p>
        <strong>Customer Name:</strong> {{ client.company_name}}<br>
        <strong>Company Email:</strong> {{ client.company_email}}<br>
        <strong>Company Phone:</strong> {{ client.company_phone }}<br>
        <strong>Company Address:</strong> {{ client.company_address }}<br>
      </p>
    </td>

    <td>
      <p>
        <strong>Main Contact First Name:</strong> {{ client.main_contact_first_name }}<br>
        <strong>Main Contact Last Name:</strong> {{ client.main_contact_last_name }}<br>
        <strong>Main Contact Phone:</strong> {{ client.main_contact_phone }}<br>
        <strong>Main Contact Email:</strong> {{ client.main_contact_email }}<br>
      </p>
    </td>

    <td>
      <p>
        <strong>AP First Name:</strong> {{ client.ap_first_name }}<br>
        <strong>AP Last Name:</strong> {{ client.ap_last_name }}<br>
        <strong>AP Phone:</strong> {{ client.ap_phone }}<br>
        <strong>AP Email:</strong> {{ client.ap_email }}<br>
      </p>
    </td>

    <td>
      <a v-if="client.credit_cards" href="" class="btn btn-info waves-effect waves-light">
        <i class="fa fa-credit-card"></i>
      </a>
    </td>

    <td>
      <a href="" class="btn btn-warning waves-effect waves-light">
        <i class="fa fa-edit"></i>
      </a>
    </td>
  </tr>
</template>

<style scoped lang="scss">

</style>
