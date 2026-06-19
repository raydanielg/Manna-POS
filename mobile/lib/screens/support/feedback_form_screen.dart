import 'package:flutter/material.dart';
import '../../shared/theme/app_colors.dart';
import '../../widgets/glass_card.dart';
import '../../core/api_service.dart';

class FeedbackFormScreen extends StatefulWidget {
  const FeedbackFormScreen({super.key});
  @override State<FeedbackFormScreen> createState() => _FeedbackFormScreenState();
}

class _FeedbackFormScreenState extends State<FeedbackFormScreen> {
  final _subjectCtrl = TextEditingController();
  final _messageCtrl = TextEditingController();
  String _category = 'Support';
  String _priority = 'Medium';
  bool _saving = false;

  static const _categories = ['Bug', 'Feature Request', 'Support', 'Other'];
  static const _priorities = ['Low', 'Medium', 'High'];

  @override void dispose() { _subjectCtrl.dispose(); _messageCtrl.dispose(); super.dispose(); }

  Future<void> _submit() async {
    if (_subjectCtrl.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Please enter a subject'), backgroundColor: AppColors.danger));
      return;
    }
    if (_messageCtrl.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Please enter a message'), backgroundColor: AppColors.danger));
      return;
    }
    setState(() => _saving = true);
    try {
      await ApiService.post('/support/feedback', {
        'subject': _subjectCtrl.text.trim(),
        'category': _category,
        'priority': _priority.toLowerCase(),
        'message': _messageCtrl.text.trim(),
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Feedback submitted'), backgroundColor: AppColors.success));
        Navigator.pop(context);
      }
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e'), backgroundColor: AppColors.danger));
    }
    if (mounted) setState(() => _saving = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Submit Feedback')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: GlassCard(
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(children: [
              TextFormField(controller: _subjectCtrl, decoration: const InputDecoration(labelText: 'Subject', prefixIcon: Icon(Icons.subject, size: 20))),
              const SizedBox(height: 14),
              DropdownButtonFormField<String>(
                value: _category,
                decoration: const InputDecoration(labelText: 'Category', prefixIcon: Icon(Icons.category, size: 20)),
                items: _categories.map((c) => DropdownMenuItem(value: c, child: Text(c))).toList(),
                onChanged: (v) => setState(() => _category = v!),
              ),
              const SizedBox(height: 14),
              DropdownButtonFormField<String>(
                value: _priority,
                decoration: const InputDecoration(labelText: 'Priority', prefixIcon: Icon(Icons.flag, size: 20)),
                items: _priorities.map((p) => DropdownMenuItem(value: p, child: Row(children: [
                  Container(width: 10, height: 10, decoration: BoxDecoration(
                    color: p == 'High' ? AppColors.danger : p == 'Medium' ? AppColors.warning : AppColors.success,
                    shape: BoxShape.circle,
                  )),
                  const SizedBox(width: 8),
                  Text(p),
                ]))).toList(),
                onChanged: (v) => setState(() => _priority = v!),
              ),
              const SizedBox(height: 14),
              TextFormField(controller: _messageCtrl, decoration: const InputDecoration(labelText: 'Message', alignLabelWithHint: true, prefixIcon: Padding(padding: EdgeInsets.only(bottom: 80), child: Icon(Icons.message, size: 20))), maxLines: 6),
              const SizedBox(height: 14),
              SizedBox(
                width: double.infinity,
                child: OutlinedButton.icon(
                  onPressed: () => ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Image attachment placeholder'))),
                  icon: const Icon(Icons.image_outlined),
                  label: const Text('Attach Image'),
                ),
              ),
              const SizedBox(height: 24),
              SizedBox(
                width: double.infinity, height: 52,
                child: ElevatedButton(
                  onPressed: _saving ? null : _submit,
                  child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : const Text('Submit', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
                ),
              ),
            ]),
          ),
        ),
      ),
    );
  }
}
