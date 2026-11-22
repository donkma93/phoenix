@echo off
title Phoenix Logistics Server
echo [1/2] Kiem tra MariaDB...
netstat -ano | findstr 127.0.0.1:3306 >nul
if %errorlevel% equ 0 (
    echo MariaDB dang chay tren cong 3306.
) else (
    echo Khoi dong MariaDB tren cong 3306...
    start "MariaDB" /min "C:\Users\donpv\mariadb-10.11.9-winx64\bin\mariadbd.exe" --datadir="C:\Users\donpv\mariadb-10.11.9-winx64\data" --port=3306
    timeout /t 2 /nobreak >nul
)

echo [2/2] Khoi dong Laravel Development Server...
echo Truy cap: http://127.0.0.1:8000
php artisan serve --host=127.0.0.1 --port=8000

