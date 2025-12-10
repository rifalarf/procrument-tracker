# Cara Mendapatkan Google OAuth Credentials

Untuk mengaktifkan login Google, ikuti langkah ini:

## 1. Buat Project
1. Buka [Google Cloud Console](https://console.cloud.google.com/).
2. Buat **New Project**.

## 2. OAuth Consent Screen
1. Menu **APIs & Services** > **OAuth consent screen**.
2. Pilih **Internal** (untuk perusahaan) atau **External** (testing).
3. Isi **App Name** dan **Email**.

## 3. Buat Keys
1. Menu **Credentials** > **+ CREATE CREDENTIALS** > **OAuth client ID**.
2. **App Type**: Web application.
3. **Authorized redirect URIs** (WAJIB SAMA PERSIS):
   `http://127.0.0.1:8000/auth/google/callback`
4. Klik **Create**.

## 4. Setup .env
Salin **Client ID** dan **Client Secret** ke file `.env`:

```ini
GOOGLE_CLIENT_ID=nomor-panjang.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=rahasia-anda
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```
*Jangan lupa restart server (`php artisan serve`) setelah update .env!*
