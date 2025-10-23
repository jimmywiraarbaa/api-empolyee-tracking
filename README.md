# 🛰️ Employee Tracking API

API sederhana untuk mendemonstrasikan sistem **tracking lokasi karyawan** menggunakan **Laravel 12 + Sanctum + Swagger (l5-swagger)**.  
Aplikasi ini memungkinkan user untuk:
- Register & login  
- Mengirim lokasi GPS (latitude, longitude, akurasi)  
- Melihat posisi terakhir semua user  
- Dokumentasi otomatis di Swagger UI  

---

## 🚀 Tech Stack
- **Laravel 12.x**
- **Laravel Sanctum** – autentikasi token
- **L5-Swagger** – dokumentasi API otomatis
- **MySQL**
- **PHP 8.2+**
- **Composer**

---

## ⚙️ Instalasi

1️⃣ Clone & Install Dependency  
```bash
git clone https://github.com/jimmywiraarbaa/api-empolyee-tracking.git
cd api-employee-tracking
composer install
```

2️⃣ Konfigurasi `.env`  
```bash
cp .env.example .env
```
Isi variabel sesuai kebutuhan:
```env
APP_NAME="Employee Tracking API"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tracking
DB_USERNAME=root
DB_PASSWORD=

L5_SWAGGER_CONST_HOST=http://localhost:8000
```

3️⃣ Generate Key & Migrasi Database  
```bash
php artisan key:generate
php artisan migrate
```

4️⃣ Jalankan Server  
```bash
php artisan serve
```
API aktif di:
```
http://localhost:8000
```

---

## 🔐 Autentikasi
Gunakan **Bearer Token** dari Laravel Sanctum:

- Register → `/api/register`  
- Login → `/api/login`  
- Token digunakan di header:
  ```
  Authorization: Bearer <token>
  ```

---

## 📦 Endpoint

| Method | Endpoint | Deskripsi |
|--------|-----------|-----------|
| `POST` | `/api/register` | Register user baru |
| `POST` | `/api/login` | Login user & ambil token |
| `GET`  | `/api/locations` | Ambil semua lokasi user (wajib login) |
| `PUT`  | `/api/locations/{id}` | Update atau create lokasi user (wajib login) |

---

## 🧠 Contoh Request

### Register
```http
POST /api/register
Content-Type: application/json

{
  "name": "Jimmy",
  "email": "jimmy@example.com",
  "password": "secret123",
  "password_confirmation": "secret123"
}
```

### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "jimmy@example.com",
  "password": "secret123"
}
```

### Update Lokasi
```http
PUT /api/locations/1
Authorization: Bearer <token>
Content-Type: application/json

{
  "lat": -0.947812,
  "lng": 100.417523,
  "recorded_at": "2025-10-23T20:00:00+07:00",
  "accuracy_m": 10
}
```

---

## 🧭 Swagger Documentation

Generate Dokumentasi:  
```bash
php artisan l5-swagger:generate
```

Buka di Browser:  
```
http://localhost:8000/api/documentation
```

Swagger UI akan menampilkan seluruh endpoint API dengan form interaktif.

---

## 🗺️ Struktur Folder

```
app/
 ├── Http/
 │   ├── Controllers/
 │   │   ├── Auth/
 │   │   │   └── AuthController.php
 │   │   └── UserLocationController.php
 ├── Models/
 │   └── LatestUserLocation.php
config/
 └── l5-swagger.php
```

---

## 🧾 Lisensi
Proyek ini dibuat untuk keperluan **demo & pembelajaran**.  
Bebas dimodifikasi sesuai kebutuhan.

---

### ✨ Author
**Jimmy Wira Arba’a**  
💼 Diskominfo Padang  
📧 jimmywiraarbaa03@gmail.com  
🌐 [jimmywiraarbaa.my.id](https://jimmywiraarbaa.my.id)
