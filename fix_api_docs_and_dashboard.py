import re

# Fix API Docs page - reduce large fonts
print("Fixing api-docs.html...")
with open('api-docs.html', 'r', encoding='utf-8') as f:
    content = f.read()

replacements = {
    'font-size: 48px': 'font-size: 24px',  # Main header
    'font-size: 26px': 'font-size: 18px',  # Section headers
}

for old, new in replacements.items():
    content = content.replace(old, new)

with open('api-docs.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("✓ Fixed api-docs.html")
print("  - Main header: 48px → 24px")
print("  - Section headers: 26px → 18px")

# Fix Dashboard page - API Keys button redirect
print("\nFixing client-dashboard.html...")
with open('client-dashboard.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Find and fix the API Keys button onclick
# It should go to client-settings.html (where API Keys section is) not api-docs.html
content = content.replace(
    "onclick=\"window.location.href='api-docs.html'\"",
    "onclick=\"window.location.href='client-settings.html'\""
)

with open('client-dashboard.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("✓ Fixed client-dashboard.html")
print("  - API Keys button now redirects to client-settings.html (where API Keys are)")
print("  - Previously was incorrectly redirecting to api-docs.html")

print("\n✅ All fixes completed!")
