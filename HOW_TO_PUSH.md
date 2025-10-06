# 🚀 GitHub Par Push Kaise Karein

## Option 1: Personal Access Token Use Karein (Recommended)

### Step 1: GitHub Personal Access Token Banayein
1. GitHub par login karein: https://github.com
2. Settings > Developer settings > Personal access tokens > Tokens (classic)
3. "Generate new token" par click karein
4. Token ko naam dein: "PayMe 2D Gateway"
5. Permissions select karein: `repo` (full control)
6. "Generate token" par click karein
7. Token ko copy kar lein (ye sirf ek baar dikhega!)

### Step 2: Git Remote URL Update Karein
```bash
cd /home/code/payme-2d-gateway

# Current remote check karein
git remote -v

# Remote URL update karein with token
git remote set-url origin https://YOUR_TOKEN@github.com/2lokeshrao/payme-2d-gateway.git

# Push karein
git push origin main
```

## Option 2: SSH Key Use Karein

### Step 1: SSH Key Generate Karein
```bash
ssh-keygen -t ed25519 -C "your_email@example.com"
# Enter dabayein (default location)
# Passphrase optional hai

# Public key copy karein
cat ~/.ssh/id_ed25519.pub
```

### Step 2: GitHub Par SSH Key Add Karein
1. GitHub > Settings > SSH and GPG keys
2. "New SSH key" par click karein
3. Public key paste karein
4. "Add SSH key" par click karein

### Step 3: Remote URL Change Karein
```bash
cd /home/code/payme-2d-gateway
git remote set-url origin git@github.com:2lokeshrao/payme-2d-gateway.git
git push origin main
```

## Option 3: Apne Local Machine Se Push Karein (Easiest)

### Agar aapke paas local machine par already repository hai:

```bash
# Apne local machine par
cd /path/to/payme-2d-gateway

# Latest changes pull karein
git pull origin main

# Ya phir fresh clone karein
git clone https://github.com/2lokeshrao/payme-2d-gateway.git
cd payme-2d-gateway
```

### Files Download Karein Is Session Se:

Ye files download karein aur apne local repo mein copy karein:

**New Files:**
- `js/admin-auth-check.js`
- `ADMIN_AUTH_FIX_SUMMARY.md`
- `TEST_INSTRUCTIONS.md`

**Modified File:**
- `admin/login.html`

**All Admin Pages (38 files):**
- `admin/*.html` (except login.html and forgot-password.html)

### Phir Local Se Push Karein:
```bash
git add .
git commit -m "Fix: Admin authentication - prevent unauthorized redirects"
git push origin main
```

## Quick Command (If you have credentials)

```bash
cd /home/code/payme-2d-gateway

# Username aur password ke saath push
git push https://YOUR_USERNAME:YOUR_TOKEN@github.com/2lokeshrao/payme-2d-gateway.git main
```

## ⚠️ Important Notes

1. **Personal Access Token** sabse secure method hai
2. **Password** directly use mat karein (GitHub ne disable kar diya hai)
3. **Token** ko safe rakhein, share mat karein
4. **SSH** long-term ke liye best hai

## 🎯 Recommended: Option 1 (Personal Access Token)

Sabse aasan aur secure!

---

**Need Help?** 
- GitHub Token Guide: https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token
