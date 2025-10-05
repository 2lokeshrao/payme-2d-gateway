# PayMe 2D Gateway - Correct Settlement Flow

## Money Flow Architecture:

### Transaction: ₹10,000

**Day 0 (Transaction Day):**
```
Customer pays ₹10,000
    ↓ [INSTANT - 2-3 seconds]
CLIENT (Gateway Owner) receives ₹10,000
    ↓ [HOLDS MONEY]
Merchant waits for T+2 settlement
```

**Day 2 (T+2 Settlement):**
```
CLIENT transfers ₹9,800 to Merchant (98%)
CLIENT keeps ₹200 as commission (2%)
RESELLER gets ₹30-60 from client's ₹200 (15-30%)
```

## Settlement Schedule:

| Party | Amount | Settlement Time | Example (₹10,000) |
|-------|--------|----------------|-------------------|
| Customer | Pays | Instant deduction | -₹10,000 |
| CLIENT (Gateway Owner) | Receives | INSTANT | +₹10,000 |
| Merchant | Receives | T+2 Days | +₹9,800 (after 2 days) |
| Reseller | Receives | T+2 Days | +₹30-60 (after 2 days) |

## Dashboard Updates Needed:

1. **Client Dashboard:**
   - Show "Instant Settlements" section
   - Display real-time money received
   - Show pending merchant payouts (T+2)
   - Commission earned per transaction

2. **Merchant Dashboard:**
   - Show "Pending Settlements" (T+2)
   - Display settlement schedule
   - Show transaction fee (2%)
   - Expected payout date

3. **Reseller Dashboard:**
   - Show "Pending Commissions" (T+2)
   - Display commission rate (15-30%)
   - Expected payout date

