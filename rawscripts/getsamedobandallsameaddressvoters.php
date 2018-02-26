<?php
/*
	make a list of voterids for voters with duplicate firstname dob and genders from one data set
	compare that to the same list for a second data set.
	
	find all the dupes from the first set that are missing from the second set
	
	print details of these records to a file
	
	syntax:
	php getsamedobandallsameaddressvoters.php 2016-11-07
	output dupes/sameaddressandallsamedobvoters_2016-11-07.txt
*/
$outputfilename = 'dupes/sameaddressallsamedobvoters_'.$argv[1].'.txt';
$outputfile = fopen( $outputfilename, 'w' );
$addresses = file( $argv[1].'/addresses.txt' );
$dobs = file( $argv[1].'/dobs.txt' );
$fullids = file( $argv[1].'/fullids.txt' );
$fullnames = file( $argv[1].'/firstnamelastnamedobs.txt' );
$addressesetc = file( $argv[1].'/addressesetc.txt' );
$count = 0;

$addresshashes = array();
foreach( $addresses as $address )
{
	$addresshashes[] = hexdec( md5( str_replace( "\n", '', $address ) ) );
}
unset( $addresses );
// find all the dupe addresses
// then walk through them looking for dupe dobs at the same address
$uniqueaddresses = array_unique( $addresshashes );
$dupeaddresses = array_diff_key( $addresshashes,  $uniqueaddresses );
unset( $uniqueaddresses );

echo 'addresses: '.count( $addresshashes )."\n";
echo 'addresses with more than one voter: '.count( $dupeaddresses )."\n";
//return;
echo "sorting dupeaddresses\n";
sort( $dupeaddresses );

echo "doing multisort\n";
array_multisort( $addresshashes, $dobs, $fullids, $fullnames, $addressesetc );
$firstkey = 0;
$foundaddress = '';
//print all info for each dupe to output file
foreach( $addresshashes as $dupe )
{
	//find first match, then keep going until the data changes
	$key = binary_search( $addresshashes, $firstkey, sizeof( $addresshashes ), $dupe );
	//$key = array_search( $dupe, $addresshashes );
	//echo str_replace( "\n", '', $dupe ).' - '.$key."\n";
	$lastaddress = $addresshashes[ $key ];
	$found = 0;
	while( $addresshashes[ $key ] == $lastaddress && $addresshashes[ $key ] != $foundaddress )
	{
		$name = trim( $fullnames[ $key ] );
		if( is_numeric( substr( $name, -1 ) )  )
		{
			$gender = '';
			$dob = substr( $name, -10 );
			$justname = substr( $name, 0, strlen( $name ) - 10 );

		}
		else
		{
			$gender = substr( $name, -1 );
			$dob = substr( $name, -12, -2 );
			$justname = substr( $name, 0, strlen( $name ) - 12  );
		}
		$name = $justname."	".$dob."	".$gender;
		$line = str_replace( "\n", "\t", ( $fullids[ $key ]."\t".$name."\t".$addressesetc[ $key ] ) )."\n";
		$lines[] = $line;
		$idsataddress[] = $fullids[ $key ];
		$dobsataddress[] = $dobs[ $key ];
		$lastaddress = $addresshashes[ $key ];
		$key++;
		$found = 1;
	}

	if( $found )
	{
		//echo str_replace( "\n", '', $dupe ).' - '.count( $dobsataddress )."\n";
		//if there are more dobs than uniquedobs and there are no duplicate ids, print all the occupants of that address that share a birthday
		if( count( $dobsataddress ) <= 20 && count( $dobsataddress ) > count( array_unique( $dobsataddress ) ) && count( $idsataddress ) == count( array_unique( $idsataddress ) ) )
		{
			array_multisort( $dobsataddress, $lines );
			foreach( $lines as $line )
			{
				echo $line;
				fwrite( $outputfile, $line );
			}
			echo "\n";
			fwrite( $outputfile, "\n" );
			$count++;
		}
		//$firstkey = $key;
		$foundaddress = $lastaddress;
		$idsataddress = array();
		$dobsataddress = array();
		$lines = array();
	}
}
echo 'addresses: '.count( $addresshashes )."\n";
echo 'addresses with more than one voter: '.count( $dupeaddresses )."\n";
echo 'addresses where people share a birthdate: '.$count."\n";
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
function xbinary_search( $a, $first, $last, $value ) {
	$lo = $first; 
	$hi = bcsub( $last, 1, 10 );

	while ( $lo <= $hi ) {
		$mid = (int) bcadd( bcdiv( bcsub( $hi, $lo, 10 ), 2, 10 ), $lo, 10 );
		$cmp = $a[$mid] - $value;

		if ( $cmp < 0 ) {
			$lo = bcadd( $mid, 1, 10 );
		} elseif ( $cmp > 0 ) {
			$hi = bcsub( $mid, 1, 10 );
		} else {
			return $mid;
		}
	}
	return false;
}
function binary_search( $a, $first, $last, $value ) {
	$lo = $first; 
	$hi = $last - 1;

	while ($lo <= $hi) {
		$mid = (int)( ( $hi - $lo ) / 2) + $lo;
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