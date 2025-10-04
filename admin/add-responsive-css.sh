#!/bin/bash

# Script to add responsive CSS link to all admin HTML files

ADMIN_DIR="/home/code/payme-2d-gateway/admin"
CSS_LINK='    <link rel="stylesheet" href="responsive-admin.css">'

cd "$ADMIN_DIR"

# Counter for files updated
count=0

# Loop through all HTML files
for file in *.html; do
    # Skip if file doesn't exist
    [ -f "$file" ] || continue
    
    # Check if responsive CSS is already included
    if grep -q "responsive-admin.css" "$file"; then
        echo "⏭️  Skipping $file (already has responsive CSS)"
        continue
    fi
    
    # Create backup
    cp "$file" "${file}.backup-$(date +%Y%m%d)"
    
    # Add CSS link before </head> tag
    if grep -q "</head>" "$file"; then
        sed -i "s|</head>|$CSS_LINK\n</head>|" "$file"
        echo "✅ Updated: $file"
        ((count++))
    else
        echo "⚠️  Warning: No </head> tag found in $file"
    fi
done

echo ""
echo "════════════════════════════════════════"
echo "✅ COMPLETED: Updated $count HTML files"
echo "════════════════════════════════════════"
