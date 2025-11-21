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
      <button @click="fetchData" class="btn">Actualitza</button>
    </div>

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
<<<<<<< HEAD
      * OK si la cobertura global (docent + no docent) està aproximadament entre el 90% i el 110%.
      Es mantenen avisos especials: Abs, No out (NO_SALIDA), Just, Act, Com, Off.
=======
      * Avisos: OK, Parcial, Abs, No out (NO_SALIDA),   Off (sense horari).
>>>>>>> 98af8979178d1b9ce12c24c3c539a3bdc55b4b67
    </p>
  </div>
</template>

<script>
export default {
  name: 'ControlResumenRangoView',
  props: {
    // Professors per al combo (dni + nom), passats des de la vista Blade
    profes: {
      type: Array,
      default: () => []
    }
  },
  data() {
    const { monday, friday } = this.currentWeek()
    return {
      desde: monday,
      hasta: friday,
      dni: '',
      rows: []
    }
  },
  computed: {
    daysList() {
      const a = new Date(this.desde)
      const b = new Date(this.hasta)
      const out = []
      if (isNaN(a) || isNaN(b)) return out
      const d = new Date(a)
      while (d <= b) {
        out.push(d.toISOString().slice(0,10))
        d.setDate(d.getDate() + 1)
      }
      return out
    },
    filteredRows() {
      if (!this.dni) return this.rows
      return this.rows.filter(r => r.dni === this.dni)
    }
  },
  methods: {
    currentWeek() {
      const today = new Date()
      const day = today.getDay() || 7 // dl=1..dg=7
      const monday = new Date(today)
      monday.setDate(today.getDate() - (day - 1))
      const friday = new Date(monday)
      friday.setDate(monday.getDate() + 4)
      const fmt = d => d.toISOString().slice(0,10)
      return { monday: fmt(monday), friday: fmt(friday) }
    },

    async fetchData() {
      const url = new URL('/api/presencia/resumen-rango', window.location.origin)
      url.searchParams.set('desde', this.desde)
      url.searchParams.set('hasta', this.hasta)
      if (this.dni) url.searchParams.set('dni', this.dni)
      const res = await fetch(url.toString(), { credentials: 'same-origin' })
      this.rows = await res.json()
    },

    nomProf(p) {
      return [p.apellido1, p.apellido2, p.nombre].filter(Boolean).join(' ')
    },

    nomRow(r) {
      return [r.apellido1, r.apellido2, r.nombre].filter(Boolean).join(' ')
    },

    formatDia(s) {
      const d = new Date(s + 'T00:00:00')
      const wd = d.toLocaleDateString('ca-ES', { weekday: 'short' })
      const dm = d.toLocaleDateString('ca-ES', { day: '2-digit', month: '2-digit' })
      return `${wd} ${dm}`
    },
<<<<<<< HEAD

    cellInfo(day) {
      const status = day.status || ''

      // Colors base per estats especials
      const COLORS = {
=======

    tinyLabel(s) {
      return ({
        OK: 'OK',
        PARTIAL: 'Parc',
        ABSENT: 'Abs',
        JUSTIFIED: 'Just',
        ACTIVITY: 'Act',
        COMMISSION: 'Com',
        OFF: 'Off',
        NO_SALIDA: 'No out'
      }[s] || s)
    },

    badgeClass(s) {
      return ({
>>>>>>> 98af8979178d1b9ce12c24c3c539a3bdc55b4b67
        OK: 'bg-g',
        PARTIAL: 'bg-a',
        ABSENT: 'bg-r',
        JUSTIFIED: 'bg-y',
        ACTIVITY: 'bg-b',
        COMMISSION: 'bg-p',
        OFF: 'bg-s',
        NO_SALIDA: 'bg-r'
      }

      // ----- Estats especials que no volem resumir amb % -----

      // No ha fitxat la eixida
      if (status === 'NO_SALIDA') {
        let label = 'No out'
        if (day.first_entry) {
          const hm = day.first_entry.slice(0,5) // 'HH:MM'
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

      // ----- Càlcul global relaxat per a la resta (OK / PARTIAL) -----

      const plannedTotal = day.planned_total_minutes != null
        ? day.planned_total_minutes
        : (day.planned_docencia_minutes || 0) + (day.planned_altres_minutes || 0)

      const coveredTotal = day.covered_total_minutes != null
        ? day.covered_total_minutes
        : (day.covered_docencia_minutes || 0) + (day.covered_altres_minutes || 0)

      if (!plannedTotal || plannedTotal <= 0) {
        // sense horari, però no marcat com OFF: neutre
        return { label: '—', class: COLORS.OFF }
      }

      const percent = Math.round((coveredTotal * 100) / plannedTotal)

      // Criteri: si la suma queda entre el 90% i el 110% → OK en verd
      if (percent >= 90 && percent <= 110) {
        return { label: 'OK', class: COLORS.OK }
      }

      // Per sobre del 110% també el considerem OK (ha estat de més)
      if (percent > 110) {
        return { label: 'OK', class: COLORS.OK }
      }

      // Per sota del 90% → parcial en ambre amb % per veure quant falta
      return {
        label: `${percent}%`,
        class: COLORS.PARTIAL
      }
    }
  },
  mounted() {
    this.fetchData()
  }
}
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