<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import avatar1 from '@images/avatars/avatar-1.png';
import axios from "axios";

const user = computed(() => baseService.getUserFromLocalStorage());
const loading = ref(false);

const logout = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/logout',{
      headers: {
        'Authorization': 'Bearer ' + user.value.token
      },
    });
    if(response.data.success){
      localStorage.removeItem('invoice-client-token');
    }

    // Redirect to the login page
    window.location.href = '/login';

  } catch (error) {
    if (axios.isAxiosError(error)) {
      console.error('Logout failed:', error.response?.data.message);
    } else {
      console.error('Logout failed:', error);
    }
  }
  loading.value = false;
};

onMounted(() => {
  console.log("AUTH USER", user.value);
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
      <VImg :src="avatar1" />

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
                <VBadge
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="success"
                >
                  <VAvatar
                    color="primary"
                    variant="tonal"
                  >
                    <VImg :src="avatar1" />
                  </VAvatar>
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{ user.name }}
            </VListItemTitle>
            <VListItemSubtitle>Master Admin</VListItemSubtitle>
          </VListItem>

          <VDivider class="my-2" />

<!--          &lt;!&ndash; 👉 Settings &ndash;&gt;-->
<!--          <VListItem link>-->
<!--            <template #prepend>-->
<!--              <VIcon-->
<!--                class="me-2"-->
<!--                icon="tabler-settings"-->
<!--                size="22"-->
<!--              />-->
<!--            </template>-->

<!--            <VListItemTitle>Settings</VListItemTitle>-->
<!--          </VListItem>-->

<!--          &lt;!&ndash; Divider &ndash;&gt;-->
<!--          <VDivider class="my-2" />-->

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
