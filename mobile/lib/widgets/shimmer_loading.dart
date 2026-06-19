import 'package:flutter/material.dart';
import '../shared/theme/app_theme.dart';

class ShimmerLoading extends StatefulWidget {
  final int itemCount;
  final double itemHeight;
  final EdgeInsetsGeometry? padding;
  final bool isGrid;

  const ShimmerLoading({
    super.key,
    this.itemCount = 6,
    this.itemHeight = 100,
    this.padding,
    this.isGrid = false,
  });

  @override
  State<ShimmerLoading> createState() => _ShimmerLoadingState();
}

class _ShimmerLoadingState extends State<ShimmerLoading> with SingleTickerProviderStateMixin {
  late AnimationController _controller;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(vsync: this, duration: const Duration(milliseconds: 1500))..repeat();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: _controller,
      builder: (context, child) {
        final opacity = 0.3 + (_controller.value * 0.4);
        return _buildShimmer(opacity);
      },
    );
  }

  Widget _buildShimmer(double opacity) {
    final shimmerColor = Colors.grey.withValues(alpha: opacity);

    if (widget.isGrid) {
      return Padding(
        padding: widget.padding ?? const EdgeInsets.all(16),
        child: GridView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            crossAxisSpacing: 12,
            mainAxisSpacing: 12,
            childAspectRatio: 1.3,
          ),
          itemCount: widget.itemCount,
          itemBuilder: (_, i) => _shimmerCard(shimmerColor),
        ),
      );
    }

    return Padding(
      padding: widget.padding ?? const EdgeInsets.all(16),
      child: Column(
        children: List.generate(widget.itemCount, (i) => Padding(
          padding: const EdgeInsets.only(bottom: 12),
          child: _shimmerCard(shimmerColor),
        )),
      ),
    );
  }

  Widget _shimmerCard(Color color) {
    return Container(
      height: widget.itemHeight,
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(14),
      ),
      padding: const EdgeInsets.all(16),
      child: Row(
        children: [
          Container(width: 44, height: 44, decoration: BoxDecoration(color: color.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(12))),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(height: 14, width: double.infinity, decoration: BoxDecoration(color: color.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(8))),
                const SizedBox(height: 8),
                Container(height: 10, width: 140, decoration: BoxDecoration(color: color.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(8))),
              ],
            ),
          ),
          Container(height: 14, width: 80, decoration: BoxDecoration(color: color.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(8))),
        ],
      ),
    );
  }
}
