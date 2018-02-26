<?php
/*
Counts the number of duplicate voter IDs in a dataset, writes them to output file

The voter id is the part of the full id before the hyphen

*/

$outputfilename = 'dupevoterids.txt';
$outputfile = fopen( $outputfilename, 'w');
$ids = file( 'voterids.txt' );
$uniqueids = array_unique( $ids );


$dupeids = array_diff_key( $ids,  $uniqueids );

foreach ( $dupeids as $dupeid )
{
	fwrite( $outputfile, $dupeid );
}

echo 'count dupe ids = '.count( $dupeids )."\n";

fclose( $outputfile );

?>