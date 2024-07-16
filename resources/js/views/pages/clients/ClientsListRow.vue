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

    <td>{{ client.company_name}}</td>

    <td>{{ client.main_contact_first_name }}</td>

    <td>{{ client.main_contact_last_name }}</td>

    <td>{{ client.company_address }}</td>

    <td>{{ client.company_email }}</td>

    <td>
      <VIcon
        v-if="client.credit_cards.length > 0"
        icon="tabler-credit-card"
      />
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
              class="btn btn-warning waves-effect waves-light"
              exact
              :to="{name: 'ClientsEdit', params: { hash: client.hash }}"
            >
              <v-btn class="ma-0 rounded-0">View</v-btn>
            </router-link>

          </v-btn-toggle>
        </template>
      </v-menu>

<!--      <router-link-->
<!--        class="btn btn-warning waves-effect waves-light"-->
<!--        exact-->
<!--        :to="{name: 'ClientsEdit', params: { hash: client.hash }}"-->
<!--      >-->
<!--        <i class="fa fa-edit"></i>-->
<!--      </router-link>-->
    </td>
  </tr>
</template>

<style scoped lang="scss">

</style>
