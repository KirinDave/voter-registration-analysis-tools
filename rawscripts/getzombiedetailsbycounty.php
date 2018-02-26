<?php
/*
Sorts zombie records by address, writes relevant details to output files

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
php getzombiedetailsbycounty.php suspectedzombierecords_2016-08-15_2017-02-27.txt

*/
$filenameparts = explode( '_', $argv[1] );
$previousdate = $filenameparts[1];
$nextdate = str_replace( '.txt', '', $filenameparts[2] );
$earliervoterids = file( '../../'.$previousdate.'/voterids.txt' );
$earlieraddresses = file( '../../'.$previousdate.'/addressesetc.txt' );
$latervoterids = file( '../../'.$nextdate.'/voterids.txt' );
$lateraddresses = file( '../../'.$nextdate.'/addressesetc.txt' );

array_multisort( $earliervoterids, $earlieraddresses );
array_multisort( $latervoterids, $lateraddresses );

$outputfilename = str_replace( 'txt', 'csv', str_replace( 'zombierecords', 'zombiedetails', $argv[1] ) );

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
	$address = $countyname.' '.$num.' '.$street.' '.$apt.' '.$city;
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
	$status =  trim( str_replace( '"', '', $lineparts[9] ) );
	$partycode =  str_replace( '"', '', $lineparts[11] );
	$num =  trim( str_replace( '"', '', $lineparts[12] ) );
	$street =  trim( str_replace( '"', '', $lineparts[14] ) );
	$apt =  trim( str_replace( '"', '', $lineparts[15] ) );
	$city =  trim( str_replace( '"', '', $lineparts[17] ) );
	$lastvoteddate =  trim( str_replace( '"', '', $lineparts[25] ) );
	$phone =  trim( str_replace( '"', '', $lineparts[150] ) );
	$earliercount =  trim( str_replace( '"', '', $lineparts[153] ) );
	$latercount =  trim( str_replace( '"', '', $lineparts[154] ) );
	$currentcount =  trim( str_replace( '"', '', $lineparts[155] ) );
	$address = $num.' '.$street.' '.$apt.' '.$city;
	//check voterid against voterids in both earlier and later sets
	// if found copy in earlier address
	$earlieraddress = $address;
	$earlierkey = binary_search( $earliervoterids, $lastearlierkey, sizeof( $earliervoterids ), $voterid."\n" );
	$lastearlierkey = $earlierkey;
	if( $earlierkey )
	{
		$addressparts = explode( "\t", $earlieraddresses[ $earlierkey ] );
		$earliercheckaddress = $addressparts[0];
		$earlierparty = $addressparts[2];
		$earlierstatus = $addressparts[3];
		$earlierlastvoteddate = $addressparts[4];
		if( strpos( $earliercheckaddress, $address ) === false ) $earlieraddress =  trim( $earliercheckaddress );
	}
	else
	{
		$earlierparty = '-';
		$earlierstatus = '-';
		$earlierlastvoteddate = '-';
		$earlieraddress = '-';
	}
	$lateraddress = $address;
	$laterkey = binary_search( $latervoterids, $lastlaterkey, sizeof( $latervoterids ), $voterid."\n" );
	$lastlaterkey = $laterkey;
	if( $laterkey )
	{
		$addressparts = explode( "\t", $lateraddresses[ $laterkey ] );
		$latercheckaddress = $addressparts[0];
		$laterparty = $addressparts[2];
		$laterstatus = $addressparts[3];
		$laterlastvoteddate = $addressparts[4];
		if( strpos( $latercheckaddress, $address ) === false ) $lateraddress =  trim( $latercheckaddress );
	}
	else
	{
		$laterparty = '-';
		$laterstatus = '-';
		$laterlastvoteddate = '-';
		$lateraddress = '-';
	}
	$outputline = str_replace( "\n", '',$firstname.' '.$middlename.' '.$lastname."\t".$birthdate."\t".$earlieraddress."\t".$address."\t".$lateraddress."\t".$phone."\t".$gender."\t".$voterid."\t".$earlierparty."\t".$partycode."\t".$laterparty."\t".$earlierstatus."\t".$status."\t".$laterstatus."\t".$earlierlastvoteddate."\t".$lastvoteddate."\t".$laterlastvoteddate."\t".$earliercount."\t".$currentcount."\t".$latercount)."\n";
	echo $outputline;

	if( $previousaddress != $address)
	{
		fwrite( $countyfile, "\n" );
	}
	fwrite( $countyfile, $outputline );
	
	$previousaddress = $address;
	fclose( $countyfile );
}

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