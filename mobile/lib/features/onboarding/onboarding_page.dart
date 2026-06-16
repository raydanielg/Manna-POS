import 'dart:async';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../shared/theme/app_colors.dart';

class OnboardingPage extends StatefulWidget {
  const OnboardingPage({super.key});
  @override
  State<OnboardingPage> createState() => _OnboardingPageState();
}

class _OnboardingPageState extends State<OnboardingPage> {
  final PageController _ctrl = PageController();
  int _current = 0;
  bool _showDownload = false;

  final _slides = const [
    _SlideData('Manage Your Business', 'Track sales, inventory, and customers all in one place. Run your business from anywhere.', Icons.store_rounded, 'Point of Sale'),
    _SlideData('Smart Reports', 'Get real-time insights into your business performance with beautiful charts and analytics.', Icons.bar_chart_rounded, 'Analytics'),
    _SlideData('Works Offline', 'Continue working even without internet. Your data syncs automatically when you are back online.', Icons.wifi_off_rounded, 'Offline Mode'),
    _SlideData('Secure & Reliable', 'Your business data is encrypted and backed up. Focus on growing your business with peace of mind.', Icons.shield_rounded, 'Security'),
  ];

  void _next() {
    if (_current < _slides.length - 1) {
      _ctrl.nextPage(duration: const Duration(milliseconds: 350), curve: Curves.easeInOut);
    } else {
      _startDownload();
    }
  }

  void _skip() {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('Skip Onboarding?', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
        content: const Text('You can always view this again. Are you sure?', style: TextStyle(fontSize: 14, color: AppColors.textSec)),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
          ElevatedButton(
            onPressed: () { Navigator.pop(ctx); _startDownload(); },
            style: ElevatedButton.styleFrom(backgroundColor: AppColors.orange, foregroundColor: Colors.white),
            child: const Text('Skip'),
          ),
        ],
      ),
    );
  }

  void _startDownload() => setState(() => _showDownload = true);

  @override
  void dispose() { _ctrl.dispose(); super.dispose(); }

  @override
  Widget build(BuildContext context) {
    if (_showDownload) return const _DownloadScreen();
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: SafeArea(
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
              child: Row(
                children: [
                  Container(
                    width: 40, height: 40,
                    decoration: BoxDecoration(color: AppColors.primary, borderRadius: BorderRadius.circular(12)),
                    child: const Icon(Icons.store_rounded, color: Colors.white, size: 22),
                  ),
                  const SizedBox(width: 10),
                  const Text('MannaPOS', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppColors.primary)),
                  const Spacer(),
                  GestureDetector(onTap: _skip, child: const Text('Skip', style: TextStyle(fontSize: 14, color: AppColors.textSec, fontWeight: FontWeight.w500))),
                ],
              ),
            ),
            Expanded(
              child: PageView.builder(
                controller: _ctrl,
                onPageChanged: (i) => setState(() => _current = i),
                itemCount: _slides.length,
                itemBuilder: (_, i) => _SlideContent(slide: _slides[i]),
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: List.generate(_slides.length, (i) => AnimatedContainer(
                      duration: const Duration(milliseconds: 300),
                      margin: const EdgeInsets.symmetric(horizontal: 4),
                      width: _current == i ? 24 : 8, height: 8,
                      decoration: BoxDecoration(
                        color: _current == i ? AppColors.primary : AppColors.border,
                        borderRadius: BorderRadius.circular(4),
                      ),
                    )),
                  ),
                  const SizedBox(height: 28),
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton(
                          onPressed: _skip,
                          style: OutlinedButton.styleFrom(side: const BorderSide(color: AppColors.border), padding: const EdgeInsets.symmetric(vertical: 16)),
                          child: const Text('Skip', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w600)),
                        ),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        flex: 2,
                        child: ElevatedButton(
                          onPressed: _next,
                          child: Text(_current == _slides.length - 1 ? 'Get Started' : 'Next', style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w600)),
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

class _SlideData {
  final String title, subtitle, badge;
  final IconData icon;
  const _SlideData(this.title, this.subtitle, this.icon, this.badge);
}

