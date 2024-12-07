@echo off
echo Apache is trying to start - please wait  ...

apache\bin\httpd

if errorlevel 0 goto finish
if errorlevel 1 goto error
goto finish

:error
echo.
echo Apache could not be started
pause

:finish
pause