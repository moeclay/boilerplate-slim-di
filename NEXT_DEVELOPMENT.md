Untuk fitur CRUD baru, pola yang paling rapi di project ini biasanya ubah atau tambah:

- `config/routes.php` → daftar route
- `src/Domain/<Feature>/Controller.php` → terima request dan kirim response
- `src/Domain/<Feature>/Service.php` → logika bisnis
- `src/Infrastructure/...` → kalau butuh DB/mail/security/helper baru
- `database/migrations/` → kalau tabel/kolom baru
- `database/seeds/` → kalau butuh data awal
- `src/Middleware/` → kalau fitur butuh proteksi khusus

## Pola kerjanya
```text
Route -> Controller -> Service -> Database / Infrastruktur -> Response
```

## Kalau CRUD sederhana
Biasanya cukup:
- route
- controller
- service
- migration kalau tabel baru

## Kalau CRUD lebih serius
Tambah juga:
- repository layer
- validator
- request/response helper
- policy atau middleware auth

## Contoh saat bikin fitur `Product`
Anda biasanya buat:
- `config/routes.php`
- `src/Domain/Product/ProductController.php`
- `src/Domain/Product/ProductService.php`
- `database/migrations/...create_products_table.php`
- `database/seeds/...`

## Jadi jawabannya
**Ya**, alurnya memang update/tambah:
- `route`
- `domain`
- `controller`
- `service`

Lalu **kalau perlu** tambah:
- migration
- seed
- middleware
- infrastructure class

