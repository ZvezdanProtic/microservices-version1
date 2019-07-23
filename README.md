# microservices-version1
This is a full fleged VOIP application.

Before you can use it you must do several things:
1. Copy the application to an Apache web server
2. Setup an MySQL database 'microservicesv1' using 'database/dupm2104.sql' file, with 'testuser' as user and 'testpassword' as password
3. You can chose the parameters of the connection to the database at 'database/databaseconnect.php' file
4. Generate secure keys for year 2019 by using 'security/generatekeys.php' (each new year needs new security keys)
