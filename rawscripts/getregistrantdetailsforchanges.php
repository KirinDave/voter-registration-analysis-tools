<?php
/*
Checks a "changed party" input file against the original file.
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
php getregistrantdetailsforchanges.php inputfilename datafiledate1 datafiledate2

Example:
php getregistrantdetailsforchanges.php changedparty/ALLEGHENY_2016-11-07_2017-07-31.csv 2016-11-07 2017-07-31

*/

$filenameparts = explode( '_', $argv[1] );
$countyname = str_replace( 'changedparty/', '', $filenameparts[0] );
$olddate = $filenameparts[1];
$newdate = str_replace( '.csv', '', $filenameparts[2] );
$outputfilename = str_replace( '.csv', '_details.csv', $argv[1] );

$outputfile = fopen( $outputfilename, 'w');

$datecode = str_replace( '-', '', $argv[2] );
$filenamesuffix = '_FVE_'.$datecode.'.txt';
$datafile1 = $argv[2].'/'.$countyname.$filenamesuffix;

$datecode = str_replace( '-', '', $argv[3] );
$filenamesuffix = '_FVE_'.$datecode.'.txt';
$datafile2 = $argv[3].'/'.$countyname.$filenamesuffix;

fwrite ( $outputfile, "VOTERS WHO CHANGED PARTIES FROM ".$countyname." COUNTY VOTER ROLLS BETWEEN ".$olddate." AND ".$newdate."\n" );
fwrite ( $outputfile, "ID	Old Party	New Party	Old Name	New Name	Phone	Old DOB	New DOB		Old Home Address	New Home Address	Old Gender	New Gender	Old Status	New Status	Old Registration Date	New Registration Date	Old Status Change Date	New Status Change Date	Old Date Last Changed	New Date Last Changed	Old Last Vote Date	New Last Vote Date	Changed Address	Changed Name	Changed Gender	Changed DOB	Changed Party\n" );


$tsvlines = file( $argv[1] );
foreach ($tsvlines as $tsvline )
{
	//echo $tsvline."\n";
	$lineparts = explode( "\t", $tsvline );
	$id = str_replace( '"', '', $lineparts[0] );
	$idarray[] = $id;
}
$name = array();
$homeaddress = array();
$mailingaddress = array();
$phone = array();
$gender = array();
$status = array();
$registrationdate = array();
$statuschangedate = array();
$partycode = array();
$lastvotedate = array();
$datelastchanged = array();


