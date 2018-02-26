<?php
/*
Looks at the list of voters from the first data set who voted in the November 8 election and finds those whose voter ids are missing from the second data set.

Write names, addresses, etc to output file.

This runs from within the data directory of the first data set

Syntax:
php getvotedthendisappeared.php date2

Example
php getvotedthendisappeared.php '.$argv[1].'

Output: 
votedthendisappeared_date2.csv
*/

$counties = array(
1 => 'ADAMS',
2 => 'ALLEGHENY',
3 => 'ARMSTRONG',
4 => 'BEAVER',
5 => 'BEDFORD',
6 => 'BERKS',
7 => 'BLAIR',
8 => 'BRADFORD',
9 => 'BUCKS',
10 => 'BUTLER',
11 => 'CAMBRIA',
12 => 'CAMERON',
13 => 'CARBON',
14 => 'CENTRE',
15 => 'CHESTER',
16 => 'CLARION',
17 => 'CLEARFIELD',
18 => 'CLINTON',
19 => 'COLUMBIA',
20 => 'CRAWFORD',
21 => 'CUMBERLAND',
22 => 'DAUPHIN',
23 => 'DELAWARE',
24 => 'ELK',
25 => 'ERIE',
26 => 'FAYETTE',
27 => 'FOREST',
28 => 'FRANKLIN',
29 => 'FULTON',
30 => 'GREENE',
31 => 'HUNTINGDON',
32 => 'INDIANA',
33 => 'JEFFERSON',
34 => 'JUNIATA',
35 => 'LACKAWANNA',
36 => 'LANCASTER',
37 => 'LAWRENCE',
38 => 'LEBANON',
39 => 'LEHIGH',
40 => 'LUZERNE',
41 => 'LYCOMING',
42 => 'McKEAN',
43 => 'MERCER',
44 => 'MIFFLIN',
45 => 'MONROE',
46 => 'MONTGOMERY',
47 => 'MONTOUR',
48 => 'NORTHAMPTON',
49 => 'NORTHUMBERLAND',
50 => 'PERRY',
51 => 'PHILADELPHIA',
52 => 'PIKE',
53 => 'POTTER',
54 => 'SCHUYLKILL',
55 => 'SNYDER',
56 => 'SOMERSET',
57 => 'SULLIVAN',
58 => 'SUSQUEHANNA',
59 => 'TIOGA',
60 => 'UNION',
61 => 'VENANGO',
62 => 'WARREN',
63 => 'WASHINGTON',
64 => 'WAYNE',
65 => 'WESTMORELAND',
66 => 'WYOMING',
67 => 'YORK'
);


$outputfilename = 'votedthendisappeared.csv';
$outputfile = fopen( $outputfilename, 'w' );
fwrite( $outputfile, "ID	Name	DOB	Gender	Phone	Address	County	Party	Status	Registered	Last Voted	Record Changed	Grep Code\n" );

$outputfilename2 = 'votedthendidntvote.csv';
$outputfile2 = fopen( $outputfilename2, 'w' );
fwrite( $outputfile2, "ID	Name1	Name2	DOB1	DOB2	Gender1	Gender2	Phone1	Phone2	Address1	County1	Address2	County2	Party1	Party2	Status1	Status2	Registered1	Registered2	Last Voted1	Last Voted2	Record Changed1	Record Changed2	Grep Code\n" );

$voterids1 = file( 'voterids.txt' );
$fullids1 = file( 'fullids.txt' );
$addressesetc1 = file( 'addressesetc.txt' );
$names1 = file('firstnamelastnamedobs.txt' );
echo 'sorting arrays from nov 28'."\n";
array_multisort( $voterids1, $fullids1, $addressesetc1, $names1 );

$voterids2 = file( '../'.$argv[1].'/voterids.txt' );
$fullids2 = file( '../'.$argv[1].'/fullids.txt' );
$addressesetc2 = file( '../'.$argv[1].'/addressesetc.txt' );
$names2 = file( '../'.$argv[1].'/firstnamelastnamedobs.txt' );
echo 'sorting arrays from '.$argv[1]."\n";
array_multisort( $voterids2, $fullids2, $addressesetc2, $names2 );



