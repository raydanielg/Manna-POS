import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

class AppCard extends StatelessWidget {
  final Widget child;
  final EdgeInsetsGeometry? padding;
  final Color? color;
  final VoidCallback? onTap;
  final double radius;
  const AppCard({super.key, required this.child, this.padding, this.color, this.onTap, this.radius = 16});
  @override
  Widget build(BuildContext context) {
    final card = Container(
      decoration: BoxDecoration(
        color: color ?? AppColors.surface,
        borderRadius: BorderRadius.circular(radius),
        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.06), blurRadius: 12, offset: const Offset(0, 4))],
      ),
      child: padding != null ? Padding(padding: padding!, child: child) : child,
    );
    if (onTap != null) {
      return ClipRRect(borderRadius: BorderRadius.circular(radius),
        child: Material(color: Colors.transparent, child: InkWell(onTap: onTap, child: card)));
    }
    return card;
  }
}

class StatCard extends StatelessWidget {
  final String label;
  final String value;
  final IconData icon;
  final Color color;
  final Color? bgColor;
  final String? subtitle;
  const StatCard({super.key, required this.label, required this.value, required this.icon, required this.color, this.bgColor, this.subtitle});
  @override
  Widget build(BuildContext context) {
    return AppCard(
      padding: const EdgeInsets.all(16),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          Container(padding: const EdgeInsets.all(10), decoration: BoxDecoration(color: bgColor ?? color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(12)),
            child: Icon(icon, color: color, size: 22)),
          const Spacer(),
        ]),
        const SizedBox(height: 12),
        Text(value, style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: AppColors.textPri)),
        const SizedBox(height: 2),
        Text(label, style: const TextStyle(fontSize: 12, color: AppColors.textSec, fontWeight: FontWeight.w500)),
        if (subtitle != null) ...[const SizedBox(height: 4), Text(subtitle!, style: TextStyle(fontSize: 11, color: color, fontWeight: FontWeight.w600))],
      ]),
    );
  }
}