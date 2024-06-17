<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Requirements

-   PHP >= 8.2
-   Node >= 20.13.1
-   Composer
-   MySQL 8.0
-   Laravel 11.x

# How to install ?

Berikut adalah langkah-langkahnya:

1. Pertama, buka direktori tempat kumpulan project anda, lalu buka terminal.
2. Ketik perintah
   `git clone https://github.com/dwirobbin/it_helpdesk.git` atau bisa download manual dengan meng-klik tautan https://github.com/dwirobbin/it_helpdesk/archive/refs/heads/main.zip.
3. Jika sudah selesai meng-clone atau mendownload, akses folder hasil clone/download (nama project: it_helpdesk) dengan cara mengetikkan `cd it_helpdesk` pada terminal.
4. Lalu ketikkan perintah `composer install` dan tunggu sampai selesai prosesnya.
5. Kemudian ketik `npm install`, ini juga harus ditunggu sampai selesai.
6. Lanjut meng-copy file .env.example menjadi .env menggunakan perintah `cp .env.example .env` (asumsi menggunakan OS turunan UNIX) atau lakukanlah copy secara manual.
7. Terus jalankan perintah `php artisan key:generate` pada terminal.
8. Silahkan buat database menggunakan DBMS kesukaan kalian (ex: phpMyAdmin).
9. Buka file .env, lalu edit pada bagian berikut :

    ```
    APP_DEBUG=false
    APP_TIMEZONE=Asia/Jakarta

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=it_helpdesk
    DB_USERNAME=root
    DB_PASSWORD=
    ```

    Sesuaikan settingan database dengan punya kalian.

10. Ngapain lagi ? di terminal jalankan perintah ini `php artisan migrate --seed`
11. Ketik `php artisan storage:link` pada terminal.
12. Untuk menjalankan project ketik perintah `php artisan serve`.
13. Ada tambahan lagi nih, buka 1 tab terminal lagi pada directory project it_helpdesk, lalu ketikkan perintah `npm run dev`.
14. Dan untuk melihat hasilnya.. buka link http://127.0.0.1:8000 pada browser kesayangan anda.

<hr>

# Data Akun

| **Role**    | **Email**       | **Password** |
| ----------- | --------------- | ------------ |
| Super Admin | super@admin.com | password     |
| It Support  | xonny@gmail.com | password     |
| Staff       | xanny@gmail.com | password     |

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
