<?php
/*
	make a list of records with duplicate first name, last name, dob and gender
	
	find all the dupes from the first set that are missing from the second set
	
	print details of these records to a file
	
	syntax:
	php findupefirstnamelastnamedobgenders.php 2016-11-07
	output dupes/firstnamelastnamedobgenders_2016-11-07.txt
*/
$outputfilename = 'dupes/firstnamelastnamedobgenders_'.$argv[1].'.txt';

$outputfile = fopen( $outputfilename, 'w' );
$fullnames = file( $argv[1].'/firstnamelastnamedobsnogender.txt' );
$addressesetc = file( $argv[1].'/addressesetc.txt' );



//find all the dupes in first list
//find all the dupes in the second list
//find dupes from the first list that aren't on the second list

array_multisort( $fullnames, $addressesetc );

$uniquenames = array_unique( $fullnames );
$dupenames = array_diff_key( $fullnames,  $uniquenames );

print_r( $dupenames );
echo 'count = '.count( $dupenames )."\n";

$firstkey = 0;
//print all info for each dupe to output file
foreach( $dupenames as $dupe )
{
	//find first match, then keep going until the data changes
	//$key = binary_search( $names1, $firstkey, sizeof( $names1 ), $dupe );
	$key = array_search( $dupe, $fullnames );
	$k = $key;
	echo $dupe.' - '.$k."\n";
	while( $fullnames[ $k ] == $dupe )
	{
		$line = str_replace( "\n", "\t", $fullnames[ $k ]."\t".$addressesetc[ $k ] )."\n";
		echo $line;
		fwrite( $outputfile, $line );
		$k++;
	}
	fwrite( $outputfile, "\n" );
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