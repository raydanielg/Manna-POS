#!/usr/bin/env python3
import yaml
import os

try:
    file_path = 'mobile/pubspec.yaml'
    if not os.path.exists(file_path):
        print(f"❌ File not found: {file_path}")
        exit(1)
        
    with open(file_path, 'r') as file:
        content = file.read()
        
    try:
        data = yaml.safe_load(content)
        print('✅ pubspec.yaml is valid YAML')
        print('Name: ${data["name"]}')
        if 'flutter' in data and 'assets' in data['flutter']:
            print('Assets configured: ${data["flutter"]["assets"]}')
        else:
            print('❌ No assets configured in flutter section')
            
        # Check if logo.png exists
        if os.path.exists('mobile/assets/images/logo.png'):
            size = os.path.getsize('mobile/assets/images/logo.png')
            print("✅ Logo file exists: ${size} bytes")
        else:
            print("❌ Logo file not found: mobile/assets/images/logo.png")
            
    except yaml.YAMLError as e:
        print(f'❌ YAML parsing error: $e')
        
except Exception as e:
    print(f'❌ Error: $e')
