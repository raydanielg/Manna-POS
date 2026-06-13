import 'dart:async';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../shared/theme/app_theme.dart';

class OnboardingPage extends StatefulWidget {
  const OnboardingPage({super.key});
  @override
  State<OnboardingPage> createState() => _OnboardingPageState();
}

class _OnboardingPageState extends State<OnboardingPage> {
  final PageController _ctrl = PageController();
  int _current = 0;
  bool _showDownload = false;

  static const _slides = [
    _SlideData(
      icon: Icons.point_of_sale_rounded,
      accentColor: Color(0xFF2563EB),
      bgColor: Color(0xFFEFF6FF),
      featureIcons: [
        _FeatureIcon(Icons.label_rounded, Color(0xFF10B981)),
        _FeatureIcon(Icons.shopping_cart_rounded, Color(0xFFEF4444)),
        _FeatureIcon(Icons.people_alt_rounded, Color(0xFF2563EB)),
        _FeatureIcon(Icons.receipt_long_rounded, Color(0xFF6366F1)),
        _FeatureIcon(Icons.payments_rounded, Color(0xFF10B981)),
        _FeatureIcon(Icons.inventory_2_rounded, Color(0xFFF59E0B)),
      ],
      badgeText: 'All In One Place',
      title: 'Business on Your Mobile',
      subtitle: 'Manage your POS, inventory & customers easily from your mobile at your fingertip.',
    ),
    _SlideData(
      icon: Icons.bar_chart_rounded,
      accentColor: Color(0xFF10B981),
      bgColor: Color(0xFFECFDF5),
      featureIcons: [],
      badgeText: '',
      title: 'Insightful Sales Reports',
      subtitle: 'Make smarter business decisions with real-time sales and performance reports.',
      isChart: true,
    ),
    _SlideData(
      icon: Icons.wifi_off_rounded,
      accentColor: Color(0xFF6366F1),
      bgColor: Color(0xFFF0F0FF),
      featureIcons: [],
      badgeText: '',
      title: 'Works Offline & Online',
      subtitle: 'Run your business anytime seamlessly even without an internet connection.',
      isOffline: true,
    ),
    _SlideData(
      icon: Icons.lock_rounded,
      accentColor: Color(0xFF10B981),
      bgColor: Color(0xFFECFDF5),
      featureIcons: [
        _FeatureIcon(Icons.cloud_upload_rounded, Color(0xFF10B981)),
        _FeatureIcon(Icons.support_agent_rounded, Color(0xFF2563EB)),
        _FeatureIcon(Icons.card_giftcard_rounded, Color(0xFFF59E0B)),
        _FeatureIcon(Icons.volunteer_activism_rounded, Color(0xFFEF4444)),
      ],
      badgeText: '',
      title: 'Secure & Reliable',
      subtitle: 'Your data is securely stored and backed up which you can recover anytime.',
    ),
  ];

  void _next() {
    if (_current < _slides.length - 1) {
      _ctrl.nextPage(duration: const Duration(milliseconds: 350), curve: Curves.easeInOut);
    } else {
      _startDownload();
    }
  }

  void _skip() => _startDownload();

  void _startDownload() {
    setState(() => _showDownload = true);
  }

