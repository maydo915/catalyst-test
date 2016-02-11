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
    // Make a connection to server using paramaters of driver and given credentials
    $connect_mysql = new PDO("mysql:host=$servername;dbname=$mysql_database", $mysql_user, $mysql_password);
    
    // Set the PDO error mode to exception
    $connect_mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<strong>Connection is made successfully</strong>"; 

    // Define a query for searching any existing talbe users
    $search_users_sql = "SHOW TABLES LIKE 'users'";
    $search_stmt = $connect_mysql->prepare($search_users_sql);
    
    // Execute the sql to search table users
    $search_stmt->execute();
   
    $table_user_exist = $search_stmt->rowCount();
    if(!$table_user_exist){
        $create_users_table = "CREATE TABLE users (
            UserID INT(6) UNSIGNED AUTO_INCREMENT,
            Name VARCHAR(20) NOT NULL,
            Surname VARCHAR(20) NOT NULL,
            Email VARCHAR(50) NOT NULL,
            PRIMARY KEY(UserID),
            UNIQUE KEY (Email)
        )" ;
        
        // Prepare a statement to create new table 
        $create_table_stmt = $connect_mysql->prepare($create_users_table);
        // Execute the stament to create a new table users
        $create_table_stmt->execute();
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
    
    // Count rows in CSV file
    $line_number = 1;

    // Display all user's data from csv file
    while( ! feof($source)){
        // Parses a line from an open file, checking for CSV fields.
        // This return a index array.
        $line = fgetcsv($source);
        
        // Escapes special characters in a string for use in an SQL statement
        $name = mysql_real_escape_string($line[0]);
        // Convert name & surname to be captitialised
        $name = ucwords(strtolower($name));
        
        // Escapes special letters in surname string for use in SQL statement
        $surname = mysql_real_escape_string($line[1]);
        $surname = ucwords(strtolower($surname));

        // Convert email field into lowercase
        $email = mysql_real_escape_string($line[2]);
        $email = strtolower($email);

        // Validate email address such as @ and . (dot) characters
        if  (!(strstr($email, "@")) or !(strstr($email, "."))){
            // Display error message for invalid email format
            echo '<p>Line ' .$line_number. ' - Email is invalid format. This record can not be inserted to Mysql database.<br>';
        }else{ 
            // Set query for inserting data to MySQL database
            // MySQL will ignore duplicated email
            $insert_to_users = "INSERT IGNORE INTO users(Name, Surname, Email) "
                    . "         VALUES(:name, :surname, :email)";
            // Prepare the query
            $stmt = $connect_mysql->prepare($insert_to_users);
            
            // Execute to insert user's data into database
            $stmt->execute(array(
                ':name' => $name , ':surname' => $surname , ':email' => $email
            ));
        }	
        
        // Increment line number as moving to next line of record
        $line_number++;
            
    } //end while

    // Close file stream
    fclose($source);
    
    // Prompt user for ending inserting of records
    echo '<p>Data being successfully inserted.<br>';
    
    // Show total lines in CSV file
    echo "There are <strong>$line_number</strong> lines in CSV file.</p>";
    echo '<hr>';

    // Dispaly number of row be inserted into DB
    $select_users = "SELECT * FROM users";
    $select_stmt = $connect_mysql->prepare($select_users);
    $select_stmt->execute();
    
    $no_rows = $select_stmt->fetchAll();
    
    // Get number of rows inserted from database
    echo "<p>There are <strong>" .count($no_rows). "</strong> rows inserted into MySQL database.</p>";
    
    // Display user's record details only if they exist
    if(count($no_rows) >= 1 ){
        $counter = 1;
        foreach($no_rows as $row){
            echo $counter. '. ' .$row['Name']. ' ' .$row['Surname']. ' ' .$row['Email']. '<br>';
            $counter++;
        }
    }else{
        echo 'No rows are inserted.';
    }
    
} catch(PDOException $e){
    echo "<p>Errors: " . $e->getMessage(). '</p>';
}
