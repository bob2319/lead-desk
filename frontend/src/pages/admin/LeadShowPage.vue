<script setup>
import { computed, onMounted, reactive, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useLeadsStore } from '../../stores/leads'

const route = useRoute()
const leads = useLeadsStore()

const form = reactive({
  status: 'new',
  notes: '',
})

const statuses = ['new', 'in_progress', 'won', 'lost']
const saved = computed(() => !leads.loading && !!leads.current)

async function load() {
  const lead = await leads.fetchLead(route.params.id)
  form.status = lead.status
  form.notes = lead.notes || ''
}

async function onSave() {
  await leads.updateLead(route.params.id, {
    status: form.status,
    notes: form.notes,
  })
}

onMounted(load)
watch(() => route.params.id, load)
</script>

<template>
  <section class="page page--narrow">
    <RouterLink class="back-link" :to="{ name: 'admin.leads' }">← Back to leads</RouterLink>

    <p v-if="leads.loading && !leads.current" class="muted">Loading…</p>
    <p v-else-if="leads.error && !leads.current" class="form__error">{{ leads.error }}</p>

    <template v-else-if="leads.current">
      <h1 class="page__title">{{ leads.current.name }}</h1>
      <p class="page__lead">{{ leads.current.email }} · {{ leads.current.phone || 'no phone' }}</p>

      <dl class="details">
        <div>
          <dt>Company</dt>
          <dd>{{ leads.current.company || '—' }}</dd>
        </div>
        <div>
          <dt>Message</dt>
          <dd>{{ leads.current.message || '—' }}</dd>
        </div>
        <div>
          <dt>Created</dt>
          <dd>{{ new Date(leads.current.created_at).toLocaleString() }}</dd>
        </div>
      </dl>

      <form class="form" @submit.prevent="onSave">
        <label class="form__field">
          <span>Status</span>
          <select v-model="form.status">
            <option v-for="item in statuses" :key="item" :value="item">{{ item }}</option>
          </select>
        </label>

        <label class="form__field">
          <span>Notes</span>
          <textarea v-model="form.notes" rows="5" maxlength="5000" />
        </label>

        <p v-if="leads.error" class="form__error">{{ leads.error }}</p>

        <button class="button" type="submit" :disabled="leads.loading">
          {{ leads.loading ? 'Saving…' : 'Save changes' }}
        </button>
        <p v-if="saved && !leads.loading" class="muted">Last loaded / saved state is current.</p>
      </form>
    </template>
  </section>
</template>
