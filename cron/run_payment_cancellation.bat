@echo off
rem This batch file runs the cancel_expired_payments.php script

cd /d C:\laragon\www\hotel-reservation\cron
php cancel_expired_payments.php >> payment_cancellation.log 2>&1
