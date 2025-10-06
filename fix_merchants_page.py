import re

# Read the file
with open('client-merchants.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Font size replacements for merchants page
replacements = {
    'font-size: 42px': 'font-size: 24px',  # Main header "My Merchants"
    'font-size: 80px': 'font-size: 48px',  # Empty state icon
    'font-size: 48px': 'font-size: 32px',  # Search icon
}

# Apply replacements
for old, new in replacements.items():
    content = content.replace(old, new)

# Write back
with open('client-merchants.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("✓ Fixed client-merchants.html - Reduced large font sizes")
print("  - Main header: 42px → 24px")
print("  - Empty state icon: 80px → 48px")
print("  - Search icon: 48px → 32px")
