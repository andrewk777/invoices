<script setup>
import { ref, defineProps, reactive } from 'vue'
import DocumentLicenseIcon from "@/components/icons/DocumentLicenseIcon.vue";
import axios from "axios";

const props = defineProps({
  subscription: {
    type: Object,
    required: true,
  },
});

const token = computed(() => baseService.getTokenFromLocalStorage());
const subscription = ref(props.subscription);

const chargeCreditCard = () => {

}

const deleteSubscription = () => {

}

</script>

<template>
  <tr>

    <td>
      <p>
        {{ subscription.name }}
      </p>
    </td>

    <td>
      <p>
        {{ subscription.company?.name }}
      </p>
    </td>

    <td>
      <p>
        {{ subscription.client.company_name }}
      </p>
    </td>

    <td>
      <p>
        {{ subscription.total }}
      </p>
    </td>

    <td>
      <p>
        {{ subscription.frequency_month }}
      </p>
    </td>

    <td>
      <p>
        {{ subscription.next_charge_date }}
      </p>
    </td>

    <td>
      <p>
        {{ subscription.due_in_days }}
      </p>
    </td>

    <td>
      <p class="text-center">
        <VIcon
          icon="tabler-credit-card"
        />
      </p>
    </td>

    <td>
      <div class="d-flex">

        <router-link
          :to="{ name: 'SubscriptionsEdit', params: { hash: subscription.hash } }"
          class="btn btn-primary btn-sm mr-2"
        >
          <VIcon
            icon="tabler-edit"
          />
        </router-link>

        <a href="" @click.prevent="deleteSubscription">
          <VIcon
            icon="tabler-trash"
          />
        </a>

      </div>
    </td>

  </tr>
</template>

<style scoped lang="scss">

</style>
