import 'package:intl/intl.dart';

String fmtCurrency(double amount) {
  return NumberFormat('#,##0', 'en_US').format(amount);
}

String fmtNum(double value) {
  if (value >= 1000000) return '${(value / 1000000).toStringAsFixed(1)}M';
  if (value >= 1000) return '${(value / 1000).toStringAsFixed(1)}K';
  return value.toStringAsFixed(0);
}

String fmtDate(String? dateStr) {
  if (dateStr == null) return '';
  try {
    final dt = DateTime.parse(dateStr);
    return DateFormat('dd MMM yyyy').format(dt);
  } catch (_) { return dateStr; }
}

String fmtDateShort(String? dateStr) {
  if (dateStr == null) return '';
  try {
    final dt = DateTime.parse(dateStr);
    return DateFormat('dd MMM').format(dt);
  } catch (_) { return dateStr; }
}
