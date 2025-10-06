import re

# Read the file
with open('merchant-register.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Font size replacements for merchant registration page
replacements = {
    'font-size: 50px': 'font-size: 32px',  # Success icon
    'font-size: 48px': 'font-size: 32px',  # Loading icon
    'font-size: 32px': 'font-size: 20px',  # Headers
    'font-size: 24px': 'font-size: 18px',  # Section headers
}

# Apply replacements
for old, new in replacements.items():
    content = content.replace(old, new)

# Write back
with open('merchant-register.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("✓ Fixed merchant-register.html - Reduced large font sizes")
print("  - Success icon: 50px → 32px")
print("  - Loading icon: 48px → 32px")
print("  - Main headers: 32px → 20px")
print("  - Section headers: 24px → 18px")
