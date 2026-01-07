# Tugas UAS Web — Aplikasi Manajemen Artikel

## Deskripsi 📘
Aplikasi web sederhana untuk manajemen artikel (CRUD) dan autentikasi pengguna. 

Dibangun dengan PHP, menggunakan framework css dan dirancang untuk dijalankan menggunakan XAMPP (Apache + MySQL) pada Windows.

## Fitur ✅
- Menambahkan, melihat, mengubah, dan menghapus artikel (CRUD)
- Autentikasi pengguna (login/logout)
- Struktur modular untuk memisahkan fungsi (module/artikel, module/home)
- Template sederhana (`header`, `footer`, `sidebar`) dan file CSS untuk tampilan

## Struktur proyek 🔧
Deskripsi singkat tiap folder dan file penting agar mudah dipelajari dan dikembangkan, serta struktur folder secara visual:

- `config.php` — konfigurasi koneksi database (konstanta `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`).
- `index.php` — titik masuk aplikasi; router sederhana yang memuat modul berdasarkan parameter `?url=`.

- `class/` — kelas pembantu dan utilitas:
  - `Database.php` — wrapper MySQLi (koneksi dan metode helper: `getById`, `getAll`, `getPaged`, `insert`, `update`, `delete`, `query`).
  - `Form.php` — helper sederhana untuk membangun form secara programatik.

- `module/` — modul fitur aplikasi (dipisahkan per domain):
  - `artikel/` — manajemen artikel (CRUD + view)
  - `home/` — halaman dashboard/menu dan potongan UI terkait
  - `user/` — manajemen pengguna dan autentikasi

- `style/` — asset CSS (`style.css`)
- `template/` — potongan tampilan (`header.php`, `footer.php`, `sidebar.php`)
- `role_migration.sql` — file SQL untuk menambahkan kolom `role` pada tabel `users`

### Struktur folder (visual)
```
tugas_uas_web/
├─ config.php
├─ index.php
├─ README.md
├─ .htaccess
├─ class/
│  ├─ Database.php
│  └─ Form.php
├─ module/
│  ├─ artikel/
│  │  ├─ index.php
│  │  ├─ tambah.php
│  │  ├─ submit_tambah.php
│  │  ├─ ubah.php
│  │  ├─ hapus.php
│  │  └─ view.php
│  ├─ home/
│  │  └─ user.php
│  └─ user/
│     ├─ index.php
│     ├─ tambah.php
│     ├─ ubah.php
│     ├─ hapus.php
│     ├─ register.php
│     ├─ login.php
│     └─ logout.php
├─ style/
│  └─ style.css
└─ template/
   ├─ header.php
   ├─ footer.php
   └─ sidebar.php
```

### Catatan singkat
- Tombol dan aksi untuk **tambah/ubah/hapus** hanya tersedia untuk pengguna dengan `role = 'admin'` (baik di UI maupun server-side).
- Pastikan tabel `users` memiliki kolom: `id`, `username`, `password` (hash), `nama`, `role`.
- Saya sudah menambahkan folder `migrations/` berisi skrip SQL untuk penyesuaian skema (mis. menambahkan kolom `role` dan `nama`). Jika kolom `role` atau `nama` belum ada di tabel `users`, jalankan `migrations/000_add_role.sql` lalu `migrations/001_add_nama.sql`.

### Registrasi pengguna
- `module/user/register.php` — halaman registrasi pengguna: input `username`, `password` (minimal 8 karakter), `nama` (opsional), dan `role`.
- Untuk keamanan, registrasi **admin** hanya bisa dilakukan jika `ADMIN_REG_CODE` diisi di `config.php` dan kode yang dimasukkan cocok. Jika `ADMIN_REG_CODE` kosong, pendaftaran admin dinonaktifkan dari UI.

## Penjelasan singkat modul 💡
- `module/artikel/*`: berisi file untuk menampilkan daftar artikel, form tambah/ubah, dan aksi hapus/submit.
- `module/user/*`: halaman login/logout dan pengelolaan sesi pengguna.
- `class/Database.php`: gunakan untuk interaksi dengan database (koneksi, query sederhana).
- `config.php`: ubah informasi host/username/password/database agar sesuai lingkungan Anda.

