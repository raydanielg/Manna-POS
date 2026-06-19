import 'package:flutter/material.dart';
import '../shared/theme/app_colors.dart';

class MiniLineChart extends StatelessWidget {
  final List<num> data;
  final Color? color;
  final double height;

  const MiniLineChart({
    super.key,
    required this.data,
    this.color,
    this.height = 60,
  });

  @override
  Widget build(BuildContext context) {
    if (data.isEmpty) return SizedBox(height: height);
    final c = color ?? AppColors.primary;
    final maxVal = data.map((e) => e.toDouble()).reduce((a, b) => a > b ? a : b);
    final minVal = data.map((e) => e.toDouble()).reduce((a, b) => a < b ? a : b);
    final range = maxVal - minVal > 0 ? maxVal - minVal : 1.0;

    return SizedBox(
      height: height,
      child: CustomPaint(
        size: Size.infinite,
        painter: _LineChartPainter(
          data: data.map((e) => e.toDouble()).toList(),
          color: c,
          minVal: minVal,
          range: range,
        ),
      ),
    );
  }
}

class _LineChartPainter extends CustomPainter {
  final List<double> data;
  final Color color;
  final double minVal;
  final double range;

  _LineChartPainter({required this.data, required this.color, required this.minVal, required this.range});

  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = color
      ..strokeWidth = 2.5
      ..strokeCap = StrokeCap.round
      ..style = PaintingStyle.stroke;

    final fillPaint = Paint()
      ..shader = LinearGradient(
        begin: Alignment.topCenter,
        end: Alignment.bottomCenter,
        colors: [color.withValues(alpha: 0.3), color.withValues(alpha: 0.0)],
      ).createShader(Rect.fromLTWH(0, 0, size.width, size.height));

    final path = Path();
    final fillPath = Path();
    final stepX = size.width / (data.length - 1);

    for (int i = 0; i < data.length; i++) {
      final x = i * stepX;
      final y = size.height - ((data[i] - minVal) / range) * (size.height - 20) - 10;
      if (i == 0) {
        path.moveTo(x, y);
        fillPath.moveTo(x, size.height);
        fillPath.lineTo(x, y);
      } else {
        path.lineTo(x, y);
        fillPath.lineTo(x, y);
      }
    }
    fillPath.lineTo((data.length - 1) * stepX, size.height);
    fillPath.close();

    canvas.drawPath(fillPath, fillPaint);
    canvas.drawPath(path, paint);

    for (int i = 0; i < data.length; i++) {
      final x = i * stepX;
      final y = size.height - ((data[i] - minVal) / range) * (size.height - 20) - 10;
      canvas.drawCircle(Offset(x, y), 3, Paint()..color = color);
    }
  }

  @override
  bool shouldRepaint(covariant _LineChartPainter old) => old.data != data;
}

class BarChartWidget extends StatelessWidget {
  final List<BarChartItem> items;
  final double height;
  final Color? defaultColor;

  const BarChartWidget({
    super.key,
    required this.items,
    this.height = 180,
    this.defaultColor,
  });

  @override
  Widget build(BuildContext context) {
    if (items.isEmpty) return SizedBox(height: height);
    final maxVal = items.map((e) => e.value).reduce((a, b) => a > b ? a : b);

    return SizedBox(
      height: height + 30,
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.end,
        children: items.map((item) {
          final pct = maxVal > 0 ? item.value / maxVal : 0.0;
          return Expanded(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 3),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  if (item.value > 0)
                    Text(
                      item.value >= 1000
                          ? '${(item.value / 1000).toStringAsFixed(1)}K'
                          : item.value.toStringAsFixed(0),
                      style: const TextStyle(fontSize: 9, color: AppColors.textSec, fontWeight: FontWeight.w600),
                    ),
                  const SizedBox(height: 4),
                  AnimatedContainer(
                    duration: const Duration(milliseconds: 500),
                    height: (height - 20) * pct + 6,
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [
                          item.color ?? defaultColor ?? AppColors.primary,
                          (item.color ?? defaultColor ?? AppColors.primary).withValues(alpha: 0.6),
                        ],
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                      ),
                      borderRadius: const BorderRadius.vertical(top: Radius.circular(6)),
                      boxShadow: [
                        BoxShadow(
                          color: (item.color ?? AppColors.primary).withValues(alpha: 0.3),
                          blurRadius: 6,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    item.label,
                    style: const TextStyle(fontSize: 10, color: AppColors.textSec, fontWeight: FontWeight.w600),
                    textAlign: TextAlign.center,
                    overflow: TextOverflow.ellipsis,
                    maxLines: 1,
                  ),
                ],
              ),
            ),
          );
        }).toList(),
      ),
    );
  }
}

