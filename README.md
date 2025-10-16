# book swap

the WT project

## Setup

1. install XAMPP
2. start the MySQL, Apache services
3. run the `.\scripts\windows\setup.bat` if you are on windows or `./scripts/linux/setup.sh` on linux
4. setup database
   1. SCRIPT: `.\scripts\windows\setup_database.bat` on Windows or `./scripts/linux/setup_database.sh` for linux
   2. MANUAL: copy the contents of `queries.sql` go to `http://localhost/phpmyadmin/index.php?route=/server/sql&lang=en` and run the query to create all the tables
5. then access it on http://localhost/bookswap

## License

[GNU General Public License v2.0](https://choosealicense.com/licenses/gpl-2.0/)
