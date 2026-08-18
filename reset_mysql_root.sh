#!/bin/bash
#
# Reset password root MySQL (instalasi resmi macOS di /usr/local/mysql).
# TIDAK menghapus data — hanya mengganti password root.
#
# Pakai:
#   bash reset_mysql_root.sh 'PasswordBaruAnda'
#
# Anda akan diminta password admin Mac (untuk sudo) satu kali.
#
set -u

NEWPASS="${1:-}"
if [ -z "$NEWPASS" ]; then
  echo "Usage: bash reset_mysql_root.sh '<password_root_baru>'"
  exit 1
fi

BIN=/usr/local/mysql/bin
PLIST=/Library/LaunchDaemons/com.oracle.oss.mysql.mysqld.plist

echo "==> [1/6] Menghentikan service MySQL..."
sudo launchctl bootout system "$PLIST" 2>/dev/null || true
# fallback: hentikan proses mysqld yang masih jalan
sudo pkill -x mysqld 2>/dev/null || true
sleep 3

echo "==> [2/6] Menjalankan MySQL mode aman (skip-grant-tables)..."
sudo "$BIN/mysqld_safe" --skip-grant-tables --skip-networking >/tmp/mysql_reset.log 2>&1 &
sleep 7

echo "==> [3/6] Mereset password root..."
"$BIN/mysql" -u root <<SQL
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED BY '${NEWPASS}';
FLUSH PRIVILEGES;
SQL
RC=$?
if [ $RC -ne 0 ]; then
  echo "!! Gagal mereset password. Lihat /tmp/mysql_reset.log"
fi

echo "==> [4/6] Mematikan server mode aman..."
sudo "$BIN/mysqladmin" -u root -p"${NEWPASS}" shutdown 2>/dev/null \
  || { sudo pkill -f mysqld_safe 2>/dev/null; sudo pkill -x mysqld 2>/dev/null; }
sleep 3

echo "==> [5/6] Menjalankan ulang MySQL secara normal..."
sudo launchctl bootstrap system "$PLIST" 2>/dev/null || sudo launchctl load -w "$PLIST" 2>/dev/null || true
sleep 4

echo "==> [6/6] Verifikasi..."
"$BIN/mysql" -u root -p"${NEWPASS}" -e "SELECT 'root OK' AS status, VERSION() AS versi;" \
  && echo "SELESAI ✔  Password root MySQL sudah diganti." \
  || echo "!! Verifikasi gagal. Coba: mysql -u root -p"
