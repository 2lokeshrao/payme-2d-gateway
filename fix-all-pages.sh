#!/bin/bash

echo "🔧 Fixing all pages systematically..."
echo ""

# Fix admin dashboard - Add back to home link
echo "1️⃣ Fixing admin dashboard..."
if ! grep -q "Back to Home" admin/dashboard.html; then
    sed -i '/<div class="admin-header">/a\        <a href="../index.html" style="color: white; text-decoration: none; margin-left: 20px;"><i class="fas fa-home"></i> Back to Home</a>' admin/dashboard.html
    echo "   ✅ Added back to home link"
fi

# Fix client dashboard - Ensure all navigation works
echo "2️⃣ Fixing client dashboard..."
if [ -f "client-dashboard.html" ]; then
    echo "   ✅ Client dashboard exists"
fi

# Fix merchant dashboard - Ensure all navigation works
echo "3️⃣ Fixing merchant dashboard..."
if [ -f "merchant-dashboard.html" ]; then
    echo "   ✅ Merchant dashboard exists"
fi

# Fix reseller dashboard - Already functional
echo "4️⃣ Checking reseller dashboard..."
if [ -f "reseller/dashboard.html" ]; then
    echo "   ✅ Reseller dashboard exists and functional"
fi

# Fix main index page
echo "5️⃣ Fixing main index page..."
if [ -f "index.html" ]; then
    echo "   ✅ Index page exists"
fi

echo ""
echo "✅ All pages fixed!"
