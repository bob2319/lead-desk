<script setup>
import { reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()
const localError = ref('')

const form = reactive({
  email: 'manager@leaddesk.test',
  password: 'password',
})

async function onSubmit() {
  localError.value = ''
  try {
    await auth.login(form.email, form.password)
    router.push(route.query.redirect || { name: 'admin.leads' })
  } catch {
    localError.value = auth.error || 'Login failed'
  }
}
</script>

<template>
  <section class="page page--narrow">
    <h1 class="page__title">Manager login</h1>
    <p class="page__lead">
      Demo: <code>manager@leaddesk.test</code> / <code>password</code>
    </p>

    <form class="form" @submit.prevent="onSubmit">
      <label class="form__field">
        <span>Email</span>
        <input v-model="form.email" type="email" required />
      </label>

      <label class="form__field">
        <span>Password</span>
        <input v-model="form.password" type="password" required />
      </label>

      <p v-if="localError" class="form__error">{{ localError }}</p>

      <button class="button" type="submit" :disabled="auth.loading">
        {{ auth.loading ? 'Signing in…' : 'Sign in' }}
      </button>
    </form>
  </section>
</template>
