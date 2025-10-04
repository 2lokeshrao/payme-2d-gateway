#!/bin/bash

# Add auth-check.js to all protected pages
echo "🔧 Fixing all pages..."

# List of protected pages that need auth check
PROTECTED_PAGES=(
    "dashboard.html"
    "transactions.html"
    "api-keys.html"
    "payment-methods.html"
    "settlements.html"
    "webhooks.html"
    "refunds.html"
    "disputes.html"
    "reports.html"
    "analytics.html"
    "account-settings.html"
)

for page in "${PROTECTED_PAGES[@]}"; do
    if [ -f "$page" ]; then
        # Check if auth-check.js is already included
        if ! grep -q "auth-check.js" "$page"; then
            # Add auth-check.js before closing body tag
            sed -i 's|</body>|    <script src="js/auth-check.js"></script>\n</body>|' "$page"
            echo "✅ Fixed: $page"
        else
            echo "⏭️  Skipped: $page (already has auth-check)"
        fi
    fi
done

echo "✅ All pages fixed!"
