import os
import json

# Check if pubspec.yaml exists
if not os.path.exists('pubspec.yaml'):
    print("❌ pubspec.yaml not found in current directory")
    exit(1)

# Read pubspec.yaml
with open('pubspec.yaml', 'r') as f:
    content = f.read()

# Basic syntax check
if "publish_to:" not in content:
    print("❌ Missing 'publish_to:' field")
    exit(1)

if "flutter:" not in content:
    print("❌ Missing 'flutter:' section")
    exit(1)

# Check for basic YAML structure
lines = content.split('\n')
for i, line in enumerate(lines, 1):
    # Check for obvious YAML syntax errors
    if ':' in line:
        key, value = line.split(':', 1)
        key = key.strip()
        value = value.strip()
        
        # Check for unclosed quotes
        if ('"' in value and value.count('"') % 2 != 0) or ("'" in value and value.count("'") % 2 != 0):
            print(f"❌ Line $i: Unclosed quotes in: {line}")
            exit(1)

print("✅ pubspec.yaml syntax looks good!")

# Check assets configuration
flutter_section = False
assets_found = False
for i, line in enumerate(lines):
    if line.strip().startswith('flutter:'):
        flutter_section = True
    elif flutter_section and line.strip().startswith('assets:'):
        assets_found = True
        # Check if there are assets listed
        j = i + 1
        while j < len(lines) and (lines[j].strip() == '' or lines[j].startswith('  ')):
            if 'assets/images/' in lines[j] or 'assets/icons/' in lines[j]:
                print("✅ Assets configured correctly")
                exit(0)
            j += 1
        break

if not assets_found:
    print("❌ Assets not configured in flutter section")
    print("Expected to find:")
    print("  assets:")
    print("    - assets/images/")
    print("    - assets/icons/")
    exit(1)
