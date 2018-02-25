<?php
/*
Writes selected data from a Pennsylvania SURE dataset into a set of files, each containing all data for the state. Each line of these exported files contains selected data fields, delimited by tabs and wrapped in double quotes.


In Pennsylvania each voter record has a “base id” of nine digits, then a hyphen, then a two digit code indicating the county. For instance the voter id 00123467-01 indicates that the voter lives in county 01, or Adams County. If that voter moved to Allegheny county, his or her new “full id” would be 00123467-02. That voter’s “base id” is 00123467, and that id should identify the voter as long as he or she is a voter in Pennsylvania.

Syntax:
php getsubsets_pa.php %SUBDIRNAME% %CUSTOMINDEXSTRING%

Examples:
php getsubsets_pa.php (default)
php getsubsets_pa.php datasubsets 3+4+2+8

Run in default mode with no arguments, this script produces the following output files:
address_set.csv
address_plus_set.csv (includes address, phone, party, status, last voted date, registration date, status change date, date last changed) 
baseid_set.csv
id_set.csv
firstname_set.csv
firstname_lastname_set.csv
firstname_dob_set.csv
firstname_gender_set.csv
firstname_middlename_lastname_set.csv
firstname_middlename_lastname_dob_set.csv
firstname_middlename_lastname_dob_gender_set.csv
firstname_middlename_lastname_dob_gender_party_set.csv
lastname_dob_set.csv
lastname_dob_gender_set.csv
party_set.csv
party_lastvoted_set.csv

This script is intended to be run in the directory where you have uncompressed your data files.
If the %SUBDIRNAME% argument is set it will write the data to a given subdirectory, otherwise it will write it locally.

If the %CUSTOM% argument is set, the output will be a single file containing the requested field indexes from the list below.
The %CUSTOM% argument syntax is the set of field indexes separated by plus signs, named according to the field values.
for instance setting the argument to 3+4+2+8 will yield a file named firstname_middlename_lastname_registrationdate_set.csv

Uesful fields in 2013-2017 (at least) FVE files
If any further fields are desired, use the document Voter Export.docx to add them to this array, making sure to decrement the index

TODO: Use the Election Map data provided for each county so we interpret the "election history" fields and create a data subset with fields named something like "last general election", "last primary election", and "last municipal election"

*/
$fields = 
array(
0 => 'ID Number',
1 => 'Title',
2 => 'Last Name',
3 => 'First Name',
4 => 'Middle Name',
5 => 'Suffix',
6 => 'Gender',
7 => 'DOB',
8 => 'Registration Date',
9 => 'Voter Status',
10 => 'Status Change Date',
11 => 'Party Code',
12 => 'House Number',
13 => 'House Number Suffix',
14 => 'Street Name',
15 => 'Apartment Number',
16 => 'Address Line 2',
17 => 'City',
18 => 'State',
19 => 'Zip',
20 => 'Mail Address 1',
21 => 'Mail Address 2',
22 => 'City',
23 => 'State',
24 => 'Zip',
25 => 'Last Vote Date',
26 => 'Precinct Code',
27 => 'Precinct Split ID',
28 => 'Date Last Changed',
150 => 'Phone',
151 => 'County',
152 => 'Country'
);

//keep script from timing out
set_time_limit(0);
// create subdir if $argv[1] is set
if( isset( $argv[1] ) )
{
	$subdir = $argv[1].'/';
	@shell_exec( 'mkdir '.$argv[1] );
}
else $subdir = '';

