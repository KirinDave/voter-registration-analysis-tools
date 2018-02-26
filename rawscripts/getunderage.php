<?php
/*
Looks at addressesetc.txt - containing all dobs and all lastvoted dates among other things
Checks "lastvoted" date against birthdate. 
If the diff is less than 18*356*24*60*60 the voter was under 18 on his or her "last voted" date

Syntax:
php getunderage.php


Output: 
underage.csv

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


$outputfilename = 'underage.csv';
$outputfile = fopen( $outputfilename, 'w' );
fwrite( $outputfile, "ID	Name	DOB	Gender	Phone	Address	County	Party	Status	Changed	Last Voted	Grep Code\n" );

$fullids = file( 'fullids.txt' );
$addressesetc = file( 'addressesetc.txt' );
$names = file( 'firstnamelastnamedobs.txt' );

$count = 0;
foreach( $addressesetc as $key=>$addressetc )
{
	$name= "";
	$justname= "";
	$dob= "";
	$gender= "";
	$fullid= "";
	$countyname= "";
	$address= "";
	$phone= "";
	$party= "";
	$status= "";
	$recordchangeddate= "";
	$lastvoteddate= "";

	$name = trim( $names[ $key ] );
	$fullid = trim( $fullids[ $key ] );

	$parts = explode( '-', $fullid );
	$voterid = $parts[0];
	$countycode = $parts[1] * 1;
	if( strlen( $countycode ) == 1 ) $paddedcountycode = '0'.$countycode;
	else $paddedcountycode = $countycode;
	$countyname = $counties[ $countycode ];
	$parts = explode( "\t", $addressetc );
	$address = $parts[0];
	$addressparts = explode( ' ', $address );
	$address = str_replace( $addressparts[0], '', $address );
	$phone = $parts[1];
	$party = $parts[2];
	if( isset( $parts[3] ) ) $status = $parts[3]; else $status = '';
	if( isset( $parts[4] ) ) $lastvoteddate = $parts[4]; else $lastvoteddate = '';
	if( isset( $parts[7] ) ) $recordchangeddate = str_replace( "\n", '', $parts[7] ); else $recordchangeddate = '';
	
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
	$diffseconds = 100*365*24*60*60;
	if( $lastvoteddate && $dob)
	{
		$lastvotedjulian = strtotime( $lastvoteddate );
		$dobjulian = strtotime( $dob );
		$diffseconds = $lastvotedjulian - $dobjulian;
		$agewhenlastvoted = round( $diffseconds / (365*24*60*60 ), 2 );
	}
	// look at difference between "last voted" date and age
	if( $diffseconds < 18*365*24*60*60 )
	{
		$count++;
		echo trim( $fullid )."\t".$justname."\t".$dob."\t".$gender."\t".$phone."\t".$address."\t".$countyname."\t".$party."\t".$status."\t".$recordchangeddate."\t".$lastvoteddate."\t".$agewhenlastvoted."\t".$paddedcountycode.'xxxxxxx'."\n";
		fwrite( $outputfile, trim( $fullid )."\t".$justname."\t".$dob."\t".$gender."\t".$phone."\t".$address."\t".$countyname."\t".$party."\t".$status."\t".$recordchangeddate."\t".$lastvoteddate."\t".$agewhenlastvoted."\t".$paddedcountycode.'xxxxxxx'."\n" );
	}
}

echo 'Underaged voters: '.$count."\n";
?>