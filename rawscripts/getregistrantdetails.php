<?php
/*
Checks a "missing data" input file against the original file.
Creates a new file with names and addresses added in.
Fields
0 ID Number
1 Title
2 Last Name
3 First Name
4 Middle Name
5 Suffix
6 Gender
7 DOB
8 Registration Date
9 Voter Status
10 Status Change Date
11 Party Code
12 House Number
13 House Number Suffix
14 Street Name
15 Apartment Number
16 Address Line 2
17 City
18 State
19 Zip
20 Mail Address 1
22 Mail Address 2
22 City
23 State
24 Zip
25 Last Vote Date
26 Precinct Code
27 Precinct Split ID
28 Date Last Changed
150 Phone
152 Country

Syntax:
php getregistrantdetails.php inputfilename datafiledata

Example:
php getregistrantdetails.php missing/ALLEGHENY_2016-11-07_2017-02-27.csv 2016-11-07 2017-02-27

*/

$filenameparts = explode( '_', $argv[1] );
$countyname = str_replace( 'missing/', '', $filenameparts[0] );
$countyname = str_replace( 'added/', '', $countyname );
$olddate = $filenameparts[1];
$newdate = str_replace( '.csv', '', $filenameparts[2] );
$outputfilename = str_replace( '.csv', '_details.csv', $argv[1] );

$outputfile = fopen( $outputfilename, 'w');

$datecode = str_replace( '-', '', $argv[2] );
$filenamesuffix = '_FVE_'.$datecode.'.txt';
$datafile = $argv[2].'/'.$countyname.$filenamesuffix;

//this is the set of voterids that exist in the second data set
//if our "missing" voter id is in this set the person moved, otherwise the person simply disappeared.
$checkidfile = $argv[3].'/voterids.txt';
$checkidarray = file( $checkidfile );
sort( $checkidarray );
if( strpos( $filenameparts[0], 'added' ) !== false ) fwrite ( $outputfile, "VOTERS ADDED TO ".$countyname." COUNTY VOTER ROLLS BETWEEN ".$newdate." AND ".$olddate."\n" );
else fwrite ( $outputfile, "VOTERS REMOVED FROM ".$countyname." COUNTY VOTER ROLLS BETWEEN ".$olddate." AND ".$newdate."\n" );

fwrite ( $outputfile, "ID	Name	DOB	Home Address	Mailing Address	Phone	Gender	Status	Party	Registration Date	Status Change Date	Date Last Changed	Last Vote Date\n" );

