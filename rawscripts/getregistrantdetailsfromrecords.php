<?php
/*
Looks at files containing full registrant data and writes a detailed line for each id to a county specific output file.
Writes a summary file containing counts by party per county

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
70-149 Election History Vote = even Party = odd
150 Phone
151 County
152 Country

For 2011

Syntax:
php getregistrantdetailsfromrecords.php filename 
Example:
php getregistrantdetailsfromrecords.php dupefullidrecords.csv

*/

$counties = array(
1 =>'ADAMS',
2 =>'ALLEGHENY',
3 =>'ARMSTRONG',
4 =>'BEAVER',
5 =>'BEDFORD',
6 =>'BERKS',
7 =>'BLAIR',
8 =>'BRADFORD',
9 =>'BUCKS',
10 =>'BUTLER',
11 =>'CAMBRIA',
12 =>'CAMERON',
13 =>'CARBON',
14 =>'CENTRE',
15 =>'CHESTER',
16 =>'CLARION',
17 =>'CLEARFIELD',
18 =>'CLINTON',
19 =>'COLUMBIA',
20 =>'CRAWFORD',
21 =>'CUMBERLAND',
22 =>'DAUPHIN',
23 =>'DELAWARE',
24 =>'ELK',
25 =>'ERIE',
26 =>'FAYETTE',
27 =>'FOREST',
28 =>'FRANKLIN',
29 =>'FULTON',
30 =>'GREENE',
31 =>'HUNTINGDON',
32 =>'INDIANA',
33 =>'JEFFERSON',
34 =>'JUNIATA',
35 =>'LACKAWANNA',
36 =>'LANCASTER',
37 =>'LAWRENCE',
38 =>'LEBANON',
39 =>'LEHIGH',
40 =>'LUZERNE',
41 =>'LYCOMING',
42 =>'McKEAN',
43 =>'MERCER',
44 =>'MIFFLIN',
45 =>'MONROE',
46 =>'MONTGOMERY',
47 =>'MONTOUR',
48 =>'NORTHAMPTON',
49 =>'NORTHUMBERLAND',
50 =>'PERRY',
51 =>'PHILADELPHIA',
52 =>'PIKE',
53 =>'POTTER',
54 =>'SCHUYLKILL',
55 =>'SNYDER',
56 =>'SOMERSET',
57 =>'SULLIVAN',
58 =>'SUSQUEHANNA',
59 =>'TIOGA',
60 =>'UNION',
61 =>'VENANGO',
62 =>'WARREN',
63 =>'WASHINGTON',
64 =>'WAYNE',
65 =>'WESTMORELAND',
66 =>'WYOMING',
67 =>'YORK'
);
$count = array();
$repcount = array();
$demcount = array();
$othercount = array();
foreach( $counties as $countycode=>$county )
{
	$countyoutputfilename = $county.'_'.$argv[1];
	$countyfile[ $county ] = fopen( $countyoutputfilename, 'w' );
	fwrite( $countyfile[ $county ], "ID	Name	DOB	Home Address	County	Phone	Gender	Status	Party	Registration Date	Status Change Date	Date Last Changed	Last Vote Date	Precinct Code	VotingHistory\n" );
	$count[ $county ] = 0;
	$repcount[ $county ] = 0;
	$demcount[ $county ] = 0;
	$othercount[ $county ] = 0;
}
$alloutputfilename = str_replace( 'records', 'records_allcounties', $argv[1] );
$alloutputfile = fopen( $alloutputfilename, 'w' );
fwrite( $alloutputfile, "ID	Name	DOB	Home Address	County	Phone	Gender	Status	Party	Registration Date	Status Change Date	Date Last Changed	Last Vote Date	Precinct Code	VotingHistory\n" );

$outputfilename = str_replace( 'records', 'summary', $argv[1] );
$outputfile = fopen( $outputfilename, 'w' );
fwrite( $outputfile, "County	Total	Republicans	Democrats	Other\n" );

