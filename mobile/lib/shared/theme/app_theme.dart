import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

class AppColors {
  static const primary    = Color(0xFF2563EB);
  static const primaryDk  = Color(0xFF1D4ED8);
  static const primaryLt  = Color(0xFFEFF6FF);
  static const secondary  = Color(0xFF7C3AED);
  static const success    = Color(0xFF10B981);
  static const successLt  = Color(0xFFECFDF5);
  static const warning    = Color(0xFFF59E0B);
  static const warningLt  = Color(0xFFFFFBEB);
  static const danger     = Color(0xFFEF4444);
  static const dangerLt   = Color(0xFFFEF2F2);
  static const bg         = Color(0xFFF1F5F9);
  static const surface    = Color(0xFFFFFFFF);
  static const textPri    = Color(0xFF1E293B);
  static const textSec    = Color(0xFF64748B);
  static const border     = Color(0xFFE2E8F0);
  static const divider    = Color(0xFFF1F5F9);
}

class AppTheme {
  static ThemeData get light => ThemeData(
    useMaterial3: true,
    colorScheme: ColorScheme.fromSeed(seedColor: AppColors.primary, brightness: Brightness.light),
    scaffoldBackgroundColor: AppColors.bg,
    appBarTheme: const AppBarTheme(
      backgroundColor: AppColors.primary,
      foregroundColor: Colors.white,
      elevation: 0,
      centerTitle: false,
      systemOverlayStyle: SystemUiOverlayStyle(
        statusBarColor: AppColors.primaryDk,
        statusBarIconBrightness: Brightness.light,
      ),
      titleTextStyle: TextStyle(fontSize: 18, fontWeight: FontWeight.w700, color: Colors.white),
    ),
    cardTheme: CardThemeData(
      color: AppColors.surface,
      elevation: 0,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.all(Radius.circular(16))),
      margin: EdgeInsets.zero,
    ),
    inputDecorationTheme: InputDecorationTheme(
      filled: true, fillColor: AppColors.surface,
      contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      border: OutlineInputBorder(borderRadius: BorderRadius.all(Radius.circular(12)), borderSide: BorderSide(color: AppColors.border)),
      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.all(Radius.circular(12)), borderSide: BorderSide(color: AppColors.border)),
      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.all(Radius.circular(12)), borderSide: BorderSide(color: AppColors.primary, width: 2)),
      errorBorder: OutlineInputBorder(borderRadius: BorderRadius.all(Radius.circular(12)), borderSide: BorderSide(color: AppColors.danger)),
      labelStyle: TextStyle(color: AppColors.textSec),
    ),
    elevatedButtonTheme: ElevatedButtonThemeData(
      style: ElevatedButton.styleFrom(
        backgroundColor: AppColors.primary, foregroundColor: Colors.white,
        elevation: 0, padding: EdgeInsets.symmetric(vertical: 14),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.all(Radius.circular(12))),
        textStyle: TextStyle(fontSize: 15, fontWeight: FontWeight.w600),
      ),
    ),
    outlinedButtonTheme: OutlinedButtonThemeData(
      style: OutlinedButton.styleFrom(
        foregroundColor: AppColors.primary,
        side: BorderSide(color: AppColors.primary),
        padding: EdgeInsets.symmetric(vertical: 14),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.all(Radius.circular(12))),
        textStyle: TextStyle(fontSize: 15, fontWeight: FontWeight.w600),
      ),
    ),
    bottomNavigationBarTheme: const BottomNavigationBarThemeData(
      backgroundColor: Colors.white,
      selectedItemColor: AppColors.primary,
      unselectedItemColor: AppColors.textSec,
      type: BottomNavigationBarType.fixed,
      elevation: 12,
      selectedLabelStyle: TextStyle(fontSize: 11, fontWeight: FontWeight.w600),
      unselectedLabelStyle: TextStyle(fontSize: 11),
    ),
    floatingActionButtonTheme: const FloatingActionButtonThemeData(
      backgroundColor: AppColors.primary, foregroundColor: Colors.white, elevation: 4,
    ),
    chipTheme: ChipThemeData(
      backgroundColor: AppColors.primaryLt, labelStyle: TextStyle(color: AppColors.primary, fontSize: 12, fontWeight: FontWeight.w500),
      padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
    ),
    dividerTheme: const DividerThemeData(color: AppColors.divider, space: 1),
    textTheme: const TextTheme(
      headlineLarge: TextStyle(fontSize: 24, fontWeight: FontWeight.w700, color: AppColors.textPri),
      headlineMedium: TextStyle(fontSize: 20, fontWeight: FontWeight.w700, color: AppColors.textPri),
      titleLarge: TextStyle(fontSize: 18, fontWeight: FontWeight.w700, color: AppColors.textPri),
      titleMedium: TextStyle(fontSize: 16, fontWeight: FontWeight.w600, color: AppColors.textPri),
      titleSmall: TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: AppColors.textPri),
      bodyLarge: TextStyle(fontSize: 15, color: AppColors.textPri),
      bodyMedium: TextStyle(fontSize: 14, color: AppColors.textPri),
      bodySmall: TextStyle(fontSize: 12, color: AppColors.textSec),
      labelLarge: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: AppColors.textSec),
    ),
  );
}