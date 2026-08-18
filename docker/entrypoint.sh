#!/bin/sh
set -e
cd /var/www/html

DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"

echo "[entrypoint] Menunggu database ${DB_HOST}:${DB_PORT} ..."
until php -r '$h=getenv("DB_HOST")?:"db";$p=(int)(getenv("DB_PORT")?:3306);exit(@fsockopen($h,$p)?0:1);' 2>/dev/null; do
    sleep 2
done
echo "[entrypoint] Database siap."

# APP_KEY: pakai dari .env bila ada; jika kosong, buat kunci sementara.
if [ -z "${APP_KEY:-}" ]; then
    APP_KEY="$(php artisan key:generate --show)"
    export APP_KEY
    echo "[entrypoint] APP_KEY belum diset -> memakai kunci SEMENTARA."
    echo "[entrypoint] Agar sesi login stabil antar-restart, set APP_KEY di file .env."
fi

php artisan package:discover --ansi >/dev/null 2>&1 || true

echo "[entrypoint] Migrasi database ..."
php artisan migrate --force

# Seed HANYA saat Knowledge Base masih kosong (first run) supaya data
# yang dimasukkan expert tidak terhapus saat container restart.
# Pakai PDO PHP (mendukung MySQL 8 caching_sha2), bukan mysql client.
CNT=$(php -r '
    try {
        $pdo = new PDO(
            sprintf("mysql:host=%s;port=%s;dbname=%s", getenv("DB_HOST")?:"db", getenv("DB_PORT")?:"3306", getenv("DB_DATABASE")),
            getenv("DB_USERNAME"), getenv("DB_PASSWORD")
        );
        echo (int) $pdo->query("SELECT COUNT(*) FROM risk_factors")->fetchColumn();
    } catch (Throwable $e) { echo "ERR"; }
' 2>/dev/null)
if [ "${CNT}" = "0" ]; then
    echo "[entrypoint] Knowledge Base kosong -> seeding ..."
    php artisan db:seed --class=KnowledgeBaseSeeder --force
    if [ "${SEED_DEMO:-true}" = "true" ]; then
        php artisan db:seed --class=DemoSeeder --force
    fi
else
    echo "[entrypoint] Knowledge Base sudah terisi (${CNT} faktor) -> lewati seeding."
fi

# Cache konfigurasi & view (route:cache dilewati karena route '/' memakai closure).
php artisan config:cache
php artisan view:cache

chown -R www-data:www-data storage bootstrap/cache

echo "[entrypoint] Siap. Menjalankan: $*"
exec "$@"
