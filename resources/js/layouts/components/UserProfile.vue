<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import avatar1 from '@images/avatars/avatar-1.png';
import {apiClient} from "@/utils/apiClient.js";

const loading = ref(false);
const user = baseService.getUserFromLocalStorage();

const logout = async () => {
  loading.value = true;
  try {
    const response = await apiClient.get('/logout');
    if(response.data.success){
      localStorage.removeItem('invoice-client-token');
    }

    // Redirect to the login page
    window.location.href = '/login';

  } catch (error) {
    console.error(error);
  }
  loading.value = false;
};

onMounted(() => {

});

</script>

<template>
  <VBadge
    dot
    location="bottom right"
    offset-x="3"
    offset-y="3"
    bordered
    color="success"
  >
    <VAvatar
      class="cursor-pointer"
      color="primary"
      variant="tonal"
    >
      <VIcon icon="tabler-user-circle" size="30"/>

      <!-- SECTION Menu -->
      <VMenu
        activator="parent"
        width="230"
        location="bottom end"
        offset="14px"
      >
        <VList>
          <!-- 👉 User Avatar & Name -->
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VIcon icon="tabler-user-circle" size="30"/>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{ user.name }}
            </VListItemTitle>
            <VListItemSubtitle>{{ user.email }}</VListItemSubtitle>
          </VListItem>

          <VDivider class="my-2" />

          <!-- 👉 Logout -->
          <VListItem @click.prevent="logout">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="tabler-logout"
                size="22"
              />
            </template>

            <VListItemTitle>Logout</VListItemTitle>
          </VListItem>

        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>
