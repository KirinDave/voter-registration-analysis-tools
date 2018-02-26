<?php
/*
Counts the number of duplicate records in a dataset, writes them to output file
*/

$outputfilename = 'dupe'.$argv[1];
$outputfile = fopen( $outputfilename, 'w');
$records = file( $argv[1] );
$uniquerecords = array_unique( $records );

$duperecords = array_diff_key( $records,  $uniquerecords );

foreach ( $duperecords as $duperecord )
{
	fwrite( $outputfile, $duperecord );
}

echo 'count dupe records = '.count( $duperecords )."\n";

fclose( $outputfile );

?>