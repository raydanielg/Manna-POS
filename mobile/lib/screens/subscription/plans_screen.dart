import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/status_badge.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../../providers/plan_provider.dart';

class PlansScreen extends StatefulWidget {
  const PlansScreen({super.key});
  @override State<PlansScreen> createState() => _PlansScreenState();
}

class _PlansScreenState extends State<PlansScreen> {
  bool _yearly = false;

  @override void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<PlanProvider>().fetchPlans();
      context.read<PlanProvider>().fetchSubscription();
    });
  }

  @override
  Widget build(BuildContext context) {
    final planProv = context.watch<PlanProvider>();
    final plans = planProv.plans;
    final subscription = planProv.subscription;

    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Subscription Plans')),
      body: planProv.isLoading
          ? const Center(child: CircularProgressIndicator())
          : planProv.error != null
              ? Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                  const Icon(Icons.error_outline, size: 48, color: AppColors.danger),
                  const SizedBox(height: 12),
                  Text(planProv.error!, style: const TextStyle(color: AppColors.danger)),
                  const SizedBox(height: 12),
                  ElevatedButton(onPressed: () { planProv.fetchPlans(); planProv.fetchSubscription(); }, child: const Text('Retry')),
                ]))
              : SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: Column(children: [
                    if (subscription != null) ...[
                      GlassCard(
                        child: Container(
                          decoration: BoxDecoration(
                            gradient: LinearGradient(colors: [AppColors.primary, AppColors.purple], begin: Alignment.topLeft, end: Alignment.bottomRight),
                            borderRadius: BorderRadius.circular(14),
                          ),
                          padding: const EdgeInsets.all(20),
                          child: Column(children: [
                            Row(children: [
                              Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                Text('Current Plan', style: const TextStyle(color: Colors.white70, fontSize: 12)),
                                Text(subscription.planName ?? '', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 20)),
                              ])),
                              StatusBadge(label: subscription.status ?? '', color: Colors.white, bgColor: Colors.white.withValues(alpha: 0.2)),
                            ]),
                            const SizedBox(height: 12),
                            Row(children: [
                              Icon(Icons.calendar_today, color: Colors.white70, size: 14),
                              const SizedBox(width: 6),
                              Text('Expires: ${subscription.expiryDate ?? 'N/A'}', style: const TextStyle(color: Colors.white70, fontSize: 13)),
                              const Spacer(),
                              Text('${subscription.daysRemaining ?? 0} days left', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w700, fontSize: 13)),
                            ]),
                          ]),
                        ),
                      ),
                      const SizedBox(height: 20),
                    ],
                    Row(children: [
                      Expanded(
                        child: GestureDetector(
                          onTap: () => setState(() => _yearly = false),
                          child: Container(
                            padding: const EdgeInsets.symmetric(vertical: 12),
                            decoration: BoxDecoration(
                              color: !_yearly ? AppColors.primary : Colors.white,
                              borderRadius: const BorderRadius.horizontal(left: Radius.circular(10)),
                              border: Border.all(color: AppColors.primary),
                            ),
                            child: Text('Monthly', textAlign: TextAlign.center, style: TextStyle(color: !_yearly ? Colors.white : AppColors.textSec, fontWeight: FontWeight.w700, fontSize: 14)),
                          ),
                        ),
                      ),
                      Expanded(
                        child: GestureDetector(
                          onTap: () => setState(() => _yearly = true),
                          child: Container(
                            padding: const EdgeInsets.symmetric(vertical: 12),
                            decoration: BoxDecoration(
                              color: _yearly ? AppColors.primary : Colors.white,
                              borderRadius: const BorderRadius.horizontal(right: Radius.circular(10)),
                              border: Border.all(color: AppColors.primary),
                            ),
                            child: Text('Yearly', textAlign: TextAlign.center, style: TextStyle(color: _yearly ? Colors.white : AppColors.textSec, fontWeight: FontWeight.w700, fontSize: 14)),
                          ),
                        ),
                      ),
                    ]),
                    const SizedBox(height: 20),
                    ...plans.map((plan) {
                      final price = _yearly ? plan.yearlyPrice : plan.monthlyPrice;
                      final isPopular = plan.isPopular;
                      final isFree = (price ?? 0) <= 0;
                      return Padding(
                        padding: const EdgeInsets.only(bottom: 14),
                        child: GlassCard(
                          child: Container(
                            decoration: BoxDecoration(
                              border: isPopular ? Border.all(color: AppColors.primary, width: 2) : null,
                              borderRadius: BorderRadius.circular(14),
                            ),
                            padding: const EdgeInsets.all(20),
                            child: Column(children: [
                              if (isPopular) Container(
                                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                                decoration: BoxDecoration(
                                  gradient: const LinearGradient(colors: [AppColors.primary, AppColors.purple]),
                                  borderRadius: BorderRadius.circular(20),
                                ),
                                child: const Text('Most Popular', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700, fontSize: 11)),
                              ),
                              if (isPopular) const SizedBox(height: 12),
                              Text(plan.name ?? '', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 22)),
                              const SizedBox(height: 4),
                              Text(plan.description ?? '', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
                              const SizedBox(height: 12),
                              Row(mainAxisAlignment: MainAxisAlignment.center, crossAxisAlignment: CrossAxisAlignment.start, children: [
                                Text('TSh ', style: TextStyle(color: AppColors.textPri, fontSize: 16, fontWeight: FontWeight.w600)),
                                Text('${price ?? 0}', style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 36, color: AppColors.primary)),
                                Text(' /${_yearly ? 'yr' : 'mo'}', style: const TextStyle(color: AppColors.textSec, fontSize: 14)),
                              ]),
                              const SizedBox(height: 16),
                              ...(plan.features ?? []).map((f) => Padding(
                                padding: const EdgeInsets.symmetric(vertical: 4),
                                child: Row(children: [
                                  Icon(Icons.check_circle, color: AppColors.success, size: 18),
                                  const SizedBox(width: 8),
                                  Expanded(child: Text(f.toString(), style: const TextStyle(fontSize: 14))),
                                ]),
                              )),
                              const SizedBox(height: 20),
                              SizedBox(
                                width: double.infinity, height: 50,
                                child: ElevatedButton(
                                  onPressed: () {
                                    if (isFree) {
                                      planProv.subscribe(plan.id, isYearly: _yearly);
                                    } else {
                                      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Redirecting to checkout...')));
                                    }
                                  },
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: isPopular ? AppColors.primary : Colors.white,
                                    foregroundColor: isPopular ? Colors.white : AppColors.primary,
                                    side: isPopular ? null : const BorderSide(color: AppColors.primary),
                                  ),
                                  child: Text(isFree ? 'Activate Free' : 'Subscribe'),
                                ),
                              ),
                            ]),
                          ),
                        ),
                      );
                    }),
                    const SizedBox(height: 30),
                  ]),
                ),
    );
  }
}
