import yaml

try {
  final file = File('mobile/pubspec.yaml');
  final content = file.readAsStringSync();
  final data = yaml.safeLoad(content);
  print('✅ pubspec.yaml is valid YAML');
  print('Name: ${data['name']}');
  print('Assets configured: ${data['flutter']['assets']}');
} on Exception catch (e) {
  print('❌ Error parsing pubspec.yaml: $e');
}