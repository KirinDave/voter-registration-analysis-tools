<?php
/*
Looks at lists of all voterids and all full names (first name + last name + DOB + Gender )for two data sets

Runs through one list of voterids and finds all matches on second list. Compares the name that has the same index as the first id with the name that has the same index as the second id.

If the two names don't match, parse names to tab separate name, dob and gender. Write both names to same line of output file.

Syntax:
php getchangednameswithaddresses.php firstdate seconddate

Example:
php getchangednameswithaddresses.php 2016-04-04 2016-11-28 

Output: 
changedidentities/changednames_firstdate_seconddate.txt
changedidentities/changeddobs_firstdate_seconddate.txt
changedidentities/changedgenders_firstdate_seconddate.txt
changedidentities/changedpartysummary_firstdate_seconddate.txt
changedidentities/changedaddressummary_firstdate_seconddate.txt
changedidentities/lastvoteddiscrepancysummary_firstdate_seconddate.txt
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


$nameoutputfilename = 'changedidentities/changednames_'.$argv[1].'_'.$argv[2].'.txt';
$nameoutputfile = fopen( $nameoutputfilename, 'w' );
fwrite( $nameoutputfile, "ID	Previous Name	Name	DOB	Gender	Phone	Previous Address	Previous County	Address	County	Previous Party	Party	Previous Status	Status	Previous Last Voted	Last Voted	Grep Code\n" );

$doboutputfilename = 'changedidentities/changeddobs_'.$argv[1].'_'.$argv[2].'.txt';
$doboutputfile = fopen( $doboutputfilename, 'w' );
fwrite( $doboutputfile, "ID	Name	Previous DOB	DOB	Gender	Phone	Previous Address	Previous County	Address	County	Previous Party	Party	Previous Status	Previous Status	Previous Last Voted	Last Voted	Grep Code\n"  );

$genderoutputfilename = 'changedidentities/changedgenders_'.$argv[1].'_'.$argv[2].'.txt';
$genderoutputfile = fopen( $genderoutputfilename, 'w' );
fwrite( $genderoutputfile, "ID	Name	DOB	Previous Gender	Gender	Phone	Previous Address	Previous County	Address	County	Previous Party	Party	Previous Status	Status	Previous Last Voted	Last Voted	Grep Code\n"  );

$lastvotediscrepancyoutputfilename = 'changedidentities/lastvoteddiscrepancies_'.$argv[1].'_'.$argv[2].'.txt';
$lastvotediscrepancyoutputfile = fopen( $lastvotediscrepancyoutputfilename, 'w' );
fwrite( $lastvotediscrepancyoutputfile, "ID	Previous Name	Name	Previous DOB	DOB	Previous Gender	Gender	Phone	Previous Address	Previous County	Address	County	Previous Party	Party	Previous Status	Status	Previous Last Voted	Last Voted	Grep Code\n"  );

$partysummaryfilename = 'changedidentities/changedpartysummary_'.$argv[1].'_'.$argv[2].'.txt';
$partysummaryfile = fopen( $partysummaryfilename, 'w' );
fwrite( $partysummaryfile, "County	Changed Party	Changed ID	Changed ID and Party	Changed Name	Changed Name and Party	Changed DOB	Changed DOB and Party	Changed Gender	Changed Gender and Party\n"  );

$addresssummaryfilename = 'changedidentities/changedaddresssummary_'.$argv[1].'_'.$argv[2].'.txt';
$addresssummaryfile = fopen( $addresssummaryfilename, 'w' );
fwrite( $addresssummaryfile, "County	Changed Address	Changed ID	Changed ID and Address	Changed Name	Changed Name and Address	Changed DOB	Changed DOB and Address	Changed Gender	Changed Gender and Address\n"  );

$lastvoteddiscrepancysummaryfilename = 'changedidentities/lastvoteddiscrepancysummary_'.$argv[1].'_'.$argv[2].'.txt';
$lastvoteddiscrepancysummaryfile = fopen( $lastvoteddiscrepancysummaryfilename, 'w' );
fwrite( $lastvoteddiscrepancysummaryfile, "County	Total Discrepancies	Changed ID	Changed ID And Discrepancy	Changed Name	Changed Name and Discrepancy	Changed DOB	Changed DOB and Discrepancy	Changed Gender	Changed Gender and Discrepancy\n"  );

$voterids1 = file( $argv[1].'/voterids.txt' );
$fullids1 = file( $argv[1].'/fullids.txt' );
$addressesetc1 = file( $argv[1].'/addressesetc.txt' );
$names1 = file( $argv[1].'/firstnamelastnamedobs.txt' );
echo 'sorting arrays from '.$argv[1]."\n";
array_multisort( $voterids1, $fullids1, $addressesetc1, $names1 );

$voterids2 = file( $argv[2].'/voterids.txt' );
$fullids2 = file( $argv[2].'/fullids.txt' );
$addressesetc2 = file( $argv[2].'/addressesetc.txt' );
$names2 = file( $argv[2].'/firstnamelastnamedobs.txt' );
echo 'sorting arrays from '.$argv[2]."\n";
array_multisort( $voterids2, $fullids2, $addressesetc2, $names2 );


$changedparty = array();
$changedaddress = array();
$changedlastvotedate = array();

$changedid = array();
$changedname = array();
$changeddob = array();
$changedgender = array();

$changedidandparty = array();
$changednameandparty = array();
$changeddobandparty = array();
$changedgenderandparty = array();

$changedidandaddress = array();
$changednameandaddress = array();
$changeddobandaddress = array();
$changedgenderandaddress = array();

$changedidandlastvotedate = array();
$changednameandlastvotedate = array();
$changeddobandlastvotedate = array();
$changedgenderandlastvotedate = array();

$key2 = 0;
foreach( $voterids1 as $key1=>$voterid1 )
{
	$firstkey = $key2;
	$names = array();
	$justnames = array();
	$dobs = array();
	$genders = array();
	$fullids = array();
	$countynames = array();
	$addressetc = array();
	$addresses = array();
	$phones = array();
	$parties = array();
	$statuses = array();
	$lastvoteddates = array();
	$key2 = binary_search( $voterids2, $firstkey, sizeof( $voterids2 ), $voterid1 );
	//$key2 = array_search( $voterid1, $voterids2 );
	if( $key2 )
	{
		$names[1] = trim( $names1[ $key1 ] );
		$names[2] = trim( $names2[ $key2 ] );
		$fullids[1] = trim( $fullids1[ $key1 ] );
		$fullids[2] = trim( $fullids2[ $key2 ] );
		$addressetc[1] = trim( $addressesetc1[ $key1 ] );
		$addressetc[2] = trim( $addressesetc2[ $key2 ] );
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
		foreach( $addressetc as $key=>$addressetc)
		{
			$parts = explode( "\t", $addressetc );
			$addresses[ $key ] = $parts[0];
			$phones[ $key ] = $parts[1];
			$parties[ $key ] = $parts[2];
			if( isset( $parts[3] ) ) $statuses[ $key ] = $parts[3]; else $statuses[ $key ] = '';
			if( isset( $parts[4] ) ) $lastvoteddates[ $key ] = $parts[4]; else $lastvoteddates[ $key ] = '';
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
		// look at changed party
		if( $parties[1] != $parties[2] )
		{
			if( ! isset( $changedparty[ $countycode ] ) ) $changedparty[ $countycode ] = 1; else $changedparty[ $countycode ] = $changedparty[ $countycode ] + 1;
		}
		// look at changed address
		if( $addresses[1] != $addresses[2] )
		{
			if( ! isset( $changedaddress[ $countycode ] ) ) $changedaddress[ $countycode ] = 1; else $changedaddress[ $countycode ] = $changedaddress[ $countycode ] + 1;
		}
		// look at changed "last voted" date. 
		if( ( ( $argv[1] == '2016-11-28' || $argv[1] == '2017-02-27' ) && ( $lastvoteddates[ 2 ] != '03/21/2017' && $lastvoteddates[ 2 ] != '05/16/2017' && $lastvoteddates[ 1 ] != $lastvoteddates[ 2 ] ) ) || ( $argv[2] == '2016-11-28' && ( $lastvoteddates[ 2 ] != '04/26/2016' && $lastvoteddates[ 2 ] != '07/12/2016' && $lastvoteddates[ 2 ] != '11/08/2016' && $lastvoteddates[ 1 ] != $lastvoteddates[ 2 ] ) ) )
		{
			if( ! isset( $changedlastvotedate[ $countycode ] ) ) $changedlastvotedate[ $countycode ] = 1; else $changedlastvotedate[ $countycode ] = $changedlastvotedate[ $countycode ] + 1;
			
			fwrite( $lastvotediscrepancyoutputfile, trim( $fullids[1] )."\t".$justnames[1]."\t".$justnames[2]."\t".$dobs[1]."\t".$dobs[2]."\t".$genders[1]."\t".$genders[2]."\t".$phones[2]."\t".$addresses[1]."\t".$countynames[1]."\t".$addresses[2]."\t".$countynames[2]."\t".$parties[1]."\t".$parties[2]."\t".$statuses[1]."\t".$statuses[2]."\t".$lastvoteddates[1]."\t".$lastvoteddates[2]."\t".$paddedcountycode.'xxxxxxx'."\n" );
		}
		if( $names[1] != $names[2]  )
		{
			// look at changed identity
			if( ! isset( $changedid[ $countycode ] ) ) $changedid[ $countycode ] = 1; else $changedid[ $countycode ] = $changedid[ $countycode ] + 1;

			// look at changed party
			if( $parties[1] != $parties[2] )
			{
				if( ! isset( $changedidandparty[ $countycode ] ) ) $changedidandparty[ $countycode ] = 1; else $changedidandparty[ $countycode ] = $changedidandparty[ $countycode ] + 1;
			}
			// look at changed address
			if( $addresses[1] != $addresses[2] )
			{
				if( ! isset( $changedidandaddress[ $countycode ] ) ) $changedidandaddress[ $countycode ] = 1; else $changedidandaddress[ $countycode ] = $changedidandaddress[ $countycode ] + 1;
			}
			// look at changed "last voted" date. 
			if( ( ( $argv[1] == '2016-11-28' || $argv[1] == '2017-02-27' ) && ( $lastvoteddates[ 2 ] != '03/21/2017' && $lastvoteddates[ 2 ] != '05/16/2017' && $lastvoteddates[ 1 ] != $lastvoteddates[ 2 ] ) ) || ( $argv[2] == '2016-11-28' && ( $lastvoteddates[ 2 ] != '04/26/2016' && $lastvoteddates[ 2 ] != '07/12/2016' && $lastvoteddates[ 2 ] != '11/08/2016' && $lastvoteddates[ 1 ] != $lastvoteddates[ 2 ] ) ) )
			{
				if( ! isset( $changedidandlastvotedate[ $countycode ] ) ) $changedidandlastvotedate[ $countycode ] = 1; else $changedidandlastvotedate[ $countycode ] = $changedidandlastvotedate[ $countycode ] + 1;
			}
			//write data to output files and summary files depending on what was changed
			if( $justnames[1] != $justnames[2] )
				{
				// look at changed identity
				if( ! isset( $changedname[ $countycode ] ) ) $changedname[ $countycode ] = 1; else $changedname[ $countycode ] = $changedname[ $countycode ] + 1;

				// look at changed party
				if( $parties[1] != $parties[2] )
				{
					if( ! isset( $changednameandparty[ $countycode ] ) ) $changednameandparty[ $countycode ] = 1; else $changednameandparty[ $countycode ] = $changednameandparty[ $countycode ] + 1;
				}
				// look at changed address
				if( $addresses[1] != $addresses[2] )
				{
					if( ! isset( $changednameandaddress[ $countycode ] ) ) $changednameandaddress[ $countycode ] = 1; else $changednameandaddress[ $countycode ] = $changednameandaddress[ $countycode ] + 1;
				}
				// look at changed "last voted" date. 
				if( ( ( $argv[1] == '2016-11-28' || $argv[1] == '2017-02-27' ) && ( $lastvoteddates[ 2 ] != '03/21/2017' && $lastvoteddates[ 2 ] != '05/16/2017' && $lastvoteddates[ 1 ] != $lastvoteddates[ 2 ] ) ) || ( $argv[2] == '2016-11-28' && ( $lastvoteddates[ 2 ] != '04/26/2016' && $lastvoteddates[ 2 ] != '07/12/2016' && $lastvoteddates[ 2 ] != '11/08/2016' && $lastvoteddates[ 1 ] != $lastvoteddates[ 2 ] ) ) )
				{
					if( ! isset( $changednameandlastvotedate[ $countycode ] ) ) $changednameandlastvotedate[ $countycode ] = 1; else $changednameandlastvotedate[ $countycode ] = $changednameandlastvotedate[ $countycode ] + 1;
				}
				//write
				echo 'NAME CHANGE '.trim( $fullids[1] )."\t".$justnames[2]."\t".$justnames[1]."\t".$dobs[2]."\t".$genders[2]."\n";
				
				fwrite( $nameoutputfile, trim( $fullids[1] )."\t".$justnames[1]."\t".$justnames[2]."\t".$dobs[2]."\t".$genders[2]."\t".$phones[2]."\t".$addresses[1]."\t".$countynames[1]."\t".$addresses[2]."\t".$countynames[2]."\t".$parties[1]."\t".$parties[2]."\t".$statuses[1]."\t".$statuses[2]."\t".$lastvoteddates[1]."\t".$lastvoteddates[2]."\t".$paddedcountycode.'xxxxxxx'."\n" );
			}
			if( $genders[1] != $genders[2] )
			{
				// look at changed identity
				if( ! isset( $changedgender[ $countycode ] ) ) $changedgender[ $countycode ] = 1; else $changedgender[ $countycode ] = $changedgender[ $countycode ] + 1;

				// look at changed party
				if( $parties[1] != $parties[2] )
				{
					if( ! isset( $changedgenderandparty[ $countycode ] ) ) $changedgenderandparty[ $countycode ] = 1; else $changedgenderandparty[ $countycode ] = $changedgenderandparty[ $countycode ] + 1;
				}
				// look at changed address
				if( $addresses[1] != $addresses[2] )
				{
					if( ! isset( $changedgenderandaddress[ $countycode ] ) ) $changedgenderandaddress[ $countycode ] = 1; else $changedgenderandaddress[ $countycode ] = $changedgenderandaddress[ $countycode ] + 1;
				}
				// look at changed "last voted" date. 
				if( ( ( $argv[1] == '2016-11-28' || $argv[1] == '2017-02-27' ) && ( $lastvoteddates[ 2 ] != '03/21/2017' && $lastvoteddates[ 2 ] != '05/16/2017' && $lastvoteddates[ 1 ] != $lastvoteddates[ 2 ] ) ) || ( $argv[2] == '2016-11-28' && ( $lastvoteddates[ 2 ] != '04/26/2016' && $lastvoteddates[ 2 ] != '07/12/2016' && $lastvoteddates[ 2 ] != '11/08/2016' && $lastvoteddates[ 1 ] != $lastvoteddates[ 2 ] ) ) )
				{
					if( ! isset( $changedgenderandlastvotedate[ $countycode ] ) ) $changedgenderandlastvotedate[ $countycode ] = 1; else $changedgenderandlastvotedate[ $countycode ] = $changedgenderandlastvotedate[ $countycode ] + 1;
				}
				//write
				echo 'GENDER CHANGE '.trim( $fullids[1] )."\t".$justnames[2]."\t".$dobs[2]."\t".$genders[2]."\t".$genders[1]."\n";
				
				fwrite( $genderoutputfile, trim( $fullids[1] )."\t".$justnames[2]."\t".$dobs[2]."\t".$genders[1]."\t".$genders[2]."\t".$phones[2]."\t".$addresses[1]."\t".$countynames[1]."\t".$addresses[2]."\t".$countynames[2]."\t".$parties[1]."\t".$parties[2]."\t".$statuses[1]."\t".$statuses[2]."\t".$lastvoteddates[1]."\t".$lastvoteddates[2]."\t".$paddedcountycode.'xxxxxxx'."\n" );
			}
			if( $dobs[1] != $dobs[2] )
			{
				// look at changed identity
				if( ! isset( $changeddob[ $countycode ] ) ) $changeddob[ $countycode ] = 1; else $changeddob[ $countycode ] = $changeddob[ $countycode ] + 1;

				// look at changed party
				if( $parties[1] != $parties[2] )
				{
					if( ! isset( $changeddobandparty[ $countycode ] ) ) $changeddobandparty[ $countycode ] = 1; else $changeddobandparty[ $countycode ] = $changeddobandparty[ $countycode ] + 1;
				}
				// look at changed address
				if( $addresses[1] != $addresses[2] )
				{
					if( ! isset( $changeddobandaddress[ $countycode ] ) ) $changeddobandaddress[ $countycode ] = 1; else $changeddobandaddress[ $countycode ] = $changeddobandaddress[ $countycode ] + 1;
				}
				// look at changed "last voted" date. 
				if( ( ( $argv[1] == '2016-11-28' || $argv[1] == '2017-02-27' ) && ( $lastvoteddates[ 2 ] != '03/21/2017' && $lastvoteddates[ 2 ] != '05/16/2017' && $lastvoteddates[ 1 ] != $lastvoteddates[ 2 ] ) ) || ( $argv[2] == '2016-11-28' && ( $lastvoteddates[ 2 ] != '04/26/2016' && $lastvoteddates[ 2 ] != '07/12/2016' && $lastvoteddates[ 2 ] != '11/08/2016' && $lastvoteddates[ 1 ] != $lastvoteddates[ 2 ] ) ) )
				{
					if( ! isset( $changeddobandlastvotedate[ $countycode ] ) ) $changeddobandlastvotedate[ $countycode ] = 1; else $changeddobandlastvotedate[ $countycode ] = $changeddobandlastvotedate[ $countycode ] + 1;
				}
				//write
				echo 'DOB CHANGE '.trim( $fullids[1] )."\t".$justnames[2]."\t".$dobs[1]."\t".$dobs[2]."\t".$genders[2]."\n";
				
				fwrite( $doboutputfile, trim( $fullids[1] )."\t".$justnames[2]."\t".$dobs[1]."\t".$dobs[2]."\t".$genders[2]."\t".$phones[2]."\t".$addresses[1]."\t".$countynames[1]."\t".$addresses[2]."\t".$countynames[2]."\t".$parties[1]."\t".$parties[2]."\t".$statuses[1]."\t".$statuses[2]."\t".$lastvoteddates[1]."\t".$lastvoteddates[2]."\t".$paddedcountycode.'xxxxxxx'."\n" );
			}
			
		}
	}
}

foreach( $counties as $countycode=>$county )
{
	if( ! isset( $changedparty[ $countycode ] ) ) $changedparty[ $countycode ] = 0;
	if( ! isset( $changedaddress[ $countycode ] ) ) $changedaddress[ $countycode ] = 0;

	if( ! isset( $changedlastvotedate[ $countycode ] ) ) $changedlastvotedate[ $countycode ] = 0;

	if( ! isset( $changedid[ $countycode ] ) ) $changedid[ $countycode ] = 0;
	if( ! isset( $changedname[ $countycode ] ) ) $changedname[ $countycode ] = 0;
	if( ! isset( $changeddob[ $countycode ] ) ) $changeddob[ $countycode ] = 0;
	if( ! isset( $changedgender[ $countycode ] ) ) $changedgender[ $countycode ] = 0;

	if( ! isset( $changedidandparty[ $countycode ] ) ) $changedidandparty[ $countycode ] = 0;
	if( ! isset( $changednameandparty[ $countycode ] ) ) $changednameandparty[ $countycode ] = 0;
	if( ! isset( $changeddobandparty[ $countycode ] ) ) $changeddobandparty[ $countycode ] = 0;
	if( ! isset( $changedgenderandparty[ $countycode ] ) ) $changedgenderandparty[ $countycode ] = 0;

	if( ! isset( $changedidandaddress[ $countycode ] ) ) $changedidandaddress[ $countycode ] = 0;
	if( ! isset( $changednameandaddress[ $countycode ] ) ) $changednameandaddress[ $countycode ] = 0;
	if( ! isset( $changeddobandaddress[ $countycode ] ) ) $changeddobandaddress[ $countycode ] = 0;
	if( ! isset( $changedgenderandaddress[ $countycode ] ) ) $changedgenderandaddress[ $countycode ] = 0;

	if( ! isset( $changedidandlastvotedate[ $countycode ] ) ) $changedidandlastvotedate[ $countycode ] = 0;
	if( ! isset( $changednameandlastvotedate[ $countycode ] ) ) $changednameandlastvotedate[ $countycode ] = 0;
	if( ! isset( $changeddobandlastvotedate[ $countycode ] ) ) $changeddobandlastvotedate[ $countycode ] = 0;
	if( ! isset( $changedgenderandlastvotedate[ $countycode ] ) ) $changedgenderandlastvotedate[ $countycode ] = 0;
	
	fwrite( $partysummaryfile, $county."	".$changedparty[ $countycode ]."	".$changedid[ $countycode ]."	".$changedidandparty[ $countycode ]."	".$changedname[ $countycode ]."	".$changednameandparty[ $countycode ]."	".$changeddob[ $countycode ]."	".$changeddobandparty[ $countycode ]."	".$changedgender[ $countycode ]."	".$changedgenderandparty[ $countycode ]."\n"  );
	
	fwrite( $addresssummaryfile, $county."	".$changedaddress[ $countycode ]."	".$changedid[ $countycode ]."	".$changedidandaddress[ $countycode ]."	".$changedname[ $countycode ]."	".$changednameandaddress[ $countycode ]."	".$changeddob[ $countycode ]."	".$changeddobandaddress[ $countycode ]."	".$changedgender[ $countycode ]."	".$changedgenderandaddress[ $countycode ]."\n"  );

	fwrite( $lastvoteddiscrepancysummaryfile, $county."	".$changedlastvotedate[ $countycode ]."	".$changedid[ $countycode ]."	".$changedidandlastvotedate[ $countycode ]."	".$changedname[ $countycode ]."	".$changednameandlastvotedate[ $countycode ]."	".$changeddob[ $countycode ]."	".$changeddobandlastvotedate[ $countycode ]."	".	$changedgender[ $countycode ]."	".$changedgenderandlastvotedate[ $countycode ]."\n"  );
}


fclose( $nameoutputfile );
fclose( $doboutputfile );
fclose( $genderoutputfile );
fclose( $partysummaryfile );
fclose( $addresssummaryfile );
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