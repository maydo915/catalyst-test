<?php
/*
 * Name: May Do
 * Date: 9 Feb 2016
 * Task description: Create a PHP script that reads and processes CVS file, the parsed file data is to be inserted into a MySQL database.
 */

// Setup a connection to server using MySQL host, username and password
// These sensitive credentials can be stored in a separated file and placed in one upper level directory for security purposes.
$mysql_host = "localhost";
$mysql_user = "may_do";
$mysql_password = "H7GFL5CyzU5JCxvT";
$mysql_database = "catalyst_test";

try {
    // Make a connection to database server using paramaters of driver and given credentials
    $connect_mysql = new PDO("mysql:host=$servername;dbname=$mysql_database", $mysql_user, $mysql_password);
    
    // Set the PDO error mode to exception
    $connect_mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connection is made successfully"; 
	
    // Set a query to retrieve user data 
    $select_user = "SELECT userID FROM users LIMIT 1";

    // Check whether table users exist
    $tableExists = $connect_mysql->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;

    // Create table users if not exist
    if(!$tableExists){
        $create_users_table = "CREATE TABLE users (
                UserID INT(6) UNSIGNED AUTO_INCREMENT,
                Name VARCHAR(20) NOT NULL,
                Surname VARCHAR(20) NOT NULL,
                Email VARCHAR(50) NOT NULL,
                PRIMARY KEY(UserID),
                UNIQUE KEY (Email)
        )" ;

        $connect_mysql->exec($create_users_table);
        echo '<p>Table users created sucessfully. </p>';
    }

    
    /* ----------------------------------------------------------------------
        Script will iterate through the CSV rows and insert each record into a 
        MySQL database into the table “users”
    ------------------------------------------------------------------------ */
    
    // Open a stream from a csv file 
    // CVS file is assumed located on the same directory
    $filename = "users.csv";
    
    // Open CSV file for reading
    $source = fopen($filename, "r");

    // Display all user's data from csv file
    while( ! feof($source)){
        // Parses a line from an open file, checking for CSV fields.
        $line = fgetcsv($source);

        // Escapes special characters in a string for use in an SQL statement
        $name = mysql_real_escape_string($line[0]);
        // Convert name & surname to be captitialised
        $name = ucwords(strtolower($name));
        echo $name;
        
        // Escapes special letters in surname string for use in SQL statement
        $surname = mysql_real_escape_string($line[1]);
        $surname = ucwords(strtolower($surname));
        echo ' ' .$surname;

        // Convert email field into lowercase
        $email = mysql_real_escape_string($line[2]);
        $email = strtolower($email);
        echo " " .$email;

        // Validate email address such as @ and . (dot) characters
        if  (!(strstr($email, "@")) or !(strstr($email, "."))){
            // Display error message for invalid email format
            // This message should be reported to STDOUT.
            echo '<p>Sorry, email is invalid format. This record is not inserted to Mysql database.</p>';
        }else{ 
            // Set query for inserting data to MySQL database
            $insert_to_users = "INSERT INTO users VALUES('', '$name', '$surname', '$email')";
            // Insert user record into Mysql database table users
            $connect_mysql->exec($insert_to_users);

            echo " - record inserted successfully";
        }
        print_r("<br>");	
            
    } //end while

    // Close file stream
    fclose($source);
    
    // Prompt user for ending inserting of records
    echo '<p>Records have successfully inserted.</p>';
    
} catch(PDOException $e){
    echo "<p>Errors: " . $e->getMessage(). '</p>';
}
