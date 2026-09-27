# Project Work Log

This document records project work completed for monthly client reporting.

## 2026-09-08 - Phone Lookup, WhatsApp OTP & Pincode Auto-Fill Checkout Flow

### Client requirement

When a user browses products, adds items to cart, and opens checkout:
1. Entering phone number checks if customer exists in database.
2. If customer exists, auto-fill details (name, whatsapp, district, pincode, address) and place order directly.
3. If new customer, send a 4-digit OTP to WhatsApp number, verify OTP, allow user to fill details, create customer/user account, and place order.
4. Integrate Pincode checking API (`https://api.postalpincode.in/pincode/{pincode}`) to automatically fetch and fill District/State.

### Work completed

- Integrated Postal Pincode API (`https://api.postalpincode.in/pincode/{pincode}`) in `home.blade.php`. Entering a 6-digit pincode automatically fetches and populates the District field.
- Created `/guest/send-otp` and `/guest/verify-otp` routes in `routes/web.php`.
- Made `guest/order` accessible directly for guest checkout without requiring password login.
- Added `sendOtp` and `verifyOtp` methods in `HomeController.php` with 4-digit OTP generation, session/cache storage, and NextSMS WhatsApp API dispatch.
- Updated `guestOrder` to enforce WhatsApp OTP verification for new customers, auto-create customer and user accounts, and log in customer upon order completion.
- Redesigned checkout modal form in `home.blade.php` with WhatsApp OTP verification block, auto-fill for existing customers, countdown timer for OTP resend, auto-verification on 4-digit entry, and direct order placement.

### Files changed or added

- `routes/web.php`
- `app/Http/Controllers/HomeController.php`
- `resources/views/home.blade.php`

### Validation completed

- `php -l` syntax check passed for modified PHP files.
- `php artisan route:list` passed with code 0.


### Client requirement

Allow a customer to browse products as a guest, keep the cart during login, return to checkout after login, view orders in the user dashboard, and access the service page from the user area.

### Work completed

- Added checkout login intent handling so a guest is sent to login only when placing an order.
- Preserved the cart in browser `localStorage` during login.
- Returned the customer to the cart after successful login.
- Prevented WhatsApp API timeout from changing a successfully created order into a 500 error.
- Enabled customer registration with the `user` role.
- Added customer order listing to the user dashboard.
- Added Dashboard, My Orders, Service, and Logout navigation.
- Changed the customer service URL from `/admin/service2` to `/user/service2`.
- Added a separate user layout so customers do not see the admin sidebar.
- Fixed Logout buttons to use secure POST requests with CSRF protection.

### Files changed or added

- `routes/web.php`
  - Added the checkout login route.
  - Changed the service route to `/user/service2`.
  - Added authentication and role protection for the service route.
- `app/Http/Controllers/HomeController.php`
  - Loaded customer orders for the user dashboard.
  - Added a timeout-safe WhatsApp notification request.
- `app/Http/Controllers/auth/LoginController.php`
  - Enabled registration.
  - Assigned the `user` role to new customer accounts.
- `resources/views/user/userdashboard.blade.php`
  - Added order listing, left navigation, service link, and styled Logout button.
- `resources/views/layouts/user.blade.php`
  - Added the customer layout and left navigation for service pages.
- `resources/views/admin/service/setting2.blade.php`
  - Uses the customer layout for users and the admin layout for admin/manager accounts.
- `resources/views/dashboard.blade.php`
  - Changed Logout from GET link to secure POST form.

### Validation completed

- PHP syntax check passed.
- Laravel route registration checked.
- Blade templates compiled successfully.
- `git diff --check` passed.

## 2026-09-03 - Route Organization and Navbar Navigation

### Client requirement

Split the large web route file and add visible cart, login, and Support navigation to the customer navbar.

### Work completed

- Split the admin and manager routes into `routes/web/admin.php`.
- Reduced `routes/web.php` to the public, guest, customer, and route-loader definitions.
- Added a cart icon to the customer navbar with a quantity badge.
- Connected the navbar cart icon to the existing cart modal.
- Added a login icon and link for guest users.
- Updated navbar colors for visibility against the dark teal background.
- Made the Support link open `userdashboard` for authenticated users and `login_page` for guests.
- Added a Support headset icon to the mobile navbar.
- Positioned the cart quantity badge above the cart icon.

### Files changed or added

- `routes/web.php`
  - Loads the extracted admin route module.
- `routes/web/admin.php`
  - Contains admin and manager route definitions.
- `resources/views/layouts/guestLayout/navbar.blade.php`
  - Added cart, login, and Support navigation controls.
  - Updated navigation colors and cart badge position.
- `resources/views/home.blade.php`
  - Updates the navbar cart quantity and opens the existing cart modal.

### Validation completed

- PHP syntax checks passed for both route files.
- Laravel route compilation passed.
- Blade templates compiled successfully.
- No editor errors found in the modified Blade files.
- `git diff --check` passed.

### Client test steps

1. Open the website.
2. Register or login with a customer account whose role is `user`.
3. Add a product to the cart and place an order.
4. Confirm that login returns to the checkout cart.
5. Open `/userdashboard` and check **My Orders**.
6. Click **Service** and confirm `/user/service2` opens with the customer menu.
7. Test the Logout button from both Dashboard and Service.

## Monthly reporting template

### YYYY-MM-DD - Work title

**Client request:**

**Work completed:**

- 

**Files changed:**

- `path/to/file`

**Validation/test result:**

- 

**Client verification:**

- 

**Pending items:**

- None
