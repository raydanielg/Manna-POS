@extends('layouts.dashboard')
@section('page_title', 'Calendar & Tasks')
@section('page_styles')
<style>
:root{--cal-accent:#2563eb;--cal-accent2:#10b981;--cal-bg:#fff;--cal-border:#e9edf5;}

/* ── Layout ────────────────────────────── */
.cal-layout{display:grid;grid-template-columns:1fr 380px;gap:1.5rem;align-items:start;}
@media(max-width:1024px){.cal-layout{grid-template-columns:1fr;}}
.cal-sidebar{display:flex;flex-direction:column;gap:1.25rem;}

/* ── Mini Stats ────────────────────────── */
.cal-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:.75rem;}
.cal-stat{background:#fff;border-radius:14px;border:1px solid var(--cal-border);padding:.9rem 1rem;text-align:center;transition:all .2s;}
.cal-stat:hover{box-shadow:0 6px 20px rgba(15,23,42,.07);transform:translateY(-2px);}
.cal-stat .ico{width:32px;height:32px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto .35rem;}
.cal-stat .val{font-size:1.3rem;font-weight:800;color:#0f172a;line-height:1.2;}
.cal-stat .lbl{font-size:.62rem;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-top:.1rem;}

/* ── Calendar ──────────────────────────── */
.cal-card{background:#fff;border-radius:20px;border:1px solid var(--cal-border);overflow:hidden;}
.cal-head{display:flex;align-items:center;justify-content:space-between;padding:1.1rem 1.5rem;border-bottom:1px solid #f1f5f9;}
.cal-head h2{font-size:1.05rem;font-weight:800;color:#0f172a;}
.cal-nav{display:flex;align-items:center;gap:.35rem;}
.cal-nav-btn{width:32px;height:32px;border-radius:8px;border:1px solid #e2e8f0;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .15s;color:#475569;}
.cal-nav-btn:hover{background:#f1f5f9;border-color:#cbd5e1;}
.cal-nav-btn:active{transform:scale(.92);}
.cal-nav-btn.today{border-color:var(--cal-accent);color:var(--cal-accent);font-size:.7rem;font-weight:700;padding:0 .6rem;width:auto;}
.cal-grid{display:grid;grid-template-columns:repeat(7,1fr);}
.cal-day-header{padding:.5rem;text-align:center;font-size:.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid #f1f5f9;}
.cal-day{min-height:90px;padding:.35rem;border-right:1px solid #f8fafc;border-bottom:1px solid #f8fafc;cursor:pointer;transition:all .12s;position:relative;}
.cal-day:nth-child(7n){border-right:none;}
.cal-day:hover{background:#fafbff;}
.cal-day.other-month{background:#fafbff;}
.cal-day.other-month .day-num{color:#cbd5e1;}
.cal-day.today{background:#eff6ff;}
.cal-day.today .day-num{background:var(--cal-accent);color:#fff;border-radius:50%;width:26px;height:26px;display:flex;align-items:center;justify-content:center;}
.cal-day.selected{background:#dbeafe;border-color:var(--cal-accent);}
.day-num{font-size:.78rem;font-weight:700;color:#0f172a;margin-bottom:.25rem;width:26px;height:26px;display:flex;align-items:center;justify-content:center;}
.day-dots{display:flex;flex-wrap:wrap;gap:3px;padding:0 2px;}
.day-dot{width:6px;height:6px;border-radius:50%;}
.day-dot.todo{background:var(--cal-accent);}
.day-dot.crm{background:var(--cal-accent2);}
.day-more{font-size:.58rem;color:#94a3b8;font-weight:600;padding:0 2px;margin-top:2px;}

/* ── Todo List ─────────────────────────── */
.todo-card{background:#fff;border-radius:20px;border:1px solid var(--cal-border);display:flex;flex-direction:column;max-height:600px;}
.todo-head{display:flex;align-items:center;justify-content:space-between;padding:1.1rem 1.5rem;border-bottom:1px solid #f1f5f9;flex-shrink:0;}
.todo-head h3{font-size:.95rem;font-weight:800;color:#0f172a;display:flex;align-items:center;gap:.4rem;}
.todo-head-actions{display:flex;align-items:center;gap:.5rem;}
.filter-btn{padding:.25rem .6rem;border-radius:6px;border:1px solid #e2e8f0;background:#fff;font-size:.68rem;font-weight:600;color:#64748b;cursor:pointer;transition:all .15s;}
.filter-btn:hover{border-color:#cbd5e1;background:#f8fafc;}
.filter-btn.active{background:var(--cal-accent);color:#fff;border-color:var(--cal-accent);}
.todo-list{flex:1;overflow-y:auto;padding:.5rem 0;}
.todo-item{display:flex;align-items:flex-start;gap:.6rem;padding:.55rem 1.5rem;transition:all .15s;border-left:3px solid transparent;}
.todo-item:hover{background:#f8fafc;}
.todo-item.completed{opacity:.55;}
.todo-item.completed .todo-title{text-decoration:line-through;color:#94a3b8;}
.todo-item.high{border-left-color:#ef4444;}
.todo-item.medium{border-left-color:#f59e0b;}
.todo-item.low{border-left-color:#94a3b8;}
.todo-check{width:20px;height:20px;border-radius:6px;border:2px solid #cbd5e1;flex-shrink:0;cursor:pointer;margin-top:2px;display:flex;align-items:center;justify-content:center;transition:all .2s;}
.todo-check:hover{border-color:var(--cal-accent);}
.todo-check.checked{background:var(--cal-accent2);border-color:var(--cal-accent2);}
.todo-check.checked svg{display:block;}
.todo-body{flex:1;min-width:0;}
.todo-title{font-size:.82rem;font-weight:600;color:#0f172a;}
.todo-desc{font-size:.72rem;color:#94a3b8;margin-top:1px;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;}
.todo-meta{display:flex;align-items:center;gap:.5rem;margin-top:.25rem;}
.todo-priority{font-size:.58rem;font-weight:700;padding:.08rem .4rem;border-radius:4px;text-transform:uppercase;letter-spacing:.04em;}
.todo-priority.high{background:#fef2f2;color:#dc2626;}
.todo-priority.medium{background:#fffbeb;color:#d97706;}
.todo-priority.low{background:#f1f5f9;color:#64748b;}
.todo-date-badge{font-size:.62rem;color:#94a3b8;display:flex;align-items:center;gap:.2rem;}
.todo-actions{display:flex;gap:.2rem;opacity:0;transition:opacity .15s;}
.todo-item:hover .todo-actions{opacity:1;}
.todo-action-btn{width:26px;height:26px;border-radius:6px;border:none;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;transition:all .15s;}
.todo-action-btn:hover{background:#f1f5f9;color:#475569;}
.todo-action-btn.danger:hover{background:#fef2f2;color:#dc2626;}
.todo-empty{text-align:center;padding:2.5rem 1rem;color:#94a3b8;font-size:.85rem;}

/* ── Modal ─────────────────────────────── */
.todo-modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.5);backdrop-filter:blur(4px);z-index:100;display:none;align-items:center;justify-content:center;padding:1rem;}
.todo-modal-overlay.open{display:flex;}
.todo-modal{background:#fff;border-radius:20px;width:100%;max-width:480px;max-height:90vh;overflow-y:auto;box-shadow:0 25px 50px rgba(0,0,0,.2);animation:modalIn .25s ease;}
@keyframes modalIn{from{opacity:0;transform:scale(.95) translateY(10px);}to{opacity:1;transform:scale(1) translateY(0);}}
.todo-modal-head{display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #f1f5f9;}
.todo-modal-head h3{font-size:1rem;font-weight:800;color:#0f172a;}
.todo-modal-close{width:32px;height:32px;border-radius:8px;border:none;background:#f1f5f9;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748b;transition:all .15s;}
.todo-modal-close:hover{background:#e2e8f0;color:#0f172a;}
.todo-modal-body{padding:1.25rem 1.5rem;}
.todo-modal-body .form-group{margin-bottom:1rem;}
.todo-modal-body .form-group:last-child{margin-bottom:0;}
.todo-modal-body label{display:block;font-size:.75rem;font-weight:700;color:#475569;margin-bottom:.3rem;}
.todo-modal-body .form-control{width:100%;padding:.55rem .75rem;border-radius:10px;border:1px solid #e2e8f0;font-size:.82rem;font-family:inherit;transition:border-color .15s;outline:none;background:#fff;}
.todo-modal-body .form-control:focus{border-color:var(--cal-accent);box-shadow:0 0 0 3px rgba(37,99,235,.08);}
.todo-modal-body textarea.form-control{min-height:80px;resize:vertical;}
.todo-modal-body select.form-control{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%2394a3b8' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M6 9l6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .6rem center;padding-right:2rem;}
.todo-modal-footer{display:flex;gap:.5rem;justify-content:flex-end;padding:1rem 1.5rem;border-top:1px solid #f1f5f9;}
.btn-todo-primary{background:var(--cal-accent);color:#fff;border:none;padding:.55rem 1.25rem;border-radius:10px;font-size:.82rem;font-weight:700;cursor:pointer;transition:all .2s;font-family:inherit;}
.btn-todo-primary:hover{background:#1d4ed8;transform:translateY(-1px);}
.btn-todo-secondary{background:#f1f5f9;color:#475569;border:none;padding:.55rem 1.25rem;border-radius:10px;font-size:.82rem;font-weight:600;cursor:pointer;transition:all .2s;font-family:inherit;}
.btn-todo-secondary:hover{background:#e2e8f0;}
.btn-todo-danger{background:#fef2f2;color:#dc2626;border:none;padding:.55rem 1.25rem;border-radius:10px;font-size:.82rem;font-weight:600;cursor:pointer;transition:all .2s;font-family:inherit;}
.btn-todo-danger:hover{background:#fee2e2;}

.priority-group{display:flex;gap:.35rem;}
.prio-opt{flex:1;padding:.4rem;border-radius:8px;border:2px solid #e2e8f0;background:#fff;text-align:center;cursor:pointer;font-size:.72rem;font-weight:700;transition:all .15s;font-family:inherit;}
.prio-opt:hover{border-color:#cbd5e1;}
.prio-opt.active{border-color:var(--cal-accent);}
.prio-opt.low-active{border-color:#64748b;background:#f1f5f9;}
.prio-opt.med-active{border-color:#d97706;background:#fffbeb;}
.prio-opt.high-active{border-color:#dc2626;background:#fef2f2;}

/* ── Date picker inline ────────────────── */
.date-input-wrap{position:relative;}
.date-input-wrap input[type="date"]{padding:.55rem .75rem;border-radius:10px;border:1px solid #e2e8f0;font-size:.82rem;font-family:inherit;width:100%;outline:none;transition:border-color .15s;background:#fff;color:#0f172a;}
.date-input-wrap input[type="date"]:focus{border-color:var(--cal-accent);box-shadow:0 0 0 3px rgba(37,99,235,.08);}
.date-input-wrap input[type="date"]::-webkit-calendar-picker-indicator{cursor:pointer;opacity:.5;}
.date-input-wrap input[type="date"]::-webkit-calendar-picker-indicator:hover{opacity:1;}

/* ── Toast ─────────────────────────────── */
.todo-toast{position:fixed;bottom:2rem;right:2rem;background:#0f172a;color:#fff;padding:.75rem 1.25rem;border-radius:12px;font-size:.82rem;font-weight:500;box-shadow:0 8px 30px rgba(0,0,0,.2);z-index:200;transform:translateY(100px);opacity:0;transition:all .35s;}
.todo-toast.show{transform:translateY(0);opacity:1;}
.todo-toast.success{background:#065f46;}
.todo-toast.error{background:#991b1b;}

/* ── Empty state ───────────────────────── */
.empty-state{padding:3rem 1rem;text-align:center;}
.empty-state svg{margin:0 auto 1rem;display:block;}
.empty-state p{color:#94a3b8;font-size:.85rem;}
.empty-state .btn{display:inline-flex;align-items:center;gap:.35rem;margin-top:.75rem;padding:.5rem 1rem;border-radius:8px;font-size:.8rem;font-weight:700;cursor:pointer;border:none;font-family:inherit;transition:all .2s;}

/* ── Drag handle ───────────────────────── */
.drag-handle{cursor:grab;color:#d1d5db;display:flex;align-items:center;padding-right:.3rem;opacity:0;transition:opacity .15s;}
.todo-item:hover .drag-handle{opacity:1;}
</style>
@endsection

@section('content')
@php
$now = now();
$curYear = $now->year;
$curMonth = $now->month;
$curDay = $now->day;
@endphp

<div class="dash-content">

  <div class="cal-layout">

    {{-- ═══ LEFT: Calendar ═══════════════════════════════ --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">

      {{-- Stats --}}
      <div class="cal-stats" id="calStats">
        <div class="cal-stat">
          <div class="ico" style="background:#eff6ff;"><svg width="16" height="16" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
          <div class="val" id="st-total">—</div>
          <div class="lbl">Total Tasks</div>
        </div>
        <div class="cal-stat">
          <div class="ico" style="background:#f0fdf4;"><svg width="16" height="16" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
          <div class="val" id="st-done">—</div>
          <div class="lbl">Completed</div>
        </div>
        <div class="cal-stat">
          <div class="ico" style="background:#fffbeb;"><svg width="16" height="16" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
          <div class="val" id="st-pending">—</div>
          <div class="lbl">Pending</div>
        </div>
        <div class="cal-stat">
          <div class="ico" style="background:#fef2f2;"><svg width="16" height="16" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg></div>
          <div class="val" id="st-high">—</div>
          <div class="lbl">High Priority</div>
        </div>
      </div>

      {{-- Calendar Card --}}
      <div class="cal-card">
        <div class="cal-head">
          <h2 id="calTitle">{{ $now->format('F Y') }}</h2>
          <div class="cal-nav">
            <button class="cal-nav-btn" onclick="goMonth(-1)" title="Previous month">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button class="cal-nav-btn today" onclick="goToday()">Today</button>
            <button class="cal-nav-btn" onclick="goMonth(1)" title="Next month">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
          </div>
        </div>
        <div class="cal-grid" id="calGrid">
          <div class="cal-day-header">Mon</div>
          <div class="cal-day-header">Tue</div>
          <div class="cal-day-header">Wed</div>
          <div class="cal-day-header">Thu</div>
          <div class="cal-day-header">Fri</div>
          <div class="cal-day-header">Sat</div>
          <div class="cal-day-header">Sun</div>
        </div>
      </div>
    </div>

    {{-- ═══ RIGHT: Todo List ═══════════════════════════════ --}}
    <div class="cal-sidebar">

      <div class="todo-card">
        <div class="todo-head">
          <h3>
            <svg width="16" height="16" fill="none" stroke="#0f172a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span id="todoListTitle">Today's Tasks</span>
          </h3>
          <div class="todo-head-actions">
            <button class="filter-btn active" data-filter="all" onclick="filterTodos('all',this)">All</button>
            <button class="filter-btn" data-filter="pending" onclick="filterTodos('pending',this)">Active</button>
            <button class="filter-btn" data-filter="completed" onclick="filterTodos('completed',this)">Done</button>
            <button class="btn-todo-primary" onclick="openAddModal()" style="padding:.35rem .7rem;font-size:.72rem;display:flex;align-items:center;gap:.25rem;">
              <svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
              Add
            </button>
          </div>
        </div>
        <div class="todo-list" id="todoList">
          <div class="todo-empty">
            <svg width="36" height="36" fill="none" stroke="#cbd5e1" stroke-width="1.2" viewBox="0 0 24 24" style="margin:0 auto .75rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <p>No tasks for this day</p>
            <button class="btn" style="display:inline-flex;align-items:center;gap:.3rem;margin-top:.5rem;padding:.4rem .85rem;border-radius:8px;font-size:.78rem;font-weight:600;background:#2563eb;color:#fff;border:none;cursor:pointer;" onclick="openAddModal()">+ Add a task</button>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

{{-- ═══ Add/Edit Todo Modal ═══════════════════════════════ --}}
<div class="todo-modal-overlay" id="todoModal">
  <div class="todo-modal">
    <div class="todo-modal-head">
      <h3 id="modalTitle">New Task</h3>
      <button class="todo-modal-close" onclick="closeModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="todo-modal-body">
      <form id="todoForm">
        <input type="hidden" name="edit_id" id="editId">
        <div class="form-group">
          <label>Task Title *</label>
          <input type="text" class="form-control" name="title" id="fTitle" placeholder="What needs to be done?" required autofocus>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea class="form-control" name="description" id="fDesc" placeholder="Add details…"></textarea>
        </div>
        <div class="form-group">
          <label>Due Date</label>
          <div class="date-input-wrap">
            <input type="date" name="date" id="fDate" value="{{ $now->toDateString() }}">
          </div>
        </div>
        <div class="form-group">
          <label>Priority</label>
          <div class="priority-group" id="priorityGroup">
            <button type="button" class="prio-opt" data-value="low" onclick="selectPriority('low')">Low</button>
            <button type="button" class="prio-opt active med-active" data-value="medium" onclick="selectPriority('medium')">Medium</button>
            <button type="button" class="prio-opt" data-value="high" onclick="selectPriority('high')">High</button>
          </div>
          <input type="hidden" name="priority" id="fPriority" value="medium">
        </div>
      </form>
    </div>
    <div class="todo-modal-footer">
      <button class="btn-todo-secondary" onclick="closeModal()">Cancel</button>
      <button class="btn-todo-danger" id="deleteBtn" style="display:none;" onclick="deleteTodo()">Delete</button>
      <button class="btn-todo-primary" id="saveBtn" onclick="saveTodo()">Save Task</button>
    </div>
  </div>
</div>

{{-- Toast --}}
<div class="todo-toast" id="todoToast"></div>
@endsection

@section('scripts')
<script>
// ── State ──────────────────────────────────────────────
const API_TODOS = '/api/dashboard/todos';
const API_CAL = '/api/dashboard/calendar/data';

let currentYear = {{ $curYear }};
let currentMonth = {{ $curMonth }};
let selectedDate = '{{ $now->toDateString() }}';
let currentFilter = 'all';
let allTodos = [];
let calDates = {};
let editId = null;

const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
const priorityLabels = { low:'Low', medium:'Medium', high:'High' };

// ── Init ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  renderCalendar();
  loadTodos();

  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('add') === '1') {
    openAddModal();
  }
});

// ── Calendar ──────────────────────────────────────────
function renderCalendar() {
  const grid = document.getElementById('calGrid');
  const title = document.getElementById('calTitle');
  title.textContent = monthNames[currentMonth - 1] + ' ' + currentYear;

  // Remove old day cells (keep headers)
  while (grid.children.length > 7) {
    grid.removeChild(grid.lastChild);
  }

  const firstDay = new Date(currentYear, currentMonth - 1, 1);
  const lastDay = new Date(currentYear, currentMonth, 0);
  const startDay = firstDay.getDay(); // 0=Sun, need Mon=0
  const startOffset = startDay === 0 ? 6 : startDay - 1;
  const daysInMonth = lastDay.getDate();
  const daysInPrev = new Date(currentYear, currentMonth - 1, 0).getDate();

  const today = new Date();
  const todayStr = fmtDate(today);

  // Load calendar data
  loadCalendarData();

  for (let i = 0; i < startOffset; i++) {
    const d = daysInPrev - startOffset + i + 1;
    grid.appendChild(createDayCell(d, true));
  }

  for (let d = 1; d <= daysInMonth; d++) {
    const dateStr = currentYear + '-' + String(currentMonth).padStart(2,'0') + '-' + String(d).padStart(2,'0');
    const isToday = dateStr === todayStr;
    const cell = createDayCell(d, false, isToday, dateStr);
    cell.dataset.date = dateStr;

    // Click to select date
    cell.addEventListener('click', () => {
      selectDate(dateStr);
    });

    if (dateStr === selectedDate) {
      cell.classList.add('selected');
    }

    grid.appendChild(cell);
  }

  const totalCells = startOffset + daysInMonth;
  const remaining = (7 - (totalCells % 7)) % 7;
  for (let i = 1; i <= remaining; i++) {
    grid.appendChild(createDayCell(i, true));
  }

  // Apply dots after rendering
  setTimeout(() => applyDateDots(), 100);
}

function createDayCell(dayNum, isOther, isToday, dateStr) {
  const div = document.createElement('div');
  div.className = 'cal-day' + (isOther ? ' other-month' : '') + (isToday ? ' today' : '');

  const numDiv = document.createElement('div');
  numDiv.className = 'day-num';
  numDiv.textContent = dayNum;
  div.appendChild(numDiv);

  const dotsDiv = document.createElement('div');
  dotsDiv.className = 'day-dots';
  dotsDiv.id = 'dots-' + (dateStr || 'other-' + dayNum);
  div.appendChild(dotsDiv);

  const moreDiv = document.createElement('div');
  moreDiv.className = 'day-more';
  moreDiv.id = 'more-' + (dateStr || 'other-' + dayNum);
  div.appendChild(moreDiv);

  return div;
}

function applyDateDots() {
  for (const [dateStr, info] of Object.entries(calDates)) {
    const dotsEl = document.getElementById('dots-' + dateStr);
    const moreEl = document.getElementById('more-' + dateStr);
    if (!dotsEl) continue;

    dotsEl.innerHTML = '';
    if (info.todos > 0) {
      const dot = document.createElement('span');
      dot.className = 'day-dot todo';
      dot.title = info.todos + ' task(s)';
      dotsEl.appendChild(dot);
    }
    if (info.activities > 0) {
      const dot = document.createElement('span');
      dot.className = 'day-dot crm';
      dot.title = info.activities + ' activity(ies)';
      dotsEl.appendChild(dot);
    }
    if (info.total > 2 && moreEl) {
      moreEl.textContent = '+' + (info.total - 2) + ' more';
    }
  }
}

async function loadCalendarData() {
  try {
    const res = await fetch(`${API_CAL}?year=${currentYear}&month=${currentMonth}`);
    const data = await res.json();
    calDates = data.dates || {};
    applyDateDots();

    document.getElementById('st-total').textContent = data.total_todos || 0;
    document.getElementById('st-done').textContent = data.completed_todos || 0;
    document.getElementById('st-pending').textContent = data.pending_todos || 0;
    document.getElementById('st-high').textContent = data.high_priority || 0;
  } catch(e) {
    console.error('Failed to load calendar data', e);
  }
}

function goMonth(delta) {
  currentMonth += delta;
  if (currentMonth > 12) { currentMonth = 1; currentYear++; }
  if (currentMonth < 1) { currentMonth = 12; currentYear--; }
  renderCalendar();
}

function goToday() {
  const now = new Date();
  currentYear = now.getFullYear();
  currentMonth = now.getMonth() + 1;
  selectedDate = fmtDate(now);
  renderCalendar();
  loadTodos();
}

function selectDate(dateStr) {
  selectedDate = dateStr;
  document.querySelectorAll('.cal-day').forEach(el => el.classList.remove('selected'));
  const cell = document.querySelector(`.cal-day[data-date="${dateStr}"]`);
  if (cell) cell.classList.add('selected');
  document.getElementById('todoListTitle').textContent = formatTitleDate(dateStr);
  loadTodos();
}

function formatTitleDate(dateStr) {
  if (!dateStr) return 'Tasks';
  const d = new Date(dateStr + 'T00:00:00');
  const today = new Date();
  const tomorrow = new Date(today); tomorrow.setDate(tomorrow.getDate()+1);
  const yesterday = new Date(today); yesterday.setDate(yesterday.getDate()-1);

  if (fmtDate(d) === fmtDate(today)) return "Today's Tasks";
  if (fmtDate(d) === fmtDate(tomorrow)) return "Tomorrow's Tasks";
  if (fmtDate(d) === fmtDate(yesterday)) return "Yesterday's Tasks";

  return d.toLocaleDateString('en-US', { weekday:'long', month:'short', day:'numeric' });
}

function fmtDate(d) {
  return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
}

// ── Todos ──────────────────────────────────────────────
async function loadTodos() {
  const list = document.getElementById('todoList');
  list.innerHTML = '<div style="text-align:center;padding:2rem;color:#94a3b8;font-size:.82rem;">Loading...</div>';

  try {
    const params = new URLSearchParams({ date: selectedDate });
    if (currentFilter !== 'all') params.append('status', currentFilter);

    const res = await fetch(API_TODOS + '?' + params.toString());
    const data = await res.json();

    allTodos = data.todos || [];

    if (!allTodos.length) {
      list.innerHTML = `
        <div class="todo-empty">
          <svg width="36" height="36" fill="none" stroke="#cbd5e1" stroke-width="1.2" viewBox="0 0 24 24" style="margin:0 auto .75rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          <p>No tasks for this day</p>
          <button class="btn" style="display:inline-flex;align-items:center;gap:.3rem;margin-top:.5rem;padding:.4rem .85rem;border-radius:8px;font-size:.78rem;font-weight:600;background:#2563eb;color:#fff;border:none;cursor:pointer;" onclick="openAddModal()">+ Add a task</button>
        </div>`;
      return;
    }

    list.innerHTML = allTodos.map(t => renderTodoItem(t)).join('');

    // Also show CRM activities
    if (data.activities && data.activities.length) {
      const actHtml = data.activities.filter(a => a.status !== 'completed').map(a => `
        <div class="todo-item" style="border-left-color:#10b981;background:#f0fdf4;">
          <div class="todo-check checked" style="background:#10b981;border-color:#10b981;">
            <svg width="10" height="10" fill="none" stroke="#fff" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          </div>
          <div class="todo-body">
            <div class="todo-title" style="color:#065f46;">${a.title}</div>
            <div class="todo-desc">${a.description || 'CRM ' + a.crm_type}</div>
            <div class="todo-meta">
              <span class="todo-priority" style="background:#dcfce7;color:#16a34a;">CRM</span>
              <span class="todo-date-badge">${a.crm_type}</span>
            </div>
          </div>
        </div>`).join('');
      if (actHtml) {
        list.innerHTML += `<div style="padding:.4rem 1.5rem .2rem;font-size:.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;border-top:1px solid #f1f5f9;">CRM Activities</div>` + actHtml;
      }
    }

  } catch(e) {
    list.innerHTML = '<div style="text-align:center;padding:2rem;color:#ef4444;">Failed to load tasks.</div>';
  }
}

function renderTodoItem(t) {
  const isDone = t.status === 'completed';
  const dateStr = t.date || '';
  return `
    <div class="todo-item ${isDone ? 'completed' : ''} ${t.priority}" data-id="${t.id}">
      <div class="todo-check ${isDone ? 'checked' : ''}" onclick="toggleTodo(${t.id})">
        ${isDone ? '<svg width="10" height="10" fill="none" stroke="#fff" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>' : ''}
      </div>
      <div class="todo-body">
        <div class="todo-title">${escHtml(t.title)}</div>
        ${t.description ? `<div class="todo-desc">${escHtml(t.description)}</div>` : ''}
        <div class="todo-meta">
          <span class="todo-priority ${t.priority}">${priorityLabels[t.priority] || 'Medium'}</span>
          ${dateStr ? `<span class="todo-date-badge"><svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> ${formatDate(dateStr)}</span>` : ''}
        </div>
      </div>
      <div class="todo-actions">
        <button class="todo-action-btn" onclick="openEditModal(${t.id})" title="Edit">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </button>
        <button class="todo-action-btn danger" onclick="deleteTodo(${t.id})" title="Delete">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
      </div>
    </div>`;
}

function formatDate(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr + 'T00:00:00');
  return d.toLocaleDateString('en-US', { month:'short', day:'numeric' });
}

// ── CRUD ───────────────────────────────────────────────
function openAddModal() {
  editId = null;
  document.getElementById('modalTitle').textContent = 'New Task';
  document.getElementById('saveBtn').textContent = 'Save Task';
  document.getElementById('deleteBtn').style.display = 'none';
  document.getElementById('todoForm').reset();
  document.getElementById('fDate').value = selectedDate;
  document.getElementById('fPriority').value = 'medium';
  selectPriority('medium');
  document.getElementById('todoModal').classList.add('open');
  setTimeout(() => document.getElementById('fTitle').focus(), 200);
}

function openEditModal(id) {
  const t = allTodos.find(x => x.id === id);
  if (!t) return;

  editId = id;
  document.getElementById('modalTitle').textContent = 'Edit Task';
  document.getElementById('saveBtn').textContent = 'Update Task';
  document.getElementById('deleteBtn').style.display = '';

  document.getElementById('editId').value = id;
  document.getElementById('fTitle').value = t.title || '';
  document.getElementById('fDesc').value = t.description || '';
  document.getElementById('fDate').value = t.date || selectedDate;
  document.getElementById('fPriority').value = t.priority || 'medium';
  selectPriority(t.priority || 'medium');

  document.getElementById('todoModal').classList.add('open');
}

function closeModal() {
  document.getElementById('todoModal').classList.remove('open');
}

function selectPriority(val) {
  document.querySelectorAll('.prio-opt').forEach(el => {
    el.className = 'prio-opt';
    if (el.dataset.value === val) {
      el.classList.add('active');
      if (val === 'low') el.classList.add('low-active');
      if (val === 'medium') el.classList.add('med-active');
      if (val === 'high') el.classList.add('high-active');
    }
  });
  document.getElementById('fPriority').value = val;
}

async function saveTodo() {
  const title = document.getElementById('fTitle').value.trim();
  if (!title) { showToast('Please enter a task title', 'error'); return; }

  const payload = {
    title: title,
    description: document.getElementById('fDesc').value.trim(),
    date: document.getElementById('fDate').value,
    priority: document.getElementById('fPriority').value,
  };

  try {
    if (editId) {
      await fetch(API_TODOS + '/' + editId, {
        method: 'PUT',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify(payload),
      });
      showToast('Task updated');
    } else {
      await fetch(API_TODOS, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify(payload),
      });
      showToast('Task created');
    }

    closeModal();
    loadTodos();
    loadCalendarData();
  } catch(e) {
    showToast('Failed to save task', 'error');
  }
}

async function toggleTodo(id) {
  try {
    await fetch(API_TODOS + '/' + id + '/toggle', {
      method: 'PUT',
      headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'},
    });
    loadTodos();
    loadCalendarData();
  } catch(e) {
    showToast('Failed to update task', 'error');
  }
}

async function deleteTodo(id) {
  const todoId = id || editId;
  if (!todoId) return;

  if (!confirm('Delete this task?')) return;

  try {
    await fetch(API_TODOS + '/' + todoId, {
      method: 'DELETE',
      headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'},
    });
    showToast('Task deleted');
    closeModal();
    loadTodos();
    loadCalendarData();
  } catch(e) {
    showToast('Failed to delete task', 'error');
  }
}

function filterTodos(filter, btn) {
  currentFilter = filter;
  document.querySelectorAll('.filter-btn').forEach(el => el.classList.remove('active'));
  if (btn) btn.classList.add('active');
  loadTodos();
}

// ── Toast ──────────────────────────────────────────────
function showToast(msg, type = 'success') {
  const t = document.getElementById('todoToast');
  t.textContent = msg;
  t.className = 'todo-toast ' + type;
  t.classList.add('show');
  clearTimeout(t._hide);
  t._hide = setTimeout(() => t.classList.remove('show'), 3000);
}

// ── Helpers ────────────────────────────────────────────
function escHtml(str) {
  if (!str) return '';
  const d = document.createElement('div');
  d.textContent = str;
  return d.innerHTML;
}
</script>
@endsection
