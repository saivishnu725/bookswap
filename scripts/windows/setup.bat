@echo off
echo ======================================================
echo  Setting up Student BookSwap for Windows...
echo ======================================================

:: Set the path to your project's source code
set "PROJECT_DIR=%cd%\src"

:: Set the target path in XAMPP's web directory
set "TARGET_DIR=C:\xampp\htdocs\bookswap"

:: Check if the target directory already exists
if exist "%TARGET_DIR%" (
  echo.
  echo A folder already exists at %TARGET_DIR%.
  echo Please remove it and run this script again.
  pause
  exit /b
)

echo.
echo Creating a link from your project folder to the XAMPP server...
echo  Source: %PROJECT_DIR%
echo  Target: %TARGET_DIR%
echo.

:: Create the symbolic link. Requires admin rights.
mklink /D "%TARGET_DIR%" "%PROJECT_DIR%"

if %errorlevel% neq 0 (
  echo.
  echo FAILED! Please try running this script as an Administrator.
  ) else (
  echo.
  echo SUCCESS!
  echo You can now access the project at http://localhost/bookswap
)

echo.
pause

