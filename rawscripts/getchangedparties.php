<?php
/*
Looks at lists of all voterids and all full names (first name + last name + DOB + Gender )for two data sets

Runs through later list of voterids and finds all that have no match on earlier list. 

Write summary details to list by county in subdir added/

Write files of those who were added with a recent vote date ( > November 2009 ) to bad/recentvoter.txt or with a status of Inactive to added/badinactive.txt

Syntax:
php getadded.php firstdate seconddate

Example:
php getadded.php 2016-04-04 2016-11-07 

Output: 
added/%countyname%_firstdate_seconddate.txt
added/inactive.txt
added/recentvoter.txt
added/ids.txt

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

foreach( $counties as $countycode=>$county )
{
	$countyoutputfilename = 'added/'.$county.'_'.$argv[1].'_'.$argv[2].'.txt';
	$countyfile[ $county ] = fopen( $countyoutputfilename, 'w' );
	fwrite( $countyfile[ $county ], "ID	Name	DOB	Gender	Phone	Address	County	Party	Status	Registered	Status Changed	Last Changed	Last Voted\n" );
}

$recentfilename = 'added/recent_'.$argv[1].'_'.$argv[2].'.txt';
$recentfile = fopen( $recentfilename, 'w' );
fwrite( $recentfile, "ID	Name	DOB	Gender	Phone	Address	County	Party	Status	Registered	Status Changed	Last Changed	Last Voted	Grep Code\n" );

$inactivefilename = 'added/inactive_'.$argv[1].'_'.$argv[2].'.txt';
$inactivefile = fopen( $inactivefilename, 'w' );
fwrite( $inactivefile, "ID	Name	DOB	Gender	Phone	Address	County	Party	Status	Registered	Status Changed	Last Changed	Last Voted	Grep Code\n" );

$idsfilename = 'added/ids_'.$argv[1].'_'.$argv[2].'.txt';
$idsfile = fopen( $idsfilename, 'w' );

$voterids1 = file( $argv[1].'/voterids.txt' );
echo 'sorting arrays from '.$argv[1]."\n";
sort( $voterids1 );

$voterids2 = file( $argv[2].'/voterids.txt' );
$fullids2 = file( $argv[2].'/fullids.txt' );
$addressesetc2 = file( $argv[2].'/addressesetc.txt' );
$namesetc2 = file( $argv[2].'/firstnamelastnamedobs.txt' );
echo 'sorting arrays from '.$argv[2]."\n";
array_multisort( $voterids2, $fullids2, $addressesetc2, $namesetc2 );



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

		// check for inactive
		if( $status == 'I' )
		{
				echo 'RESURRECTED - INACTIVE '.trim( $fullid)."\t".$justname."\t".$dob."\t".$gender."\t".$lastvoteddate."\n";
				
				fwrite( $inactivefile, trim( $fullid )."\t".$justname."\t".$dob."\t".$gender."\t".$phone."\t".$address."\t".$countyname."\t".$party."\t".$status."\t".$registrationdate."\t".$statuschangedate."\t".$lastchangedate."\t".$lastvoteddate."\t".$paddedcountycode.'xxxxxxx'."\n" );
		}
		// check for recent last voted date
		// look for any date between Nov 20 2009 and the earlier of our two dates
		$firstdatejulian = strtotime( $argv[1] );
		$oldvoterjulian = strtotime( '2009-11-20' );
		$lastvotedjulian = strtotime( $lastvoteddate );
		if( $lastvoteddate > $oldvoterjulian && $lastvoteddate < $firstdatejulian )
		{
				echo 'RESURRECTED - RECENT VOTER '.trim( $fullid )."\t".$justname."\t".$dob."\t".$gender."\t".$lastvoteddate."\n";
				
				fwrite( $recentfile, trim( $fullid )."\t".$justname."\t".$dob."\t".$gender."\t".$phone."\t".$address."\t".$countyname."\t".$party."\t".$status."\t".$registrationdate."\t".$statuschangedate."\t".$lastchangedate."\t".$lastvoteddate."\t".$paddedcountycode.'xxxxxxx'."\n" );
		}
		fwrite( $idsfile, $voterid."\n" );
		//write to county file
		fwrite( $countyfile[ $countyname ], trim( $fullid )."\t".$justname."\t".$dob."\t".$gender."\t".$phone."\t".$address."\t".$countyname."\t".$party."\t".$status."\t".$registrationdate."\t".$statuschangedate."\t".$lastchangedate."\t".$lastvoteddate."\n" );
	}
}


fclose( $recentfile );
fclose( $inactivefile );


foreach( $counties as $countycode=>$county )
{
	fclose( $countyfile[ $county ] );
}
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