if( isset( $argv[2] ) )
{
	$fieldindeces = explode( '+', $argv[2] );
	$outputfilename = '';
	foreach( $fieldindeces as $fieldindex )
	{
		$outputfilename .= str_replace( ' ', '', strtolower( $fields[ $fieldindex ] ) ).'_';
	}
	$outputfilename .= 'set.csv';
	$outputfile = fopen( $subdir.$outputfilename, 'w' );
}
else
{
	$outputfiles = array();
	// use mnemonic indexes so it's easier to assign fields to files when writing data
	$outputfiles[ 'a' ] = fopen( $subdir.'address_set.csv', 'w' );
	$outputfiles[ 'a+' ] = fopen( $subdir.'address_plus_set.csv', 'w' );
	$outputfiles[ 'bid' ] = fopen( $subdir.'baseid_set.csv', 'w' );
	$outputfiles[ 'id' ] = fopen( $subdir.'id_set.csv', 'w' );
	$outputfiles[ 'fn' ] = fopen( $subdir.'firstname_set.csv', 'w' );
	$outputfiles[ 'fn-dob' ] = fopen( $subdir.'firstname_dob_set.csv', 'w' );
	$outputfiles[ 'fn-ln' ] = fopen( $subdir.'firstname_lastname_set.csv', 'w' );
	$outputfiles[ 'fn-g' ] = fopen( $subdir.'firstname_gender_set.csv', 'w' );
	$outputfiles[ 'fn-mn-ln' ] = fopen( $subdir.'firstname_middlename_lastname_set.csv', 'w' );
	$outputfiles[ 'fn-mn-ln-dob' ] = fopen( $subdir.'firstname_middlename_lastname_dob_set.csv', 'w' );
	$outputfiles[ 'fn-mn-ln-dob-g' ] = fopen( $subdir.'firstname_middlename_lastname_dob_gender_set.csv', 'w' );
	$outputfiles[ 'fn-mn-ln-dob-g-p' ] = fopen( $subdir.'firstname_middlename_lastname_dob_gender_party_set.csv', 'w' );
	$outputfiles[ 'ln-dob' ] = fopen( $subdir.'lastname_dob_set.csv', 'w' );
	$outputfiles[ 'ln-dob-g' ] = fopen( $subdir.'lastname_dob_gender_set.csv', 'w' );
	$outputfiles[ 'p' ] = fopen( $subdir.'party_set.csv', 'w' );
	$outputfiles[ 'p-lv' ] = fopen( $subdir.'party_lastvoted_set.csv', 'w' );
}

$fvefiles = shell_exec( 'ls -1 *_FVE_*' );
// if this is the first time running the script let's start by renaming the FVE files so they use underbars rather than spaces
if( ! $fvefiles )
{
	$oldfvefiles = shell_exec( 'ls -1 *FVE*' );
	$oldfvearray = explode( "\n", $oldfvefiles );
	//get the date from the first file
	$fvefilename = $oldfvearray[0];
	$nameparts = explode( ' ', $fvefilename );
	$date = str_replace( '.txt', '', $nameparts[2] );
	shell_exec( 'sh rename_files_pa.sh '.$date );
	$fvefiles = shell_exec( 'ls -1 *_FVE_*' );
}