  @override
  void dispose() {
    _ctrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (_showDownload) return _DownloadDataScreen();

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: SafeArea(
        child: Column(
          children: [
            // Header with logo + language
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
              child: Row(
                children: [
                  Container(
                    width: 36, height: 36,
                    decoration: BoxDecoration(
                      color: AppColors.primaryLt,
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: const Icon(Icons.point_of_sale_rounded, color: AppColors.primary, size: 20),
                  ),
                  const SizedBox(width: 10),
                  const Text('MannaPOS', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppColors.textPri)),
                  const Spacer(),
                  GestureDetector(
                    onTap: _skip,
                    child: const Text('Skip', style: TextStyle(fontSize: 14, color: AppColors.textSec, fontWeight: FontWeight.w500)),
                  ),
                ],
              ),
            ),

            // Slides
            Expanded(
              child: PageView.builder(
                controller: _ctrl,
                onPageChanged: (i) => setState(() => _current = i),
                itemCount: _slides.length,
                itemBuilder: (_, i) => _SlidePage(slide: _slides[i]),
              ),
            ),

            // Bottom section: dots + buttons
            Padding(
              padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
              child: Column(
                children: [
                  // Dot indicators
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: List.generate(_slides.length, (i) => AnimatedContainer(
                      duration: const Duration(milliseconds: 300),
                      margin: const EdgeInsets.symmetric(horizontal: 4),
                      width: _current == i ? 24 : 8,
                      height: 8,
                      decoration: BoxDecoration(
                        color: _current == i ? AppColors.primary : AppColors.border,
                        borderRadius: BorderRadius.circular(4),
                      ),
                    )),
                  ),
                  const SizedBox(height: 28),
                  // Buttons row
                  Row(
                    children: [
                      // Skip / Back button
                      Expanded(
                        child: OutlinedButton(
                          onPressed: _skip,
                          style: OutlinedButton.styleFrom(
                            side: const BorderSide(color: AppColors.border),
                            foregroundColor: AppColors.textSec,
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                          ),
                          child: const Text('Skip', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w600)),
                        ),
                      ),
                      const SizedBox(width: 16),
                      // Next / Get Started button
                      Expanded(
                        flex: 2,
                        child: ElevatedButton(
                          onPressed: _next,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppColors.primary,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            elevation: 0,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                          ),
                          child: Text(
                            _current == _slides.length - 1 ? 'Get Started' : 'Next',
                            style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w600),
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ─── Slide Page ────────────────────────────────────────────────────────────────
class _SlidePage extends StatelessWidget {
  final _SlideData slide;
  const _SlidePage({required this.slide});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Column(
        children: [
          const SizedBox(height: 8),
          // Phone mockup area
          Expanded(
            child: Stack(
              alignment: Alignment.center,
              children: [
                // Decorative sparkle top-right
                Positioned(
                  top: 10, right: 10,
                  child: _Sparkle(color: const Color(0xFF06B6D4)),
                ),
                // Decorative circle bottom-right
                Positioned(
                  bottom: 20, right: 20,
                  child: Container(
                    width: 14, height: 14,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      border: Border.all(color: const Color(0xFF2563EB), width: 2),
                    ),
                  ),
                ),
                // Decorative triangle left
                Positioned(
                  left: 10, top: 100,
                  child: _Triangle(color: const Color(0xFFF97316).withOpacity(0.7)),
                ),
                // Phone frame
                _PhoneFrame(slide: slide),
              ],
            ),
          ),
          const SizedBox(height: 24),
          // Title
          Text(
            slide.title,
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: AppColors.textPri),
          ),
          const SizedBox(height: 10),
          // Subtitle
          Text(
            slide.subtitle,
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 14, color: AppColors.textSec, height: 1.55),
          ),
          const SizedBox(height: 16),
        ],
      ),
    );
  }
}

// ─── Phone Frame ───────────────────────────────────────────────────────────────
class _PhoneFrame extends StatelessWidget {
  final _SlideData slide;
  const _PhoneFrame({required this.slide});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 220,
      height: 280,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: const Color(0xFFE5E7EB), width: 2),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.06), blurRadius: 24, offset: const Offset(0, 8)),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(26),
        child: Column(
          children: [
            // Mini status bar
            Container(
              height: 28,
              color: Colors.white,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text('9:41', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: AppColors.textPri)),
                  Row(children: const [
                    Icon(Icons.signal_cellular_alt, size: 12, color: AppColors.textPri),
                    SizedBox(width: 3),
                    Icon(Icons.wifi, size: 12, color: AppColors.textPri),
                  ]),
                ],
              ),
            ),
            Expanded(
              child: Container(
                color: const Color(0xFFF8F9FA),
                child: _buildContent(),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildContent() {
    if (slide.isChart) return _ChartContent();
    if (slide.isOffline) return _OfflineContent();

    // Default: icon grid
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          // Lock / main icon at top
          Container(
            width: 52, height: 52,
            decoration: BoxDecoration(
              color: slide.bgColor,
              shape: BoxShape.circle,
            ),
            child: Icon(slide.icon, color: slide.accentColor, size: 26),
          ),
          const SizedBox(height: 12),
          if (slide.featureIcons.isNotEmpty) ...[
            GridView.count(
              crossAxisCount: 2,
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              mainAxisSpacing: 10,
              crossAxisSpacing: 10,
              childAspectRatio: 1.4,
              children: slide.featureIcons.map((fi) => Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(10),
                  boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 4)],
                ),
                child: Icon(fi.icon, color: fi.color, size: 22),
              )).toList(),
            ),
            if (slide.badgeText.isNotEmpty) ...[
              const SizedBox(height: 10),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 6)],
                ),
                child: Row(mainAxisSize: MainAxisSize.min, children: [
                  const Icon(Icons.check_circle_rounded, size: 14, color: Color(0xFF10B981)),
                  const SizedBox(width: 5),
                  Text(slide.badgeText, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: AppColors.textPri)),
                ]),
              ),
            ],
          ],
        ],
      ),
    );
  }
}

