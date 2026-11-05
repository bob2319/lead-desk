<script setup>
import { onMounted, ref, watch } from 'vue'
import { useLeadsStore } from '../../stores/leads'

const leads = useLeadsStore()
const status = ref('')
const search = ref('')
const page = ref(1)

const statuses = [
  { value: '', label: 'All statuses' },
  { value: 'new', label: 'New' },
  { value: 'in_progress', label: 'In progress' },
  { value: 'won', label: 'Won' },
  { value: 'lost', label: 'Lost' },
]

async function load() {
  await leads.fetchLeads({
    status: status.value,
    search: search.value,
    page: page.value,
  })
}

function statusClass(value) {
  return `badge badge--${value}`
}

function formatDate(value) {
  if (!value) return '—'
  return new Date(value).toLocaleString()
}

onMounted(load)

watch([status, search], () => {
  page.value = 1
  load()
})
</script>

<template>
  <section class="page">
    <div class="page__toolbar">
      <div>
        <h1 class="page__title">Leads</h1>
        <p class="page__lead">Pipeline for incoming requests.</p>
      </div>
    </div>

    <div class="filters">
      <label class="filters__field">
        <span>Status</span>
        <select v-model="status">
          <option v-for="item in statuses" :key="item.value" :value="item.value">
            {{ item.label }}
          </option>
        </select>
      </label>

      <label class="filters__field filters__field--grow">
        <span>Search</span>
        <input
          v-model="search"
          type="search"
          placeholder="Name, email, company, phone"
        />
      </label>
    </div>

    <p v-if="leads.loading" class="muted">Loading…</p>
    <p v-else-if="leads.error" class="form__error">{{ leads.error }}</p>
    <p v-else-if="!leads.items.length" class="muted">No leads found.</p>

    <div v-else class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Company</th>
            <th>Status</th>
            <th>Created</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="lead in leads.items" :key="lead.id">
            <td>{{ lead.name }}</td>
            <td>{{ lead.email }}</td>
            <td>{{ lead.company || '—' }}</td>
            <td>
              <span :class="statusClass(lead.status)">{{ lead.status }}</span>
            </td>
            <td>{{ formatDate(lead.created_at) }}</td>
            <td>
              <RouterLink
                class="table__link"
                :to="{ name: 'admin.leads.show', params: { id: lead.id } }"
              >
                Open
              </RouterLink>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="leads.meta && leads.meta.last_page > 1" class="pager">
      <button
        class="button button--ghost"
        type="button"
        :disabled="page <= 1 || leads.loading"
        @click="page -= 1; load()"
      >
        Previous
      </button>
      <span class="muted">Page {{ leads.meta.current_page }} / {{ leads.meta.last_page }}</span>
      <button
        class="button button--ghost"
        type="button"
        :disabled="page >= leads.meta.last_page || leads.loading"
        @click="page += 1; load()"
      >
        Next
      </button>
    </div>
  </section>
</template>
