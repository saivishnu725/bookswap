@echo off
echo =================================================================
echo  BookSwap Database Setup Script for XAMPP
echo  - Creates the 'BS' database and tables from queries.sql
echo =================================================================

TITLE BookSwap Database Setup

echo --- Configuration ---
SET "MYSQL_EXECUTABLE=C:\xampp\mysql\bin\mysql.exe"
SET "SQL_FILE=.\src\backend\database\queries.sql"
SET "DB_USER=root"

cls
echo.
echo  ======================================
echo    BookSwap Database Setup Script
echo  ======================================
echo.

echo --- Pre-run Checks ---
echo Check if the SQL file exists in the current directory
IF NOT EXIST "%SQL_FILE%" (
    echo ERROR: The file '%SQL_FILE%' was not found in this directory.
    echo Please make sure this script is in the same folder as your SQL file.
    echo.
    pause
    exit /b
)

echo Check if the MySQL executable exists at the specified path
IF NOT EXIST "%MYSQL_EXECUTABLE%" (
    echo ERROR: MySQL executable not found at '%MYSQL_EXECUTABLE%'.
    echo Please verify your XAMPP installation path and update the script if needed.
    echo.
    pause
    exit /b
)


echo --- Execution ---
echo You will now be prompted for the MySQL root password.
echo NOTE: For a default XAMPP install, the password is blank.
echo Just press ENTER at the password prompt.
echo.
echo --- Executing SQL script, please wait... ---
echo.

"%MYSQL_EXECUTABLE%" -u %DB_USER% -p < "%SQL_FILE%"

echo --- Check for errors ---
IF %ERRORLEVEL% NEQ 0 (
    echo.
    echo --- SCRIPT FINISHED WITH ERRORS ---
    echo There was a problem creating the database or tables.
    echo Please review any error messages above.
) ELSE (
    echo.
    echo --- SCRIPT FINISHED SUCCESSFULLY ---
    echo The 'bookswap' database and all tables were created successfully!
)

echo.
pause