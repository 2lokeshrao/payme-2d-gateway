#!/bin/bash

# Script to add admin authentication to all admin pages except login and forgot-password

for file in admin/*.html; do
    # Skip backup files, login, and forgot-password pages
    if [[ "$file" == *"backup"* ]] || [[ "$file" == *"login.html"* ]] || [[ "$file" == *"forgot-password.html"* ]]; then
        continue
    fi
    
    # Check if auth script is already included
    if grep -q "admin-auth-check.js" "$file"; then
        echo "Auth already added to $file, skipping..."
        continue
    fi
    
    # Find the closing </body> tag and add script before it
    if grep -q "</body>" "$file"; then
        # Add the auth script before closing body tag
        sed -i 's|</body>|    <script src="../js/admin-auth-check.js"></script>\n</body>|' "$file"
        echo "Added auth to $file"
    else
        echo "No </body> tag found in $file"
    fi
done

echo "Authentication script added to all admin pages!"
