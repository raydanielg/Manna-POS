import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/status_badge.dart';
import '../../widgets/glass_card.dart';
import '../../providers/todo_provider.dart';

class CalendarScreen extends StatefulWidget {
  const CalendarScreen({super.key});
  @override State<CalendarScreen> createState() => _CalendarScreenState();
}

class _CalendarScreenState extends State<CalendarScreen> {
  DateTime _currentMonth = DateTime(DateTime.now().year, DateTime.now().month);
  DateTime? _selectedDay;

  @override void initState() {
    super.initState();
    _selectedDay = DateTime.now();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<TodoProvider>().fetchTodos();
      context.read<TodoProvider>().fetchCalendarData();
    });
  }

  void _prevMonth() => setState(() => _currentMonth = DateTime(_currentMonth.year, _currentMonth.month - 1));
  void _nextMonth() => setState(() => _currentMonth = DateTime(_currentMonth.year, _currentMonth.month + 1));

  @override
  Widget build(BuildContext context) {
    final todoProv = context.watch<TodoProvider>();
    final todos = todoProv.todos;
    final calendar = todoProv.calendarData;

    final firstDay = DateTime(_currentMonth.year, _currentMonth.month, 1);
    final lastDay = DateTime(_currentMonth.year, _currentMonth.month + 1, 0);
    final startWeekday = firstDay.weekday % 7;
    final daysInMonth = lastDay.day;

    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Calendar & Tasks')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(children: [
          GlassCard(child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(children: [
              Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                IconButton(onPressed: _prevMonth, icon: const Icon(Icons.chevron_left)),
                Text(DateFormat('MMMM yyyy').format(_currentMonth), style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 18)),
                IconButton(onPressed: _nextMonth, icon: const Icon(Icons.chevron_right)),
              ]),
              const SizedBox(height: 8),
              GridView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(crossAxisCount: 7, childAspectRatio: 1),
                itemCount: startWeekday + daysInMonth,
                itemBuilder: (context, i) {
                  if (i < startWeekday) return const SizedBox();
                  final day = i - startWeekday + 1;
                  final date = DateTime(_currentMonth.year, _currentMonth.month, day);
                  final isSelected = _selectedDay != null && date.day == _selectedDay!.day && date.month == _selectedDay!.month && date.year == _selectedDay!.year;
                  final isToday = date.day == DateTime.now().day && date.month == DateTime.now().month && date.year == DateTime.now().year;

                  final dayData = calendar?.days?.firstWhere(
                    (d) => d.date == DateFormat('yyyy-MM-dd').format(date),
                    orElse: () => throw 'not found',
                  );

                  bool hasError = false;
                  CalendarDay? calDay;
                  try { calDay = dayData; } catch (_) { hasError = true; }

                  final hasTodos = todos.any((t) {
                    try { return t.date == DateFormat('yyyy-MM-dd').format(date); } catch (_) { return false; }
                  });
                  final hasActivities = calDay?.hasActivity ?? false;

                  return GestureDetector(
                    onTap: () => setState(() => _selectedDay = date),
                    child: Container(
                      margin: const EdgeInsets.all(2),
                      decoration: BoxDecoration(
                        color: isSelected ? AppColors.primary : isToday ? AppColors.primaryLt : Colors.transparent,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                        Text('$day', style: TextStyle(
                          fontWeight: isSelected || isToday ? FontWeight.w800 : FontWeight.w500,
                          color: isSelected ? Colors.white : isToday ? AppColors.primary : AppColors.textPri,
                          fontSize: 13,
                        )),
                        const SizedBox(height: 2),
                        Row(mainAxisAlignment: MainAxisAlignment.center, children: [
                          if (hasTodos) Container(width: 5, height: 5, decoration: const BoxDecoration(color: AppColors.primary, shape: BoxShape.circle)),
                          if (hasTodos && hasActivities) const SizedBox(width: 2),
                          if (hasActivities) Container(width: 5, height: 5, decoration: const BoxDecoration(color: AppColors.success, shape: BoxShape.circle)),
                        ]),
                      ]),
                    ),
                  );
                },
              ),
            ]),
          )),
          const SizedBox(height: 16),
          GlassCard(child: Padding(
            padding: const EdgeInsets.all(12),
            child: Row(mainAxisAlignment: MainAxisAlignment.spaceAround, children: [
              _statItem('Total', '${todoProv.todos.length}', AppColors.primary),
              _statItem('Done', '${todoProv.todos.where((t) => t.completed).length}', AppColors.success),
              _statItem('Pending', '${todoProv.todos.where((t) => !t.completed).length}', AppColors.warning),
              _statItem('High Priority', '${todoProv.todos.where((t) {
                try { return t.priority == 'high'; } catch (_) { return false; }
              }).length}', AppColors.danger),
            ]),
          )),
          const SizedBox(height: 16),
          Row(children: [
            const Text('Tasks', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
            const Spacer(),
            Text(DateFormat('MMM d, yyyy').format(_selectedDay ?? DateTime.now()), style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
          ]),
          const SizedBox(height: 8),
          ...todos.where((t) {
            try { return t.date == DateFormat('yyyy-MM-dd').format(_selectedDay ?? DateTime.now()); }
            catch (_) { return false; }
          }).map((todo) => Padding(
            padding: const EdgeInsets.only(bottom: 8),
            child: GlassCard(
              child: ListTile(
                leading: GestureDetector(
                  onTap: () => context.read<TodoProvider>().toggleTodo(todo.id),
                  child: Container(
                    width: 24, height: 24,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: todo.completed ? AppColors.success : Colors.transparent,
                      border: Border.all(color: todo.completed ? AppColors.success : AppColors.border, width: 2),
                    ),
                    child: todo.completed ? const Icon(Icons.check, color: Colors.white, size: 14) : null,
                  ),
                ),
                title: Text(todo.title ?? '', style: TextStyle(
                  fontWeight: FontWeight.w600, fontSize: 14,
                  decoration: todo.completed ? TextDecoration.lineThrough : null,
                  color: todo.completed ? AppColors.textSec : AppColors.textPri,
                )),
                subtitle: todo.description != null && todo.description!.isNotEmpty
                    ? Text(todo.description!, style: const TextStyle(fontSize: 12, color: AppColors.textSec))
                    : null,
                trailing: Row(mainAxisSize: MainAxisSize.min, children: [
                  if (todo.priority != null) _priorityBadge(todo.priority!),
                  const SizedBox(width: 4),
                  IconButton(
                    icon: const Icon(Icons.delete_outline, size: 20, color: AppColors.danger),
                    onPressed: () => context.read<TodoProvider>().deleteTodo(todo.id),
                  ),
                ]),
              ),
            ),
          )),
          if (!todos.any((t) {
            try { return t.date == DateFormat('yyyy-MM-dd').format(_selectedDay ?? DateTime.now()); }
            catch (_) { return false; }
          }))
            Padding(
              padding: const EdgeInsets.all(24),
              child: Text('No tasks for this day', style: const TextStyle(color: AppColors.textSec)),
            ),
          const SizedBox(height: 16),
          SizedBox(
            width: double.infinity,
            child: OutlinedButton.icon(
              onPressed: () => _addTodo(context, todoProv),
              icon: const Icon(Icons.add),
              label: const Text('Add Task'),
            ),
          ),
          const SizedBox(height: 30),
        ]),
      ),
    );
  }

  Widget _statItem(String label, String value, Color color) {
    return Column(children: [
      Text(value, style: TextStyle(fontWeight: FontWeight.w800, fontSize: 18, color: color)),
      Text(label, style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
    ]);
  }

  Widget _priorityBadge(String priority) {
    Color c;
    switch (priority.toLowerCase()) {
      case 'high': c = AppColors.danger; break;
      case 'medium': c = AppColors.warning; break;
      default: c = AppColors.success;
    }
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(color: c.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(10)),
      child: Text(priority, style: TextStyle(color: c, fontSize: 10, fontWeight: FontWeight.w700)),
    );
  }

  void _addTodo(BuildContext context, TodoProvider todoProv) {
    final titleCtrl = TextEditingController();
    final descCtrl = TextEditingController();
    String priority = 'medium';
    final date = _selectedDay ?? DateTime.now();

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setSheetState) => Container(
          decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
          padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
          child: Padding(
            padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
            child: Column(mainAxisSize: MainAxisSize.min, children: [
              Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
              const SizedBox(height: 20),
              Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                const Text('Add Task', style: TextStyle(fontSize: 19, fontWeight: FontWeight.w800)),
                IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(ctx)),
              ]),
              TextFormField(controller: titleCtrl, decoration: const InputDecoration(labelText: 'Title', prefixIcon: Icon(Icons.task, size: 20)), autofocus: true),
              const SizedBox(height: 12),
              TextFormField(controller: descCtrl, decoration: const InputDecoration(labelText: 'Description', prefixIcon: Icon(Icons.description, size: 20))),
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                value: priority,
                decoration: const InputDecoration(labelText: 'Priority', prefixIcon: Icon(Icons.flag, size: 20)),
                items: ['low', 'medium', 'high'].map((p) => DropdownMenuItem(value: p, child: Text(p[0].toUpperCase() + p.substring(1)))).toList(),
                onChanged: (v) => setSheetState(() => priority = v!),
              ),
              const SizedBox(height: 12),
              Text('Date: ${DateFormat('MMM d, yyyy').format(date)}', style: const TextStyle(color: AppColors.textSec)),
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity, height: 52,
                child: ElevatedButton(
                  onPressed: () {
                    if (titleCtrl.text.trim().isEmpty) return;
                    todoProv.createTodo(titleCtrl.text.trim(), description: descCtrl.text.trim(), date: DateFormat('yyyy-MM-dd').format(date), priority: priority);
                    Navigator.pop(ctx);
                  },
                  child: const Text('Add Task'),
                ),
              ),
            ]),
          ),
        ),
      ),
    );
  }
}
