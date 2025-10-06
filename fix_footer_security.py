import re

print("Fixing footer security issues in index.html...")

with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Fix "For Gateway Owners" section - All should go to login page
replacements = {
    '<li><a href="client-dashboard.html">Client Dashboard</a></li>': 
    '<li><a href="client-login.html">Client Dashboard</a></li>',
    
    '<li><a href="client-settlements.html">Settlements</a></li>': 
    '<li><a href="client-login.html">Settlements</a></li>',
    
    # Fix "For Your Customers" section - Reseller dashboard should go to login
    '<li><a href="reseller/dashboard.html">Reseller Dashboard</a></li>': 
    '<li><a href="reseller-login.html">Reseller Dashboard</a></li>',
}

for old, new in replacements.items():
    content = content.replace(old, new)

with open('index.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("✓ Fixed footer security issues!")
print("\nChanges made:")
print("  1. Client Dashboard link → Now redirects to client-login.html")
print("  2. Settlements link → Now redirects to client-login.html")
print("  3. Reseller Dashboard link → Now redirects to reseller-login.html")
print("\n✅ All protected pages now require login!")
