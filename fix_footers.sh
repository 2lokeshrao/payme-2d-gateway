#!/bin/bash

# List of HTML files to update (excluding admin pages)
files=(
    "subscription.html"
    "register.html"
    "login.html"
    "api-documentation.html"
    "webhooks.html"
    "documentation.html"
    "setup-guide.html"
    "README.html"
)

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "Updating $file..."
        
        # Fix footer h3 colors
        sed -i '/<div class="footer-section">/,/<\/div>/ s/<h3>/<h3 style="color: white;">/g' "$file"
        
        # Fix support links
        sed -i 's|<li><a href="#">Help Center</a></li>|<li><a href="help-center.html">Help Center</a></li>|g' "$file"
        sed -i 's|<li><a href="#">Contact Us</a></li>|<li><a href="contact.html">Contact Us</a></li>|g' "$file"
        
        # Fix PayMe 2D Gateway description color
        sed -i 's|<p>Complete payment solution|<p style="color: #cbd5e0;">Complete payment solution|g' "$file"
        
        echo "✅ $file updated"
    fi
done

echo ""
echo "✅ All footer fixes applied!"
