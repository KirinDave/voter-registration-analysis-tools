# voter-registration-analysis-tools
A collection of tools for examining voter registration records.

## Getting Started
These scripts are built to pull in and examine aspects of data from one or more sets of voter registration records.

The scripts named getallsubsets_??.php, where ?? is a two character U.S. state abbreviation, are built to parse the state-specific raw format and produce a set of files containing selected data from each record, one voter per line.

These trimmed-down files can easily be imported as arrays and then searched for duplicate records and records matching given criteria. Since all the files for a given dataset are indexed identically, the array_multisort function can be used to sort them by voterid, for instance, while preserving the key/value relationship among the arrays.

This is very useful for making comparisons between one snapshot date and another, for instance finding records where voter ids are the same but other values are different, or using a binary search routine to identify the ids that have been added or removed since the previous snapshot.

When a particular type of data is needed for an output set, the file containing the necessary fields can be processed.

Due to legal and ethical considerations, registration data is not included in the repository.

Here are some sources for it:

**Pennsylvania**  - Voter Registration Data, aka SURE data, is available for purchase here: https://www.pavoterservices.pa.gov/pages/purchasepafullvoterexport.aspx. 

**Florida** - Voter Registration Data, is available on CD by request from here: 
http://dos.myflorida.com/elections/data-statistics/voter-registration-statistics/voter-extract-disk-request/


## Prerequisites
These scripts are built to run in a bash environment with PHP 5.3 or later.

**bash** - https://en.wikipedia.org/wiki/Bash_(Unix_shell)

**PHP** - http://php.net/downloads.php


These scripts assume enough memory to handle some rather large arrays. We recommend at least 24GB RAM. PHP must be configured to utilize as much memory as it may need. This is done by setting the memory_limit to -1 in the php.ini file:

memory_limit = -1;


## Deployment
There are two types of scripts - those that convert raw data from the state's original format into a (mostly) standardized set of flat "data subset" files, and those that operate on the data subset files.

The files that convert raw data are built to be run from within the directory where the data has been uncompressed.

The remaining scripts are built to run from within the directory where the data subset files are located, unless they are comparing or pulling data from more than one data snapshot in which case the paths to source files are input variables.

**The current set of state-specific files are:**

*rename_files_pa.sh* - replaces spaces in the data file names with underbars for easier processing

*getsubsets_pa.php* - generates a set of "standard" data subsets, each containing one line per vote and selected fields from the raw Pennsylvania SURE data.


**The current set of files that operate on a single data set are:**

*birthdates_by_decade.sh* - Grep for birthdate strings

*checkgenders.php* - Count the voters of each gender that have first name matching common given names

*getcentenarians.php* - Count the number of voters over the age of 100 in a data set, and also count centenarians who last voted in a given election

*getunderagevoters.php* - Count the number of voters who were under the age of eighteen on their reported "last voted" date.

*printcounts.php* - Echoes the number of records and the number of unique and non-unique records in each data subsets.

*printallcounts.sh* - Executes printcounts.php for a number of interesting data subsets.

**Also included:**

*rawscripts.tgz* - Contains the original files written while exploring the data and experimenting with the best approaches for identifying interesting features and extracting associated data from the raw files. Caution - contains inelegant, unoptimized and casually commented code. View at your own risk.


## Versioning
For the versions available, see the tags on this repository.

## Authors
Saill - Initial author. http://www.votesleuth.org  - @saill on Twitter

## License
This project is licensed under the GPL 3 - see the LICENSE.md file for details

## Acknowledgments

