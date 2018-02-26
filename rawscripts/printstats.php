<?php
/*
Prints the total and unique counts for a file

*/

$records = file( $argv[1] );

$recordscount = count( $records );
$uniquerecordscount = count( array_unique( $records ) );
$diff = $recordscount - $uniquerecordscount;
echo $argv[1].': total records = '.$recordscount."\n";
echo $argv[1].': total unique records = '.$uniquerecordscount."\n";
echo $argv[1].': total non-unique records = '.$diff."\n\n";


?>