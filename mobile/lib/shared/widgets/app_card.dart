import 'package:flutter/material.dart';
import '../theme/app_colors.dart';

class AppCard extends StatelessWidget {
  final Widget child;
  final EdgeInsetsGeometry? padding;
  final VoidCallback? onTap;
  final Color? color;
  const AppCard({super.key, required this.child, this.padding, this.onTap, this.color});

  @override
  Widget build(BuildContext context) {
    final card = Container(
      padding: padding ?? const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: color ?? Colors.white,
        borderRadius: BorderRadius.circular(14),
        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 8, offset: const Offset(0, 2))],
      ),
      child: child,
    );
    if (onTap != null) return InkWell(onTap: onTap, borderRadius: BorderRadius.circular(14), child: card);
    return card;
  }
}

class StatCard extends StatelessWidget {
  final IconData icon;
  final String value;
  final String label;
  final Color color;
  final Color? bg;
  final String? subtitle;
  final VoidCallback? onTap;
  const StatCard({super.key, required this.icon, required this.value, required this.label, required this.color, this.bg, this.subtitle, this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: bg ?? Colors.white,
          borderRadius: BorderRadius.circular(12),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  width: 32, height: 32,
                  decoration: BoxDecoration(color: color.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(8)),
                  child: Icon(icon, size: 16, color: color),
                ),
                const Spacer(),
                const Icon(Icons.chevron_right, size: 16, color: Color(0xFFBBBBBB)),
              ],
            ),
            const SizedBox(height: 10),
            Text(value, style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: color)),
            if (subtitle != null) Text(subtitle!, style: const TextStyle(fontSize: 11, color: AppColors.textSec)),
            Text(label, style: const TextStyle(fontSize: 11, color: AppColors.textSec)),
          ],
        ),
      ),
    );
  }
}
