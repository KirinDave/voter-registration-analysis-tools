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
$outputfilename = 'dupes/sameaddresssamedobvotersonly_'.$argv[1].'.txt';
$outputfile = fopen( $outputfilename, 'w' );
$addresses = file( $argv[1].'/addresses.txt' );
$dobs = file( $argv[1].'/dobs.txt' );
$fullids = file( $argv[1].'/fullids.txt' );
$fullnames = file( $argv[1].'/firstnamelastnamedobs.txt' );
$addressesetc = file( $argv[1].'/addressesetc.txt' );
$count = 0;


//find all the dupe dobs
//walk through them looking for dupe addresses for the same dob
foreach( $dobs as $dob )
{
	$numericdobs[] = strtotime( str_replace( "\n", '', $dob ) );
}
$uniquedobs = array_unique( $numericdobs );
$dupedobs = array_diff_key( $numericdobs,  $uniquedobs );
echo 'count = '.count( $dupedobs )."\n";
//return;
sort( $dupedobs );
array_multisort( $numericdobs, $dobs, $addresses, $fullids, $fullnames, $addressesetc );
$firstkey = 0;
//print all info for each dupe to output file
foreach( $dupedobs as $dupekey=>$dupe )
{
	//find first match, then keep going until the data changes
	$key = binary_search( $numericdobs, $firstkey, sizeof( $numericdobs ), $dupe );
	//$key = array_search( $dupe, $addresses );
	//echo str_replace( "\n", '', $dupe ).' - '.$key."\n";
	$lastdob = $dupe;
	$found = 0;
	//get all addresses for each DOB
	while( $numericdobs[ $key ] == $lastdob && $numericdobs[ $key ]!= $founddob )
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
		
		$nameparts = explode( trim( ' ', $justname ) );
		$name = $justname."	".$dob."	".$gender;
		$line = str_replace( "\n", "\t", ( $fullids[ $key ]."\t".$name."\t".$addressesetc[ $key ] ) )."\n";
		
		$linesfordob[] = $line;
		$firstnamesfordob[] = $nameparts[0];
		$idsfordob[] = $fullids[ $key ];
		$addressesfordob[] = $addresses[ $key ];
		$lastdob = $numericdobs[ $key ];
		$key++;
		$found = 1;
	}
	// for a given DOB
	if( $found )
	{
		//echo str_replace( "\n", '', $dupe ).' - '.count( $dobsfordob )."\n";
		//if there are more addresses than uniqueaddresses and there are no duplicate ids, print all people having that birthday who also share an address but not an ID
		if( count( $addressesfordob ) > count( array_unique( $addressesfordob ) ) )
		{
			array_multisort( $addressesfordob, $linesfordob, $firstnamesfordob , $idsfordob );
			foreach( $addressesfordob as $dobkey=>$address )
			{
				if( $lastaddress == $address && $lastid != $idsfordob[ $dobkey ] )
				{
					$lastline = $linesfordob[ $dobkey ];
					$lastaddress = $address;
					$lastid = $idsfordob[ $dobkey ]
					echo $lastline;
					echo $line;
					echo "\n";
					fwrite( $outputfile, $lastline );
					fwrite( $outputfile, $line );
					fwrite( $outputfile, "\n" );
					$count++;
				}
			}
		}
		//$firstkey = $key;
		$founddob = $lastdob;
		
		$linesfordob = array();
		$firstnamesfordob =array();
		$idsfordob = array();
		$addressesfordob = array();
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