<template>
  <div class="space-y-4">
    <div class="controls" style="display:flex;flex-wrap:wrap;gap:8px;align-items:flex-end">
      <div>
        <label>Data</label>
        <input type="date" v-model="dia" class="input">
      </div>
      <div>
        <label>Departament</label>
        <select v-model="dep" class="input">
          <option value="">Tots</option>
          <option v-for="d in departaments" :key="d.id" :value="d.id">{{ d.depcurt }}</option>
        </select>
      </div>
      <div style="flex:1;min-width:240px">
        <label>Cerca</label>
        <input v-model="q" class="input" placeholder="Nom, cognoms o DNI">
      </div>
      <button @click="fetchData" class="btn">Actualitza</button>
    </div>

    <div class="table-wrap" style="overflow:auto;border:1px solid #e5e7eb;border-radius:8px">
      <table class="min-w-full" style="width:100%;font-size:14px;border-collapse:separate;border-spacing:0">
        <thead style="background:#f9fafb;position:sticky;top:0;z-index:1">
          <tr>
            <th class="th w-56">Professor/a</th>
            <th class="th w-28">Dept.</th>
            <th class="th w-28 text-right">Docència (plan)</th>
            <th class="th w-28 text-right">Docència (cob.)</th>
            <th class="th w-28 text-right">Altres (plan)</th>
            <th class="th w-28 text-right">Altres (cob.)</th>
            <th class="th w-28 text-right">Al centre</th>
            <th class="th w-28">Estat</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in filtrats" :key="r.dni" class="tr">
            <td class="td">
              {{ nom(r) }}<div class="muted">{{ r.dni }}</div>
            </td>
            <td class="td">{{ r.departamento || '' }}</td>
            <td class="td tright">{{ r.planned_docencia_minutes }}</td>
            <td class="td tright" :class="r.covered_docencia_minutes < r.planned_docencia_minutes ? 'neg' : 'pos'">
              {{ r.covered_docencia_minutes }}
            </td>
            <td class="td tright">{{ r.planned_altres_minutes }}</td>
            <td class="td tright" :class="r.covered_altres_minutes < r.planned_altres_minutes ? 'warn' : 'pos'">
              {{ r.covered_altres_minutes }}
            </td>
            <td class="td tright">{{ r.in_center_minutes }}</td>
            <td class="td">
              <span class="badge" :class="badgeClass(r.status)">{{ label(r.status) }}</span>
            </td>
          </tr>
          <tr v-if="!filtrats.length">
            <td class="td" colspan="8" style="text-align:center;color:#6b7280;padding:24px">Sense resultats.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <p class="muted" style="font-size:12px">
      * Els minuts “coberts” inclouen comissions/activitats/faltes justificades i toleràncies configurables.
    </p>
  </div>
</template>

<script>
export default {
  name: 'ControlResumenDiaView',
  props: {
    departaments: { type: Array, default: () => [] }
  },
  data() {
    return {
      dia: new Date().toISOString().slice(0,10),
      dep: '',
      q: '',
      rows: []
    }
  },
  computed: {
    filtrats() {
      const needle = this.q.trim().toLowerCase()
      return this.rows.filter(r => {
        if (!needle) return true
        const t = [r.dni, r.nombre, r.apellido1, r.apellido2].join(' ').toLowerCase()
        return t.includes(needle)
      })
    }
  },
  methods: {
    async fetchData() {
      const url = new URL('/api/presencia/resumen-dia', window.location.origin)
      url.searchParams.set('dia', this.dia)
      if (this.dep) url.searchParams.set('departamento', this.dep)
      const res = await fetch(url.toString(), { credentials: 'same-origin' })
      this.rows = await res.json()
    },
    nom(r) {
      return [r.apellido1, r.apellido2, r.nombre].filter(Boolean).join(' ')
    },
    label(s) {
      return ({
        OK: 'OK',
        PARTIAL: 'Parcial',
        ABSENT: 'Absent',
        JUSTIFIED: 'Justificada',
        ACTIVITY: 'Activitat',
        COMMISSION: 'Comissió',
        OFF: 'Sense horari'
      }[s] || s)
    },
    badgeClass(s) {
      return ({
        OK: 'bg-g',
        PARTIAL: 'bg-a',
        ABSENT: 'bg-r',
        JUSTIFIED: 'bg-y',
        ACTIVITY: 'bg-b',
        COMMISSION: 'bg-p',
        OFF: 'bg-s'
      }[s] || 'bg-s')
    }
  },
  mounted() { this.fetchData() }
}
</script>

<style scoped>
.input { border:1px solid #e5e7eb;border-radius:6px;padding:6px 10px }
.btn { background:#2563eb;color:#fff;border:0;border-radius:6px;padding:8px 12px;cursor:pointer }
.th { text-align:left;padding:8px 12px;white-space:nowrap }
.td { padding:8px 12px;vertical-align:top }
.tr { border-top:1px solid #e5e7eb }
.tright { text-align:right }
.muted { color:#6b7280;font-size:12px }
.badge { display:inline-block;padding:3px 8px;border-radius:999px;font-size:12px }
.bg-g { background:#d1fae5;color:#065f46 }
.bg-a { background:#fde68a;color:#92400e }
.bg-r { background:#fecaca;color:#7f1d1d }
.bg-y { background:#fef9c3;color:#854d0e }
.bg-b { background:#dbeafe;color:#1e3a8a }
.bg-p { background:#e9d5ff;color:#6b21a8 }
.bg-s { background:#e5e7eb;color:#374151 }
.pos { color:#065f46;font-weight:600 }
.warn { color:#92400e;font-weight:600 }
.neg { color:#7f1d1d;font-weight:600 }
</style>
