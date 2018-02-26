<?php
/*
Looks at the list of voters from the Nov 07 data set who either registered or changed registration after August 15, then make a lists of all those who are missing from Feb 28 data set or according to Feb 28 data set did not vote.

Write names, addresses, etc to output files.

This runs from within the Nov 07 data directory

Syntax:
php getchangedregthendidntvote.php


Output: 
changedregthendisappeared.csv
changedregthendidntvote.csv
registeredthendisappeared.csv
registeredthendidntvote.csv


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


$outputfilename1 = 'registeredthendisappeared.csv';
$outputfile1 = fopen( $outputfilename1, 'w' );
fwrite( $outputfile1, "ID	Name	DOB	Gender	Phone	Address	County	Party	Status	Registered	Last Voted	Record Changed	Grep Code\n" );

$outputfilename2 = 'registeredthendidntvote.csv';
$outputfile2 = fopen( $outputfilename2, 'w' );
fwrite( $outputfile2, "ID	Name1	Name2	DOB1	DOB2	Gender1	Gender2	Phone1	Phone2	Address1	County1	Address2	County2	Party1	Party2	Status1	Status2	Registered1	Registered2	Last Voted1	Last Voted2	Record Changed1	Record Changed2	Grep Code\n" );

$outputfilename3 = 'changedregthendisappeared.csv';
$outputfile3 = fopen( $outputfilename3, 'w' );
fwrite( $outputfile3, "ID	Name	DOB	Gender	Phone	Address	County	Party	Status	Registered	Last Voted	Record Changed	Grep Code\n" );

$outputfilename4 = 'changedregthendidntvote.csv';
$outputfile4 = fopen( $outputfilename4, 'w' );
fwrite( $outputfile4, "ID	Name1	Name2	DOB1	DOB2	Gender1	Gender2	Phone1	Phone2	Address1	County1	Address2	County2	Party1	Party2	Status1	Status2	Registered1	Registered2	Last Voted1	Last Voted2	Record Changed1	Record Changed2	Grep Code\n" );

$outputfilename5 = 'regandchangesbreakdown.csv';
$outputfile5 = fopen( $outputfilename5, 'w' );
fwrite( $outputfile5, "		Total							Didn't Vote							Disappeared						\n" );
fwrite( $outputfile5, "Date 1	Date 2	Registered	Registered Dems	Registered Reps	Registered Others	Changed	Changed Dems	Changed Reps	Changed Others	Registered	Registered Dems	Registered Reps	Registered Others	Changed	Changed Dems	Changed Reps	Changed Others	Registered	Registered Dems	Registered Reps	Registered Others	Changed	Changed Dems	Changed Reps	Changed Others	\n" );

$outputfilename6 = 'regandchangesbycounty_may.csv';
$outputfile6 = fopen( $outputfilename6, 'w' );
fwrite( $outputfile6, "	Total							Didn't Vote							Disappeared						\n" );
fwrite( $outputfile6, "County	Registered	Registered Dems	Registered Reps	Registered Others	Changed	Changed Dems	Changed Reps	Changed Others	Registered	Registered Dems	Registered Reps	Registered Others	Changed	Changed Dems	Changed Reps	Changed Others	Registered	Registered Dems	Registered Reps	Registered Others	Changed	Changed Dems	Changed Reps	Changed Others	\n" );

$voterids1 = file( 'voterids.txt' );
$fullids1 = file( 'fullids.txt' );
$addressesetc1 = file( 'addressesetc.txt' );
$names1 = file('firstnamelastnamedobs.txt' );
echo 'sorting arrays from nov 07'."\n";
array_multisort( $voterids1, $fullids1, $addressesetc1, $names1 );

$voterids2 = file( '../2017-02-27/voterids.txt' );
$fullids2 = file( '../2017-02-27/fullids.txt' );
$addressesetc2 = file( '../2017-02-27/addressesetc.txt' );
$names2 = file( '../2017-02-27/firstnamelastnamedobs.txt' );
echo 'sorting arrays from feb 27'."\n";
array_multisort( $voterids2, $fullids2, $addressesetc2, $names2 );



$key2 = 0;
$afterdates = array();
//look at two week increments starting May 1
$afterdates[1] = strtotime( '04/30/2016' );
$afterdates[2] = strtotime( '05/14/2016' );
$afterdates[3] = strtotime( '05/31/2016' );
$afterdates[4] = strtotime( '06/14/2016' );
$afterdates[5] = strtotime( '06/30/2016' );
$afterdates[6] = strtotime( '07/14/2016' );
$afterdates[7] = strtotime( '07/31/2016' );
$afterdates[8] = strtotime( '08/14/2016' );
$afterdates[9] = strtotime( '08/31/2016' );
$afterdates[10] = strtotime( '09/14/2016' );
$afterdates[11] = strtotime( '09/30/2016' );
$afterdates[12] = strtotime( '10/14/2016' );
$afterdates[13] = strtotime( '10/31/2016' );

$saveafterdate = $afterdates[1];

rsort( $afterdates, SORT_NUMERIC);

// set up arrays for each time bin
$registered = array();
$registereddems = array();
$registeredreps = array();
$registereddidntvote = array();
$registereddidntvotedems = array();
$registereddidntvotereps = array();
$registereddisappeared = array();
$registereddisappeareddems = array();
$registereddisappearedreps = array();
$changed = array();
$changeddems = array();
$changedreps = array();
$changeddidntvote = array();
$changeddidntvotedems = array();
$changeddidntvotereps = array();
$changeddisappeared = array();
$changeddisappeareddems = array();
$changeddisappearedreps = array();
foreach( $afterdates as $afterdate )
{
	$registered[ $afterdate ] = 0;
	$registereddems[ $afterdate ] = 0;
	$registeredreps[ $afterdate ] = 0;
	$registereddidntvote[ $afterdate ] = 0;
	$registereddidntvoteems[ $afterdate ] = 0;
	$registereddidntvotereps[ $afterdate ] = 0;
	$registereddisappeared[ $afterdate ] = 0;
	$registereddisappeareddems[ $afterdate ] = 0;
	$registereddisappearedreps[ $afterdate ] = 0;
	$changed[ $afterdate ] = 0;
	$changeddems[ $afterdate ] = 0;
	$changedreps[ $afterdate ] = 0;
	$changeddidntvote[ $afterdate ] = 0;
	$changeddidntvotedems[ $afterdate ] = 0;
	$changeddidntvotereps[ $afterdate ] = 0;
	$changeddisappeared[ $afterdate ] = 0;
	$changeddisappeareddems[ $afterdate ] = 0;
	$changeddisappearedreps[ $afterdate ] = 0;
}
// set up arrays for each county
$registered_bycounty = array();
$registereddems_bycounty = array();
$registeredreps_bycounty = array();
$registereddidntvote_bycounty = array();
$registereddidntvotedems_bycounty = array();
$registereddidntvotereps_bycounty = array();
$registereddisappeared_bycounty = array();
$registereddisappeareddems_bycounty = array();
$registereddisappearedreps_bycounty = array();
$changed_bycounty = array();
$changeddems_bycounty = array();
$changedreps_bycounty = array();
$changeddidntvote_bycounty = array();
$changeddidntvotedems_bycounty = array();
$changeddidntvotereps_bycounty = array();
$changeddisappeared_bycounty = array();
$changeddisappeareddems_bycounty = array();
$changeddisappearedreps_bycounty = array();
foreach( $counties as $county )
{
	$registered_bycounty[ $county ] = 0;
	$registereddems_bycounty[ $county ] = 0;
	$registeredreps_bycounty[ $county ] = 0;
	$registereddidntvote_bycounty[ $county ] = 0;
	$registereddidntvoteems_bycounty[ $county ] = 0;
	$registereddidntvotereps_bycounty[ $county ] = 0;
	$registereddisappeared_bycounty[ $county ] = 0;
	$registereddisappeareddems_bycounty[ $county ] = 0;
	$registereddisappearedreps_bycounty[ $county ] = 0;
	$changed_bycounty[ $county ] = 0;
	$changeddems_bycounty[ $county ] = 0;
	$changedreps_bycounty[ $county ] = 0;
	$changeddidntvote_bycounty[ $county ] = 0;
	$changeddidntvotedems_bycounty[ $county ] = 0;
	$changeddidntvotereps_bycounty[ $county ] = 0;
	$changeddisappeared_bycounty[ $county ] = 0;
	$changeddisappeareddems_bycounty[ $county ] = 0;
	$changeddisappearedreps_bycounty[ $county ] = 0;
}
foreach( $voterids1 as $key1=>$voterid1 )
{
	$firstkey = $key2;
	$addressesandstuff = $addressesetc1[ $key1 ];
	$addressparts = explode( "\t", $addressesandstuff);
	$address = $addressparts[0];
	$addressbits = explode( ' ', $address );
	$county = trim( $addressbits[0] );
	$party = trim( $addressparts[2] );
	if( isset( $addressparts[4] ) ) $lastvoteddate1 = $addressparts[4]; else $lastvoteddate1 = '';
	if( isset( $addressparts[5] ) ) $registereddate1 = $addressparts[5]; else $registereddate1 = '';
	if( isset( $addressparts[7] ) ) $recordchangeddate1 = str_replace("\n", '', $addressparts[7] ); else $recordchangeddate1 = '';
	$registeredjulian = strtotime( $registereddate1 );
	$recordchangedjulian = strtotime( $recordchangeddate1 );
	if( ! $registeredjulian ) $registeredjulian = 0;
	if( ! $recordchangedjulian ) $recordchangedjulian = 0;
	// now walk through the dates, which have been sorted latest to earliest
	$lastafterdate = time();
	foreach( $afterdates as $afterdate )
	{
		if( $registeredjulian > $afterdate && $registeredjulian <= $lastafterdate )
		{
			$registered[ $afterdate ] = $registered[ $afterdate ] + 1;
			if( $party == 'D' ) $registereddems[ $afterdate ] = $registereddems[ $afterdate ] + 1;
			if( $party == 'R' ) $registeredreps[ $afterdate ] = $registeredreps[ $afterdate ] + 1;
		}
		elseif( $recordchangedjulian > $afterdate && $recordchangedjulian <= $lastafterdate )
		{
			$changed[ $afterdate ] = $changed[ $afterdate ] + 1;
			if( $party == 'D' ) $changeddems[ $afterdate ] = $changeddems[ $afterdate ] + 1;
			if( $party == 'R' ) $changedreps[ $afterdate ] = $changedreps[ $afterdate ] + 1;

		}
		$lastafterdate = $afterdate;
	}
	if( $registeredjulian > $saveafterdate )
	{
		$registered_bycounty[ $county ] = $registered_bycounty[ $county ] + 1;
		if( $party == 'D' ) $registereddems_bycounty[ $county ] = $registereddems_bycounty[ $county ] + 1;
		if( $party == 'R' ) $registeredreps_bycounty[ $county ] = $registeredreps_bycounty[ $county ] + 1;
	}
	elseif( $recordchangedjulian > $saveafterdate  )
	{
		$changed_bycounty[ $county ] = $changed_bycounty[ $county ] + 1;
		if( $party == 'D' ) $changeddems_bycounty[ $county ] = $changeddems_bycounty[ $county ] + 1;
		if( $party == 'R' ) $changedreps_bycounty[ $county ] = $changedreps_bycounty[ $county ] + 1;
	}
	//echo $lastvoted."\n";
	$key2 = binary_search( $voterids2, $firstkey, sizeof( $voterids2 ), $voterid1 );
	
	if( $registeredjulian > $afterdates[1] || $recordchangedjulian > $afterdates[1] )
	{
		if( $registeredjulian > $saveafterdate ) $registeredcount++;
		if( $recordchangedjulian > $saveafterdate ) $changedcount++;
		//echo $key2."\n";
		if( !$key2 )
		{
			$name = trim( $names1[ $key1 ] );
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
			$fullid = trim( $fullids1[ $key1 ] );
			{
				$parts = explode( '-', $fullid );
				$voterid = $parts[0];
				$countycode = $parts[1] * 1;
				if( strlen( $countycode ) == 1 ) $paddedcountycode = '0'.$countycode;
				else $paddedcountycode = $countycode;
			}
			$namestring = $justname."	".$dob."	".$gender;
			$address = $addressparts[0];
			$addressbits = explode( ' ', $address );
			$county = trim( $addressbits[0] );
			$realaddress = trim( str_replace( $county, '', $address ) );
			$phone = $addressparts[1];
			$party = $addressparts[2];
			if( isset( $addressparts[3] ) ) $status = $addressparts[3]; else $status = '';
			if( isset( $addressparts[4] ) ) $lastvoteddate = $addressparts[4]; else $lastvoteddate = '';
			if( isset( $addressparts[5] ) ) $registereddate = $addressparts[5]; else $registereddate = '';
			if( isset( $addressparts[7] ) ) $recordchangeddate = str_replace("\n", '', $addressparts[7] ); else $recordchangeddate = '';
			//save the info for people who registered after Sept 30
			if( $registeredjulian > $saveafterdate )
			{
				$registeredmissing++;
				echo $voterid.' registered and went missing'."\n";
				fwrite( $outputfile1, trim( $voterid )."\t".$namestring."\t".$phone."\t".$realaddress."\t".$county."\t".$party."\t".$status."\t".$registereddate."\t".$lastvoteddate."\t".$recordchangeddate."\t".$paddedcountycode.'xxxxxxx'."\n" );
				$registereddisappeared_bycounty[ $county ] = $registereddisappeared_bycounty[ $county ] + 1;
				if( $party == 'D' ) $registereddisappeareddems_bycounty[ $county ] = $registereddisappeareddems_bycounty[ $county ] + 1;
				if( $party == 'R' ) $registereddisappearedreps_bycounty[ $county ] = $registereddisappearedreps_bycounty[ $county ] + 1;
			}
			elseif( $recordchangedjulian > $saveafterdate )
			{
				$changedmissing++;
				echo $voterid.' changed registration and went missing'."\n";
				fwrite( $outputfile3, trim( $voterid )."\t".$namestring."\t".$phone."\t".$realaddress."\t".$county."\t".$party."\t".$status."\t".$registereddate."\t".$lastvoteddate."\t".$recordchangeddate."\t".$paddedcountycode.'xxxxxxx'."\n" );
				$changeddisappeared_bycounty[ $county ] = $changeddisappeared_bycounty[ $county ] + 1;
				if( $party == 'D' ) $changeddisappeareddems_bycounty[ $county ] = $changeddisappeareddems_bycounty[ $county ] + 1;
				if( $party == 'R' ) $changeddisappearedreps_bycounty[ $county ] = $changeddisappearedreps_bycounty[ $county ] + 1;
			}
			// now walk through the dates, which have been sorted latest to earliest
			$lastafterdate = time();
			foreach( $afterdates as $afterdate )
			{
				if( $registeredjulian > $afterdate && $registeredjulian <= $lastafterdate )
				{
					$registereddisappeared[ $afterdate ] = $registereddisappeared[ $afterdate ] + 1;
					if( $party == 'D' ) $registereddisappeareddems[ $afterdate ] = $registereddisappeareddems[ $afterdate ] + 1;
					if( $party == 'R' ) $registereddisappearedreps[ $afterdate ] = $registereddisappearedreps[ $afterdate ] + 1;
				}
				elseif( $recordchangedjulian > $afterdate && $recordchangedjulian <= $lastafterdate )
				{
					$changeddisappeared[ $afterdate ] = $changeddisappeared[ $afterdate ] + 1;
					if( $party == 'D' ) $changeddisappeareddems[ $afterdate ] = $changeddisappeareddems[ $afterdate ] + 1;
					if( $party == 'R' ) $changeddisappearedreps[ $afterdate ] = $changeddisappearedreps[ $afterdate ] + 1;
				}
				$lastafterdate = $afterdate;
			}
		}
		else
		{
			//now look for voters whose "last voted" date is NOT 11/08/2016
			$addressesandstuff2 = $addressesetc2[ $key2 ];
			$addressparts2 = explode( "\t", $addressesandstuff2 );
			$lastvoted2 = trim( $addressparts2[4] );
			if( $lastvoted2 != '11/08/2016' )
			{
				$names[1] = trim( $names1[ $key1 ] );
				$names[2] = trim( $names2[ $key2 ] );
				$fullids[1] = trim( $fullids1[ $key1 ] );
				$fullids[2] = trim( $fullids2[ $key2 ] );
				$addressetcs[1] = trim( $addressesetc1[ $key1 ] );
				$addressetcs[2] = trim( $addressesetc2[ $key2 ] );
				// get county names from full ids
				foreach( $fullids as $key=>$fullid )
				{
					$parts = explode( '-', $fullid );
					$voterid = $parts[0];
					$countycode = $parts[1] * 1;
					if( strlen( $countycode ) == 1 ) $paddedcountycode = '0'.$countycode;
					else $paddedcountycode = $countycode;
					$countynames[ $key ] = $counties[ $countycode ];
				}
				//$address."\t".$phone."\t".$party."\t".$status."\t".$lastvotedate.
				foreach( $addressetcs as $key=>$addressetc)
				{
					$parts = explode( "\t", $addressetc );
					$addresses[ $key ] = $parts[0];
					$phones[ $key ] = $parts[1];
					$parties[ $key ] = $parts[2];
					if( isset( $parts[3] ) ) $statuses[ $key ] = $parts[3]; else $statuses[ $key ] = '';
					if( isset( $parts[4] ) ) $lastvoteddates[ $key ] = $parts[4]; else $lastvoteddates[ $key ] = '';
					if( isset( $parts[5] ) ) $registereddates[ $key ] = $parts[5]; else $registereddates[ $key ] = '';
					if( isset( $parts[7] ) ) $recordchangeddates[ $key ] = $parts[7]; else $recordchangeddates[ $key ] = '';
				}
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
				if( $registeredjulian > $saveafterdate )
				{
					$registerednovote++;
					echo $voterid.' registered and didn\t vote'."\n";
					fwrite( $outputfile2, trim( $voterid )."\t".$justnames[1]."\t".$justnames[2]."\t".$dobs[1]."\t".$dobs[2]."\t".$genders[1]."\t".$genders[2]."\t".$phones[1]."\t".$phones[2]."\t".$addresses[1]."\t".$countynames[1]."\t".$addresses[2]."\t".$countynames[2]."\t".$parties[1]."\t".$parties[2]."\t".$statuses[1]."\t".$statuses[2]."\t".$registereddates[1]."\t".$registereddates[2]."\t".$lastvoteddates[1]."\t".$lastvoteddates[2]."\t".$recordchangeddates[1]."\t".$recordchangeddates[2]."\t".$paddedcountycode.'xxxxxxx'."\n" );
					$registereddidntvote_bycounty[ $county ] = $registereddidntvote_bycounty[ $county ] + 1;
					if( $party == 'D' ) $registereddidntvotedems_bycounty[ $county ] = $registereddidntvotedems_bycounty[ $county ] + 1;
					if( $party == 'R' ) $registereddidntvotereps_bycounty[ $county ] = $registereddidntvotereps_bycounty[ $county ] + 1;
				}
				elseif( $recordchangedjulian > $saveafterdate )
				{
					$changednovote++;
					echo $voterid.' changed registration and didn\t vote'."\n";
					fwrite( $outputfile4, trim( $voterid )."\t".$justnames[1]."\t".$justnames[2]."\t".$dobs[1]."\t".$dobs[2]."\t".$genders[1]."\t".$genders[2]."\t".$phones[1]."\t".$phones[2]."\t".$addresses[1]."\t".$countynames[1]."\t".$addresses[2]."\t".$countynames[2]."\t".$parties[1]."\t".$parties[2]."\t".$statuses[1]."\t".$statuses[2]."\t".$registereddates[1]."\t".$registereddates[2]."\t".$lastvoteddates[1]."\t".$lastvoteddates[2]."\t".$recordchangeddates[1]."\t".$recordchangeddates[2]."\t".$paddedcountycode.'xxxxxxx'."\n" );
					$changeddidntvote_bycounty[ $county ] = $changeddidntvote_bycounty[ $county ] + 1;
					if( $party == 'D' ) $changeddidntvotedems_bycounty[ $county ] = $changeddidntvotedems_bycounty[ $county ] + 1;
					if( $party == 'R' ) $changeddidntvotereps_bycounty[ $county ] = $changeddidntvotereps_bycounty[ $county ] + 1;
				}
				// now walk through the dates, which have been sorted latest to earliest
				$lastafterdate = time();
				foreach( $afterdates as $afterdate )
				{
					if( $registeredjulian > $afterdate && $registeredjulian <= $lastafterdate )
					{
						$registereddidntvote[ $afterdate ] = $registereddidntvote[ $afterdate ] + 1;
						if( $party == 'D' ) $registereddidntvotedems[ $afterdate ] = $registereddidntvotedems[ $afterdate ] + 1;
						if( $party == 'R' ) $registereddidntvotereps[ $afterdate ] = $registereddidntvotereps[ $afterdate ] + 1;
					}
					elseif( $recordchangedjulian > $afterdate && $recordchangedjulian <= $lastafterdate )
					{
						$changeddidntvote[ $afterdate ] = $changeddidntvote[ $afterdate ] + 1;
						if( $party == 'D' ) $changeddidntvotedems[ $afterdate ] = $changeddidntvotedems[ $afterdate ] + 1;
						if( $party == 'R' ) $changeddidntvotereps[ $afterdate ] = $changeddidntvotereps[ $afterdate ] + 1;
					}
					$lastafterdate = $afterdate;
				}
			}
		}
	}
}
echo 'registered: '.$registeredcount."\n";
echo 'registered then went missing: '.$registeredmissing."\n";
echo 'registered then didn\'t vote: '.$registerednovote."\n\n";

echo 'changed: '.$changedcount."\n";
echo 'changed then went missing: '.$changedmissing."\n";
echo 'changed then didn\'t vote: '.$changednovote."\n";

foreach( $afterdates as $afterdate )
{
	$date2 = $afterdate + 24*60*60*14;
	$datestring1 = date('m/d/Y', $afterdate );
	$datestring2 = date('m/d/Y', $date2 );
	
	$registeredothers = $registered[ $afterdate ] - $registereddems[ $afterdate ] - $registeredreps[ $afterdate ];
	$registereddidntvoteothers = $registereddidntvote[ $afterdate ] - $registereddidntvotedems[ $afterdate ] - $registereddidntvotereps[ $afterdate ];
	$registereddisappearedothers = $registereddisappeared[ $afterdate ] - $registereddisappeareddems[ $afterdate ] - $registereddisappearedreps[ $afterdate ];
	$changedothers = $changed[ $afterdate ] - $changeddems[ $afterdate ] - $changedreps[ $afterdate ];
	$changeddidntvoteothers = $changeddidntvote[ $afterdate ] - $changeddidntvotedems[ $afterdate ] - $changeddidntvotereps[ $afterdate ];
	$changeddisappearedothers = $changeddisappeared[ $afterdate ] - $changeddisappeareddems[ $afterdate ] - $changeddisappearedreps[ $afterdate ];
	
	fwrite( $outputfile5, $datestring1."	".$datestring2."	".$registered[ $afterdate ]."	".$registereddems[ $afterdate ]."	".$registeredreps[ $afterdate ]."	".$registeredothers."	".$changed[ $afterdate ]."	".$changeddems[ $afterdate ]."	".$changedreps[ $afterdate ]."	".$changedothers."	".$registereddidntvote[ $afterdate ]."	".$registereddidntvotedems[ $afterdate ]."	".$registereddidntvotereps[ $afterdate ]."	".$registereddidntvoteothers."	".$changeddidntvote[ $afterdate ]."	".$changeddidntvotedems[ $afterdate ]."	".$changeddidntvotereps[ $afterdate ]."	".$changeddidntvoteothers."	".$registereddisappeared[ $afterdate ]."	".$registereddisappeareddems[ $afterdate ]."	".$registereddisappearedreps[ $afterdate ]."	".$registereddisappearedothers."	".$changeddisappeared[ $afterdate ]."	".$changeddisappeareddems[ $afterdate ]."	".$changeddisappearedreps[ $afterdate ]."	".$changeddisappearedothers."\n" );
}
foreach( $counties as $county )
{	
	$registeredothers = $registered_bycounty[ $county ] - $registereddems_bycounty[ $county ] - $registeredreps_bycounty[ $county ];
	$registereddidntvoteothers = $registereddidntvote_bycounty[ $county ] - $registereddidntvotedems_bycounty[ $county ] - $registereddidntvotereps_bycounty[ $county ];
	$registereddisappearedothers = $registereddisappeared_bycounty[ $county ] - $registereddisappeareddems_bycounty[ $county ] - $registereddisappearedreps_bycounty[ $county ];
	$changedothers = $changed_bycounty[ $county ] - $changeddems_bycounty[ $county ] - $changedreps_bycounty[ $county ];
	$changeddidntvoteothers = $changeddidntvote_bycounty[ $county ] - $changeddidntvotedems_bycounty[ $county ] - $changeddidntvotereps_bycounty[ $county ];
	$changeddisappearedothers = $changeddisappeared_bycounty[ $county ] - $changeddisappeareddems_bycounty[ $county ] - $changeddisappearedreps_bycounty[ $county ];
	
	fwrite( $outputfile6, $county."	".$registered_bycounty[ $county ]."	".$registereddems_bycounty[ $county ]."	".$registeredreps_bycounty[ $county ]."	".$registeredothers."	".$changed_bycounty[ $county ]."	".$changeddems_bycounty[ $county ]."	".$changedreps_bycounty[ $county ]."	".$changedothers."	".$registereddidntvote_bycounty[ $county ]."	".$registereddidntvotedems_bycounty[ $county ]."	".$registereddidntvotereps_bycounty[ $county ]."	".$registereddidntvoteothers."	".$changeddidntvote_bycounty[ $county ]."	".$changeddidntvotedems_bycounty[ $county ]."	".$changeddidntvotereps_bycounty[ $county ]."	".$changeddidntvoteothers."	".$registereddisappeared_bycounty[ $county ]."	".$registereddisappeareddems_bycounty[ $county ]."	".$registereddisappearedreps_bycounty[ $county ]."	".$registereddisappearedothers."	".$changeddisappeared_bycounty[ $county ]."	".$changeddisappeareddems_bycounty[ $county ]."	".$changeddisappearedreps_bycounty[ $county ]."	".$changeddisappearedothers."\n" );
}

fclose( $outputfile1 );
fclose( $outputfile2 );
fclose( $outputfile3 );
fclose( $outputfile4 );
fclose( $outputfile5 );
fclose( $outputfile6 );
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