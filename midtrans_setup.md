# Panduan Implementasi Pembayaran Midtrans

Jika tombol "Lanjutkan Pembayaran" tidak memunculkan popup pembayaran (dan hanya memunculkan error *Gagal menginisiasi pembayaran*), berarti ada konfigurasi yang belum di-set di lingkungan Midtrans Anda.

Ikuti langkah-langkah berikut:

## 1. Dapatkan Keys dari Dashboard Midtrans
1. Login ke akun [Midtrans Dashboard](https://dashboard.midtrans.com/).
2. Pastikan Anda berada pada environment **Sandbox** (pojok kanan atas).
3. Buka menu **Settings > Access Keys**.
4. Anda akan melihat **Merchant ID**, **Client Key**, dan **Server Key**.

## 2. Salin Keys ke file `.env` Laravel Anda
Buka file `c:\wamp64\www\e-course_delta\platform\.env` dan pastikan kredensial berikut lengkap:

```env
MIDTRANS_SERVER_KEY="SB-Mid-server-xxxxxxxxxxxxxxxxx"
MIDTRANS_CLIENT_KEY="SB-Mid-client-xxxxxxxxxxxxxxxxx"
MIDTRANS_MERCHANT_ID="Gxxxxxxx"
MIDTRANS_IS_PRODUCTION=false
```
*(Ganti "SB-Mid-..." dengan kunci Sandbox milik Anda)*

## 3. Konfigurasi Payment URL (Webhook)
Agar status Midtrans bisa otomatis memperbarui database Laravel menjadi "Lunas" (settlement):
1. Masuk ke **Settings > Configuration** di Midtrans Dashboard.
2. Isi kolom **Payment Notification URL** dengan URL ngrok (jika di localhost) atau domain anda: 
   `http://[domain_anda]/webhook/midtrans`
3. Centang semua event yang relevan (minimal `Payment Notification`).

## 4. Reset Config Cache
Karena variabel dari `.env` telah berubah, Anda wajib menerapkan ulang cache configurasi pada Laravel agar web bisa membacanya:
```bash
cd c:\wamp64\www\e-course_delta\platform
php artisan config:clear
```

Setelah itu coba ulangi checkout kembali. Gateway Snap Popup dari Midtrans akan otomatis muncul.
