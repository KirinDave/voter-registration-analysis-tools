<?php
/*
Prints the total, unique and non-unique data counts for a file containing one datum per line

Syntax:
php printcounts.php %DATAFILENAME%

Example:
php printcounts.php address_set.csv


*/

$records = file( $argv[1] );

$recordscount = count( $records );
$uniquerecordscount = count( array_unique( $records ) );
$diff = $recordscount - $uniquerecordscount;

echo $argv[1].': total records = '.$recordscount."\n";
echo $argv[1].': total unique records = '.$uniquerecordscount."\n";
echo $argv[1].': total non-unique records = '.$diff."\n\n";

?>