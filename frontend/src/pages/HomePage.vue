<script setup>
import { reactive, ref } from 'vue'
import { useLeadsStore } from '../stores/leads'

const leads = useLeadsStore()
const submitted = ref(false)

const form = reactive({
  name: '',
  email: '',
  phone: '',
  company: '',
  message: '',
})

async function onSubmit() {
  submitted.value = false
  try {
    await leads.submitLead({ ...form })
    submitted.value = true
    form.name = ''
    form.email = ''
    form.phone = ''
    form.company = ''
    form.message = ''
  } catch {
    // errors are in the store
  }
}
</script>

<template>
  <section class="page page--narrow">
    <h1 class="page__title">Get in touch</h1>
    <p class="page__lead">
      Leave your details and a manager will follow up. This is the public lead capture form.
    </p>

    <div v-if="submitted" class="alert alert--success">
      Thanks! Your request was received.
    </div>

    <form class="form" @submit.prevent="onSubmit">
      <label class="form__field">
        <span>Name *</span>
        <input v-model="form.name" type="text" required maxlength="255" />
        <small v-if="leads.formErrors.name" class="form__error">
          {{ leads.formErrors.name[0] }}
        </small>
      </label>

      <label class="form__field">
        <span>Email *</span>
        <input v-model="form.email" type="email" required maxlength="255" />
        <small v-if="leads.formErrors.email" class="form__error">
          {{ leads.formErrors.email[0] }}
        </small>
      </label>

      <label class="form__field">
        <span>Phone</span>
        <input v-model="form.phone" type="text" maxlength="50" />
        <small v-if="leads.formErrors.phone" class="form__error">
          {{ leads.formErrors.phone[0] }}
        </small>
      </label>

      <label class="form__field">
        <span>Company</span>
        <input v-model="form.company" type="text" maxlength="255" />
      </label>

      <label class="form__field">
        <span>Message</span>
        <textarea v-model="form.message" rows="4" maxlength="5000" />
      </label>

      <p v-if="leads.error && !Object.keys(leads.formErrors).length" class="form__error">
        {{ leads.error }}
      </p>

      <button class="button" type="submit" :disabled="leads.loading">
        {{ leads.loading ? 'Sending…' : 'Send request' }}
      </button>
    </form>
  </section>
</template>
