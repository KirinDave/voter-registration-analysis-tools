<?php
/*
Looks at lists of all voterids and all full names (first name + last name + DOB + Gender )for two data sets

Runs through one list of voterids and finds all matches on second list. Compares the name that has the same index as the first id with the name that has the same index as the second id.

If the two names don't match, parse names to tab separate name, dob and gender. Write both names to same line of output file.

Syntax:
php getchangednames.php firstdate seconddate

Example:
php getchangednames.php 2016-04-04 2016-11-07 

Output: changednames_firstdate_seconddate.txt
Output: changednames_firstdate_seconddate_summary.txt
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

$outputfilename = 'changednames_'.$argv[1].'_'.$argv[2].'.txt';
$outputfile = fopen( $outputfilename, 'w' );
fwrite( $outputfile, "County1	ID1	Party1	Name1	DOB1	Gender1	County2	Name2	DOB2	Gender2\n" );
$summaryfilename = 'changednames_'.$argv[1].'_'.$argv[2].'_summary.txt';
$summaryfile = fopen( $summaryfilename, 'w' );

$voterids1 = file( $argv[1].'/voterids.txt' );
$fullids1 = file( $argv[1].'/fullids.txt' );
$parties1 = file( $argv[1].'/parties.txt' );
$names1 = file( $argv[1].'/firstnamelastnamedobs.txt' );
echo 'sorting arrays from '.$argv[1]."\n";
array_multisort( $voterids1, $fullids1, $parties1, $names1 );
echo "done\n";
$voterids2 = file( $argv[2].'/voterids.txt' );
$fullids2 = file( $argv[2].'/fullids.txt' );
$parties2 = file( $argv[2].'/parties.txt' );
$names2 = file( $argv[2].'/firstnamelastnamedobs.txt' );
echo 'sorting arrays from '.$argv[2]."\n";
array_multisort( $voterids2, $fullids2, $parties2, $names2 );
echo "done\n";

$changedids = array();
$changednames = array();
$malechangednames = array();
$changedgender = array();
$addedgender = array();
$removedgender = array();
$changeddob = array();
$changedparty = array();
$changedcounty = array();
$demsbefore = array();
$demsafter = array();
$repsbefore = array();
$repsafter = array();
$othersbefore = array();
$othersafter = array();
$key2 = 0;
foreach( $voterids1 as $key1=>$voterid1 )
{
	//echo 'found: '.$names1[ $key1 ];
	$firstkey = $key2;
	$names = array();
	$justnames = array();
	$dobs = array();
	$genders = array();
	$fullids = array();
	$countynames = array();
	$key2 = binary_search( $voterids2, $firstkey, sizeof( $voterids2 ), $voterid1 );
	//$key2 = array_search( $voterid1, $voterids2 );
	if( $key2 )
	{	
		//echo '        found: '.$names2[ $key2 ];
		$names[1] = trim( $names1[ $key1 ] );
		$names[2] = trim( $names2[ $key2 ] );
		$fullids[1] = trim( $fullids1[ $key1 ] );
		$fullids[2] = trim( $fullids2[ $key2 ] );
		$party1 = trim( $parties1[ $key1 ] );
		$party2 = trim( $parties2[ $key2 ] );
		if( $names[1] != $names[2]  )
		{
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
			// get county names from full ids
			foreach( $fullids as $key=>$fullid )
			{
				$parts = explode( '-', $fullid );
				$voterid = $parts[0];
				$countycode = $parts[1] * 1;
				$countynames[ $key ] = $counties[ $countycode ];
			}
			//collect statistics per county - countycode is the code from the second full id, which corresponds to the later of the two dates
			if( ! isset( $changedids[ $countycode ] ) ) $changedids[ $countycode ] = 1; else $changedids[ $countycode ] = $changedids[ $countycode ] + 1;
			if( $justnames[1] != $justnames[2] )
			{
				if( ! isset( $changedname[ $countycode ] ) ) $changedname[ $countycode ] = 1; else $changedname[ $countycode ] = $changedname[ $countycode ] + 1;
			}
			if( $justnames[1] != $justnames[2] && ( $genders[1] == 'M' || $genders[2] == 'M' ) )
			{
				if( ! isset( $malechangedname[ $countycode ] ) ) $malechangedname[ $countycode ] = 1; else $malechangedname[ $countycode ] = $malechangedname[ $countycode ] + 1;
			}
			if( ( $genders[1] != $genders[2] ) && ( $genders[1] != '' && $genders[2] != '' ) )
			{
				if( ! isset( $changedgender[ $countycode ] ) ) $changedgender[ $countycode ] = 1; else $changedgender[ $countycode ] = $changedgender[ $countycode ] + 1;
			}
			if( ( $genders[1] != $genders[2] ) &&  $genders[1] == '' )
			{
				if( ! isset( $addedgender[ $countycode ] ) ) $addedgender[ $countycode ] = 1; else $addedgender[ $countycode ] = $addedgender[ $countycode ] + 1;
			}
			if( ( $genders[1] != $genders[2] ) && $genders[2] == '' )
			{
				if( ! isset( $removedgender[ $countycode ] ) ) $removedgender[ $countycode ] = 1; else $removedgender[ $countycode ] = $removedgender[ $countycode ] + 1;
			}
			if( $dobs[1] != $dobs[2] )
			{
				if( ! isset( $changeddob[ $countycode ] ) ) $changeddob[ $countycode ] = 1; else $changeddob[ $countycode ] = $changeddob[ $countycode ] + 1;
			}
			if( $party1 != $party2 )
			{
				if( ! isset( $changedparty[ $countycode ] ) ) $changedparty[ $countycode ] = 1; else $changedparty[ $countycode ] = $changedparty[ $countycode ] + 1;
			}
			if( $countynames[1] != $countynames[2] )
			{
				if( ! isset( $changedcounty[ $countycode ] ) ) $changedcounty[ $countycode ] = 1; else $changedcounty[ $countycode ] = $changedcounty[ $countycode ] + 1;
			}
			if( $party1 == 'D' )
			{
				if( ! isset( $demsbefore[ $countycode ] ) ) $demsbefore[ $countycode ] = 1; else $demsbefore[ $countycode ] = $demsbefore[ $countycode ] + 1;
			}
			if( $party2 == 'D' )
			{
				if( ! isset( $demsafter[ $countycode ] ) ) $demsafter[ $countycode ] = 1; else $demsafter[ $countycode ] = $demsafter[ $countycode ] + 1;
			}
			if( $party1 == 'R' )
			{
				if( ! isset( $repsbefore[ $countycode ] ) ) $repsbefore[ $countycode ] = 1; else $repsbefore[ $countycode ] = $repsbefore[ $countycode ] + 1;
			}
			if( $party2 == 'R' )
			{
				if( ! isset( $repsafter[ $countycode ] ) ) $repsafter[ $countycode ] = 1; else $repsafter[ $countycode ] = $repsafter[ $countycode ] + 1;
			}
			if( ( $party1 != 'R' && $party1 != 'D' ) )
			{
				if( ! isset( $othersbefore[ $countycode ] ) ) $othersbefore[ $countycode ] = 1; else $othersbefore[ $countycode ] = $othersbefore[ $countycode ] + 1;
			}
			if( ( $party2 != 'R' && $party2 != 'D' ) )
			{
				if( ! isset( $othersafter[ $countycode ] ) ) $othersafter[ $countycode ] = 1; else $othersafter[ $countycode ] = $othersafter[ $countycode ] + 1;
			}			
			//write
			echo $countynames[1]."	".trim( $fullids[1] )."	".trim( $party1 )."	".$names[1]."	".$countynames[2]."	".trim( $fullids[2] )."	".trim( $party2 )."	".$names[2]."\n";
			fwrite( $outputfile, $countynames[1]."	".trim( $fullids[1] )."	".trim( $party1 )."	".$names[1]."	".$countynames[2]."	".trim( $fullids[2] )."	".trim( $party2 )."	".$names[2]."\n" );
		}
	}
}
fwrite( $summaryfile, "New County	Registered in November	Changed Ids	Changed Names	Male Changed Names	Changed Gender	Added Gender	Removed Gender	Changed DOB	Changed Party 	Changed County	Dem Increase	Rep Increase	Other Increase\n" );
foreach( $counties as $countycode=>$county )
{

	if( !isset( $changedids[ $countycode ] ) ) $changedids[ $countycode ] = 0;
	if( !isset( $changedname[ $countycode ] ) ) $changedname[ $countycode ] = 0;
	if( !isset( $malechangedname[ $countycode ] ) ) $malechangedname[ $countycode ] = 0;
	if( !isset( $changedgender[ $countycode ] ) ) $changedgender[ $countycode ] = 0;
	if( !isset( $addedgender[ $countycode ] ) ) $addedgender[ $countycode ] = 0;
	if( !isset( $removedgender[ $countycode ] ) ) $removedgender[ $countycode ] = 0;
	if( !isset( $changeddob[ $countycode ] ) ) $changeddob[ $countycode ] = 0;
	if( !isset( $changedparty[ $countycode ] ) ) $changedparty[ $countycode ] = 0;
	if( !isset( $changedcounty[ $countycode ] ) ) $changedcounty[ $countycode ] = 0;
	if( !isset( $demsbefore[ $countycode ] ) ) $demsbefore[ $countycode ] = 0;
	if( !isset( $demsafter[ $countycode ] ) ) $demsafter[ $countycode ] = 0;
	if( !isset( $repsbefore[ $countycode ] ) ) $repsbefore[ $countycode ] = 0;
	if( !isset( $repsafter[ $countycode ] ) ) $repsafter[ $countycode ] = 0;
	if( !isset( $othersbefore[ $countycode ] ) ) $othersbefore[ $countycode ] = 0;
	if( !isset( $othersafter[ $countycode ] ) ) $othersafter[ $countycode ] = 0;
	
	$demincrease = $demsafter[ $countycode ] - $demsbefore[ $countycode ];
	$repincrease = $repsafter[ $countycode ] - $repsbefore[ $countycode ];
	$otherincrease = $othersafter[ $countycode ] - $othersbefore[ $countycode ];
	
	$summaryline =  $county."	".$countyregisteredelectionday[ $countycode ]."	".$changedids[ $countycode ]."	".$changedname[ $countycode ]."	".$malechangedname[ $countycode ]."	".$changedgender[ $countycode ]."	".$addedgender[ $countycode ]."	".$removedgender[ $countycode ]."	".$changeddob[ $countycode ]."	".$changedparty[ $countycode ]."	".$changedcounty[ $countycode ]."	".$demincrease."	".$repincrease."	".$otherincrease."\n";
	echo $summaryline;
	fwrite( $summaryfile, $summaryline );
}

fclose( $outputfile );
fclose( $summaryfile );

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