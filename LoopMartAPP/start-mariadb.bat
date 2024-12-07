@echo off
echo MariaDb is trying to start - please wait ...

mariadb\bin\mysqld --no-defaults

if errorlevel 0 goto finish
if errorlevel 1 goto error
goto finish

:error
echo.
echo MariaDb could not be started
pause

:finish
pause