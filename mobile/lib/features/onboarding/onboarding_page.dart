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

  final _slides = const [
    _SlideData('Manage Your Business', 'Track sales, inventory, and customers all in one place. Run your business from anywhere.', 'assets/images/smartbusiness.png', 'Point of Sale'),
    _SlideData('Smart Reports', 'Get real-time insights into your business performance with beautiful charts and analytics.', 'assets/images/smartreport.png', 'Analytics'),
    _SlideData('Works Offline', 'Continue working even without internet. Your data syncs automatically when you are back online.', 'assets/images/worksoffline.png', 'Offline Mode'),
    _SlideData('Secure & Reliable', 'Your business data is encrypted and backed up. Focus on growing your business with peace of mind.', 'assets/images/secureandscallable.png', 'Security'),
  ];

  void _next() {
    if (_current < _slides.length - 1) {
      _ctrl.nextPage(duration: const Duration(milliseconds: 350), curve: Curves.easeInOut);
    } else {
      _finish();
    }
  }

  void _skip() {
    _finish();
  }

  Future<void> _finish() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool('onboarding_done', true);
    if (!mounted) return;
    context.go('/login');
  }

  @override
  void dispose() { _ctrl.dispose(); super.dispose(); }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: SafeArea(
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
              child: Row(
                children: [
                  ClipRRect(
                    borderRadius: BorderRadius.circular(12),
                    child: Image.asset(
                      'assets/icons/app_logo.png',
                      width: 40,
                      height: 40,
                      fit: BoxFit.cover,
                    ),
                  ),
                  const SizedBox(width: 12),
                  const Text('Manna', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppColors.primary)),
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
  final String title, subtitle, image, badge;
  const _SlideData(this.title, this.subtitle, this.image, this.badge);
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
              child: SizedBox(
                width: 260,
                height: 260,
                child: Image.asset(
                  slide.image,
                  fit: BoxFit.contain,
                  errorBuilder: (context, error, stackTrace) {
                    return const Icon(Icons.broken_image_rounded, size: 64, color: AppColors.textLight);
                  },
                ),
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
