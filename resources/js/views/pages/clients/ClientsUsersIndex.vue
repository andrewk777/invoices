<script setup>
import { ref } from 'vue'
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";

const props = defineProps({
    users: {
      type: Array,
      required: true,
    },
});

const users = ref(props.users);
const search = ref('');

// headers
const headers = [
  {
    title: 'FIRST NAME',
    key: 'first_name',
  },
  {
    title: 'LAST NAME',
    key: 'last_name',
  },
  {
    title: 'EMAIL',
    key: 'email',
  },
  {
    title: 'LAST LOGIN',
    key: 'last_login',
  },
  {
    title: 'ACCESS',
    key: 'system_access',
  },
  {
    title: 'ACTION',
    key: 'delete',
    sortable: false,
  },
]

const deleteItem = itemId => {
  if (!users.value)
    return
  const index = users.value.findIndex(item => item.id === itemId)

  users.value.splice(index, 1)
}

// const categoryIcons = [
//   {
//     name: 'Mouse',
//     icon: 'tabler-mouse',
//     color: 'warning',
//   },
//   {
//     name: 'Glass',
//     icon: 'tabler-eyeglass',
//     color: 'primary',
//   },
//   {
//     name: 'Smart Watch',
//     icon: 'tabler-device-watch',
//     color: 'success',
//   },
//   {
//     name: 'Bag',
//     icon: 'tabler-briefcase',
//     color: 'info',
//   },
//   {
//     name: 'Storage Device',
//     icon: 'tabler-device-audio-tape',
//     color: 'warning',
//   },
//   {
//     name: 'Bluetooth',
//     icon: 'tabler-bluetooth',
//     color: 'error',
//   },
//   {
//     name: 'Gaming',
//     icon: 'tabler-device-gamepad-2',
//     color: 'warning',
//   },
//   {
//     name: 'Home',
//     icon: 'tabler-home',
//     color: 'error',
//   },
//   {
//     name: 'VR',
//     icon: 'tabler-badge-vr',
//     color: 'primary',
//   },
//   {
//     name: 'Shoes',
//     icon: 'tabler-shoe',
//     color: 'success',
//   },
//   {
//     name: 'Electronics',
//     icon: 'tabler-cpu',
//     color: 'info',
//   },
//   {
//     name: 'Projector',
//     icon: 'tabler-theater',
//     color: 'warning',
//   },
//   {
//     name: 'iPod',
//     icon: 'tabler-device-airpods',
//     color: 'error',
//   },
//   {
//     name: 'Keyboard',
//     icon: 'tabler-keyboard',
//     color: 'primary',
//   },
//   {
//     name: 'Smart Phone',
//     icon: 'tabler-device-mobile',
//     color: 'success',
//   },
//   {
//     name: 'Smart TV',
//     icon: 'tabler-device-tv',
//     color: 'info',
//   },
//   {
//     name: 'Google Home',
//     icon: 'tabler-brand-google',
//     color: 'warning',
//   },
//   {
//     name: 'Mac',
//     icon: 'tabler-brand-apple',
//     color: 'error',
//   },
//   {
//     name: 'Headphone',
//     icon: 'tabler-headphones',
//     color: 'primary',
//   },
//   {
//     name: 'iMac',
//     icon: 'tabler-device-imac',
//     color: 'success',
//   },
//   {
//     name: 'iPhone',
//     icon: 'tabler-brand-apple',
//     color: 'warning',
//   },
// ]
//
// const resolveStatusColor = status => {
//   if (status === 'Confirmed')
//     return 'primary'
//   if (status === 'Completed')
//     return 'success'
//   if (status === 'Cancelled')
//     return 'error'
// }

// const categoryIconFilter = categoryName => {
//   const index = categoryIcons.findIndex(category => category.name === categoryName)
//   if (index !== -1)
//     return [{
//       icon: categoryIcons[index].icon,
//       color: categoryIcons[index].color,
//     }]
//
//   return [{
//     icon: 'tabler-help-circle',
//     color: 'primary',
//   }]
// }

</script>

<template>
  <div>
    <VCardText>
      <h3>Users</h3>
      <VRow>
        <VCol
          cols="12"
          offset-md="8"
          md="4"
        >
<!--          <AppTextField-->
<!--            v-model="search"-->
<!--            placeholder="Search ..."-->
<!--            append-inner-icon="tabler-search"-->
<!--            single-line-->
<!--            hide-details-->
<!--            dense-->
<!--            outlined-->
<!--          />-->
        </VCol>
      </VRow>
    </VCardText>

    <!-- 👉 Data Table  -->
    <VDataTable
      :headers="headers"
      :items="users || []"
      :search="search"
      :items-per-page="5"
      class="text-no-wrap"
    >

      <template #item.first_name="{ item }">
        <div class="d-flex align-center">
          <div class="d-flex flex-column ms-3">
            <span class="d-block font-weight-medium text-truncate text-high-emphasis">{{ item.first_name }}</span>
          </div>
        </div>
      </template>

      <template #item.last_name="{ item }">
        <div class="d-flex align-center">
          <div class="d-flex flex-column ms-3">
            <span class="d-block font-weight-medium text-truncate text-high-emphasis">{{ item.last_name }}</span>
          </div>
        </div>
      </template>

      <template #item.email="{ item }">
        <div class="d-flex align-center">
          <div class="d-flex flex-column ms-3">
            <span class="d-block font-weight-medium text-truncate text-high-emphasis">{{ item.email }}</span>
          </div>
        </div>
      </template>

      <template #item.last_login="{ item }">
        <div class="d-flex align-center">
          <div class="d-flex flex-column ms-3">
            <span class="d-block font-weight-medium text-truncate text-high-emphasis">{{ item.last_login }}</span>
          </div>
        </div>
      </template>

      <!-- Status -->
      <template #item.system_access="{ item }">
        <div class="d-flex align-center">
          <div class="d-flex flex-column ms-3">
            <span class="d-block font-weight-medium text-truncate text-high-emphasis">
              {{ item.system_access ? 'Yes' : 'No' }}
            </span>
          </div>
        </div>
      </template>

      <!-- Delete -->
      <template #item.delete="{ item }">
        <IconBtn @click="deleteItem(item.id)">
          <VIcon icon="tabler-trash" />
        </IconBtn>
      </template>
    </VDataTable>
  </div>
</template>
