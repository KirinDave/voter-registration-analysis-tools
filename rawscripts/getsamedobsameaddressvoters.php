<?php
/*
	make a list of voterids for voters with duplicate firstname dob and genders from one data set
	compare that to the same list for a second data set.
	
	find all the dupes from the first set that are missing from the second set
	
	print details of these records to a file
	
	syntax:
	php getsamedobsameaddressvoters.php 2016-11-07
	output dupes/sameaddresssamedobvoters_2016-11-07.txt
*/
$outputfilename = 'dupes/sameaddresssamedobvoters_'.$argv[1].'.txt';
$outputfile = fopen( $outputfilename, 'w' );
$addresses = file( $argv[1].'/addresses.txt' );
$dobs = file( $argv[1].'/dobs.txt' );
$fullids = file( $argv[1].'/fullids.txt' );
$fullnames = file( $argv[1].'/firstnamelastnamedobs.txt' );
$addressesetc = file( $argv[1].'/addressesetc.txt' );
$count = 0;


//find all the dupe addresses
//walk through them looking for dupe dobs at the same address
$uniqueaddresses = array_unique( $addresses );
$dupeaddresses = array_diff_key( $addresses,  $uniqueaddresses );

echo 'count = '.count( $dupeaddresses )."\n";
//return;
sort( $dupeaddresses );
array_multisort( $addresses, $dobs, $fullids, $fullnames, $addressesetc );
$firstkey = 0;
//print all info for each dupe to output file
foreach( $dupeaddresses as $dupekey=>$dupe )
{
	if( in_array( $dupe[0], array( 'V', 'W', 'X', 'Y', 'Z' ) ) )
	{
		//find first match, then keep going until the data changes
		//$key = binary_search( $addresses, $firstkey, sizeof( $addresses ), $dupe );
		$key = array_search( $dupe, $addresses );
		//echo str_replace( "\n", '', $dupe ).' - '.$key."\n";
		$lastaddress = $addresses[ $key ];
		$found = 0;
		while( $addresses[ $key ] == $lastaddress && $addresses[ $key ]!= $foundaddress )
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
			$names[ $key ] = $justname."	".$dob."	".$gender;
			$line = str_replace( "\n", "\t", ( $fullids[ $key ]."\t".$names[ $key ]."\t".$addressesetc[ $key ] ) )."\n";
			$lines[] = $line;
			$idsataddress[] = $fullids[ $key ];
			$dobsataddress[] = $dobs[ $key ];
			$lastaddress = $addresses[ $key ];
			$key++;
			$found = 1;
		}
		if( $found )
		{
			//echo str_replace( "\n", '', $dupe ).' - '.count( $dobsataddress )."\n";
			//if there are more dobs than uniquedobs and there are no duplicate ids, print all the occupants of that address that share a birthday
			if( count( $dobsataddress ) <= 20 && count( $dobsataddress ) > count( array_unique( $dobsataddress ) ) && count( $idsataddress ) == count( array_unique( $idsataddress ) ) )
			{
				array_multisort( $dobsataddress, $idsataddress, $lines );
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
}

echo $count.' found';
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