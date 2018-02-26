<?php
/*
Looks at lists of changed names, dobs or genders for two different data set comparisons (three data snapshots)

Example:
php getchangedtwice.php changeddobs_2016-04-04_2016-11-28.txt changeddobs_2016-11-28_2017-07-31.txt

Output:
changedtwicedobs_2016-04-04_2016-11-28_2017-07-31.txt

*/
// read input files and grab voter ids and first name + last name + dob + gender strings and write to their own arrays

$diffs1 = file( $argv[1] );
$diffs2 = file( $argv[2] );

$diff1filenameparts = explode( '_', $argv[1] );
$diff2filenameparts = explode( '_', $argv[2] );
$root = $diff1filenameparts[0];
$newroot = str_replace( 'changed', 'changedtwice', $root );
$date1 = $diff1filenameparts[1];
$date2 = str_replace( '.txt', '', $diff1filenameparts[2] );
$date3 = str_replace( '.txt', '', $diff2filenameparts[2] );

$outputfilename = $newroot.'_'.$date1.'_'.$date2.'_'.$date3.'.txt';
$outputfile = fopen( $outputfilename, 'w' );
fwrite( $outputfile, "ID	Snapshot Date	Name	DOB	Gender	Phone	Address	County	Party	Status	Changed Date	Last Voted\n" );

$voterids1 = array();
$voterids2 = array();
foreach( $diffs1 as $key=>$diff1 )
{
	$parts = explode( "\t", $diff1 );
	$fullid = $parts[0];
	$idparts = explode( '-', $fullid );
	$voterids1[ $key ] = $idparts[0];
}
foreach( $diffs2 as $key=>$diff2 )
{
	$parts = explode( "\t", $diff2 );
	$fullid = $parts[0];
	$idparts = explode( '-', $fullid );
	$voterids2[ $key ] = $idparts[0];
}

//find the voter ids that appear in both sets
// do array_intersect twice so we have key/value pairs for lookups in both master arrays
$changedtwice1 = array_intersect( array_unique( $voterids1 ), array_unique( $voterids2 ) );
$changedtwice2 = array_intersect( array_unique( $voterids2 ), array_unique( $voterids1 ) );

$changedtwicekeys1 = array_keys( $changedtwice1 );
$changedtwicekeys2 = array_keys( $changedtwice2 );
$changedtwicevalues1 = array_values( $changedtwice1 );
$changedtwicevalues2 = array_values( $changedtwice2 );

print_r( $changedtwice1 );
echo "\n\n";
print_r( $changedtwice2 );
echo "\n\n";
echo 'Changed Twice 1: '.count( $changedtwice1 )."\n";
echo 'Changed Twice 2: '.count( $changedtwice2 )."\n";
/*extract the "previous" and "new" fields from the original lines depending on which sort of data it was. Get first and second date data from first set, third data data from second set.
fwrite( $doboutputfile, "ID	Name	Previous DOB	DOB	Gender	Phone	Previous Address	Previous County	Address	County	Previous Party	Party	Previous Status	Previous Status	Previous Last Voted	Last Voted	Grep Code\n"  );
fwrite( $nameoutputfile, "ID	Previous Name	Name	DOB	Gender	Phone	Previous Address	Previous County	Address	County	Previous Party	Party	Previous Status	Status	Previous Last Voted	Last Voted	Grep Code\n" );
fwrite( $genderoutputfile, "ID	Name	DOB	Previous Gender	Gender	Phone	Previous Address	Previous County	Address	County	Previous Party	Party	Previous Status	Status	Previous Last Voted	Last Voted	Grep Code\n"  );
*/

$date1lines = array();
$date2lines = array();
$date3lines = array();