$duperecords = file( $argv[1] );
$lastvoterid = '';
$previousoutputline = '';
$ids = array();
//first make an array of voterids and multisort the two arrays
if( $duperecords )
{
	foreach( $duperecords as $key=>$dataline )
	{
		if( strlen( $dataline ) > 20 )
		{
			$lineparts = explode( "\t", $dataline );
			$ids[] = $lineparts[0];
		}
	}
}

array_multisort( $ids, $duperecords );

if( $duperecords )
{
	foreach( $duperecords as $key=>$dataline )
	{
		if( strlen( $dataline ) > 20 )
		{
			$lineparts = explode( "\t", $dataline );
			$id = str_replace( '"', '', $lineparts[0] );
			$parts = explode( '-', $id );
			$voterid = $parts[0];
			$countycode = $parts[1] * 1;
			$county = $counties[ $countycode ];
			if( !isset( $count[ $county ] ) ) $count[ $county ] = 0;
			if( !isset( $demcount[ $county ] ) ) $demcount[ $county ] = 0;
			if( !isset( $repcount[ $county ] ) ) $repcount[ $county ] = 0;
			if( !isset( $othercount[ $county ] ) ) $othercount[ $county ] = 0;
			echo $voterid.' - '.$county."\n";

			
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
			$precinctcode = str_replace( '"', '', $lineparts[26] );
			//get vote history as a string of pluses and minuses
			$field = 70;
			$votehistorystring = '';
			while( $field < 150 )
			{
				// script somehow picked up same record twice instead of picking up the altered one.
				// alter voter history as the data was altered in the original file
				$votehistory = trim( str_replace( '"', '', $lineparts[ $field ] ) );
				if( $voterid == $previousvoterid && $field < 90 )
				{
					if( $votehistory ) $votehistorystring  = $votehistorystring .'_';
					else $votehistorystring  = $votehistorystring .'+';
				}
				else
				{
					if( $votehistory ) $votehistorystring  = $votehistorystring .'+';
					else $votehistorystring  = $votehistorystring .'_';
				}
				$field = $field + 2;
			}
			$outputline = $id."	".$name."	".$dob."	".$homeaddress."	".$county."	".$phone."	".$gender."	".$status."	".$partycode."	".$registrationdate."	".$statuschangedate."	".$datelastchanged."	".$lastvotedate."	".$precinctcode."	".$votehistorystring ."\n";
			echo $outputline;
			
			// if we are getting data from dupevoterids.txt file we need to write lines from the two counties next to each other in BOTH county output files
			if( $argv[1] == 'dupetwocountyvoteridrecords.txt' )
			{
				if( $previousvoterid != $voterid )
				{
					fwrite( $countyfile[ $county ], $outputline );
					fwrite( $countyfile[ $county ], $previousoutputline );
					fwrite( $countyfile[ $county ], "\n" );
					fwrite( $countyfile[ $previouscounty ], $previousoutputline );
					fwrite( $countyfile[ $previouscounty ], $outputline );
					fwrite( $countyfile[ $previouscounty ], "\n" );
				}
			}
			else
			{
				fwrite( $countyfile[ $county ], $outputline );
				fwrite( $alloutputfile, $outputline );
			}
			
			if( $previousvoterid != $voterid )
			{
				$count[ $county ] = $count[ $county ] + 1;
				if( $partycode == 'R' ) $repcount[ $county ] = $repcount[ $county ] + 1;
				elseif( $partycode == 'D' ) $demcount[ $county ] = $demcount[ $county ] + 1;
				else $othercount[ $county ] = $othercount[ $county ] + 1;
			}
			$previousoutputline = $outputline;
			$previouscounty = $county;
			$previousvoterid = $voterid;
		}
	}
}

foreach( $counties as $countycode=>$county )
{
	fwrite ( $outputfile, $county."	".$count[ $county ]."	".$repcount[ $county ]."	".$demcount[ $county ]."	".$othercount[ $county ]."\n" );
}

fclose( $outputfile );
fclose( $alloutputfile );


?>