$key2 = 0;
foreach( $voterids1 as $key1=>$voterid1 )
{
	$firstkey = $key2;
	$addressesandstuff = $addressesetc1[ $key1 ];
	$addressparts = explode( "\t", $addressesandstuff);
	$lastvoted = $addressparts[4];
	//echo $lastvoted."\n";
	$key2 = binary_search( $voterids2, $firstkey, sizeof( $voterids2 ), $voterid1 );
	//$key2 = array_search( $voterid1, $voterids2 );
	if( $lastvoted == '11/08/2016' )
	{
		//echo $key2."\n";
		if( !$key2 )
		{
			echo $voterid1.' voted and went missing'."\n";
			$name = trim( $names1[ $key1 ] );
			//divide "name" into name, dob, gender
			//since we trimmed the data, if the last character is numeric there is no gender
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
			$fullid = trim( $fullids1[ $key1 ] );
			{
				$parts = explode( '-', $fullid );
				$voterid = $parts[0];
				$countycode = $parts[1] * 1;
				if( strlen( $countycode ) == 1 ) $paddedcountycode = '0'.$countycode;
				else $paddedcountycode = $countycode;
			}
			$namestring = $justname."	".$dob."	".$gender;
			$address = $addressparts[0];
			$addressbits = explode( ' ', $address );
			$county = trim( $addressbits[0] );
			$realaddress = trim( str_replace( $county, '', $address ) );
			$phone = $addressparts[1];
			$party = $addressparts[2];
			if( isset( $addressparts[3] ) ) $status = $addressparts[3]; else $status = '';
			if( isset( $addressparts[4] ) ) $lastvoteddate = $addressparts[4]; else $lastvoteddate = '';
			if( isset( $addressparts[5] ) ) $registereddate = $addressparts[5]; else $registereddate = '';
			if( isset( $addressparts[7] ) ) $recordchangeddate = str_replace("\n", '', $addressparts[7] ); else $recordchangeddate = '';

			fwrite( $outputfile, trim( $voterid )."\t".$namestring."\t".$phone."\t".$realaddress."\t".$county."\t".$party."\t".$status."\t".$registereddate."\t".$lastvoteddate."\t".$recordchangeddate."\t".$paddedcountycode.'xxxxxxx'."\n" );
		}
		else
		{
			//now look for voters whose "last voted" date changed
			$addressesandstuff2 = $addressesetc2[ $key2 ];
			$addressparts2 = explode( "\t", $addressesandstuff );
			$lastvoted2 = $addressparts2[4];
			if( $lastvoted != $lastvoted2 )
			{
				echo $voterid1.' voted and then didn\'t vote'."\n";
				$names[1] = trim( $names1[ $key1 ] );
				$names[2] = trim( $names2[ $key2 ] );
				$fullids[1] = trim( $fullids1[ $key1 ] );
				$fullids[2] = trim( $fullids2[ $key2 ] );
				$addressetcs[1] = trim( $addressesetc1[ $key1 ] );
				$addressetcs[2] = trim( $addressesetc2[ $key2 ] );
				// get county names from full ids
				foreach( $fullids as $key=>$fullid )
				{
					$parts = explode( '-', $fullid );
					$voterid = $parts[0];
					$countycode = $parts[1] * 1;
					if( strlen( $countycode ) == 1 ) $paddedcountycode = '0'.$countycode;
					else $paddedcountycode = $countycode;
					$countynames[ $key ] = $counties[ $countycode ];
				}
				//$address."\t".$phone."\t".$party."\t".$status."\t".$lastvotedate.
				foreach( $addressetcs as $key=>$addressetc)
				{
					$parts = explode( "\t", $addressetc );
					$addresses[ $key ] = $parts[0];
					$phones[ $key ] = $parts[1];
					$parties[ $key ] = $parts[2];
					if( isset( $parts[3] ) ) $statuses[ $key ] = $parts[3]; else $statuses[ $key ] = '';
					if( isset( $parts[4] ) ) $lastvoteddates[ $key ] = $parts[4]; else $lastvoteddates[ $key ] = '';
					if( isset( $parts[4] ) ) $registereddates[ $key ] = $parts[4]; else $registereddates[ $key ] = '';
					if( isset( $parts[7] ) ) $recordchangeddates[ $key ] = $parts[7]; else $recordchangeddates[ $key ] = '';
				}
				foreach( $names as $key=>$name )
				{
					//divide "name" into name, dob, gender
					//since we trimmed the data, if the last character is numeric there is no gender
					if( is_numeric( substr( $name, -1 ) )  )
					{
						$genders[ $key ] = '';
						$dobs[ $key ] = substr( $name, -10 );
						$justnames[ $key ] = substr( $name, 0, strlen( $name ) - 10 );

					}
					else
					{
						$genders[ $key ] = substr( $name, -1 );
						$dobs[ $key ] = substr( $name, -12, -2 );
						$justnames[ $key ] = substr( $name, 0, strlen( $name ) - 12  );
					}
					$names[ $key ] = $justnames[ $key ]."	".$dobs[ $key ]."	".$genders[ $key ];
				}
				fwrite( $outputfile2, trim( $voterid )."\t".$justnames[1]."\t".$justnames[2]."\t".$dobs[1]."\t".$dobs[2]."\t".$genders[1]."\t".$genders[2]."\t".$phones[1]."\t".$phones[2]."\t".$addresses[1]."\t".$countynames[1]."\t".$addresses[2]."\t".$countynames[2]."\t".$parties[1]."\t".$parties[2]."\t".$statuses[1]."\t".$statuses[2]."\t".$registereddates[1]."\t".$registereddates[2]."\t".$lastvoteddates[1]."\t".$lastvoteddates[2]."\t".$recordchangeddates[1]."\t".$recordchangeddates[2]."\t".$paddedcountycode.'xxxxxxx'."\n" );
			}
		}
	}
}

fclose( $outputfile );
fclose( $outputfile2 );


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