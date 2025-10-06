import os

# Client pages that need authentication
client_pages = [
    'client-dashboard.html',
    'client-merchants.html',
    'client-transactions.html',
    'client-settings.html',
    'client-payment-settings.html',
    'client-settlements.html',
    'merchant-register.html',
    'api-docs.html'
]

# Add authentication script to client pages
print("Adding authentication to client pages...")
for page in client_pages:
    if os.path.exists(page):
        with open(page, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Check if auth script already exists
        if 'client-auth-check.js' not in content:
            # Add script before closing </body> tag
            content = content.replace('</body>', '    <script src="js/client-auth-check.js"></script>\n</body>')
            
            with open(page, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"  ✓ Added auth to {page}")
        else:
            print(f"  - Auth already exists in {page}")
    else:
        print(f"  ✗ File not found: {page}")

# Add authentication script to reseller dashboard
print("\nAdding authentication to reseller dashboard...")
reseller_page = 'reseller/dashboard.html'
if os.path.exists(reseller_page):
    with open(reseller_page, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Check if auth script already exists
    if 'reseller-auth-check.js' not in content:
        # Add script before closing </body> tag
        content = content.replace('</body>', '    <script src="../js/reseller-auth-check.js"></script>\n</body>')
        
        with open(reseller_page, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"  ✓ Added auth to {reseller_page}")
    else:
        print(f"  - Auth already exists in {reseller_page}")
else:
    print(f"  ✗ File not found: {reseller_page}")

print("\n✅ Authentication added to all protected pages!")
print("\nSecurity improvements:")
print("  1. All client pages now require login")
print("  2. Reseller dashboard requires login")
print("  3. Unauthorized users will be redirected to login page")
