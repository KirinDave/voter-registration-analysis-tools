<?php
/*
Gets data for all people over the age of 100 who voted on a given date, looking at the "last voted" date in a later data file. Writes data to a file called centenarianswhovoted.csv in the directory containing the data subsets for this date.

Also writes a file named centenarians.csv listing all who were over 100 on the date of the snapshot

Pulls information out of subset files, taking advantage of the fact that all subset of a given set are indentically indexed.

The "Grep Code" is a unique indicator for county. It allows one to easily extract records for a desired county

NOTE: Choose your data set wisely. If there has been anothe election between the time of the one you are interested in and the date of your set, this script will fail to list all voters from the desired election.

Syntax:
php getcentenarianswhovoted.php %SNAPSHOTDATE% %ELECTIONDATE%

Example:
php getcentenarianswhovoted.php 2017-02-27 2016-11-08

Output:
centenarianswhovoted.csv
centenarians.csv


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

$outputfilename = 'centenarianswhovoted.csv';
$outputfile1 = fopen( $outputfilename, 'w');
fwrite( $outputfile1, "ID	Name	DOB	Age	Gender	Phone	Address	County	Party	Status	Changed	Last Voted	Grep Code\n" );

$outputfilename = 'centenarians.csv';
$outputfile2 = fopen( $outputfilename, 'w');
fwrite( $outputfile2, "ID	Name	DOB	Age	Gender	Phone	Address	County	Party	Status	Changed	Last Voted	Grep Code\n" );

$fullids = file( 'id_set.csv' );
$addressesetc = file( 'address_plus_set.csv' );
$namesetc = file( 'firstname_middlename_lastname_dob_gender_set.csv' );

// if no snapshot date is given use today's date
if( isset( $argv[1] ) ) $snapshotdate = $argv[1];
else $snapshotdate = date( 'Y-m-d');

// if no election date is given use Nov 11, 2016
if( isset( $argv[2] ) ) $electiondate = $argv[2];
else $electiondate = '2016-11-08';

$count = 0;
$votedcount = 0;
foreach( $addressesetc as $key=>$addressesandstuff )
{
	if( $addressesandstuff )
	{
		// initialize
		$nameetc= "";
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

		$nameetc = trim( $namesetc[ $key ] );
		$fullid = trim( $fullids[ $key ] );
		
		//get voterid and county
		$parts = explode( '-', $fullid );
		$voterid = $parts[0];
		$countycode = $parts[1] * 1;
		if( strlen( $countycode ) == 1 ) $paddedcountycode = '0'.$countycode;
		else $paddedcountycode = $countycode;
		$countyname = $counties[ $countycode ];
		
		//get address parts
		$parts = explode( '|', $addressesandstuff );
		$address = $parts[0];
		$phone = $parts[1];
		$party = $parts[2];
		if( isset( $parts[3] ) ) $status = $parts[3]; else $status = '';
		if( isset( $parts[4] ) ) $lastvoteddate = $parts[4]; else $lastvoteddate = '';
		if( isset( $parts[7] ) ) $recordchangeddate = str_replace( "\n", '', $parts[7] ); else $recordchangeddate = '';
		
		//get name dob and gender
		$parts = explode( '|', $nameetc );
		// @ these because some records are missing middle name, dob and gender
		@$justname = $parts[0].' '.$parts[1].' '.$parts[2];
		@$dob = $parts[3];
		@$gender = $parts[4];
		$name = str_replace( "\n", '', $justname."\t".$dob."\t".$gender );
		
		$dobtime = strtotime( $dob );
		// find everyone born before 100 years before the date of this snapshot
		if( $dobtime < ( strtotime( $snapshotdate ) - ( 100 * 365 * 24 * 60 * 60 ) ) )
		{
			$count++;
			$ageatsnapshotdate = round( ( strtotime( $snapshotdate ) - $dobtime ) / ( 365 * 24 * 60 * 60 ), 1 );
			fwrite( $outputfile2, trim( $fullid )."\t".$justname."\t".$dob."\t".$ageatsnapshotdate."\t".$gender."\t".$phone."\t".$address."\t".$countyname."\t".$party."\t".$status."\t".$recordchangeddate."\t".$lastvoteddate."\t".$paddedcountycode.'xxxxxxx'."\n" );
			
			// find those who last voted on a given date, or on November 8, 2016 if no date is set
			if( strtotime( $lastvoteddate ) == strtotime( $electiondate ) )
			{
				$ageatelection = round( ( strtotime( $electiondate ) - $dobtime ) / ( 365 * 24 * 60 * 60 ), 1 );
				if( $ageatelection >= 100 )
				{
					$votedcount++;
					echo 'Voted: '.$justname.', '.$dob.', '.$ageatelection.', '.$gender."\n";
					fwrite( $outputfile1, trim( $fullid )."\t".$justname."\t".$dob."\t".$ageatelection."\t".$gender."\t".$phone."\t".$address."\t".$countyname."\t".$party."\t".$status."\t".$recordchangeddate."\t".$lastvoteddate."\t".$paddedcountycode.'xxxxxxx'."\n" );
				}
			}
		}
	}
}

echo 'Centenarians: '.$count."\n";
echo 'Centenarians who voted on '.$electiondate.': '.$votedcount."\n\n";

fclose( $outputfile1 );
fclose( $outputfile2 );


?>