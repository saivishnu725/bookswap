#!/bin/bash
echo "======================================================"
echo "  Student BookSwap Database Setup for Linux           "
echo "======================================================"

# Set the path to the MySQL executable in your XAMPP installation
MYSQL_EXECUTABLE="/opt/lampp/bin/mysql"

# Set the name of the SQL file containing the database schema
SQL_FILE="./src/backend/database/queries.sql"

# Set the database user (default for XAMPP is 'root')
DB_USER="root"

# Check if the SQL file exists in the current directory
if [ ! -f "$SQL_FILE" ]; then
  echo ""
  echo "The required file '$SQL_FILE' was not found."
  echo "Please place this script in the same folder as your SQL file and run it again."
  exit 1
fi

# Check if the MySQL executable path is correct
if [ ! -f "$MYSQL_EXECUTABLE" ]; then
  echo ""
  echo "The MySQL client was not found at '$MYSQL_EXECUTABLE'."
  echo "Please check your XAMPP installation path and update this script if necessary."
  exit 1
fi

echo ""
echo "Executing database setup from: $SQL_FILE"
echo "You will be prompted for the MySQL password for user '$DB_USER'."
echo "NOTE: For a default XAMPP setup, the password is blank. Just press Enter."
echo ""

# Execute the SQL script using input redirection
"$MYSQL_EXECUTABLE" -u "$DB_USER" -p < "$SQL_FILE"

if [ $? -ne 0 ]; then
  echo ""
  echo "FAILED! An error occurred during the database setup."
  echo "Please review the error messages above."
else
  echo ""
  echo "SUCCESS!"
  echo "The bookswap database and all associated tables have been created."
fi
