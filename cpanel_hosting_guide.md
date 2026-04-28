# Panduan Hosting Laravel 'justtwo' ke cPanel menggunakan Git

Dokumen ini menjelaskan langkah-langkah untuk melakukan hosting project Laravel kamu ke cPanel dengan otomatisasi Git.

## 1. Persiapan di cPanel

### Step 1: Buat SSH Key (Jika belum ada)
1. Login ke cPanel.
2. Cari menu **SSH Access**.
3. Klik **Manage SSH Keys** -> **Generate a New Key**.
4. Isi password, lalu klik **Generate Key**.
5. Setelah terbuat, klik **Manage** pada key tersebut dan klik **Authorize**.
6. Klik **View/Download** pada Private Key dan simpan di komputer kamu (jika ingin push dari lokal langsung ke cPanel).
7. Klik **View/Download** pada Public Key, copy isinya, dan masukkan ke **Deploy Keys** di GitHub/GitLab kamu (jika menggunakan remote repo).

### Step 2: Konfigurasi Git Version Control
1. Cari menu **Git™ Version Control** di cPanel.
2. Klik **Create**.
3. **Clone URL**: Masukkan URL repository kamu (misal: `git@github.com:username/justtwo.git`).
4. **Repository Path**: Masukkan nama folder (misal: `repositories/justtwo`). *Jangan langsung ke public_html agar aman.*
5. **Repository Name**: `justtwo`.
6. Klik **Create**.

---

## 2. Automasi Deployment ke `public_html`

Untuk memindahkan file dari folder repository ke `public_html` secara otomatis setiap kali kamu push, kita gunakan file `.cpanel.yml`.

### Step 1: Buat file `.cpanel.yml` di root project
Buat file baru bernama `.cpanel.yml` di folder `justtwo` kamu dengan isi berikut:

```yaml
deployment:
  tasks:
    - export DEPLOYPATH=/home/username_cpanel/public_html/
    - /bin/cp -R public/* $DEPLOYPATH
    - /bin/cp public/.htaccess $DEPLOYPATH
```
*Ganti `username_cpanel` dengan username cPanel kamu.*

### Step 2: Push ke Remote
Setiap kali kamu melakukan perubahan, cukup push ke GitHub/GitLab:
```bash
git add .
git commit -m "Update deployment"
git push origin main
```

### Step 3: Pull & Deploy di cPanel
1. Masuk ke **Git™ Version Control** di cPanel.
2. Klik **Manage** pada repo `justtwo`.
3. Klik tab **Pull or Deploy**.
4. Klik **Update from Remote** untuk mengambil code terbaru.
5. Klik **Deploy HEAD Commit** untuk menjalankan instruksi di `.cpanel.yml`.

---

## 3. Penyesuaian Laravel (PENTING)

Karena Laravel menggunakan folder `public` sebagai entry point, ada dua cara untuk hosting di cPanel:

### Cara A: Symlink (Direkomendasikan)
Jika cPanel kamu mengizinkan, hapus folder `public_html` (backup dulu!) lalu buat symlink:
```bash
ln -s /home/username/repositories/justtwo/public /home/username/public_html
```

### Cara B: Edit `index.php`
Jika ingin file project ada di luar dan hanya isi folder `public` yang ada di `public_html`:
1. Pastikan `.cpanel.yml` sudah mengcopy isi folder `public` ke `public_html`.
2. Edit file `public_html/index.php`, ubah path berikut:
```php
// Dari:
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Menjadi (sesuaikan dengan lokasi folder project kamu):
require __DIR__.'/../repositories/justtwo/vendor/autoload.php';
$app = require_once __DIR__.'/../repositories/justtwo/bootstrap/app.php';
```

---

## 4. Finalisasi di Terminal cPanel (Terminal/SSH)

Setelah file ter-deploy, kamu perlu menjalankan perintah ini di terminal cPanel:
1. `cd repositories/justtwo`
2. `composer install --no-dev`
3. `cp .env.example .env` (lalu edit `.env` dengan kredensial DB hosting)
4. `php artisan key:generate`
5. `php artisan migrate`
6. `php artisan storage:link`

---

**Selesai!** Project `justtwo` kamu sekarang sudah terhubung dengan Git dan siap di-deploy ke cPanel.