class BarChartItem {
  final String label;
  final double value;
  final Color? color;

  BarChartItem({required this.label, required this.value, this.color});
}

class DonutChartWidget extends StatelessWidget {
  final List<DonutChartItem> items;
  final double size;
  final double strokeWidth;
  final String? centerText;
  final String? centerSubText;

  const DonutChartWidget({
    super.key,
    required this.items,
    this.size = 160,
    this.strokeWidth = 28,
    this.centerText,
    this.centerSubText,
  });

  @override
  Widget build(BuildContext context) {
    final total = items.fold(0.0, (sum, item) => sum + item.value);
    if (total == 0 || items.isEmpty) {
      return SizedBox(
        height: size,
        child: Center(child: Text('No data', style: TextStyle(color: Colors.grey.shade400))),
      );
    }

    return SizedBox(
      height: size + 50,
      child: Column(
        children: [
          SizedBox(
            height: size,
            width: size,
            child: Stack(
              alignment: Alignment.center,
              children: [
                CustomPaint(
                  size: Size(size, size),
                  painter: _DonutPainter(
                    items: items,
                    total: total,
                    strokeWidth: strokeWidth,
                  ),
                ),
                Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    if (centerText != null)
                      Text(centerText!, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: AppColors.textPri)),
                    if (centerSubText != null)
                      Text(centerSubText!, style: const TextStyle(fontSize: 11, color: AppColors.textSec)),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          Wrap(
            spacing: 16,
            runSpacing: 8,
            alignment: WrapAlignment.center,
            children: items.map((item) => _legendItem(item)).toList(),
          ),
        ],
      ),
    );
  }

  Widget _legendItem(DonutChartItem item) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(width: 10, height: 10, decoration: BoxDecoration(color: item.color, borderRadius: BorderRadius.circular(3))),
        const SizedBox(width: 6),
        Text('${item.label} (${item.value.toStringAsFixed(0)})', style: const TextStyle(fontSize: 11, color: AppColors.textSec, fontWeight: FontWeight.w500)),
      ],
    );
  }
}

class DonutChartItem {
  final String label;
  final double value;
  final Color color;

  DonutChartItem({required this.label, required this.value, required this.color});
}

class _DonutPainter extends CustomPainter {
  final List<DonutChartItem> items;
  final double total;
  final double strokeWidth;

  _DonutPainter({required this.items, required this.total, required this.strokeWidth});

  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2);
    final radius = (size.width - strokeWidth) / 2;
    final rect = Rect.fromCircle(center: center, radius: radius);

    double startAngle = -0.5 * 3.1415927;

    for (final item in items) {
      final sweepAngle = (item.value / total) * 2 * 3.1415927;
      final paint = Paint()
        ..color = item.color
        ..strokeWidth = strokeWidth
        ..style = PaintingStyle.stroke
        ..strokeCap = StrokeCap.round;

      if (sweepAngle > 0.01) {
        canvas.drawArc(rect, startAngle, sweepAngle, false, paint);
      }
      startAngle += sweepAngle;
    }

    final bgPaint = Paint()
      ..color = Colors.grey.withValues(alpha: 0.1)
      ..strokeWidth = strokeWidth
      ..style = PaintingStyle.stroke;

    if (startAngle < 2 * 3.1415927 - 0.01) {
      canvas.drawArc(rect, startAngle, 2 * 3.1415927 - startAngle, false, bgPaint);
    }
  }

  @override
  bool shouldRepaint(covariant _DonutPainter old) => old.items != items || old.total != total;
}
