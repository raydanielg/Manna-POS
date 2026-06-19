import 'package:flutter/material.dart';
import '../shared/theme/app_colors.dart';

class AppBottomSheet {
  static void show(BuildContext context, {
    required String title,
    required Widget child,
    bool dismissible = true,
    double? initialHeight,
  }) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      barrierDismissible: dismissible,
      builder: (_) => _AppBottomSheetContent(
        title: title,
        child: child,
        initialHeight: initialHeight,
      ),
    );
  }
}

class _AppBottomSheetContent extends StatefulWidget {
  final String title;
  final Widget child;
  final double? initialHeight;

  const _AppBottomSheetContent({
    required this.title,
    required this.child,
    this.initialHeight,
  });

  @override
  State<_AppBottomSheetContent> createState() => _AppBottomSheetContentState();
}

class _AppBottomSheetContentState extends State<_AppBottomSheetContent> {
  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        boxShadow: [
          BoxShadow(color: Colors.black12, blurRadius: 20, offset: Offset(0, -4)),
        ],
      ),
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: DraggableScrollableSheet(
        expand: false,
        initialChildSize: widget.initialHeight ?? 0.85,
        minChildSize: 0.3,
        maxChildSize: 0.95,
        builder: (_, controller) => SingleChildScrollView(
          controller: controller,
          padding: const EdgeInsets.fromLTRB(24, 12, 24, 32),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Center(
                child: Container(
                  width: 40, height: 4,
                  decoration: BoxDecoration(
                    color: AppColors.border,
                    borderRadius: BorderRadius.circular(4),
                  ),
                ),
              ),
              const SizedBox(height: 20),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(widget.title, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700, color: AppColors.textPri)),
                  GestureDetector(
                    onTap: () => Navigator.pop(context),
                    child: Container(
                      padding: const EdgeInsets.all(6),
                      decoration: BoxDecoration(
                        color: AppColors.surfaceVariant,
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: const Icon(Icons.close, size: 18, color: AppColors.textSec),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 20),
              widget.child,
            ],
          ),
        ),
      ),
    );
  }
}
