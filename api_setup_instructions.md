# Panduan Setup API (Midtrans, Wablas, Gmail)

Dokumen ini berisi panduan teknis langkah demi langkah untuk mengonfigurasi API untuk Payment Gateway (Midtrans), WhatsApp Gateway (Wablas), dan SMTP Email (Gmail) pada platform Delta Education.

---

## 1. Midtrans API (Payment Gateway)

Midtrans digunakan untuk memproses pembayaran webinar dan pelatihan secara otomatis.

### Langkah-langkah:
1. **Daftar/Login**: Masuk ke dashboard Midtrans (Environment: Sandbox untuk testing, Production untuk live).
2. **Akses Keys**:
   - Di dashboard Midtrans, buka menu **Settings > Access Keys**.
   - Salin **Client Key** dan **Server Key**.
3. **Konfigurasi `.env`**: Buka file `.env` di direktori proyek `platform` dan tambahkan/sesuaikan baris berikut:
   ```env
   MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxxx   # Ganti dengan Server Key Anda
   MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxxx   # Ganti dengan Client Key Anda
   MIDTRANS_IS_PRODUCTION=false                       # Ubah ke true jika menggunakan akun Production
   ```
4. **URL Notification (Webhook)**:
   - Agar Midtrans dapat mengirimkan status konfirmasi pembayaran (berhasil/gagal) ke sistem kita, atur Notification URL di dashboard Midtrans.
   - Buka menu **Settings > Configuration** di Midtrans.
   - Isi kolom **Notification URL** dengan: `https://[domain-anda.com]/api/midtrans/notification` (Pastikan URL dapat diakses publik).

---

## 2. Wablas API (WhatsApp Gateway)

Wablas digunakan untuk sistem blasting / notifikasi WhatsApp secara otomatis kepada peserta (misal: konfirmasi pendaftaran, pengingat h-1).

### Langkah-langkah:
1. **Daftar/Login & Sewa Server**: Masuk ke [dashboard Wablas](https://wablas.com) dan pastikan Anda memiliki paket atau trial yang aktif.
2. **Scan QR Code**: Hubungkan nomor WhatsApp admin/pengirim dengan memindai QR Code di dashboard Wablas (Menu: WhatsApp > Device).
3. **Akses Api Token**:
   - Di dashboard Wablas, buka profil atau halaman referensi API untuk mendapatkan **API Token** dan **Domain URL** server yang ditugaskan kepada Anda (misal: `https://solo.wablas.com`).
4. **Konfigurasi `.env`**: Buka file `.env` dan tambahkan:
   ```env
   WABLAS_API_DOMAIN=https://[server-assigned].wablas.com   # Sesuaikan dengan server Wablas Anda
   WABLAS_API_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx        # Ganti dengan API Token Wablas Anda
   ```

---

## 3. Gmail API / App Password (SMTP Email)

Gmail digunakan untuk mengirim email notifikasi resmi kepada peserta (invoice, link zoom, dsb). Karena Google menonaktifkan "Less secure apps", kita harus menggunakan **App Passwords**.

### Langkah-langkah:
1. **Aktifkan 2-Step Verification (2FA)**:
   - Login ke akun Google (sebaiknya gunakan email resmi e.g., `admin@delta-education.com` jika via Google Workspace, atau email gmail biasa).
   - Masuk ke [Google Account Security](https://myaccount.google.com/security).
   - Aktifkan **2-Step Verification**.
2. **Buat App Password**:
   - Setelah 2-Step Verification aktif, cari opsi **App Passwords** (di menu Security atau ketik di search bar "App passwords").
   - Pilih "Select app" -> "Mail" dan "Select device" -> "Other (Custom name)", beri nama misalnya "Delta Laravel App".
   - Klik **Generate**. Google akan memberikan 16 digit password khusus (misal: `abcd efgh ijkl mnop`). **Simpan** password ini.
3. **Konfigurasi `.env`**: Buka file `.env` dan atur konfigurasi mail:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=465
   MAIL_USERNAME=email_anda@gmail.com
   MAIL_PASSWORD="16_digit_app_password_tanpa_spasi" # Masukkan app password dari langkah 2
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=email_anda@gmail.com
   MAIL_FROM_NAME="Delta Education"
   ```
4. **Testing**: Setelah konfigurasi `.env` disimpan, sistem siap menggunakan Gmail untuk menembak email konfirmasi.

---

> [!TIP]
> **Penting Setelah Mengubah `.env`**
> Jika Anda melakukan perubahan konfigurasi ini di server production, jangan lupa untuk membersihkan cache config Laravel dengan perintah:
> `php artisan config:clear`
