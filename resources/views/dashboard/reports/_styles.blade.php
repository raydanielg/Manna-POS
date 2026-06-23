<style>
/* ═══════════════════════════════════════════════════════
   SHARED REPORT STYLES — MannaPOS Analytics
   ═══════════════════════════════════════════════════════ */

/* ── Page wrapper ──────────────────────────────────────── */
.rpt-page { max-width: 1400px; }

/* ── Report Header Banner ──────────────────────────────── */
.rpt-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0f2748 100%);
    border-radius: 20px;
    padding: 1.6rem 2rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px -8px rgba(15,23,42,.35);
}
.rpt-header::before {
    content: '';
    position: absolute; top: -50%; right: -15%;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(37,99,235,.18) 0%, transparent 70%);
    border-radius: 50%; pointer-events: none;
}
.rpt-header::after {
    content: '';
    position: absolute; bottom: -60%; left: -5%;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(16,185,129,.1) 0%, transparent 70%);
    border-radius: 50%; pointer-events: none;
}
.rpt-header-left { position: relative; z-index: 1; }
.rpt-header-tag {
    display: inline-flex; align-items: center; gap: .3rem;
    font-size: .6rem; font-weight: 800; letter-spacing: .1em;
    text-transform: uppercase; color: #60a5fa;
    background: rgba(37,99,235,.15); border: 1px solid rgba(96,165,250,.2);
    padding: .2rem .65rem; border-radius: 50px; margin-bottom: .5rem;
}
.rpt-header h1 {
    font-size: 1.5rem; font-weight: 800; color: #fff;
    letter-spacing: -.03em; line-height: 1.2; margin: 0;
}
.rpt-header-sub {
    font-size: .8rem; color: #94a3b8; margin-top: .3rem;
    display: flex; align-items: center; gap: .5rem;
}
.rpt-header-sub span { color: #64748b; }
.rpt-header-right {
    display: flex; align-items: center; gap: .6rem;
    flex-wrap: wrap; position: relative; z-index: 1;
}

/* ── Action Buttons ────────────────────────────────────── */
.rpt-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .5rem 1rem; border-radius: 10px;
    font-size: .8rem; font-weight: 600;
    border: none; cursor: pointer; font-family: inherit;
    transition: all .2s; text-decoration: none; white-space: nowrap;
}
.rpt-btn-primary {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff; box-shadow: 0 4px 14px rgba(37,99,235,.3);
}
.rpt-btn-primary:hover { box-shadow: 0 6px 20px rgba(37,99,235,.45); transform: translateY(-1px); }
.rpt-btn-ghost {
    background: rgba(255,255,255,.06); color: #cbd5e1;
    border: 1px solid rgba(255,255,255,.1);
}
.rpt-btn-ghost:hover { background: rgba(255,255,255,.12); color: #fff; }

/* ── Filter Bar ────────────────────────────────────────── */
.rpt-filter {
    background: #fff;
    border: 1px solid #e9edf5;
    border-radius: 16px;
    padding: 1.1rem 1.4rem;
    margin-bottom: 1.5rem;
    display: flex; flex-wrap: wrap; align-items: flex-end; gap: .75rem;
    box-shadow: 0 2px 12px rgba(15,23,42,.05);
}
.rpt-filter-group { display: flex; flex-direction: column; gap: .3rem; }
.rpt-filter-label {
    font-size: .7rem; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: .06em;
}
.rpt-filter input[type="date"],
.rpt-filter select {
    height: 38px; padding: 0 .75rem;
    border: 1.5px solid #e2e8f0; border-radius: 10px;
    font-size: .82rem; color: #0f172a; background: #f8fafc;
    font-family: inherit; outline: none;
    transition: border-color .2s, box-shadow .2s;
    min-width: 150px;
}
.rpt-filter input[type="date"]:focus,
.rpt-filter select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.12);
    background: #fff;
}
.rpt-filter-actions { display: flex; gap: .5rem; align-items: center; }
.rpt-filter-btn {
    height: 38px; padding: 0 1.1rem; border-radius: 10px;
    font-size: .82rem; font-weight: 600;
    border: none; cursor: pointer; font-family: inherit;
    transition: all .2s;
}
.rpt-filter-btn-primary {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff; box-shadow: 0 3px 10px rgba(37,99,235,.25);
}
.rpt-filter-btn-primary:hover { box-shadow: 0 5px 16px rgba(37,99,235,.35); }
.rpt-filter-btn-reset {
    background: #f1f5f9; color: #475569;
    border: 1.5px solid #e2e8f0;
    text-decoration: none; display: inline-flex; align-items: center;
}
.rpt-filter-btn-reset:hover { background: #e2e8f0; color: #0f172a; }

/* Quick preset pills */
.rpt-presets { display: flex; flex-wrap: wrap; gap: .35rem; align-items: center; margin-left: auto; }
.rpt-preset {
    height: 30px; padding: 0 .75rem; border-radius: 50px;
    font-size: .72rem; font-weight: 600; cursor: pointer;
    border: 1.5px solid #e2e8f0; background: #f8fafc; color: #64748b;
    transition: all .18s; white-space: nowrap;
}
.rpt-preset:hover { border-color: #2563eb; color: #2563eb; background: #eff6ff; }
.rpt-preset.active { border-color: #2563eb; color: #2563eb; background: #eff6ff; }

/* ── KPI Cards ─────────────────────────────────────────── */
.rpt-kpis { display: grid; gap: 1rem; margin-bottom: 1.5rem; }
.rpt-kpis.cols-2 { grid-template-columns: repeat(2, 1fr); }
.rpt-kpis.cols-3 { grid-template-columns: repeat(3, 1fr); }
.rpt-kpis.cols-4 { grid-template-columns: repeat(4, 1fr); }
@media (max-width: 1024px) {
    .rpt-kpis.cols-4 { grid-template-columns: repeat(2, 1fr); }
    .rpt-kpis.cols-3 { grid-template-columns: repeat(2, 1fr) }
}
@media (max-width: 640px) {
    .rpt-kpis.cols-4,.rpt-kpis.cols-3,.rpt-kpis.cols-2 { grid-template-columns: 1fr; }
    .rpt-header { padding: 1.1rem 1.25rem; border-radius: 16px; }
    .rpt-header h1 { font-size: 1.2rem; }
}

.rpt-kpi {
    background: #fff;
    border: 1px solid #e9edf5;
    border-radius: 16px;
    padding: 1.25rem 1.4rem;
    display: flex; align-items: flex-start; gap: 1rem;
    box-shadow: 0 2px 12px rgba(15,23,42,.04);
    transition: transform .2s, box-shadow .2s;
    position: relative; overflow: hidden;
}
.rpt-kpi:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(15,23,42,.1); }
.rpt-kpi-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.rpt-kpi-icon svg { width: 22px; height: 22px; }
.rpt-kpi-icon.green  { background: #f0fdf4; color: #16a34a; }
.rpt-kpi-icon.red    { background: #fff1f2; color: #e03057; }
.rpt-kpi-icon.blue   { background: #eff6ff; color: #2563eb; }
.rpt-kpi-icon.purple { background: #faf5ff; color: #7c3aed; }
.rpt-kpi-icon.amber  { background: #fffbeb; color: #d97706; }
.rpt-kpi-icon.indigo { background: #eef2ff; color: #4f46e5; }
.rpt-kpi-icon.teal   { background: #f0fdfa; color: #0d9488; }
.rpt-kpi-icon.slate  { background: #f8fafc; color: #475569; }
.rpt-kpi-body { flex: 1; min-width: 0; }
.rpt-kpi-label {
    font-size: .68rem; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .08em; margin-bottom: .3rem;
}
.rpt-kpi-value {
    font-size: 1.55rem; font-weight: 900; color: #0f172a;
    letter-spacing: -.03em; line-height: 1.1;
}
.rpt-kpi-value.green  { color: #16a34a; }
.rpt-kpi-value.red    { color: #e03057; }
.rpt-kpi-value.blue   { color: #2563eb; }
.rpt-kpi-value.amber  { color: #d97706; }
.rpt-kpi-value.purple { color: #7c3aed; }
.rpt-kpi-sub { font-size: .72rem; color: #94a3b8; margin-top: .25rem; }

/* ── Chart Card ────────────────────────────────────────── */
.rpt-chart-card {
    background: #fff;
    border: 1px solid #e9edf5;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(15,23,42,.04);
    margin-bottom: 1.5rem;
}
.rpt-chart-grid { display: grid; gap: 1rem; margin-bottom: 1.5rem; }
.rpt-chart-grid.cols-2 { grid-template-columns: 1fr 1fr; }
@media (max-width: 1024px) { .rpt-chart-grid.cols-2 { grid-template-columns: 1fr; } }
.rpt-card-head {
    padding: .9rem 1.4rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
}
.rpt-card-title { font-size: .85rem; font-weight: 700; color: #0f172a; }
.rpt-card-sub { font-size: .75rem; color: #94a3b8; }
.rpt-chart-body { padding: 1.2rem 1.4rem; }

/* ── Table Card ────────────────────────────────────────── */
.rpt-table-card {
    background: #fff;
    border: 1px solid #e9edf5;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(15,23,42,.04);
    margin-bottom: 1.5rem;
}
.rpt-table { width: 100%; border-collapse: collapse; }
.rpt-table thead tr {
    background: #f8fafc;
    border-bottom: 2px solid #e9edf5;
}
.rpt-table thead th {
    padding: .75rem 1rem;
    font-size: .68rem; font-weight: 800;
    color: #64748b; text-transform: uppercase; letter-spacing: .07em;
    white-space: nowrap;
}
.rpt-table thead th:first-child { border-radius: 0; padding-left: 1.4rem; }
.rpt-table thead th:last-child  { padding-right: 1.4rem; }
.rpt-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background .12s;
}
.rpt-table tbody tr:hover { background: #fafbff; }
.rpt-table tbody tr:last-child { border-bottom: none; }
.rpt-table td {
    padding: .75rem 1rem;
    font-size: .82rem; color: #334155;
}
.rpt-table td:first-child { padding-left: 1.4rem; }
.rpt-table td:last-child  { padding-right: 1.4rem; }
.rpt-table .t-num { color: #94a3b8; font-size: .75rem; }
.rpt-table .t-ref { font-family: ui-monospace, monospace; font-size: .78rem; color: #2563eb; font-weight: 700; }
.rpt-table .t-name { font-weight: 600; color: #0f172a; }
.rpt-table .t-muted { color: #94a3b8; }
.rpt-table .t-amt { font-weight: 700; color: #0f172a; font-variant-numeric: tabular-nums; }
.rpt-table .t-amt-green { font-weight: 700; color: #16a34a; font-variant-numeric: tabular-nums; }
.rpt-table .t-amt-red { font-weight: 700; color: #e03057; font-variant-numeric: tabular-nums; }
.rpt-table .t-amt-blue { font-weight: 700; color: #2563eb; font-variant-numeric: tabular-nums; }
.rpt-table .t-amt-amber { font-weight: 700; color: #d97706; font-variant-numeric: tabular-nums; }
.rpt-table .t-right { text-align: right; }

/* ── Badges ────────────────────────────────────────────── */
.rpt-badge {
    display: inline-flex; align-items: center;
    padding: .2rem .65rem; border-radius: 50px;
    font-size: .68rem; font-weight: 700; letter-spacing: .03em;
    white-space: nowrap;
}
.rpt-badge-green  { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.rpt-badge-red    { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
.rpt-badge-amber  { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.rpt-badge-blue   { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.rpt-badge-purple { background: #faf5ff; color: #6d28d9; border: 1px solid #ddd6fe; }
.rpt-badge-slate  { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.rpt-badge-teal   { background: #f0fdfa; color: #0f766e; border: 1px solid #99f6e4; }

/* ── Empty State ───────────────────────────────────────── */
.rpt-empty {
    padding: 3.5rem 1rem; text-align: center;
}
.rpt-empty svg { margin: 0 auto .85rem; display: block; opacity: .35; }
.rpt-empty p { color: #64748b; font-size: .88rem; font-weight: 500; }
.rpt-empty span { color: #94a3b8; font-size: .8rem; display: block; margin-top: .2rem; }

/* ── Pagination ────────────────────────────────────────── */
.rpt-pagination {
    padding: .85rem 1.4rem;
    border-top: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
    font-size: .8rem; color: #64748b;
}

/* ── Progress Bar (for category breakdowns) ───────────── */
.rpt-bar-bg { background: #f1f5f9; border-radius: 50px; height: 6px; overflow: hidden; }
.rpt-bar-fill { height: 100%; border-radius: 50px; transition: width .4s; }

/* Print ───────────────────────────────────────────────── */
@media print {
    .rpt-header { background: #0f172a !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .no-print { display: none !important; }
}
</style>