$idarray = array();
$voteridarray = array();
$tsvlines = file( $argv[1] );
foreach ($tsvlines as $tsvline )
{
	//echo $tsvline."\n";
	$lineparts = explode( "\t", $tsvline );
	$id = str_replace( '"', '', $lineparts[0] );
	$idarray[] = $id;
	$idparts = explode( '-', $id );
	$voteridarray[] = $idparts[0];
}
array_multisort( $idarray, $voteridarray );
$lastidkey = 0;
/*
shell_exec( 'cp '.$datafile.' tail.tsv' );
while( filesize ( 'tail.tsv' ) > 0 )
{
	clearstatcache ();
	shell_exec( 'mv tail.tsv tmp.tsv' );
	shell_exec( 'head -n 50000 tmp.tsv > head.tsv' );
	shell_exec( 'tail -n +50001 tmp.tsv > tail.tsv' );
	$datalines = file( 'head.tsv' );
*/
	$datalines = file( $datafile );
	foreach ($datalines as $dataline )
	{
		$lineparts = explode( "\t", $dataline );
		$id = str_replace( '"', '', $lineparts[0] );
		$idparts = explode( '-', $id );
		$voterid = $idparts[0];
		$idkey = binary_search( $idarray, $lastidkey, sizeof( $idarray ), $id );
		$lastidkey = $idkey;
		if ( $lastidkey );
		{
			/*
			0 ID Number
			1 Title
			2 Last Name
			3 First Name
			4 Middle Name
			5 Suffix
			6 Gender
			7 DOB
			8 Registration Date
			9 Voter Status
			10 Status Change Date
			11 Party Code
			12 House Number
			13 House Number Suffix
			14 Street Name
			15 Apartment Number
			16 Address Line 2
			17 City
			18 State
			19 Zip
			20 Mail Address 1
			21 Mail Address 2
			22 City
			23 State
			24 Zip
			25 Last Vote Date
			26 Precinct Code
			27 Precinct Split ID
			28 Date Last Changed
			150 Phone
			152 Country
			*/
			$moved = 0;
			$gone = 0;
			$checkidkey = binary_search( $checkidarray, 0, sizeof( $checkidarray ), $voterid."\n" );
			if( $checkidkey ) $moved = 1;
			else $gone = 1;
			$name = str_replace( '  ', ' ', trim( trim( str_replace( '"', '', $lineparts[1] ) ).' '.trim( str_replace( '"', '', $lineparts[3] ) ).' '.trim( str_replace( '"', '', $lineparts[4] ) ).' '.trim( str_replace( '"', '', $lineparts[2] ) ).' '.trim( str_replace( '"', '', $lineparts[5] ) ) ) );
			$dob =  str_replace( '"', '', $lineparts[7] );
			$street =  str_replace( '"', '', $lineparts[14] );
			if( str_replace( '"', '', $lineparts[15] ) ) $aptnumber = ' #'.trim( str_replace( '"', '', $lineparts[15] ) ).' ';
			else $aptnumber = '';
			$homeaddress = str_replace( ',', '', str_replace( "\t", " ", str_replace( '  ', ' ', trim( trim( str_replace( '"', '', $lineparts[12] ) ).' '.trim( str_replace( '"', '', $lineparts[13] ) ).' '.trim( str_replace( '"', '', $lineparts[14] ) ).$aptnumber.trim( str_replace( '"', '', $lineparts[16] ) ).' '.trim( str_replace( '"', '', $lineparts[17] ) ).' '.trim( str_replace( '"', '', $lineparts[18] ) ).' '.trim( str_replace( '"', '', $lineparts[19] ) ) ) ) ) );
			$mailingaddress = str_replace( ',', '', str_replace( "\t", " ", str_replace('  ', ' ', trim( trim( str_replace( '"', '', $lineparts[20] ) ).' '.trim( str_replace( '"', '', $lineparts[21] ) ).' '.trim( str_replace( '"', '', $lineparts[22] ) ).' '.trim( str_replace( '"', '', $lineparts[23] ) ).' '.trim( str_replace( '"', '', $lineparts[24] ) ).' '.trim( str_replace( '"', '', $lineparts[152] ) ) ) ) ) );
			$phone = trim( str_replace( '"', '', $lineparts[150] ) );
			$gender = trim( str_replace( '"', '', $lineparts[6] ) );
			$status = str_replace( '"', '', $lineparts[9] );
			$registrationdate = str_replace( '"', '', $lineparts[8] );
			$statuschangedate = str_replace( '"', '', $lineparts[10] );
			$partycode = str_replace( '"', '', $lineparts[11] );
			$lastvotedate = str_replace( '"', '', $lineparts[25] );
			$datelastchanged = str_replace( '"', '', $lineparts[28] );
			$lastvotedatejulian = strtotime( $lastvotedate );
			echo $id."	".$name."	".$dob."	".$homeaddress."	".$mailingaddress."	".$phone."	".$gender."	".$status."	".$partycode."	".$moved."\n";
			fwrite( $outputfile, $id."	".$name."	".$dob."	".$homeaddress."	".$mailingaddress."	".$phone."	".$gender."	".$status."	".$partycode."	".$registrationdate."	".$statuschangedate."	".$datelastchanged."	".$lastvotedate."	".$moved."	".$gone."\n" );
		}
	}
	/*
}
*/
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