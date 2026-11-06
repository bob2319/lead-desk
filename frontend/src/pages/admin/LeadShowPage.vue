<script setup>
import { computed, onMounted, reactive, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useLeadsStore } from '../../stores/leads'

const route = useRoute()
const router = useRouter()
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


async function changeStatus(status) {
  await leads.updateLead(route.params.id, {
    status: status,
    notes: form.notes,
  })
  form.status = status
}


async function btnDeleteLead() {
  if( !confirm('Delete this lead?')){
    return
  }

  await leads.deleteLead(route.params.id)
  router.push({ name: 'admin.leads' })
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
        <button type="button" class="button-won-status" 
        :class="{'button-won-status--activate': form.status === 'won'}" :disabled="leads.loading" 
        @click="changeStatus('won')">
        {{ leads.loading ? 'Saving…' : 'Mark as won' }}
        </button>

        <button type="button" class="button-lost-status" 
        :class="{'button-lost-status--activate': form.status === 'lost'}" :disabled="leads.loading" 
        @click="changeStatus('lost')">
        {{ leads.loading ? 'Saving…' : 'Mark as lost' }}
        </button>
        <button type="button" class="button-delete-leads" 
        :disabled="leads.loading" 
        @click="btnDeleteLead()">
        {{ leads.loading ? 'Deleting…' : 'Delete lead' }}
        </button>
      </form>
    </template>
  </section>
</template>
