# 🔧 Troubleshooting: Button "Pesan Sekarang" Tidak Berfungsi

## Problem
Button "Pesan Sekarang" diklik tapi modal booking tidak muncul.

## Quick Fix Steps

### Step 1: Test Alpine.js
1. Buka: `http://localhost/test-booking.html`
2. Klik semua 3 test buttons
3. **Expected result**: 
   - "Test 3" should show simple modal immediately
   - "Test 1" and "Test 2" should open booking modal with data

**If Test 3 works**: Alpine.js is fine, problem is in booking modal component  
**If Test 3 doesn't work**: Alpine.js not loading correctly

### Step 2: Check Browser Console
1. Open landing page: `http://localhost/`
2. Press **F12** (Developer Tools)
3. Go to **Console** tab
4. Look for **red error messages**
5. Copy and check errors

**Common errors:**
```
Alpine is not defined
→ Solution: Alpine.js script not loaded

$dispatch is not a function  
→ Solution: Alpine.js loaded too late

bookingModal is not defined
→ Solution: Booking modal component missing
```

### Step 3: Hard Refresh
Sometimes browser cache causes issues:
1. Go to landing page
2. Press **Ctrl + Shift + R** (Windows) or **Cmd + Shift + R** (Mac)
3. Or **Ctrl + F5**
4. This forces browser to reload all scripts

### Step 4: Clear Laravel Cache
```bash
cd c:\xampp\htdocs\TA
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

### Step 5: Check if Vite is Running
If you're in development mode:
```bash
cd c:\xampp\htdocs\TA
npm run dev
```

Or build production assets:
```bash
npm run build
```

## Manual Test in Browser Console

Open browser console (F12) and run:

```javascript
// Test 1: Check Alpine loaded
console.log('Alpine loaded:', typeof Alpine !== 'undefined');

// Test 2: Find booking modal element
const modal = document.querySelector('[x-data*="bookingModal"]');
console.log('Modal found:', modal !== null);

// Test 3: Try to open modal manually
if (modal) {
    window.dispatchEvent(new CustomEvent('booking-modal:open', {
        detail: { id: 1, nama: 'Test Manual', harga: 75000 }
    }));
    console.log('Event dispatched - modal should open now');
}
```

**Expected output:**
```
Alpine loaded: true
Modal found: true
Event dispatched - modal should open now
(And modal should appear on screen)
```

## Common Issues & Solutions

### Issue 1: Alpine.js Not Loading
**Symptoms:** Console shows "Alpine is not defined"

**Solution:**
Check `resources/views/layouts/app.blade.php` has:
```html
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

### Issue 2: Modal Component Not Rendered
**Symptoms:** View source doesn't show booking modal HTML

**Solution:**
1. Check `resources/views/landing/index.blade.php` has:
   ```blade
   <x-booking-modal />
   ```
2. Check file exists: `resources/views/components/booking-modal.blade.php`

### Issue 3: @click Not Working
**Symptoms:** Button click does nothing, no console errors

**Solution:**
1. Make sure Alpine.js loads BEFORE button is rendered
2. Check button has correct syntax:
   ```blade
   <button @click="$dispatch('booking-modal:open', { ... })">
   ```

### Issue 4: Event Not Caught
**Symptoms:** Event dispatches but modal doesn't open

**Solution:**
Check modal wrapper has event listener:
```blade
<div x-data="bookingModal()" 
     @booking-modal:open.window="openModal($event.detail)">
```

## Verification Checklist

- [ ] `test-booking.html` works (all 3 buttons open modals)
- [ ] Browser console shows no errors
- [ ] Alpine.js loaded (check console: `typeof Alpine`)
- [ ] Booking modal element exists in page source
- [ ] Hard refresh done (Ctrl + Shift + R)
- [ ] Laravel cache cleared
- [ ] Page loads without 500 errors

## If Still Not Working

### Option 1: Check Network Tab
1. Open DevTools (F12)
2. Go to **Network** tab
3. Refresh page
4. Look for failed requests (red lines)
5. Check if Alpine.js CDN loaded successfully

### Option 2: Try Different Browser
Test in:
- Chrome
- Firefox  
- Edge

If works in one but not another = browser cache issue

### Option 3: Check PHP Errors
```bash
tail -f storage/logs/laravel.log
```

Then reload the page and see if errors appear.

## Debug Output

Run this in browser console and share output:
```javascript
console.log({
    alpine: typeof Alpine !== 'undefined',
    alpineVersion: typeof Alpine !== 'undefined' ? Alpine.version : 'not loaded',
    modalElement: !!document.querySelector('[x-data*="bookingModal"]'),
    buttonElement: !!document.querySelector('.package-btn'),
    paketData: window.pakets_db || 'not available'
});
```

## Need More Help?

1. Open browser console (F12)
2. Take screenshot of:
   - Console tab (showing errors)
   - Elements tab (showing button HTML)
   - Network tab (showing failed requests)
3. Share the screenshots

## Files to Check

1. `resources/views/layouts/app.blade.php` - Alpine.js script
2. `resources/views/landing/index.blade.php` - Button & modal include
3. `resources/views/components/booking-modal.blade.php` - Modal component
4. `storage/logs/laravel.log` - PHP errors

## Success Criteria

✅ Clicking "Pesan Sekarang" opens modal  
✅ Modal shows form with package details  
✅ No console errors  
✅ Can fill form and see total price calculated  
