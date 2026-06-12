import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

class StatusBadge extends StatelessWidget {
  final String label;
  final Color color;
  final Color bgColor;
  const StatusBadge({super.key, required this.label, required this.color, required this.bgColor});

  factory StatusBadge.fromStatus(String status) {
    switch (status.toLowerCase()) {
      case 'active': case 'completed': case 'paid': case 'received':
        return StatusBadge(label: status, color: AppColors.success, bgColor: AppColors.successLt);
      case 'pending': case 'partial': case 'draft':
        return StatusBadge(label: status, color: AppColors.warning, bgColor: AppColors.warningLt);
      case 'inactive': case 'cancelled': case 'unpaid':
        return StatusBadge(label: status, color: AppColors.danger, bgColor: AppColors.dangerLt);
      default:
        return StatusBadge(label: status, color: AppColors.primary, bgColor: AppColors.primaryLt);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(color: bgColor, borderRadius: BorderRadius.circular(20)),
      child: Text(label, style: TextStyle(color: color, fontSize: 11, fontWeight: FontWeight.w700)),
    );
  }
}