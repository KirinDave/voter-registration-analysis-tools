<?php
/*
	make a list of voterids for voters with duplicate firstname dob and genders from one data set
	compare that to the same list for a second data set.
	
	find all the dupes from the first set that are missing from the second set
	
	print details of these records to a file
	
	syntax:
	php comparedupefirstnamedobgenders.php 2016-11-07 2017-07-31
	output dupes/firstnamedobgenders_2016-11-07_2017-07-31.txt
*/
$outputfilename = 'dupes/firstnamedobgenders_'.$argv[1].'_'.$argv[2].'.txt';
$outputfile = fopen( $outputfilename, 'w' );
$names1 = file( $argv[1].'/firstnamedobs.txt' );
$fullids1 = file( $argv[1].'/fullids.txt' );
$fullnames1 = file( $argv[1].'/firstnamelastnamedobs.txt' );
$addressesetc1 = file( $argv[1].'/addressesetc.txt' );

$names2 = file( $argv[2].'/firstnamedobs.txt' );


//find all the dupes in first list
//find all the dupes in the second list
//find dupes from the first list that aren't on the second list
$uniquenames1 = array_unique( $names1 );
$dupenames1 = array_diff_key( $names1,  $uniquenames1 );

$uniquenames2 = array_unique( $names2 );
$dupenames2 = array_diff_key( $names2,  $uniquenames2 );

$dupesfromdate1only = array_diff( $dupenames1 , $dupenames2 );
print_r( $dupesfromdate1only  );
echo 'count = '.count( $dupesfromdate1only )."\n";
//return;
sort( $dupesfromdate1only );
array_multisort( $names1, $fullids1, $fullnames1, $addressesetc1 );
$firstkey = 0;
//print all info for each dupe to output file
foreach( $dupesfromdate1only as $dupe )
{
	//find first match, then keep going until the data changes
	//$key = binary_search( $names1, $firstkey, sizeof( $names1 ), $dupe );
	$key = array_search( $dupe, $names1 );
	$k = $key;
	echo $dupe.' - '.$k."\n";
	while( $names1[ $k ] == $dupe )
	{
		$line = str_replace( "\n", "\t", $fullids1[ $k ]."\t".$fullnames1[ $k ]."\t".$addressesetc1[ $k ] )."\n";
		echo $line;
		fwrite( $outputfile, $line );
		$k++;
	}
	$firstkey = $k;
}

fclose( $outputfile );
/*
* Parameters: 
*   $a - The sorted array.
*   $first - First index of the array to be searched (inclusive).
*   $last - Last index of the array to be searched (exclusive).
*   $value - The value to be searched for.
*
* Return:
*   index of the search key if found, otherwise return false. 
*   insert_index is the index of smallest element that is greater than $value or sizeof($a) if $value
*   is larger than all elements in the array.
*/
function binary_search( $a, $first, $last, $value ) {
	$lo = $first; 
	$hi = $last - 1;

	while ($lo <= $hi) {
		$mid = (int)(($hi - $lo) / 2) + $lo;
		$cmp = $a[$mid] - $value;

		if ($cmp < 0) {
			$lo = $mid + 1;
		} elseif ($cmp > 0) {
			$hi = $mid - 1;
		} else {
			return $mid;
		}
	}
	return false;
}
?>