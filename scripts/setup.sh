#!/bin/bash
echo "======================================================"
echo " Setting up Student BookSwap for Linux..."
echo "======================================================"

# Get the absolute path to the script's directory, then find the src folder
PROJECT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )/src"

# The target path in XAMPP's web directory
TARGET_DIR="/opt/lampp/htdocs/bookswap"

# Check if the target file/link already exists
if [ -e "$TARGET_DIR" ]; then
    echo ""
    echo "A file or link already exists at $TARGET_DIR."
    echo "Please remove it and run the script again:"
    echo "sudo rm -rf $TARGET_DIR"
    exit 1
fi

echo ""
echo "Creating a symbolic link to the XAMPP server..."
echo "  Source: $PROJECT_DIR"
echo "  Target: $TARGET_DIR"
echo ""

# Create the symbolic link using sudo
sudo ln -s "$PROJECT_DIR" "$TARGET_DIR"

echo "SUCCESS!"
echo "You can now access the project at http://localhost/bookswap"
echo "Don't forget to start XAMPP: sudo /opt/lampp/lampp start"