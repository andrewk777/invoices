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
const isDialogVisible = ref(false);
const deleted = ref(false);
const loading = ref(false);
const errors = ref({});

const chargeCreditCard = () => {

}

const deleteSubscription = async () => {
  loading.value = true;

  try {
    let response;
    response = await axios.delete('/api/subscriptions/delete/' + subscription.value.hash, {
      headers: {
        'Accept': 'application/json',
        "Authorization": "Bearer " + token.value,
      }
    });

    if (response.data.success){
      deleted.value = true;
      isDialogVisible.value = false;
    }

  } catch (error) {
    if (error.response) {
      if (Object.keys(error.response?.data?.errors).length > 0) {
        errors.value = error.response?.data?.errors;
        if (import.meta.env.VITE_APP_ENV === 'local') {
          console.log("Validation errors", errors.value);
        }
      }

      if (error.response?.data?.server_error) {
        errors.value.server_error = 'Server error. Please try again later or contact your admin.';
      }
    }

    console.log(error);
  }

  loading.value = false;
}

</script>

<template>
  <tr v-if="!deleted">

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
          color="success"
          icon="tabler-credit-card"
        />
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
              :to="{ name: 'SubscriptionsEdit', params: { hash: subscription.hash } }"
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
            <VDialog
              v-model="isDialogVisible"
              persistent
              class="v-dialog-sm"
            >
              <!-- Dialog Activator -->
              <template #activator="{ props }">
                <v-list-item-content>
                  <v-list-item-title v-bind="props">
                    Delete
                  </v-list-item-title>
                </v-list-item-content>
              </template>

              <!-- Dialog close btn -->
              <DialogCloseBtn @click="isDialogVisible = !isDialogVisible" />

              <!-- Dialog Content -->
              <VCard title="Use Google's location service?">
                <VCardText>
                  Delete {{ subscription.name }} ?
                </VCardText>

                <VCardText class="d-flex justify-end gap-3 flex-wrap">
                  <VBtn
                    color="secondary"
                    variant="tonal"
                    @click="isDialogVisible = false"
                  >
                    Disagree
                  </VBtn>
                  <VBtn
                    @click="deleteSubscription()"
                    :loading="loading"
                  >
                    Agree
                  </VBtn>
                </VCardText>
              </VCard>
            </VDialog>
          </v-list-item>
        </v-list>

      </v-menu>

    </td>

  </tr>
</template>

<style scoped lang="scss">

</style>
