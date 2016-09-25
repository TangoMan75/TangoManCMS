@echo off
title Clear prod cache
echo Clear prod cache
echo.
echo.
echo.
php bin/console cache:clear --env=prod
pause