class _SlideContent extends StatelessWidget {
  final _SlideData slide;
  const _SlideContent({required this.slide});
  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Column(
        children: [
          const SizedBox(height: 20),
          Expanded(
            child: Center(
              child: Container(
                width: 140, height: 140,
                decoration: BoxDecoration(
                  color: AppColors.primaryLt, borderRadius: BorderRadius.circular(32),
                ),
                child: Icon(slide.icon, size: 64, color: AppColors.primary),
              ),
            ),
          ),
          const SizedBox(height: 24),
          Text(slide.title, textAlign: TextAlign.center, style: const TextStyle(fontSize: 26, fontWeight: FontWeight.w800, color: AppColors.textPri)),
          const SizedBox(height: 12),
          Text(slide.subtitle, textAlign: TextAlign.center, style: const TextStyle(fontSize: 15, color: AppColors.textSec, height: 1.5)),
          const SizedBox(height: 16),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(
              color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12),
              border: Border.all(color: AppColors.primaryLight, width: 1),
            ),
            child: Text(slide.badge, textAlign: TextAlign.center, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: AppColors.primary)),
          ),
        ],
      ),
    );
  }
}

class _DownloadScreen extends StatefulWidget {
  const _DownloadScreen();
  @override
  State<_DownloadScreen> createState() => _DownloadScreenState();
}

class _DownloadScreenState extends State<_DownloadScreen> with SingleTickerProviderStateMixin {
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
    showModalBottomSheet(
      context: context, isDismissible: false, backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        decoration: const BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 60, height: 60,
              decoration: const BoxDecoration(color: AppColors.success, shape: BoxShape.circle),
              child: const Icon(Icons.check_rounded, color: Colors.white, size: 32),
            ),
            const SizedBox(height: 20),
            const Text('Setup Complete!', style: TextStyle(fontSize: 24, fontWeight: FontWeight.w800, color: AppColors.textPri)),
            const SizedBox(height: 8),
            const Text('Your MannaPOS is ready. Lets start selling!', textAlign: TextAlign.center, style: TextStyle(fontSize: 14, color: AppColors.textSec, height: 1.5)),
            const SizedBox(height: 24),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () { Navigator.pop(ctx); context.go('/login'); },
                child: const Text('Get Started', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600)),
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  void dispose() { _spinCtrl.dispose(); _timer?.cancel(); super.dispose(); }

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
                Container(
                  width: 80, height: 80,
                  decoration: BoxDecoration(color: AppColors.primary, borderRadius: BorderRadius.circular(20)),
                  child: const Icon(Icons.store_rounded, color: Colors.white, size: 44),
                ),
                const SizedBox(height: 24),
                RotationTransition(
                  turns: _spinCtrl,
                  child: Icon(Icons.sync_rounded, size: 60,
                    color: _done ? AppColors.success : AppColors.primary),
                ),
                const SizedBox(height: 32),
                ClipRRect(
                  borderRadius: BorderRadius.circular(12),
                  child: LinearProgressIndicator(
                    value: _progress, minHeight: 10,
                    backgroundColor: const Color(0xFFE5E7EB),
                    valueColor: AlwaysStoppedAnimation<Color>(_done ? AppColors.success : AppColors.primary),
                  ),
                ),
                const SizedBox(height: 24),
                Text(_done ? 'Setup Complete!' : 'Setting up MannaPOS',
                  textAlign: TextAlign.center,
                  style: TextStyle(fontSize: 24, fontWeight: FontWeight.w800,
                    color: _done ? AppColors.success : AppColors.primary)),
                const SizedBox(height: 8),
                Text('$pct%', textAlign: TextAlign.center,
                  style: const TextStyle(fontSize: 48, fontWeight: FontWeight.w900, color: AppColors.textPri)),
                const SizedBox(height: 16),
                Text(_done ? 'All done! Taking you to the app...'
                  : 'Setting up your experience. This will only take a moment.',
                  textAlign: TextAlign.center,
                  style: const TextStyle(fontSize: 14, color: AppColors.textSec, height: 1.5)),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
