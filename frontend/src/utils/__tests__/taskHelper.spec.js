import { describe, it, expect } from 'vitest'
import { formatStatus, countPendingTasks, filterCompletedTasks } from '../taskHelper'

describe('Task Helper Logic Tests', () => {
  describe('formatStatus', () => {
    it('mengembalikan status "✅ Selesai" jika isCompleted bernilai true', () => {
      // SENGAJA DIGAGALKAN UNTUK PEMBUKTIAN QUALITY GATE CI/CD:
      expect(formatStatus(true)).toBe('❌ STATUS_SENGAJA_DISALAHKAN_UNTUK_PENGUJIAN_CI')
    })

    it('mengembalikan status "⏳ Belum Selesai" jika isCompleted bernilai false', () => {
      expect(formatStatus(false)).toBe('⏳ Belum Selesai')
    })
  })

  describe('countPendingTasks', () => {
    it('menghitung jumlah task yang belum selesai dengan benar', () => {
      const dummyTasks = [
        { id: 1, title: 'Tugas A', is_completed: false },
        { id: 2, title: 'Tugas B', is_completed: true },
        { id: 3, title: 'Tugas C', is_completed: false },
      ]
      expect(countPendingTasks(dummyTasks)).toBe(2)
    })

    it('mengembalikan 0 jika array tugas kosong atau invalid', () => {
      expect(countPendingTasks([])).toBe(0)
      expect(countPendingTasks(null)).toBe(0)
    })
  })

  describe('filterCompletedTasks', () => {
    it('memfilter hanya tugas yang berstatus is_completed: true', () => {
      const dummyTasks = [
        { id: 1, title: 'Tugas 1', is_completed: true },
        { id: 2, title: 'Tugas 2', is_completed: false },
      ]
      const completed = filterCompletedTasks(dummyTasks)
      expect(completed).toHaveLength(1)
      expect(completed[0].title).toBe('Tugas 1')
    })
  })
})
