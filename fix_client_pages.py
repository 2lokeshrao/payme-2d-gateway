import re

# Font size replacements - reduce all large fonts
font_replacements = {
    'font-size: 36px': 'font-size: 24px',
    'font-size: 32px': 'font-size: 20px',
    'font-size: 28px': 'font-size: 18px',
    'font-size: 24px': 'font-size: 18px',
    'font-size: 20px': 'font-size: 16px',
    'font-size: 18px': 'font-size: 16px',
    'font-size: 17px': 'font-size: 14px',
}

# Padding replacements - reduce excessive padding
padding_replacements = {
    'padding: 40px': 'padding: 24px',
    'padding: 35px': 'padding: 24px',
    'padding: 30px': 'padding: 20px',
    'padding: 25px': 'padding: 20px',
}

files_to_fix = [
    'client-transactions.html',
    'client-merchants.html',
    'client-settings.html',
    'client-payment-settings.html',
    'api-docs.html'
]

for filename in files_to_fix:
    try:
        with open(filename, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_content = content
        
        # Apply font size replacements
        for old, new in font_replacements.items():
            content = content.replace(old, new)
        
        # Apply padding replacements
        for old, new in padding_replacements.items():
            content = content.replace(old, new)
        
        # Only write if changes were made
        if content != original_content:
            with open(filename, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"✓ Fixed: {filename}")
        else:
            print(f"- No changes needed: {filename}")
            
    except FileNotFoundError:
        print(f"✗ File not found: {filename}")
    except Exception as e:
        print(f"✗ Error processing {filename}: {e}")

print("\nAll client pages have been updated with consistent, clean layout!")
