/**
 * Helper fungsi logika aplikasi untuk manajemen data tugas (Task).
 */

/**
 * Format status task menjadi label teks deskriptif
 * @param {boolean} isCompleted
 * @returns {string}
 */
export function formatStatus(isCompleted) {
  return isCompleted ? '✅ Selesai' : '⏳ Belum Selesai'
}

/**
 * Menghitung jumlah tugas yang belum selesai
 * @param {Array<{is_completed: boolean}>} tasks
 * @returns {number}
 */
export function countPendingTasks(tasks) {
  if (!Array.isArray(tasks)) return 0
  return tasks.filter((t) => !t.is_completed).length
}

/**
 * Memfilter tugas berdasarkan status selesai
 * @param {Array<{is_completed: boolean}>} tasks
 * @returns {Array}
 */
export function filterCompletedTasks(tasks) {
  if (!Array.isArray(tasks)) return []
  return tasks.filter((t) => t.is_completed)
}
