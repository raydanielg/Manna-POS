import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/auth_provider.dart';

class MyBusinessPage extends StatefulWidget {
  const MyBusinessPage({super.key});
  @override
  State<MyBusinessPage> createState() => _MyBusinessPageState();
}

class _MyBusinessPageState extends State<MyBusinessPage> {
  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    const bool isDark = true; // Set dark styling as in user screenshot

    // Theme Colors
    final Color bgColor = isDark ? const Color(0xFF111827) : const Color(0xFFF4F5F7);
    final Color cardBg = isDark ? const Color(0xFF1F2937) : Colors.white;
    final Color borderColor = isDark ? const Color(0xFF374151) : const Color(0xFFE4E4E7);
    final Color textPrimary = isDark ? Colors.white : const Color(0xFF111827);
    final Color textSecondary = isDark ? const Color(0xFF9CA3AF) : const Color(0xFF6B7280);

    final String name = user?.businessName ?? 'My Business';
    final String type = user?.businessType ?? 'Sole Proprietor';
    final String country = user?.businessCountry ?? 'Tanzania';
    final String currency = user?.currency ?? 'TZS';
    final String address = user?.businessAddress ?? 'Mlimani City';
    final String city = user?.businessCity ?? 'Dar es Salaam';
    final String website = 'https://manna.co.tz/${name.toLowerCase().replaceAll(' ', '')}';

    return Scaffold(
      backgroundColor: bgColor,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios_new_rounded, color: textPrimary, size: 18),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Business',
          style: TextStyle(color: textPrimary, fontSize: 18, fontWeight: FontWeight.bold),
        ),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
        physics: const BouncingScrollPhysics(),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header Top Card
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 16),
              decoration: BoxDecoration(
                color: cardBg,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: borderColor),
              ),
              child: Column(
                children: [
                  Container(
                    width: 64,
                    height: 64,
                    decoration: BoxDecoration(
                      color: const Color(0xFF10B981).withValues(alpha: 0.15),
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(Icons.storefront_rounded, color: Color(0xFF10B981), size: 32),
                  ),
                  const SizedBox(height: 16),
                  Text(
                    name,
                    textAlign: TextAlign.center,
                    style: TextStyle(fontSize: 20, fontWeight: FontWeight.w800, color: textPrimary, letterSpacing: -0.5),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    type,
                    style: TextStyle(fontSize: 14, color: textSecondary, fontWeight: FontWeight.w500),
                  ),
                  const SizedBox(height: 12),
                  // VERIFIED Badge
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
                    decoration: BoxDecoration(
                      color: const Color(0xFF10B981).withValues(alpha: 0.12),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: const Text(
                      'VERIFIED',
                      style: TextStyle(color: Color(0xFF10B981), fontSize: 11, fontWeight: FontWeight.w800, letterSpacing: 0.5),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // DETAILS Section
            _sectionHeader('DETAILS', textSecondary),
            const SizedBox(height: 10),
            Container(
              decoration: BoxDecoration(
                color: cardBg,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: borderColor),
              ),
              padding: const EdgeInsets.symmetric(vertical: 4, horizontal: 16),
              child: Column(
                children: [
                  _detailRow('Business Name', name, textPrimary, textSecondary),
                  _divider(borderColor),
                  _detailRow('Type', type, textPrimary, textSecondary),
                  _divider(borderColor),
                  _detailRow('Address', '$address, $city', textPrimary, textSecondary),
                  _divider(borderColor),
                  _detailRow('Website', website, textPrimary, textSecondary, isUrl: true),
                  _divider(borderColor),
                  _detailRow('Country', country == 'Tanzania' ? 'TZ' : country, textPrimary, textSecondary),
                  _divider(borderColor),
                  _detailRow('Currency', currency, textPrimary, textSecondary),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // TRANSACTION LIMITS Section
            _sectionHeader('TRANSACTION LIMITS', textSecondary),
            const SizedBox(height: 10),
            Container(
              decoration: BoxDecoration(
                color: cardBg,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: borderColor),
              ),
              padding: const EdgeInsets.symmetric(vertical: 4, horizontal: 16),
              child: Column(
                children: [
                  _detailRow('Min Collection', '500 $currency', textPrimary, textSecondary),
                  _divider(borderColor),
                  _detailRow('Max Collection', '1,000,000 $currency', textPrimary, textSecondary),
                  _divider(borderColor),
                  _detailRow('Daily Collection', '3,000,000 $currency', textPrimary, textSecondary),
                  _divider(borderColor),
                  _detailRow('Min Payout', '5,000 $currency', textPrimary, textSecondary),
                  _divider(borderColor),
                  _detailRow('Max Payout', '1,000,000 $currency', textPrimary, textSecondary),
                  _divider(borderColor),
                  _detailRow('Daily Payout', '3,000,000 $currency', textPrimary, textSecondary),
                ],
              ),
            ),
            const SizedBox(height: 32),
          ],
        ),
      ),
    );
  }

  Widget _sectionHeader(String text, Color color) {
    return Padding(
      padding: const EdgeInsets.only(left: 4),
      child: Text(
        text,
        style: TextStyle(fontSize: 12, fontWeight: FontWeight.w800, color: color, letterSpacing: 0.5),
      ),
    );
  }

  Widget _detailRow(String label, String value, Color textPrimary, Color textSecondary, {bool isUrl = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 14),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: TextStyle(fontSize: 14, fontWeight: FontWeight.w500, color: textSecondary),
          ),
          const SizedBox(width: 24),
          Expanded(
            child: Text(
              value,
              textAlign: TextAlign.end,
              overflow: TextOverflow.ellipsis,
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: isUrl ? const Color(0xFF10B981) : textPrimary,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _divider(Color borderColor) {
    return Divider(height: 1, thickness: 1, color: borderColor);
  }
}
