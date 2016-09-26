@echo off
title Clear prod cache
echo php bin/console cache:clear --env=prod
echo.
echo.
echo.
php bin/console cache:clear --env=prod
pause