// ─── Chart Content (slide 2) ───────────────────────────────────────────────────
class _ChartContent extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    const bars = [
      _BarData(0.35, Color(0xFF10B981)),
      _BarData(0.55, Color(0xFFEF4444)),
      _BarData(0.70, Color(0xFF2563EB)),
      _BarData(0.90, Color(0xFFF59E0B)),
    ];
    return Padding(
      padding: const EdgeInsets.fromLTRB(14, 14, 14, 10),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(width: 80, height: 8, decoration: BoxDecoration(color: const Color(0xFFE5E7EB), borderRadius: BorderRadius.circular(4))),
          const SizedBox(height: 4),
          Container(width: 55, height: 8, decoration: BoxDecoration(color: const Color(0xFFE5E7EB), borderRadius: BorderRadius.circular(4))),
          const SizedBox(height: 16),
          Expanded(
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.end,
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: bars.map((b) => Expanded(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 4),
                  child: FractionallySizedBox(
                    heightFactor: b.height,
                    alignment: Alignment.bottomCenter,
                    child: Container(
                      decoration: BoxDecoration(
                        color: b.color,
                        borderRadius: const BorderRadius.vertical(top: Radius.circular(5)),
                      ),
                    ),
                  ),
                ),
              )).toList(),
            ),
          ),
          const SizedBox(height: 8),
          // Donut badge
          Align(
            alignment: Alignment.centerRight,
            child: Container(
              width: 44, height: 44,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: Colors.white,
                boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.08), blurRadius: 6)],
              ),
              child: const Center(
                child: Text('%', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: Color(0xFF10B981))),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

// ─── Offline Content (slide 3) ─────────────────────────────────────────────────
class _OfflineContent extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Stack(
            alignment: Alignment.bottomCenter,
            children: [
              const Icon(Icons.wifi_rounded, size: 46, color: Color(0xFF94A3B8)),
              Positioned(
                bottom: 0, right: 0,
                child: Container(
                  width: 18, height: 18,
                  decoration: const BoxDecoration(color: Color(0xFFEF4444), shape: BoxShape.circle),
                  child: const Icon(Icons.warning_rounded, size: 12, color: Colors.white),
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          ...List.generate(4, (i) => Padding(
            padding: const EdgeInsets.symmetric(vertical: 4),
            child: Row(children: [
              const Icon(Icons.check_rounded, size: 14, color: Color(0xFF10B981)),
              const SizedBox(width: 8),
              Expanded(child: Container(
                height: 8,
                decoration: BoxDecoration(color: const Color(0xFFE5E7EB), borderRadius: BorderRadius.circular(4)),
              )),
            ]),
          )),
        ],
      ),
    );
  }
}

// ─── Download Data Screen ──────────────────────────────────────────────────────
class _DownloadDataScreen extends StatefulWidget {
  @override
  State<_DownloadDataScreen> createState() => _DownloadDataScreenState();
}

class _DownloadDataScreenState extends State<_DownloadDataScreen> with SingleTickerProviderStateMixin {
  double _progress = 0;
  late AnimationController _spinCtrl;
  Timer? _timer;
  bool _done = false;

  @override
  void initState() {
    super.initState();
    _spinCtrl = AnimationController(vsync: this, duration: const Duration(seconds: 2))..repeat();
    _startProgress();
  }

  void _startProgress() {
    _timer = Timer.periodic(const Duration(milliseconds: 80), (t) {
      if (!mounted) { t.cancel(); return; }
      setState(() => _progress += 0.008);
      if (_progress >= 1.0) {
        t.cancel();
        setState(() { _progress = 1.0; _done = true; });
        Future.delayed(const Duration(milliseconds: 600), _finish);
      }
    });
  }

