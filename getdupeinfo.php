<?php
/*
Prints the total, unique and non-unique data counts for a file contain one datum per line

Finds each piece of non-unique data and get the voter's voterid, full name, dob, gender, address and other relevant info from the relevant "subset" files.

Syntax:
php getdupeinfo.php %FILENAME% 

Example:
php getdupeinfo.php firstname_middlename_lastname_set.csv ..
writes data to firstname_middlename_lastname_dupeinfo.csv

NOTE: It's a good idea to run printcounts.php on your record set of choice before running this script. The output file can easily become quite large.

*/


$outputfilename = str_replace( '_set.csv', '_dupeinfo.csv', $argv[1] );
$outputfile = fopen( $outputfilename, 'w' );
fwrite( $outputfile, "ID\tFirst Name\tMiddle Name\tLast Name\tDOB\tGender\tAddress\tCounty\tPhone\tParty\tStatus\tLast Voted\tRegistered\tStatus Changed\tLastChanged\n" );

$records = file( $argv[1] );
if( isset( $argv[2] ) )
{
	$datapath = $argv[2];
	if( strpos( '/', $datapath ) === false ) $datapath .= '/';
}
else $datapath = '';

$fullids = file( 'id_set.csv' );
$namesplus = file( 'firstname_middlename_lastname_dob_gender_set.csv' );
$addressesplus = file( 'address_plus_set.csv' );

$uniquerecords = array_unique( $records );
// this set is only ONE of the instances of each dupe. we need all of them.
$duperecords = array_diff_key( $records,  $uniquerecords );

// if we add in the records that intersect we will be all _set
$matchingduperecords = array_intersect( $uniquerecords, $duperecords );

echo 'dupes = '.count( $duperecords )."\n";
echo 'matching dupes = '.count( $matchingduperecords )."\n";



// now get full voter id and original array key for each duplicate record
$allduperecords = array();
$duperecordids = array();
$duperecordnames = array();
$duperecordaddresses = array();
foreach( $duperecords as $key=>$record )
{
	$allduperecords[] = $record;
	$dupekeys[] = $key;
	$duperecordids[] = trim( $fullids[ $key ] );
	$duperecordnames[] = trim( $namesplus[ $key ] );
	$duperecordaddresses[] = trim( $addressesplus[ $key ] );
}
foreach( $matchingduperecords as $key=>$record )
{
	$allduperecords[] = $record;
	$duperecordids[] = trim( $fullids[ $key ] );
	$duperecordnames[] = trim( $namesplus[ $key ] );
	$duperecordaddresses[] = trim( $addressesplus[ $key ] );
}


// sort arrays so the duplicate values are next to each other in the outputfile
array_multisort( $allduperecords, $duperecordids, $duperecordnames, $duperecordaddresses );

foreach( $allduperecords as $key=>$record )
{
	fwrite( $outputfile, $duperecordids[ $key ]."\t".str_replace( '|', "\t", $duperecordnames[ $key ] )."\t".str_replace( '|', "\t", $duperecordaddresses[ $key ] )."\n" );
}

$firstrecords = array();
$totallines = 0;

echo $argv[1].': total duplicates found = '.count( $allduperecords ) ."\n";


fclose( $outputfile );

?>