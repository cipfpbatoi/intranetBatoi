<template>
  <div class="space-y-4">
    <!-- Filtres -->
    <div class="controls" style="display:flex;flex-wrap:wrap;gap:8px;align-items:flex-end">
      <div>
        <label>Des de</label>
        <input type="date" v-model="desde" class="input">
      </div>
      <div>
        <label>Fins</label>
        <input type="date" v-model="hasta" class="input">
      </div>
      <div style="min-width:260px">
        <label>Professor/a</label>
        <select v-model="dni" class="input">
          <option value="">Tots</option>
          <option v-for="p in profes" :key="p.dni" :value="p.dni">
            {{ nomProf(p) }} ({{ p.dni }})
          </option>
        </select>
      </div>
      <div style="display:flex;gap:6px;align-items:center">
        <button @click="changeWeek(-1)" class="btn btn-light" title="Setmana anterior">← Setmana</button>
        <button @click="changeWeek(1)" class="btn btn-light" title="Setmana següent">Setmana →</button>
      </div>
      <label style="display:flex;align-items:center;gap:6px;margin-left:auto">
        <input type="checkbox" v-model="hideOk">
        Amaga els OK
      </label>
      <button @click="fetchData" class="btn">Actualitza</button>
    </div>
    <p v-if="loading" class="muted" style="margin:6px 0 0">Carregant dades...</p>
    <p v-if="msg" style="margin:6px 0 0;color:#b91c1c">{{ msg }}</p>

    <!-- Taula resum -->
    <div class="table-wrap" style="overflow:auto;border:1px solid #e5e7eb;border-radius:8px">
      <table class="min-w-full" style="width:100%;font-size:14px;border-collapse:separate;border-spacing:0">
        <thead style="background:#f9fafb;position:sticky;top:0;z-index:1">
          <tr>
            <th class="th w-56">Professor/a</th>
            <th class="th w-28">Dept.</th>
            <th v-for="d in daysList" :key="d" class="th">
              {{ formatDia(d) }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in filteredRows" :key="row.dni" class="tr">
            <td class="td">
              {{ nomRow(row) }}<div class="muted">{{ row.dni }}</div>
            </td>
            <td class="td">{{ row.departamento || '' }}</td>
            <td v-for="d in daysList" :key="d" class="td">
              <span
                v-if="row.days && row.days[d]"
                class="badge"
                :class="cellInfo(row.days[d]).class"
              >
                {{ cellInfo(row.days[d]).label }}
              </span>
              <span v-else class="badge bg-s">–</span>
            </td>
          </tr>
          <tr v-if="!filteredRows.length">
            <td class="td" :colspan="2 + daysList.length" style="text-align:center;color:#6b7280;padding:24px">
              Sense resultats.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <p class="muted" style="font-size:12px">
      * OK si la cobertura global (docent + no docent) està aproximadament entre el 90% i el 110%.
      Es mantenen avisos especials: Abs, No out (NO_SALIDA), Just, Act, Com, Off.
    </p>
  </div>
</template>

<script setup>
import axios from 'axios'
import { computed, onMounted, ref } from 'vue'
import { withApiAuth } from '../utils/api-auth'

defineOptions({
  name: 'ControlResumenRangoView'
})

const props = defineProps({
  profes: {
    type: Array,
    default: () => []
  }
})

const COLORS = {
  OK: 'bg-g',
  PARTIAL: 'bg-a',
  ABSENT: 'bg-r',
  JUSTIFIED: 'bg-y',
  ACTIVITY: 'bg-b',
  COMMISSION: 'bg-p',
  OFF: 'bg-s',
  NO_SALIDA: 'bg-r'
}

function formatIsoLocal(date) {
  const y = date.getFullYear()
  const m = String(date.getMonth() + 1).padStart(2, '0')
  const d = String(date.getDate()).padStart(2, '0')
  return `${y}-${m}-${d}`
}

function currentWeek() {
  const today = new Date()
  const day = today.getDay() || 7
  const mondayDate = new Date(today)
  mondayDate.setDate(today.getDate() - (day - 1))
  const fridayDate = new Date(mondayDate)
  fridayDate.setDate(mondayDate.getDate() + 4)
  return {
    monday: formatIsoLocal(mondayDate),
    friday: formatIsoLocal(fridayDate)
  }
}

function startOfWeek(date) {
  const d = new Date(date)
  const day = d.getDay() || 7
  d.setDate(d.getDate() - (day - 1))
  return d
}

function nomProf(p) {
  return [p.apellido1, p.apellido2, p.nombre].filter(Boolean).join(' ')
}

function nomRow(r) {
  return [r.apellido1, r.apellido2, r.nombre].filter(Boolean).join(' ')
}

function formatDia(s) {
  const d = new Date(s + 'T00:00:00')
  const wd = d.toLocaleDateString('ca-ES', { weekday: 'short' })
  const dm = d.toLocaleDateString('ca-ES', { day: '2-digit', month: '2-digit' })
  return `${wd} ${dm}`
}

function cellInfo(day) {
  const status = day.status || ''

  if (status === 'NO_SALIDA') {
    let label = 'No out'
    if (day.first_entry) {
      const hm = day.first_entry.slice(0, 5)
      label = `${label} (${hm})`
    }
    return { label, class: COLORS.NO_SALIDA }
  }

  if (status === 'ABSENT') {
    return { label: 'Abs', class: COLORS.ABSENT }
  }

  if (status === 'JUSTIFIED') {
    return { label: 'Just', class: COLORS.JUSTIFIED }
  }

  if (status === 'ACTIVITY') {
    return { label: 'Act', class: COLORS.ACTIVITY }
  }

  if (status === 'COMMISSION') {
    return { label: 'Com', class: COLORS.COMMISSION }
  }

  if (status === 'OFF') {
    return { label: 'Off', class: COLORS.OFF }
  }

  const plannedDoc = day.planned_docencia_minutes || 0
  const plannedAlt = day.planned_altres_minutes || 0
  const coveredDoc = day.covered_docencia_minutes || 0
  const coveredAlt = day.covered_altres_minutes || 0
  const inCenter = day.in_center_minutes || 0

  const plannedTotal = plannedDoc + plannedAlt
  if (!plannedTotal) {
    return { label: '—', class: COLORS.OFF }
  }

  const percent = Math.round((inCenter * 100) / plannedTotal)
  const missingDoc = coveredDoc < plannedDoc
  const missingAlt = coveredAlt < plannedAlt

  if (percent < 90) {
    if (missingDoc) {
      return { label: `${percent}%`, class: COLORS.ABSENT }
    }
    if (missingAlt) {
      return { label: `${percent}%`, class: COLORS.PARTIAL }
    }
    return { label: `${percent}%`, class: COLORS.PARTIAL }
  }

  if (percent >= 90 && percent <= 110) {
    return { label: 'OK', class: COLORS.OK }
  }

  return { label: `${percent}%`, class: COLORS.OK }
}

const { monday, friday } = currentWeek()

const desde = ref(monday)
const hasta = ref(friday)
const dni = ref('')
const hideOk = ref(false)
const rows = ref([])
const msg = ref('')
const loading = ref(false)

const daysList = computed(() => {
  const a = new Date(desde.value)
  const b = new Date(hasta.value)
  const out = []
  if (isNaN(a) || isNaN(b)) return out
  const d = new Date(a)
  while (d <= b) {
    out.push(formatIsoLocal(d))
    d.setDate(d.getDate() + 1)
  }
  return out
})

function isRowFullyOk(row) {
  if (!row.days) return false
  const days = daysList.value
  if (!days.length) return false
  return days.every(d => {
    const info = row.days[d]
    if (!info) return false
    return cellInfo(info).class === 'bg-g'
  })
}

const filteredRows = computed(() => {
  let out = rows.value
  if (dni.value) {
    out = out.filter(r => r.dni === dni.value)
  }
  if (hideOk.value) {
    out = out.filter(r => !isRowFullyOk(r))
  }
  return out
})

async function fetchData() {
  loading.value = true
  msg.value = ''
  try {
    const resp = await axios.get(
      '/api/presencia/resumen-rango',
      withApiAuth({
        timeout: 20000,
        params: {
          desde: desde.value,
          hasta: hasta.value,
          ...(dni.value ? { dni: dni.value } : {}),
        },
      })
    )

    if (Array.isArray(resp.data)) {
      rows.value = resp.data
      return
    }

    if (resp?.data?.success && Array.isArray(resp.data.data)) {
      rows.value = resp.data.data
      return
    }

    rows.value = []
    msg.value = 'Resposta de servidor no vàlida.'
  } catch (error) {
    rows.value = []
    const status = error?.response?.status
    const detail = error?.response?.data?.message || error?.message || 'Error desconegut'
    msg.value = `Error carregant dades${status ? ` (${status})` : ''}: ${detail}`
  } finally {
    loading.value = false
  }
}

function changeWeek(delta) {
  const base = startOfWeek(new Date(desde.value))
  base.setDate(base.getDate() + delta * 7)
  const mondayDate = base
  const fridayDate = new Date(mondayDate)
  fridayDate.setDate(mondayDate.getDate() + 4)
  desde.value = formatIsoLocal(mondayDate)
  hasta.value = formatIsoLocal(fridayDate)
  fetchData()
}

onMounted(() => {
  fetchData()
})
</script>

<style scoped>
.input { border:1px solid #e5e7eb;border-radius:6px;padding:6px 10px }
.btn { background:#2563eb;color:#fff;border:0;border-radius:6px;padding:8px 12px;cursor:pointer }
.th { text-align:left;padding:8px 12px;white-space:nowrap }
.td { padding:8px 12px;vertical-align:top }
.tr { border-top:1px solid #e5e7eb }
.muted { color:#6b7280;font-size:12px }
.badge { display:inline-block;padding:3px 8px;border-radius:999px;font-size:12px }
.bg-g { background:#d1fae5;color:#065f46 }
.bg-a { background:#fde68a;color:#92400e }
.bg-r { background:#fecaca;color:#7f1d1d }
.bg-y { background:#fef9c3;color:#854d0e }
.bg-b { background:#dbeafe;color:#1e3a8a }
.bg-p { background:#e9d5ff;color:#6b21a8 }
.bg-s { background:#e5e7eb;color:#374151 }
</style>
