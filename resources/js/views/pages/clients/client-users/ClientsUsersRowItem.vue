<script setup>
import { ref } from 'vue';
import ClientsUsersFormEdit from "@/views/pages/clients/client-users/ClientsUsersFormEdit.vue";

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
  token: {
    type: String,
    required: true,
  },
  client: {
    type: Object,
    required: true,
  },
  updatedClient: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['clientUserAccess', 'updateClientUser']);

const loading = ref(false);
const editDialogueVisible = ref(false);
const item = ref(props.item);

const clientUserAccess = () => {
  emit('clientUserAccess', props.item.hash);
};

const updateClientUser = (clientUser) => {
  item.value = clientUser;
};
</script>

<template>
  <tr>
    <td>
      <div class="d-flex align-center">
        <div class="d-flex flex-column ms-3">
          <span class="d-block font-weight-medium text-truncate text-high-emphasis">{{ item.name }}</span>
        </div>
      </div>
    </td>
    <td>
      <div class="d-flex align-center">
        <div class="d-flex flex-column ms-3">
          <span class="d-block font-weight-medium text-truncate text-high-emphasis">{{ item.email }}</span>
        </div>
      </div>
    </td>
    <td>
      <div class="d-flex align-center">
        <div class="d-flex flex-column ms-3">
          <span class="d-block font-weight-medium text-truncate text-high-emphasis">{{ item.last_login }}</span>
        </div>
      </div>
    </td>
    <td>
      <div class="d-flex align-center">
        <div class="d-flex flex-column ms-3">
          <span class="d-block font-weight-medium text-truncate text-high-emphasis">
            {{ item.system_access ? 'Yes' : 'No' }}
          </span>
        </div>
      </div>
    </td>

    <td>
      <v-menu>

        <template v-slot:activator="{ props }">
          <v-btn-toggle
            variant="text"
            density="compact"
            class="pa-0 h-auto rounded-1"
          >

            <ClientsUsersFormEdit
              :editDialogueVisible="editDialogueVisible"
              :client="client"
              :token="token"
              :updatedClient="updatedClient"
              :clientUser="item"
              @close-modal="editDialogueVisible = false"
              @close-edit-dialogue="editDialogueVisible = false"
              @edit-client-user="updateClientUser"
            />

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
          <v-list-item>

            <ClientsUsersFormEdit
              :editDialogueVisible="editDialogueVisible"
              :client="client"
              :token="token"
              :updatedClient="updatedClient"
              :clientUser="item"
              @close-modal="editDialogueVisible = false"
              @close-edit-dialogue="editDialogueVisible = false"
              @edit-client-user="updateClientUser"
            />

          </v-list-item>
        </v-list>

      </v-menu>


    </td>

  </tr>
</template>