// walk through changedtwice1 and changedtwice2 collecting relevant records
// start with 1 because record zero is headers
$i = 1;
while( $i < count( $changedtwicekeys1 ) )
{
	$key1 = $changedtwicekeys1[ $i ];
	$key2 = $changedtwicekeys2[ $i ];
	//echo 'key1 = '.$key1."\n";
	//echo 'key2 = '.$key2."\n";
	$i++;

	$record1 = $diffs1[ $key1 ];
	echo $record1."\n";
	$record2 = $diffs2[ $key2 ];
	echo $record2."\n";
	$parts1 = explode( "\t", $record1 );
	$parts2 = explode( "\t", $record2 );
	if( $root == 'changeddobs' )
	{
		$name1 =  trim( $parts1[1] );
		$name2 =  trim( $parts1[1] );
		$name3 = trim( $parts2[1] );
		$dob1 = trim( $parts1[2] );
		$dob2 = trim( $parts1[3] );
		$dob3 = trim( $parts2[3] );
		$gender1 = trim( $parts1[4] );
		$gender2 = trim( $parts1[4] );
		$gender3 = trim( $parts2[4] );
	}
	elseif( $root == 'changednames' )
	{
		$name1 =  trim( $parts1[1] );
		$name2 = trim( $parts1[2] );
		$name3 = trim( $parts2[2] );
		$dob1 = trim( $parts1[3] );
		$dob2 = trim( $parts1[3] );
		$dob3 = trim( $parts2[3] );
		$gender1 = trim( $parts1[4] );
		$gender2 = trim( $parts1[4] );
		$gender3 = trim( $parts2[4] );
	}
	elseif( $root == 'changedgenders' )
	{
		$name1 =  trim( $parts1[1] );
		$name2 =  trim( $parts1[1] );
		$name3 = trim( $parts2[1] );
		$dob1 = trim( $parts1[2] );
		$dob2 = trim( $parts1[2] );
		$dob3 = trim( $parts2[2] );
		$gender1 = trim( $parts1[3] );
		$gender2 = trim( $parts1[4] );
		$gender3 = 	trim( $parts2[4] );
	}
	else
	{
		return 'No such file.';
	}
	$id1 = trim( $parts1[0] );
	$id2 = trim( $parts1[0] );
	$id3 = trim( $parts2[0] );
	$phone1 = trim( $parts1[5] );
	$phone2 = trim( $parts1[5] );
	$phone3 = trim( $parts2[5] );
	$address1 = trim( $parts1[6] );
	$address2 = trim( $parts1[8] );
	$address3 = trim( $parts2[8] );
	$county1 = trim( $parts1[7] );
	$county2 = trim( $parts1[9] );
	$county3 = trim( $parts2[9] );
	$party1 = trim( $parts1[10] );
	$party2 = trim( $parts1[11] );
	$party3 = trim( $parts2[11] );
	$status1 = trim( $parts1[12] );
	$status2 = trim( $parts1[13] );
	$status3 = trim( $parts2[13] );
	$lastvoted1 = trim( $parts1[14] );
	$lastvoted2 = trim( $parts1[15] );
	$lastvoted3 = trim( $parts2[15] );
	
	if( ( $root == 'changeddobs' && $dob3 != $dob2 ) || ( $root == 'changednames' && $name3 != $name2 ) || ( $root == 'changedgenders' && $gender3 != $gender2 ) )
	{
		$date1line = $id1."	".$date1."	".$name1."	".$dob1."	".$gender1."	".$phone1."	".$address1."	".$county1."	".$party1."	".$status1."	".$lastvoted1."\n";
		$date2line = $id2."	".$date2."	".$name2."	".$dob2."	".$gender2."	".$phone2."	".$address2."	".$county2."	".$party2."	".$status2."	".$lastvoted2."\n";
		$date3line = $id3."	".$date3."	".$name3."	".$dob3."	".$gender3."	".$phone3."	".$address3."	".$county3."	".$party3."	".$status3."	".$lastvoted3."\n";
		$break = "											\n";
		fwrite( $outputfile, $date1line.$date2line.$date3line.$break );
	}
}

?>