<?php
/*
Looks at list of all voter ids and pulls records for the corresponding full id for each voter id that has an exact duplicate.

This is done efficiently by noting the key of each duplicate and pulling the corresponding record from the appropriate county file

This runs from within the Nov 7 data set

We also pull in address, etc info from the Feb 27 2017 dataset

Usage:
php getdupetwocountyidrecords

*/
echo "hello\n";
$outputfilename = 'dupetwocountyvoteridrecords.csv';
$outputfile = fopen( $outputfilename, 'w' );

$voterids = file( 'voterids.txt' );
$fullids = file( 'fullids.txt' );
$firstnamelastnamedobs = file( 'firstnamelastnamedobs.txt' );
$addressesetc = file( 'addressesetc.txt' );

array_multisort( $fullids, $voterids, $firstnamelastnamedobs, $addressesetc );

//remove hyphens from feb full ids for numeric comparison
$febnumericfullids =  array();
$febids = file( '../2017-02-27/fullids.txt' );
$febaddressesetc = file( '../2017-02-27/addressesetc.txt' );
foreach( $febids as $febid )
{
	$febnumericfullids[] = (int) str_replace( '-', '', $febid );
}
array_multisort( $febnumericfullids, $febids, $febaddressesetc );

$valuecounts = array_count_values( $voterids );
$dupevoterids = array();
foreach( $voterids as $key=>$id )
{
	if( $valuecounts[ $id ] > 1 )
	{
		$dupevoterids[ $key ] = $id;
		$dupefullids[ $key ] = $fullids[ $key ];
	}
}

//this array is all the dupe voter ids - both same county and two-county
echo 'count dupe voter records = '.count( $dupevoterids )."\n";

// now look ONLY at the voterid records that do NOT have duplicate full ids
$valuecounts = array_count_values( $dupefullids );
foreach( $dupefullids as $key=>$id )
{
	if( $valuecounts[ $id ] == 1 )
	{
		$dupetwocountyids[ $key ] = $id;
	}
}

$firstkey = 0;
foreach( $dupetwocountyids as $key=>$id )
{
	// search for full id in Feb data
	$firstkey = $key2;
	//divide "name" into name, dob, gender
	//since we trimmed the data, if the last character is numeric there is no gender
	$numericid = (int) str_replace( '-', '', $id );
	$key2 = binary_search( $febnumericfullids, $firstkey, sizeof( $febnumericfullids ), $numericid );
	if( $key2 )
	{
		$febdata = $febaddressesetc[ $key2 ];
	}
	else
	{
		$febdata = '';
	}
	
	
	$name = $firstnamelastnamedobs[ $key ];
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
		
	$addressparts = explode( "\t", $addressesetc[ $key ] );
	$address = $addressparts[0];
	$phone = $addressparts[1];
	$partycode = $addressparts[2];
	$status = $addressparts[3];
	$lastvoteddate = $addressparts[4];

	$outputline = str_replace( "\n", '', $justname."\t".$dob."\t".$address."\t".$phone."\t".$gender."\t".$id."\t".$partycode."\t".$status."\t".$lastvoteddate."\t".$febdata )."\n";
	echo $outputline;
	fwrite( $outputfile, $outputline );
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