shell_exec( 'cp '.$datafile1.' tail.tsv' );
while( filesize ( 'tail.tsv' ) > 0 )
{
	clearstatcache ();
	shell_exec( 'mv tail.tsv tmp.tsv' );
	shell_exec( 'head -n 30000 tmp.tsv > head.tsv' );
	shell_exec( 'tail -n +30001 tmp.tsv > tail.tsv' );
	$datalines = file( 'head.tsv' );
	foreach ($datalines as $dataline )
	{
		$lineparts = explode( "\t", $dataline );
		$id = str_replace( '"', '', $lineparts[0] );

		if( in_array( $id, $idarray ) ) 
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
			$name[ $id ] = str_replace( '  ', ' ', trim( trim( str_replace( '"', '', $lineparts[1] ) ).' '.trim( str_replace( '"', '', $lineparts[3] ) ).' '.trim( str_replace( '"', '', $lineparts[4] ) ).' '.trim( str_replace( '"', '', $lineparts[2] ) ).' '.trim( str_replace( '"', '', $lineparts[5] ) ) ) );
			if( str_replace( '"', '', $lineparts[15] ) ) $aptnumber = ' #'.trim( str_replace( '"', '', $lineparts[15] ) ).' ';
			else $aptnumber = '';
			$dob[ $id ] = trim( str_replace( '"', '', $lineparts[7] ) );
			$homeaddress[ $id ] = str_replace( "\t", " ", str_replace( '  ', ' ', trim( trim( str_replace( '"', '', $lineparts[12] ) ).' '.trim( str_replace( '"', '', $lineparts[13] ) ).' '.trim( str_replace( '"', '', $lineparts[14] ) ).$aptnumber.trim( str_replace( '"', '', $lineparts[16] ) ).' '.trim( str_replace( '"', '', $lineparts[17] ) ).' '.trim( str_replace( '"', '', $lineparts[18] ) ).' '.trim( str_replace( '"', '', $lineparts[19] ) ) ) ) );
			$phone[ $id ] = trim( str_replace( '"', '', $lineparts[150] ) );
			$gender[ $id ] = trim( str_replace( '"', '', $lineparts[6] ) );
			$status[ $id ] = str_replace( '"', '', $lineparts[9] );
			$registrationdate[ $id ] = str_replace( '"', '', $lineparts[8] );
			$statuschangedate[ $id ] = str_replace( '"', '', $lineparts[10] );
			$partycode[ $id ] = str_replace( '"', '', $lineparts[11] );
			$lastvotedate[ $id ] = str_replace( '"', '', $lineparts[25] );
			$datelastchanged[ $id ] = str_replace( '"', '', $lineparts[28] );
			echo $id."	".$name[ $id ]."	".$partycode[ $id ]."\n";
		}
	}
}
clearstatcache ();
shell_exec( 'cp '.$datafile2.' tail.tsv' );
echo $datafile2."\n";
while( filesize ( 'tail.tsv' ) > 0 )
{
	clearstatcache ();
	shell_exec( 'mv tail.tsv tmp.tsv' );
	shell_exec( 'head -n 30000 tmp.tsv > head.tsv' );
	shell_exec( 'tail -n +30001 tmp.tsv > tail.tsv' );
	$datalines = file( 'head.tsv' );
	foreach ($datalines as $dataline )
	{
		$lineparts = explode( "\t", $dataline );
		$id = str_replace( '"', '', $lineparts[0] );

		if( in_array( $id, $idarray ) ) 
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
			$newname = str_replace( '  ', ' ', trim( trim( str_replace( '"', '', $lineparts[1] ) ).' '.trim( str_replace( '"', '', $lineparts[3] ) ).' '.trim( str_replace( '"', '', $lineparts[4] ) ).' '.trim( str_replace( '"', '', $lineparts[2] ) ).' '.trim( str_replace( '"', '', $lineparts[5] ) ) ) );
			$newdob = trim( str_replace( '"', '', $lineparts[7] ) );
			if( str_replace( '"', '', $lineparts[15] ) ) $aptnumber = ' #'.trim( str_replace( '"', '', $lineparts[15] ) ).' ';
			else $aptnumber = '';
			$newhomeaddress = str_replace( ',', '', str_replace( "\t", " ", str_replace( '  ', ' ', trim( trim( str_replace( '"', '', $lineparts[12] ) ).' '.trim( str_replace( '"', '', $lineparts[13] ) ).' '.trim( str_replace( '"', '', $lineparts[14] ) ).$aptnumber.trim( str_replace( '"', '', $lineparts[16] ) ).' '.trim( str_replace( '"', '', $lineparts[17] ) ).' '.trim( str_replace( '"', '', $lineparts[18] ) ).' '.trim( str_replace( '"', '', $lineparts[19] ) ) ) ) ) );
			$newphone = trim( str_replace( '"', '', $lineparts[150] ) );
			$newgender = trim( str_replace( '"', '', $lineparts[6] ) );
			$newstatus = str_replace( '"', '', $lineparts[9] );
			$newregistrationdate = str_replace( '"', '', $lineparts[8] );
			$newstatuschangedate = str_replace( '"', '', $lineparts[10] );
			$newpartycode = str_replace( '"', '', $lineparts[11] );
			$newlastvotedate = str_replace( '"', '', $lineparts[25] );
			$newdatelastchanged = str_replace( '"', '', $lineparts[28] );
			$newlastvotedatejulian = strtotime( $newlastvotedate );
			
			$changedaddress = 0;
			$changedname = 0;
			$changedgender = 0;
			$changeddob = 0;
			if( $homeaddress[ $id ] == $newhomeaddress ) $changedaddress = 1;
			if( $name[ $id ] == $newname ) $changedname = 1;
			if( $dob[ $id ] == $newdob ) $changeddob = 1;
			if( $gender[ $id ] == $newgender ) $changedgender = 1;
			
			echo $id."	".$newname."	".$partycode[ $id ]."	".$newpartycode."\n";
			fwrite( $outputfile, $id."	".$partycode[ $id ]."	".$newpartycode."	".$newphone."	".$name[ $id ]."	".$newname."	".$dob[ $id ]."	".$newdob."	".$homeaddress[ $id ]."	".$newhomeaddress."	".$gender[ $id ]."	".$newgender."	".$status[ $id ]."	".$newstatus."	".$registrationdate[ $id ]."	".$newregistrationdate."	".$statuschangedate[ $id ]."	".$newstatuschangedate."	".$datelastchanged[ $id ]."	".$newdatelastchanged."	".$lastvotedate[ $id ]."	".$newlastvotedate."	".$changedaddress."	".$changedname."	".$changeddob."	".$changedgender."\n" );
		}
	}
}

fclose( $outputfile );

?>