// SEO Configuration for PayMe 2D Gateway
const seoConfig = {
  siteName: "PayMe 2D Gateway",
  siteUrl: "https://payme-gateway.lindy.site",
  defaultTitle: "PayMe 2D Gateway - Secure Payment Gateway India",
  defaultDescription: "India's most trusted payment gateway. Accept online payments via UPI, cards, net banking. PCI DSS certified, instant settlements, 24/7 support.",
  defaultKeywords: "payment gateway India, online payment, UPI payments, payment processing, merchant account, payment integration",
  author: "PayMe 2D Gateway",
  twitterHandle: "@payme2dgateway",
  facebookPage: "payme2dgateway",
  logo: "https://payme-gateway.lindy.site/images/logo.png",
  
  // Google AdSense
  adsenseClient: "ca-pub-XXXXXXXXXXXXXXXX", // Replace with actual AdSense ID
  
  // Google Analytics
  gaTrackingId: "G-XXXXXXXXXX", // Replace with actual GA4 ID
  
  // Structured Data
  organization: {
    "@context": "https://schema.org",
    "@type": "FinancialService",
    "name": "PayMe 2D Gateway",
    "url": "https://payme-gateway.lindy.site",
    "logo": "https://payme-gateway.lindy.site/images/logo.png",
    "description": "Secure payment gateway for online businesses in India",
    "address": {
      "@type": "PostalAddress",
      "addressCountry": "IN",
      "addressLocality": "Mumbai",
      "addressRegion": "Maharashtra"
    },
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "+91-22-1234-5678",
      "contactType": "Customer Service",
      "email": "support@payme2dgateway.com"
    },
    "sameAs": [
      "https://www.facebook.com/payme2dgateway",
      "https://twitter.com/payme2dgateway",
      "https://www.linkedin.com/company/payme2dgateway"
    ]
  }
};

// Export for use in pages
if (typeof module !== 'undefined' && module.exports) {
  module.exports = seoConfig;
}