//echo $fvefiles;
if( $fvefiles )
{
	$fvearray = explode( "\n", $fvefiles );
	//print_r( $fvearray );
	foreach( $fvearray as $fvefile )
	{
		if( $fvefile )
		{
			echo 'processing '.$fvefile."\n";
			$bits = explode( '_', $fvefile);
			$countyname = $bits[0];
			clearstatcache();
			shell_exec( 'cp '.$fvefile.' tail.tsv' );
			while( filesize ( 'tail.tsv' ) > 0 )
			{
				clearstatcache();
				shell_exec( 'mv tail.tsv tmp.tsv' );
				shell_exec( 'head -n 50000 tmp.tsv > head.tsv' );
				shell_exec( 'tail -n +50001 tmp.tsv > tail.tsv' );
				$datalines = file( 'head.tsv' );
				foreach ($datalines as $dataline )
				{
					$dataline = str_replace( '"', '', $dataline );
					$lineparts = explode( "\t", $dataline );
					if( isset( $argv[2] ) )
					{
						$outputstring = '';
						foreach( $fieldindeces as $fieldindex )
						{
							$outputstring .= $lineparts[ $fieldindex ]."\t";
						}
						$outputstring .= "\n";
						// remove final tab
						$outputstring = str_replace( "\t\n", "\n", $outputstring );
						fwrite( $outputfile, $outputstring );
					}
					else
					{
						// naming variables for clarity at the cost of some efficiency
						$fullid =  trim( $lineparts[0] );
						$idparts = explode( '-', $fullid );
						$baseid = $idparts[0];
						$firstname =  str_replace( '"', '', $lineparts[3] );
						$middlename =  str_replace( '"', '', $lineparts[4] );
						$lastname =  str_replace( '"', '', $lineparts[2] );
						$dob =  str_replace( '"', '', $lineparts[7] );
						$gender =  str_replace( '"', '', $lineparts[6] );
						
						$num =  trim( str_replace( '"', '', $lineparts[12] ) );
						$street =  trim( str_replace( '"', '', $lineparts[14] ) );
						$apt =  trim( str_replace( '#', '', str_replace( '"', '', $lineparts[15] ) ) );
						$city =  trim( str_replace( '"', '', $lineparts[17] ) );
						$phone =  trim( str_replace( '"', '', $lineparts[150] ) );
						
						$party =  trim( str_replace( '"', '', $lineparts[11] ) );
						$status =  trim( str_replace( '"', '', $lineparts[9] ) );
						$lastvotedate =  trim( str_replace( '"', '', $lineparts[25] ) );
						$registrationdate =  trim( str_replace( '"', '', $lineparts[8] ) );
						$statuschangedate =  trim( str_replace( '"', '', $lineparts[10] ) );
						$lastchangedate =  trim( str_replace( '"', '', $lineparts[28] ) );
						$address = $num.' '.$street.' '.$apt.' '.$city.' '.$countyname;
						
						// write to output files
						// use pipe as delimeter because explode isn't adding empty array elements for multiple tabs afte they are written using fwrite
						// works fine for the raw data files. go figure.
						fwrite( $outputfiles['a'], $address."\n" );
						fwrite( $outputfiles['a+'], $address.'|'.$phone.'|'.$party.'|'.$status.'|'.$lastvotedate.'|'.$registrationdate.'|'.$statuschangedate.'|'.$lastchangedate."\n" );
						fwrite( $outputfiles['bid'], $baseid."\n" );
						fwrite( $outputfiles['id'], $fullid."\n" );
						fwrite( $outputfiles['fn'], $firstname."\n" );
						fwrite( $outputfiles['fn-dob'], $firstname.'|'.$dob."\n" );
						fwrite( $outputfiles['fn-ln'], $firstname.'|'.$lastname."\n" );
						fwrite( $outputfiles['fn-g'], $firstname.'|'.$gender."\n" );
						fwrite( $outputfiles['fn-mn-ln'], $firstname.'|'.$middlename.'|'.$lastname."\n" );
						fwrite( $outputfiles['fn-mn-ln-dob'], $firstname.'|'.$middlename.'|'.$lastname.'|'.$dob."\n" );
						fwrite( $outputfiles['fn-mn-ln-dob-g'], $firstname.'|'.$middlename.'|'.$lastname.'|'.$dob.'|'.$gender."\n" );
						fwrite( $outputfiles['fn-mn-ln-dob-g-p'], $firstname.'|'.$middlename.'|'.$lastname.'|'.$dob.'|'.$gender.'|'.$party."\n" );
						fwrite( $outputfiles['ln-dob'], $lastname.'|'.$dob."\n" );
						fwrite( $outputfiles['ln-dob-g'], $lastname.'|'.$dob.'|'.$gender."\n" );
						fwrite( $outputfiles['p'], $party."\n" );
						fwrite( $outputfiles['p-lv'], $party.'|'.$lastvotedate."\n" );
					}
				}
			}
		}
	}
}
if( isset( $argv[2] ) )
{
	fclose( $outputfile );
}
else
{
	fclose( $outputfiles[ 'a' ] );
	fclose( $outputfiles[ 'a+' ] );
	fclose( $outputfiles[ 'bid' ] );
	fclose( $outputfiles[ 'id' ] );
	fclose( $outputfiles[ 'fn' ] );
	fclose( $outputfiles[ 'fn-dob' ] );
	fclose( $outputfiles[ 'fn-ln' ] );
	fclose( $outputfiles[ 'fn-g' ] );
	fclose( $outputfiles[ 'fn-mn-ln' ] );
	fclose( $outputfiles[ 'fn-mn-ln-dob' ] );
	fclose( $outputfiles[ 'fn-mn-ln-dob-g' ] );
	fclose( $outputfiles[ 'fn-mn-ln-dob-g-p' ] );
	fclose( $outputfiles[ 'ln-dob' ] );
	fclose( $outputfiles[ 'ln-dob-g' ] );
	fclose( $outputfiles[ 'p' ] );
	fclose( $outputfiles[ 'p-lv' ] );
}

?>
