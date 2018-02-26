<?php
/*
Sorts zombie records by address, writes relevant details to output file

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
151 County
152 Country
153 Earlier Occupant Count
154 Later Occupant Count
155 Current Occupant Count

Syntax:

Example:
php getzombiedetails.php suspectedzombierecords_2016-08-15_2017-02-27_1.txt

*/

$outputfilename = str_replace( 'suspectedzombierecords', 'zombiedetails', $argv[1] );

$addresses = array();
$counties = array();

$datalines = file( $argv[1] );
foreach( $datalines as $dataline )
{
	$lineparts = explode( "\t", $dataline );
	$num =  trim( str_replace( '"', '', $lineparts[12] ) );
	$street =  trim( str_replace( '"', '', $lineparts[14] ) );
	$apt =  trim( str_replace( '"', '', $lineparts[15] ) );
	$city =  trim( str_replace( '"', '', $lineparts[17] ) );
	$countyname =  trim( str_replace( '"', '', $lineparts[151] ) );
	$address = $countyname.' '.$num.' '.$street.' '.$city;
	$addresses[] = $address;
	$counties[] = $countyname;
}
array_multisort( $addresses, $counties, $datalines );

$previousaddress = '';
foreach( $datalines as $key=>$dataline )
{
	$countyfilename = $counties[ $key ].'_'.$outputfilename;
	$countyfile = fopen( $countyfilename, 'a' );
	$lineparts = explode( "\t", $dataline );
	$voterid =  str_replace( '"', '', $lineparts[0] );
	$firstname =  str_replace( '"', '', $lineparts[3] );
	$middlename =  str_replace( '"', '', $lineparts[4] );
	$lastname =  str_replace( '"', '', $lineparts[2] );
	$birthdate =  str_replace( '"', '', $lineparts[7] );
	$gender =  str_replace( '"', '', $lineparts[6] );
	$partycode =  str_replace( '"', '', $lineparts[11] );
	$num =  trim( str_replace( '"', '', $lineparts[12] ) );
	$street =  trim( str_replace( '"', '', $lineparts[14] ) );
	$apt =  trim( str_replace( '"', '', $lineparts[15] ) );
	$city =  trim( str_replace( '"', '', $lineparts[17] ) );
	$phone =  trim( str_replace( '"', '', $lineparts[150] ) );
	$earliercount =  trim( str_replace( '"', '', $lineparts[153] ) );
	$latercount =  trim( str_replace( '"', '', $lineparts[154] ) );
	$currentcount =  trim( str_replace( '"', '', $lineparts[155] ) );
	
	$address = $num.' '.$street.' '.$apt.' '.$city;
	$outputline = str_replace( "\n", '',$firstname.' '.$middlename.' '.$lastname."\t".$birthdate."\t".$address."\t".$phone."\t".$gender."\t".$partycode."\t".$earliercount."\t".$latercount."\t".$currentcount."\t".$voterid )."\n";
	
	if( $previousaddress != $address)
	{
		fwrite( $countyfile, "\n" );
	}
	fwrite( $countyfile, $outputline );
	
	$previousaddress = $address;
	fclose( $countyfile );
}


?>