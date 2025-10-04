#!/usr/bin/env python3
import os
import re

# Get all HTML files in admin directory
admin_dir = '/home/code/payme-2d-gateway/admin'
html_files = [f for f in os.listdir(admin_dir) if f.endswith('.html')]

# Exclude login and forgot-password pages (they don't need navbar)
exclude_files = ['login.html', 'forgot-password.html']
html_files = [f for f in html_files if f not in exclude_files]

print(f"Found {len(html_files)} HTML files to update")

# Navbar script tag to add
navbar_script = '    <script src="load-navbar.js"></script>\n'

updated_count = 0
already_has_count = 0

for html_file in html_files:
    file_path = os.path.join(admin_dir, html_file)
    
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Check if navbar script already exists
        if 'load-navbar.js' in content:
            already_has_count += 1
            print(f"⏭️  {html_file} - Already has navbar script")
            continue
        
        # Find </body> tag and add script before it
        if '</body>' in content:
            # Add script before </body>
            content = content.replace('</body>', navbar_script + '</body>')
            
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(content)
            
            updated_count += 1
            print(f"✅ {html_file} - Added navbar script")
        else:
            print(f"⚠️  {html_file} - No </body> tag found")
    
    except Exception as e:
        print(f"❌ {html_file} - Error: {e}")

print(f"\n📊 Summary:")
print(f"   Total files: {len(html_files)}")
print(f"   Updated: {updated_count}")
print(f"   Already had navbar: {already_has_count}")
print(f"   ✅ Done!")
