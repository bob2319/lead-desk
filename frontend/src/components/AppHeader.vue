<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()

async function onLogout() {
  await auth.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <header class="header">
    <RouterLink class="header__brand" :to="{ name: 'home' }">Lead Desk</RouterLink>

    <nav class="header__nav">
      <RouterLink class="header__link" :to="{ name: 'home' }">Public form</RouterLink>
      <RouterLink
        v-if="auth.isAuthenticated"
        class="header__link"
        :to="{ name: 'admin.leads' }"
      >
        Admin
      </RouterLink>
      <RouterLink
        v-if="!auth.isAuthenticated"
        class="header__link"
        :to="{ name: 'login' }"
      >
        Login
      </RouterLink>
      <button
        v-else
        class="header__button"
        type="button"
        @click="onLogout"
      >
        Logout
      </button>
    </nav>
  </header>
</template>
