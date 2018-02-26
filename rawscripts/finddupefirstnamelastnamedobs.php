<?php
/*
	make a list of records with duplicate first name, last name, dob and gender
	
	find all the dupes from the first set that are missing from the second set
	
	print details of these records to a file
	
	syntax:
	php finddupefirstnamelastnamedobs.php 2016-11-07
	output dupes/firstnamelastnamedobs_2016-11-07.txt
*/
$outputfilename = 'dupes/firstnamelastnamedobs_'.$argv[1].'.csv';
$outputfile = fopen( $outputfilename, 'w' );

$fullnames = file( $argv[1].'/firstnamelastnamedobsnogender.txt' );
$fullnameswithgender = file( $argv[1].'/firstnamelastnamedobs.txt' );
$addressesetc = file( $argv[1].'/addressesetc.txt' );
$fullids = file( $argv[1].'/fullids.txt' );

//find all the dupes in first list
//find all the dupes in the second list
//find dupes from the first list that aren't on the second list

array_multisort( $fullnames, $fullnameswithgender, $addressesetc, $fullids );

//remove hyphens from feb full ids for numeric comparison
$febnumericfullids =  array();
$febids = file( '2017-02-27/fullids.txt' );
$febaddressesetc = file( '2017-02-27/addressesetc.txt' );
foreach( $febids as $febid )
{
	$febnumericfullids[] = (int) str_replace( '-', '', $febid );
	
}
array_multisort( $febnumericfullids, $febids, $febaddressesetc );

$uniquenames = array_unique( $fullnames );
$dupenames = array_diff_key( $fullnames,  $uniquenames );

print_r( $dupenames );
echo 'count = '.count( $dupenames )."\n";
//return;

$firstkey = 0;
//print all info for each dupe to output file
foreach( $dupenames as $dupe )
{
	//find first match, then keep going until the data changes
	//$key = binary_search( $fullnames, $firstkey, sizeof( $fullnames ), $dupe );
	$key = array_search( $dupe, $fullnames );
	$k = $key;

	//echo $dupe.' - '.$k."\n";
	$i = 0;
	$addresses = array();
	$lines = array();
	$voteridsforset = array();
	while( $fullnames[ $k ] == $dupe )
	{
		// search for full id in Feb data
		$firstkey = $key2;
		//divide "name" into name, dob, gender
		//since we trimmed the data, if the last character is numeric there is no gender
		$numericid = (int) str_replace( '-', '', $fullids[ $k ] );
		$key2 = binary_search( $febnumericfullids, $firstkey, sizeof( $febnumericfullids ), $numericid );
		if( $key2 )
		{
			$febdata = $febaddressesetc[ $key2 ];
		}
		else
		{
			$febdata = '';
		}
	
		$addressparts = explode ( "\t", $addressesetc[ $k ] );
		$addresses[] = $addressparts[0];
		$lines[] = str_replace( "\n", "\t", $fullids[ $k ]."\t".$fullnameswithgender[ $k ]."\t".$addressesetc[ $k ]."\t".$febdata )."\n";
		$voteridparts = explode( '-', $fullids[ $k ] );
		$vid = $voteridparts[0];
		$voteridsforset[] = $vid;
		$k++;
	}
	array_multisort( $addresses, $lines );
	
	foreach( $lines as $line )
	{
		if( count( array_unique( $voteridsforset ) ) > 1 )
		{
			echo $line;
			fwrite( $outputfile, $line );
		}
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
function binary_search( $a, $first, $last, $value )
{
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