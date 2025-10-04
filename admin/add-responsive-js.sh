#!/bin/bash

# Script to add responsive JavaScript to all admin HTML files

ADMIN_DIR="/home/code/payme-2d-gateway/admin"
JS_SCRIPT='    <script src="responsive-menu.js"></script>'

cd "$ADMIN_DIR"

# Counter for files updated
count=0

# Loop through all HTML files
for file in *.html; do
    # Skip if file doesn't exist
    [ -f "$file" ] || continue
    
    # Check if responsive JS is already included
    if grep -q "responsive-menu.js" "$file"; then
        echo "⏭️  Skipping $file (already has responsive JS)"
        continue
    fi
    
    # Add JS script before </body> tag
    if grep -q "</body>" "$file"; then
        sed -i "s|</body>|$JS_SCRIPT\n</body>|" "$file"
        echo "✅ Updated: $file"
        ((count++))
    else
        echo "⚠️  Warning: No </body> tag found in $file"
    fi
done

echo ""
echo "════════════════════════════════════════"
echo "✅ COMPLETED: Updated $count HTML files"
echo "════════════════════════════════════════"
