# Mamafua Platform (Kenya)

Production-ready PHP + MySQL platform for booking verified housekeepers (mamafuas), with M-Pesa STK Push workflow, role-based dashboards, and admin safety controls.

## Features delivered

- **Client:** register/login, booking in 3-step flow, M-Pesa payment prompt, booking history, review submission.
- **Mamafua:** register profile, upload ID/photo/police clearance with file validation, manage accepted jobs.
- **Admin:** verify/suspend mamafuas, view and update bookings/disputes, payment visibility, audit logs.
- **Safety controls:** verification states, single time-slot protection, CSRF, secure password hashing, PDO prepared statements.

## Tech stack

- PHP 8+
- MySQL 8+ (PDO)
- HTML5 + Bootstrap 5 + custom CSS
- Vanilla JS + Fetch API
- M-Pesa Daraja STK Push + callback handling

## Local setup (XAMPP)

1. Copy project folder into `htdocs`, e.g. `C:\xampp\htdocs\mamafua`.
2. Start Apache + MySQL from XAMPP.
3. Create env file:
   ```bash
   cp .env.example .env
   ```
4. Import schema + sample data:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
5. Visit:
   - Public app: `http://localhost/mamafua/public/index.php`
   - Admin dashboard: `http://localhost/mamafua/admin/dashboard.php`

## Default credentials

- **Admin:** `admin@mamafua.co.ke` / `Admin@123`
- **Sample mamafua:** `grace.mamafua@example.com` / `Admin@123`
- **Sample client:** `brian.client@example.com` / `Admin@123`

## M-Pesa setup (Daraja)

1. Fill in `.env`:
   - `MPESA_CONSUMER_KEY`
   - `MPESA_CONSUMER_SECRET`
   - `MPESA_SHORTCODE`
   - `MPESA_PASSKEY`
   - `MPESA_CALLBACK_URL`
2. Expose local URL with ngrok for callbacks if testing locally:
   ```bash
   ngrok http 80
   ```
3. Set callback URL to:
   `https://<ngrok-url>/mamafua/api/payments.php?action=callback`
4. For missing keys, app gracefully uses sandbox simulation and still records pending payment objects.

## Folder structure

```text
mamafua/
│── public/
│── app/
│── admin/
│── api/
│── database/
│── storage/uploads/
│── .env.example
│── .htaccess
│── README.md
```

## Security notes

- Passwords hashed with `password_hash` (bcrypt).
- CSRF token generated per session and validated on sensitive POST requests.
- SQL injection prevention via PDO prepared statements.
- Upload validation for mime type and max 5MB.
- Session-based auth with role checks in API/admin flows.

## Production hardening checklist

- Set Apache virtual host to `public/` as document root.
- Add HTTPS and secure cookies (`Secure`, `HttpOnly`, `SameSite=Lax/Strict`).
- Add queue/retry for callback reconciliation.
- Enable background workers for notifications (SMS/WhatsApp).