## Cara Menjalankan (Windows + XAMPP) ▶️
1. Pastikan XAMPP terpasang dan Apache & MySQL dijalankan.

2. Salin folder proyek ke `C:\xampp\htdocs\tugas_uas_web`.

3. Sesuaikan pengaturan database di `config.php` (host, user, password, database).

4. Buat database (mis. `tugas_uas`) dan tabel yang dibutuhkan. Contoh skema sederhana:

```sql
-- tabel users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user'
);

-- tabel artikel
CREATE TABLE artikel (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(255) NOT NULL,
  isi TEXT NOT NULL,
  penulis VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

5. Akses aplikasi via browser: `http://localhost/tugas_uas_web/`.

6. Login melalui `module/user/login.php` (atau link login pada UI) untuk mengelola artikel.

7. Jika belum memiliki akun, maka registrasi melalui `module/user/register.php`.


## Catatan pengembangan & keamanan ⚠️
- Sanitasi input dan validasi pada `Form.php` sangat penting untuk mencegah SQL Injection dan XSS.
- Pertimbangkan untuk menggunakan prepared statements (PDO / MySQLi dengan prepared statements) pada `Database.php`.

## Teknologi & Lisensi
- Teknologi: PHP, MySQL, HTML, CSS
- Lisensi: Bebas digunakan untuk tujuan pembelajaran (sesuaikan jika ingin lisensi lain)

---


## Hasil Screenshot

<img width="1339" height="630" alt="Login" src="https://github.com/user-attachments/assets/285b7185-d9c0-4442-9215-82b80c592ab1" />

<img width="1340" height="633" alt="Registration" src="https://github.com/user-attachments/assets/c715f515-3f09-46bb-ad85-6b7aa2252ca8" />

### Administrator

<img width="1039" height="403" alt="Screenshot_63" src="https://github.com/user-attachments/assets/5d2aa700-c908-49f5-aa62-d40143096a9b" />

<img width="1031" height="501" alt="Screenshot_64" src="https://github.com/user-attachments/assets/9f248c7f-9cea-44d7-9382-ab36d352502f" />

<img width="1082" height="637" alt="Screenshot_65" src="https://github.com/user-attachments/assets/285cee51-d5db-4b05-af7e-f124db3cace9" />

<img width="1092" height="638" alt="Screenshot_66" src="https://github.com/user-attachments/assets/37d7cefa-ee00-4557-bad9-98277ffe7c68" />

<img width="1062" height="566" alt="Screenshot_67" src="https://github.com/user-attachments/assets/57b69fb1-a312-4478-8324-6e52611e1b74" />

<img width="1021" height="578" alt="Screenshot_68" src="https://github.com/user-attachments/assets/41efd51a-afaf-4a65-872a-77b4433428f8" />

<img width="1013" height="593" alt="Screenshot_69" src="https://github.com/user-attachments/assets/837e622c-5595-4bf6-9bca-d9d2f6ffbc68" />

<img width="999" height="602" alt="Screenshot_70" src="https://github.com/user-attachments/assets/37766442-9bd0-413d-aa4d-16c65724ba57" />

<img width="1040" height="332" alt="Screenshot_71" src="https://github.com/user-attachments/assets/adb742c1-905d-480b-9425-7425bf1f1f8e" />

### Guest

<img width="1023" height="472" alt="Screenshot_72 png (guest) png" src="https://github.com/user-attachments/assets/649700d5-94a6-47a1-9cff-149ad8901ddc" />

<img width="1045" height="616" alt="Screenshot_73 png (guest) png" src="https://github.com/user-attachments/assets/ed252cb6-0fdd-4afd-82c1-985bdefbaf6d" />

<img width="1088" height="302" alt="Screenshot_74 png (guest) png" src="https://github.com/user-attachments/assets/aa6b450e-7fe0-42b0-a2b6-2fb6b3f055e5" />






