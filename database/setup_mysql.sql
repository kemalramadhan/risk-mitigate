-- Setup database MySQL untuk Risk Mitigate.
-- Jalankan sebagai root:
--   mysql -u root -p < database/setup_mysql.sql
--
-- Ganti 'CHANGE_ME_STRONG_PASSWORD' dengan password yang Anda inginkan,
-- lalu isi nilai yang sama pada DB_PASSWORD di file .env.

CREATE DATABASE IF NOT EXISTS risk_mitigate
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'risk_mitigate'@'127.0.0.1'
    IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';

CREATE USER IF NOT EXISTS 'risk_mitigate'@'localhost'
    IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';

GRANT ALL PRIVILEGES ON risk_mitigate.* TO 'risk_mitigate'@'127.0.0.1';
GRANT ALL PRIVILEGES ON risk_mitigate.* TO 'risk_mitigate'@'localhost';

FLUSH PRIVILEGES;
