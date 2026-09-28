<script setup>
import { ref, onMounted } from 'vue'
import { formatStatus } from '../utils/taskHelper'

const apiUrl = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api'
const tasks = ref([])
const loading = ref(true)
const error = ref(null)

const fetchTasks = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await fetch(`${apiUrl}/tasks`)
    if (!response.ok) {
      throw new Error(`HTTP Error status: ${response.status}`)
    }
    const result = await response.json()
    // Laravel returns { status, message, data: [...] }
    tasks.value = result.data || []
  } catch (err) {
    error.value = `Gagal memuat data dari Laravel (${err.message}). Pastikan server backend 'php artisan serve' sedang aktif di port 8000.`
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchTasks()
})
</script>

<template>
  <div class="tasks-page">
    <div class="header-card">
      <h1>📋 Daftar Tugas (Data dari Laravel)</h1>
      <p class="subtitle">
        Data ini diambil secara asinkron dari API backend Laravel melalui URL environment:
        <code>{{ apiUrl }}/tasks</code>
      </p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="state-card loading">
      <div class="spinner"></div>
      <p>Sedang mengambil data dari backend Laravel...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="state-card error">
      <div class="error-icon">⚠️</div>
      <h3>Koneksi Backend Bermasalah</h3>
      <p>{{ error }}</p>
      <button class="btn-retry" @click="fetchTasks">Coba Muat Ulang</button>
    </div>

    <!-- Success State -->
    <div v-else class="content-container">
      <div class="stats-row">
        <span class="badge-total">Total Tugas: {{ tasks.length }}</span>
        <button class="btn-refresh" @click="fetchTasks">🔄 Segarkan Data</button>
      </div>

      <div v-if="tasks.length === 0" class="state-card empty">
        <p>Belum ada data tugas yang tersedia di database Laravel.</p>
      </div>

      <div v-else class="table-responsive">
        <table class="tasks-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Judul Tugas</th>
              <th>Deskripsi</th>
              <th>Status</th>
              <th>Waktu Dibuat</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="task in tasks" :key="task.id">
              <td class="col-id">#{{ task.id }}</td>
              <td class="col-title">
                <strong>{{ task.title }}</strong>
              </td>
              <td class="col-desc">{{ task.description || '-' }}</td>
              <td class="col-status">
                <span :class="['status-badge', task.is_completed ? 'completed' : 'pending']">
                  {{ formatStatus(task.is_completed) }}
                </span>
              </td>
              <td class="col-date">
                {{ new Date(task.created_at).toLocaleString('id-ID') }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
.tasks-page {
  max-width: 900px;
  margin: 0 auto;
  padding: 1.5rem;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.header-card {
  background: linear-gradient(135deg, #42b883 0%, #35495e 100%);
  color: white;
  padding: 1.8rem;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.header-card h1 {
  margin: 0 0 0.5rem 0;
  font-size: 1.8rem;
}

.subtitle {
  margin: 0;
  font-size: 0.95rem;
  opacity: 0.9;
}

.subtitle code {
  background: rgba(255, 255, 255, 0.2);
  padding: 0.2rem 0.4rem;
  border-radius: 4px;
  font-weight: 600;
}

.state-card {
  padding: 2.5rem;
  text-align: center;
  border-radius: 12px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}

.state-card.error {
  background: #fff5f5;
  border-color: #feb2b2;
  color: #c53030;
}

.error-icon {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.btn-retry,
.btn-refresh {
  background: #42b883;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.2s;
}

.btn-retry:hover,
.btn-refresh:hover {
  background: #33a06f;
}

.stats-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.badge-total {
  background: #e2e8f0;
  padding: 0.3rem 0.8rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.9rem;
  color: #475569;
}

.table-responsive {
  overflow-x: auto;
  background: white;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.tasks-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.tasks-table th {
  background: #f1f5f9;
  padding: 0.9rem 1rem;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #475569;
  border-bottom: 2px solid #e2e8f0;
}

.tasks-table td {
  padding: 1rem;
  border-bottom: 1px solid #f1f5f9;
  font-size: 0.95rem;
  color: #334155;
}

.tasks-table tr:hover {
  background-color: #f8fafc;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.6rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
}

.status-badge.completed {
  background: #def7ec;
  color: #03543f;
}

.status-badge.pending {
  background: #fef08a;
  color: #854d0e;
}
</style>
