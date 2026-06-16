import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import 'core/api_service.dart';
import 'core/auth_provider.dart';
import 'core/app_router.dart';
import 'shared/theme/app_theme.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await ApiService.init();
  SystemChrome.setPreferredOrientations([DeviceOrientation.portraitUp]);
  runApp(const MannaPOSApp());
}

class MannaPOSApp extends StatelessWidget {
  const MannaPOSApp({super.key});

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider(
      create: (_) => AuthProvider(),
      child: MaterialApp.router(
        title: 'MannaPOS',
        debugShowCheckedModeBanner: false,
        theme: AppTheme.light,
        routerConfig: AppRouter.router,
      ),
    );
  }
}
