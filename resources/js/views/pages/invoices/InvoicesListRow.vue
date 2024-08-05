<script setup>
import { ref, defineProps} from 'vue'
import moment from 'moment';

const props = defineProps({
  invoice: {
    type: Object,
    required: true,
  },
});

const user = computed(() => baseService.getUserFromLocalStorage());
const invoice = ref(props.invoice);
const actionItems = [
  { title: 'Download Receipt' },
];

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
        {{ moment(invoice.invoice_date).format('MMM DD, YYYY') }}
      </p>
    </td>

    <td>
      <p>
        {{ moment(invoice.invoice_due).format('MMM DD, YYYY') }}
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

<!--      <v-menu>-->

<!--        <template v-slot:activator="{ props }">-->
<!--          <v-btn-toggle-->
<!--            density="compact"-->
<!--            class="pa-0 pl-0 pr-0 h-auto rounded-1"-->
<!--          >-->

<!--            <router-link :rounded="0" :to="{ name: 'InvoicesEdit', params: { hash: invoice.hash } }" >-->
<!--              &lt;!&ndash; <v-btn class="ma-0 rounded-0 pl-3">View</v-btn> &ndash;&gt;-->
<!--              <VBtn class="px-8" variant="tonal" :rounded="0"> View </VBtn>-->
<!--            </router-link>-->

<!--            <v-btn variant="tonal" class="btn-tableDropDownAction text-primary" v-bind="props" >-->
<!--              <VIcon icon="tabler-dots-vertical"/>-->
<!--            </v-btn>-->

<!--          </v-btn-toggle>-->
<!--        </template>-->

<!--        <v-list class="border-label-info">-->
<!--          <v-list-item >-->
<!--            <a href="" @click.prevent="downloadInvoice">-->
<!--              <v-list-item-title>Invoice pdf</v-list-item-title>-->
<!--            </a>-->
<!--          </v-list-item>-->
<!--        </v-list>-->

<!--      </v-menu>-->

      <v-menu v-if="user.role === 'admin'">

        <template v-slot:activator="{ props }">
          <v-btn-toggle
            density="compact"
            class="pa-0 pl-0 pr-0 h-auto rounded-1"
          >
            <router-link :rounded="0" :to="{ name: 'InvoicesEdit', params: { hash: invoice.hash } }" >
              <VBtn class="px-8" variant="tonal" :rounded="0"> View </VBtn>
            </router-link>

            <v-btn variant="tonal" class="btn-tableDropDownAction text-primary" v-bind="props" >
              <VIcon icon="tabler-dots-vertical"/>
            </v-btn>
          </v-btn-toggle>
        </template>

        <v-list class="border-label-info">
          <v-list-item >
            <a :href="`/view-invoice/${invoice.hash}`" target="_blank">
              <v-list-item-title>Invoice PDF</v-list-item-title>
            </a>
          </v-list-item>
        </v-list>

      </v-menu>

      <v-menu v-else>
        <template v-slot:activator="{ props }">
          <a class="btn btn-warning waves-effect waves-light" :href="`/view-invoice/${invoice.hash}`" target="_blank">
            <VBtn class="mt-2 mb-2 w-100" variant="tonal" > View </VBtn>
          </a>
        </template>
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
