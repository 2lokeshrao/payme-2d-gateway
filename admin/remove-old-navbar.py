#!/usr/bin/env python3
import os
import re

# Get all HTML files in admin directory
admin_dir = '/home/code/payme-2d-gateway/admin'
html_files = [f for f in os.listdir(admin_dir) if f.endswith('.html')]

# Exclude component files
exclude_files = ['login.html', 'forgot-password.html', 'admin-navbar.html', 'crypto-compact.html']
html_files = [f for f in html_files if f not in exclude_files]

print(f"Found {len(html_files)} HTML files to update")

updated_count = 0

for html_file in html_files:
    file_path = os.path.join(admin_dir, html_file)
    
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_content = content
        
        # Remove old navbar - Pattern 1: <nav class="admin-nav">...</nav>
        # Use regex to find and remove navbar block
        content = re.sub(
            r'<nav class="admin-nav">.*?</nav>',
            '',
            content,
            flags=re.DOTALL
        )
        
        # Check if content changed
        if content != original_content:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(content)
            
            updated_count += 1
            print(f"✅ {html_file} - Removed old navbar")
        else:
            print(f"⏭️  {html_file} - No old navbar found")
    
    except Exception as e:
        print(f"❌ {html_file} - Error: {e}")

print(f"\n📊 Summary:")
print(f"   Total files: {len(html_files)}")
print(f"   Updated: {updated_count}")
print(f"   ✅ Done!")
