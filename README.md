# microservices-version1

This is a full fleged VOIP application.

Before you can use it you must do several things:
1. Setup an MySQL database 'microservicesv1' using 'database/dupm2104.sql' file, with 'testuser' as user and 'testpassword' as password
2. Change the path to 'openssl.conf'($opensslConfigPath) in 'security/kljucsigurnost.php'
3. Change the path to local security folder ($keylocation) in in 'security/kljucsigurnost.php'
4. Generate secure keys for year 2019 by using 'security/generatekeys.php'
5. You can now use the application
