<?php
/*
 * Name: May Do
 * Date: 9 Feb 2016
 * Task description: create a script for testing PHP logics.
 */	

// Output the numbers from 1 to 100
// Pre-set a maximum number for display
$maxNumber = 100;

for($number = 0 ; $number <= $maxNumber ; $number ++){
    echo "Number: " .$number;

    // Test where the number is divisible by three (3) output the word “triple”
    if( $number % 3 == 0 ){
        echo " - triple";
    }

    // Where the number is divisible by five (5) output the word “fiver”
    if( $number % 5 == 0 ){
        echo " fiver";
    }

    //Where the number is divisible by three (3) and five (5) output the word “triplefiver”
    if( $number % 3 == 0 && $number % 5 == 0 ){
        echo " triplefiver ";
    }

    echo "<br>";
	
} // end for loop