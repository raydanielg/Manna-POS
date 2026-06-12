class AppUser {
  final int id;
  final String name;
  final String email;
  final String role;
  AppUser({required this.id, required this.name, required this.email, required this.role});
  factory AppUser.fromJson(Map<String, dynamic> j) =>
      AppUser(id: j['id'], name: j['name'], email: j['email'], role: j['role'] ?? 'user');
  String get initials => name.trim().split(' ').map((w) => w.isNotEmpty ? w[0] : '').take(2).join().toUpperCase();
}