  Future<void> _finish() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool('onboarding_done', true);
    if (!mounted) return;
    Navigator.pushReplacementNamed(context, '/login');
  }

  @override
  void dispose() {
    _spinCtrl.dispose();
    _timer?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final pct = (_progress * 100).toInt();
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: SafeArea(
        child: Center(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 40),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                // Cloud + spin illustration
                SizedBox(
                  width: 200, height: 200,
                  child: Stack(
                    alignment: Alignment.center,
                    children: [
                      // Cloud background
                      Container(
                        width: 140, height: 110,
                        decoration: BoxDecoration(
                          color: const Color(0xFFEFF6FF),
                          borderRadius: BorderRadius.circular(60),
                        ),
                      ),
                      // Spinning arrows
                      RotationTransition(
                        turns: _spinCtrl,
                        child: Icon(
                          Icons.sync_rounded,
                          size: 52,
                          color: _done ? AppColors.success : const Color(0xFF94A3B8),
                        ),
                      ),
                      // Cart icon below cloud
                      Positioned(
                        bottom: 20,
                        child: Container(
                          width: 36, height: 36,
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(8),
                            boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.06), blurRadius: 8)],
                          ),
                          child: const Icon(Icons.point_of_sale_rounded, color: AppColors.primary, size: 20),
                        ),
                      ),
                      // Sparkle decoration
                      const Positioned(
                        top: 10, right: 20,
                        child: _Sparkle(color: Color(0xFF10B981)),
                      ),
                      // Circle decoration
                      Positioned(
                        bottom: 30, right: 10,
                        child: Container(
                          width: 12, height: 12,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            border: Border.all(color: AppColors.primary, width: 2),
                          ),
                        ),
                      ),
                      // Triangle
                      const Positioned(
                        left: 10, top: 80,
                        child: _Triangle(color: Color(0xFFFB923C)),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 20),

                // Progress bar
                ClipRRect(
                  borderRadius: BorderRadius.circular(8),
                  child: LinearProgressIndicator(
                    value: _progress,
                    minHeight: 8,
                    backgroundColor: AppColors.border,
                    valueColor: AlwaysStoppedAnimation<Color>(_done ? AppColors.success : AppColors.primary),
                  ),
                ),

                const SizedBox(height: 28),

                Text(
                  _done ? 'Download Complete! (100%)' : 'Downloading your Data ($pct%)',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w800,
                    color: _done ? AppColors.success : AppColors.textPri,
                  ),
                ),
                const SizedBox(height: 12),
                Text(
                  _done
                    ? 'All done! Taking you to the app...'
                    : 'We are downloading your data to your mobile phone for you to use offline. This might take some minutes to complete, please don\'t close or switch the app while downloading.',
                  textAlign: TextAlign.center,
                  style: const TextStyle(fontSize: 13, color: AppColors.textSec, height: 1.6),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

// ─── Data Models ───────────────────────────────────────────────────────────────
class _SlideData {
  final IconData icon;
  final Color accentColor;
  final Color bgColor;
  final List<_FeatureIcon> featureIcons;
  final String badgeText;
  final String title;
  final String subtitle;
  final bool isChart;
  final bool isOffline;

  const _SlideData({
    required this.icon,
    required this.accentColor,
    required this.bgColor,
    required this.featureIcons,
    required this.badgeText,
    required this.title,
    required this.subtitle,
    this.isChart = false,
    this.isOffline = false,
  });
}

class _FeatureIcon {
  final IconData icon;
  final Color color;
  const _FeatureIcon(this.icon, this.color);
}

class _BarData {
  final double height;
  final Color color;
  const _BarData(this.height, this.color);
}

// ─── Decorative widgets ────────────────────────────────────────────────────────
class _Sparkle extends StatelessWidget {
  final Color color;
  const _Sparkle({required this.color});
  @override
  Widget build(BuildContext context) {
    return CustomPaint(painter: _SparklePainter(color), size: const Size(20, 20));
  }
}

class _SparklePainter extends CustomPainter {
  final Color color;
  _SparklePainter(this.color);
  @override
  void paint(Canvas canvas, Size size) {
    final p = Paint()..color = color..strokeWidth = 2..strokeCap = StrokeCap.round;
    final cx = size.width / 2; final cy = size.height / 2;
    canvas.drawLine(Offset(cx, 0), Offset(cx, size.height), p);
    canvas.drawLine(Offset(0, cy), Offset(size.width, cy), p);
    canvas.drawLine(Offset(cx - 5, cy - 5), Offset(cx + 5, cy + 5), p..strokeWidth = 1.5);
    canvas.drawLine(Offset(cx + 5, cy - 5), Offset(cx - 5, cy + 5), p);
  }
  @override
  bool shouldRepaint(_) => false;
}

class _Triangle extends StatelessWidget {
  final Color color;
  const _Triangle({required this.color});
  @override
  Widget build(BuildContext context) {
    return CustomPaint(painter: _TrianglePainter(color), size: const Size(14, 14));
  }
}

class _TrianglePainter extends CustomPainter {
  final Color color;
  _TrianglePainter(this.color);
  @override
  void paint(Canvas canvas, Size size) {
    canvas.drawPath(
      Path()
        ..moveTo(0, size.height)
        ..lineTo(size.width / 2, 0)
        ..lineTo(size.width, size.height)
        ..close(),
      Paint()..color = color,
    );
  }
  @override
  bool shouldRepaint(_) => false;
}
