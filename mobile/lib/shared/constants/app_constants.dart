class AppConstants {
  // App Info
  static const String appName = 'MannaPOS';
  static const String appVersion = '1.0.0';
  static const String appDescription = 'Point of Sale System';
  
  // API
  static const String baseUrl = 'http://localhost:8000/api';
  static const int connectionTimeout = 30000;
  static const int receiveTimeout = 30000;
  
  // Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  static const String themeKey = 'theme_mode';
  static const String languageKey = 'language';
  
  // Routes
  static const String splashRoute = '/splash';
  static const String loginRoute = '/login';
  static const String registerRoute = '/register';
  static const String forgotPasswordRoute = '/forgot-password';
  static const String dashboardRoute = '/dashboard';
  static const String posRoute = '/pos';
  static const String productsRoute = '/products';
  static const String salesRoute = '/sales';
  static const String settingsRoute = '/settings';
  
  // Validation
  static const int minPasswordLength = 8;
  static const int maxPasswordLength = 32;
  static const int minUsernameLength = 3;
  static const int maxUsernameLength = 20;
  
  // Pagination
  static const int defaultPageSize = 20;
  static const int maxPageSize = 100;
}
