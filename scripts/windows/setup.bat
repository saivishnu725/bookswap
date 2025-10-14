@echo off
echo ======================================================
echo  Student BookSwap Database Setup for Windows
echo ======================================================

:: Set the path to the MySQL executable in your XAMPP installation
set "MYSQL_EXECUTABLE=C:\xampp\mysql\bin\mysql.exe"

:: Set the name of the SQL file containing the database schema
set "SQL_FILE=.\src\backend\database\queries.sql"

:: Set the database user (default for XAMPP is 'root')
set "DB_USER=root"

:: Check if the SQL file exists in the current directory
if not exist "%SQL_FILE%" (
  echo.
  echo The required file '%SQL_FILE%' was not found.
  echo Please place this script in the same folder as your SQL file and run it again.
  pause
  exit /b
)

:: Check if the MySQL executable path is correct
if not exist "%MYSQL_EXECUTABLE%" (
  echo.
  echo The MySQL client was not found at '%MYSQL_EXECUTABLE%'.
  echo Please check your XAMPP installation path and update this script if necessary.
  pause
  exit /b
)

echo.
echo Executing database setup from: %SQL_FILE%
echo You will be prompted for the MySQL password for user '%DB_USER%'.
echo NOTE: For a default XAMPP setup, the password is blank. Just press Enter.
echo.

:: Execute the SQL script using input redirection
"%MYSQL_EXECUTABLE%" -u %DB_USER% -p < "%SQL_FILE%"

if %errorlevel% neq 0 (
  echo.
  echo FAILED! An error occurred during the database setup.
  echo Please review the error messages above.
) else (
  echo.
  echo SUCCESS!
  echo The bookswap database and all associated tables have been created.
)

echo.
pause