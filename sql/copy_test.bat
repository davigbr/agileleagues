mysql -uroot agileleagues < tables\access_log.sql
mysqldump -uroot agileleagues --routines --no-data > test.sql
powershell -Command "(gc test.sql) -replace 'InnoDB', 'MEMORY' | Out-File -Encoding UTF8 test.sql " 
mysql -uroot agileleagues_test < test.sql
