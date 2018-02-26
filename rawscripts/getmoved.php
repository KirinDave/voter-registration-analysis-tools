<?php
/*
Looks at lists of all voterids and all full names (first name + last name + DOB + Gender )for two data sets

Runs through earlier list of voterids and finds all that have no match on later list. 

Write details to list by county in subdir moved/


Syntax:
php getmissing.php seconddate firstdate

Example:
php getmissing.php 2016-11-07 2016-04-04 

NOTE: SECOND DATE IS THE EARLIER DATE!

Output: 
missing/%countyname%_firstdate_seconddate.csv
missing/summary_firstdate_seconddate.csv
missing/ids.csv

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
$countyregisteredelectionday = array(
1 => 66944,
2 => 925228,
3 => 42646,
4 => 113703,
5 => 34473,
6 => 260233,
7 => 78033,
8 => 37672,
9 => 460914,
10 => 130223,
11 => 86884,
12 => 3236,
13 => 42232,
14 => 123352,
15 => 354689,
16 => 24109,
17 => 53324,
18 => 22317,
19 => 40967,
20 => 53456,
21 => 167167,
22 => 191220,
23 => 413548,
24 => 20253,
25 => 190527,
26 => 84110,
27 => 3334,
28 => 93071,
29 => 9223,
30 => 22597,
31 => 29857,
32 => 51899,
33 => 29929,
34 => 13844,
35 => 148453,
36 => 335370,
37 => 57353,
38 => 86882,
39 => 236819,
40 => 205738,
41 => 69787,
42 => 24969,
43 => 77373,
44 => 25994,
45 => 109399,
46 => 578372,
47 => 13101,
48 => 211909,
49 => 56576,
50 => 29788,
51 => 1102992,
52 => 40195,
53 => 11018,
54 => 88157,
55 => 22263,
56 => 49727,
57 => 4412,
58 => 26386,
59 => 26607,
60 => 24677,
61 => 32675,
62 => 30156,
63 => 138951,
64 => 33689,
65 => 246553,
66 => 16878,
67 => 296096,
);
foreach( $counties as $countycode=>$county )
{
	$countyoutputfilename = 'missing/'.$county.'_'.$argv[2].'_'.$argv[1].'.txt';
	$countyfile[ $county ] = fopen( $countyoutputfilename, 'w' );
	fwrite( $countyfile[ $county ], "ID	Name	DOB	Gender	Phone	Address	County	Party	Status	Registered	Status Changed	Last Changed	Last Voted\n" );
}

$summaryfilename = 'missing/summary_'.$argv[2].'_'.$argv[1].'.txt';
$summaryfile = fopen( $summaryfilename, 'w' );
fwrite( $summaryfile, "County	Total in November	Number Missing	Number Missing Eligible	Percent Missing	Percent Missing	Eligible\n" );


$idsfilename = 'missing/ids_'.$argv[2].'_'.$argv[1].'.txt';
$idsfile = fopen( $idsfilename, 'w' );

//later data
$voterids1 = file( $argv[1].'/voterids.txt' );
echo 'sorting arrays from '.$argv[1]."\n";
sort( $voterids1 );

//earlier data
$voterids2 = file( $argv[2].'/voterids.txt' );
$fullids2 = file( $argv[2].'/fullids.txt' );
$addressesetc2 = file( $argv[2].'/addressesetc.txt' );
$namesetc2 = file( $argv[2].'/firstnamelastnamedobs.txt' );
echo 'sorting arrays from '.$argv[2]."\n";
array_multisort( $voterids2, $fullids2, $addressesetc2, $namesetc2 );

$missingeligiblecount = array();

$key1 = 0;
foreach( $voterids2 as $key2=>$voterid2 )
{
	$firstkey = $key1;
	$names = array();
	$justnames = array();
	$dobs = array();
	$genders = array();
	$fullids = array();
	$countynames = array();
	$addressetc = array();
	$addresses = array();
	$phones = array();
	$parties = array();
	$statuses = array();
	$lastvoteddates = array();
	$key1 = binary_search( $voterids1, $firstkey, sizeof( $voterids1 ), $voterid2 );
	//$key1 = array_search( $voterid1, $voterids2 );
	if( ! $key1 )
	{
		$name = trim( $namesetc2[ $key2 ] );
		$fullid = trim( $fullids2[ $key2 ] );
		$addressetc = trim( $addressesetc2[ $key2 ] );
		// get county names from full ids
		$parts = explode( '-', $fullid );
		$voterid = $parts[0];
		$countycode = $parts[1] * 1;
		if( strlen( $countycode ) == 1 ) $paddedcountycode = '0'.$countycode;
		else $paddedcountycode = $countycode;
		$countyname = $counties[ $countycode ];

		//$address."\t".$phone."\t".$party."\t".$status."\t".$lastvotedate.
		$parts = explode( "\t", $addressetc );
		$address = str_replace( ',', '', str_replace( $countyname.' ', '', $parts[0] ) );
		$phone = $parts[1];
		$party = $parts[2];

		if( isset( $parts[3] ) ) $status = trim( $parts[3] ); else $status = '';
		if( isset( $parts[4] ) ) $lastvoteddate = trim( $parts[4] ); else $lastvoteddate = '';
		if( isset( $parts[5] ) ) $registrationdate = trim( $parts[5] ); else $registrationdate = '';
		if( isset( $parts[6] ) ) $statuschangedate = trim( $parts[6] ); else $statuschangedate = '';
		if( isset( $parts[7] ) ) $lastchangedate = trim( $parts[7] ); else $lastchangedate = '';

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
		$name = $justname."	".$dob."	".$gender;

		// check for active/eligible - collect data for summary
		$oldvoterjulian = strtotime( '2009-11-20' );
		$lastvotedjulian = strtotime( $lastvoteddate );
		if( ! isset( $missingcount[ $countyname ] ) ) $missingcount[ $countyname ] = 1;
		else $missingcount[ $countyname ] = $missingcount[ $countyname ] + 1;
		if( $status == 'A' || ( $lastvoteddate > $oldvoterjulian ) )
		{
			if( ! isset( $missingeligiblecount[ $countyname ] ) ) $missingeligiblecount[ $countyname ] = 1;
			else $missingeligiblecount[ $countyname ] = $missingeligiblecount[ $countyname ] + 1;
		}
		
		fwrite( $idsfile, $voterid."\n" );
		//write to county file
		fwrite( $countyfile[ $countyname ], trim( $fullid )."\t".$justname."\t".$dob."\t".$gender."\t".$phone."\t".$address."\t".$countyname."\t".$party."\t".$status."\t".$registrationdate."\t".$statuschangedate."\t".$lastchangedate."\t".$lastvoteddate."\n" );
	}
}


foreach( $counties as $countycode=>$county )
{
	fwrite( $summaryfile, $county."\t".$countyregisteredelectionday[ $countycode ]."\t".$missingcount[ $county ]."\t".$missingeligiblecount[ $county ]."\n" );
	fclose( $countyfile[ $county ] );
}

fclose( $summaryfile );
fclose( $idsfile );

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