@echo off
title Sistem Informasi Skrining Mental Mahasiswa (SPK AI)
color 0B

echo ======================================================================
echo    SISTEM PENDUKUNG KEPUTUSAN SKRINING KESEHATAN MENTAL MAHASISWA
echo ======================================================================
echo.
echo [1] Menjalankan Web PHP Server di http://localhost:8000
echo [2] Menjalankan Backend Flask ML API di http://localhost:5000 (Opsional)
echo.
echo CATATAN:
echo - Pastikan MySQL (XAMPP) sudah aktif dan database.sql sudah diimport.
echo - Web PHP sudah dilengkapi Dual-Engine: bisa panggil Flask API atau
echo   otomatis fallback ke Python CLI jika Flask belum dijalankan!
echo.
echo Membuka browser ke http://localhost:8000 ...
start http://localhost:8000
echo.
echo Server PHP berjalan (Tekan Ctrl+C untuk berhenti)...
php -S localhost:8000
pause
