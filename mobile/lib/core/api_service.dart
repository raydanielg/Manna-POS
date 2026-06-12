import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../shared/constants/app_constants.dart';

class ApiException implements Exception {
  final String message;
  final int statusCode;
  ApiException(this.message, this.statusCode);
  @override String toString() => message;
}

class ApiService {
  static String? _token;

  static Future<void> init() async {
    final prefs = await SharedPreferences.getInstance();
    _token = prefs.getString(AppConstants.tokenKey);
  }

  static void setToken(String token) => _token = token;
  static void clearToken() => _token = null;

  static Map<String, String> get _headers => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    if (_token != null) 'Authorization': 'Bearer $_token',
  };

  static Uri _uri(String path) => Uri.parse('${AppConstants.baseUrl}$path');

  static Future<dynamic> get(String path) async {
    final res = await http.get(_uri(path), headers: _headers)
        .timeout(const Duration(seconds: AppConstants.timeout));
    return _handle(res);
  }

  static Future<dynamic> post(String path, Map<String, dynamic> body) async {
    final res = await http.post(_uri(path), headers: _headers, body: jsonEncode(body))
        .timeout(const Duration(seconds: AppConstants.timeout));
    return _handle(res);
  }

  static Future<dynamic> put(String path, Map<String, dynamic> body) async {
    final res = await http.put(_uri(path), headers: _headers, body: jsonEncode(body))
        .timeout(const Duration(seconds: AppConstants.timeout));
    return _handle(res);
  }

  static Future<dynamic> delete(String path) async {
    final res = await http.delete(_uri(path), headers: _headers)
        .timeout(const Duration(seconds: AppConstants.timeout));
    return _handle(res);
  }

  static Future<dynamic> postMultipart(String path, Map<String, String> fields, {String? filePath, String? fileField = 'image'}) async {
    final request = http.MultipartRequest('POST', _uri(path));
    request.headers.addAll({
      'Accept': 'application/json',
      if (_token != null) 'Authorization': 'Bearer $_token',
    });
    request.fields.addAll(fields);
    if (filePath != null && fileField != null) {
      request.files.add(await http.MultipartFile.fromPath(fileField, filePath));
    }
    final streamed = await request.send().timeout(const Duration(seconds: AppConstants.timeout));
    final res = await http.Response.fromStream(streamed);
    return _handle(res);
  }

  static Future<dynamic> putMultipart(String path, Map<String, String> fields, {String? filePath, String? fileField = 'image'}) async {
    final request = http.MultipartRequest('POST', _uri(path));
    request.headers.addAll({
      'Accept': 'application/json',
      if (_token != null) 'Authorization': 'Bearer $_token',
    });
    request.fields['_method'] = 'PUT';
    request.fields.addAll(fields);
    if (filePath != null && fileField != null) {
      request.files.add(await http.MultipartFile.fromPath(fileField, filePath));
    }
    final streamed = await request.send().timeout(const Duration(seconds: AppConstants.timeout));
    final res = await http.Response.fromStream(streamed);
    return _handle(res);
  }

  static dynamic _handle(http.Response res) {
    dynamic data;
    try { data = jsonDecode(res.body); } catch (_) { data = {}; }
    if (res.statusCode >= 200 && res.statusCode < 300) return data;
    final msg = data is Map ? (data['message'] ?? 'Request failed') : 'Request failed';
    throw ApiException(msg.toString(), res.statusCode);
  }
}