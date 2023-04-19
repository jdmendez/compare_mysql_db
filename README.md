# compare_mysql_db
Quick script to compare two mysql databases.  It quickly check if there are missing tables in one of the tables and missing fields.  Useful when developing and checking if production and development databases are up-to-sync.
Quick setup

1. Modify the script with any text editor, adding the user and password that has access to both databases.
2. Run as follows php -f compare_mysql_db.php <database_name1> <database_name2>

if need more detail, modify the variable to be true: $verbose = true;
