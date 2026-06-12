import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../shared/models/user.dart';
import '../shared/constants/app_constants.dart';
import 'api_service.dart';

class AuthProvider extends ChangeNotifier {
  AppUser? _user;
  bool _loading = false;
  String? _savedEmail;
  bool _rememberMe = false;

  AppUser? get user => _user;
  bool get loading => _loading;
  bool get isLoggedIn => _user != null;
  String? get savedEmail => _savedEmail;
  bool get rememberMe => _rememberMe;

  Future<bool> tryAutoLogin() async {
    final prefs = await SharedPreferences.getInstance();
    _savedEmail = prefs.getString('remember_email');
    _rememberMe = prefs.getBool('remember_me') ?? false;
    final token = prefs.getString(AppConstants.tokenKey);
    if (token == null) return false;
    ApiService.setToken(token);
    try {
      final data = await ApiService.get('/auth/user');
      _user = AppUser.fromJson(data);
      notifyListeners();
      return true;
    } catch (_) {
      await _clearAuth();
      return false;
    }
  }

  Future<void> login(String email, String password) async {
    _setLoading(true);
    try {
      final data = await ApiService.post('/auth/login', {'email': email, 'password': password});
      await _saveAuth(data['token'], data['user']);
    } finally { _setLoading(false); }
  }

  Future<void> register(Map<String, dynamic> body) async {
    _setLoading(true);
    try {
      final data = await ApiService.post('/auth/register', body);
      await _saveAuth(data['token'], data['user']);
    } finally { _setLoading(false); }
  }

  Future<void> logout() async {
    try { await ApiService.post('/auth/logout', {}); } catch (_) {}
    await _clearAuth();
  }

  Future<AppUser> updateProfile(Map<String, dynamic> body) async {
    final data = await ApiService.put('/auth/profile', body);
    _user = AppUser.fromJson(data);
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(AppConstants.userKey, jsonEncode(data));
    notifyListeners();
    return _user!;
  }

  Future<void> _saveAuth(String token, dynamic userData) async {
    ApiService.setToken(token);
    _user = AppUser.fromJson(userData);
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(AppConstants.tokenKey, token);
    await prefs.setString(AppConstants.userKey, jsonEncode(userData));
    notifyListeners();
  }

  Future<void> _clearAuth() async {
    ApiService.clearToken();
    _user = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(AppConstants.tokenKey);
    await prefs.remove(AppConstants.userKey);
    notifyListeners();
  }

  void _setLoading(bool v) { _loading = v; notifyListeners(); }
}