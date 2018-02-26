<?php
/*
Writes all IDs from a dataset into a single file
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
php getregistrantdetailsfromdupes.php datestring
Example:
php getalladdresses.php

*/

$outputfilename = 'addresses.txt';
$outputfile = fopen( $outputfilename, 'w');

$fvefiles = shell_exec( 'ls -1 *_FVE_*' );
echo $fvefiles;
if( $fvefiles )
{
	$fvearray = explode( "\n", $fvefiles );
	print_r( $fvearray );
	foreach( $fvearray as $fvefile )
	{
		$bits = explode( '_', $fvefile);
		$countyname = $bits[0];
		if( $fvefile )
		{
			clearstatcache ();
			shell_exec( 'cp '.$fvefile.' tail.tsv' );
			while( filesize ( 'tail.tsv' ) > 0 )
			{
				clearstatcache ();
				shell_exec( 'mv tail.tsv tmp.tsv' );
				shell_exec( 'head -n 50000 tmp.tsv > head.tsv' );
				shell_exec( 'tail -n +50001 tmp.tsv > tail.tsv' );
				$datalines = file( 'head.tsv' );
				foreach ($datalines as $dataline )
				{
					$lineparts = explode( "\t", $dataline );
					$num =  trim( str_replace( '"', '', $lineparts[12] ) );
					$street =  trim( str_replace( '"', '', $lineparts[14] ) );
					$apt =  trim( str_replace( '"', '', $lineparts[15] ) );
					$address = $countyname.' '.$num.' '.$street.' '.$apt;
					fwrite( $outputfile, $address."\n" );
				}
			}
		}
	}
}

fclose( $outputfile );

?>