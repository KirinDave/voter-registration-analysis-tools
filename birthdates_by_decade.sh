## Let's just go ahead and assume no one has these numeric strings in their first names.
## To verify run this script on firstname_set.csv and make sure we get all zeros

#!/bin/bash
printf '1800-1809: '; grep -c 180 firstname_dob_set.csv
printf '1810-1819: '; grep -c 181 firstname_dob_set.csv
printf '1820-1829: '; grep -c 182 firstname_dob_set.csv
printf '1830-1839: '; grep -c 183 firstname_dob_set.csv
printf '1840-1849: '; grep -c 184 firstname_dob_set.csv
printf '1850-1859: '; grep -c 185 firstname_dob_set.csv
printf '1860-1869: '; grep -c 186 firstname_dob_set.csv
printf '1870-1870: '; grep -c 187 firstname_dob_set.csv
printf '1880-1889: '; grep -c 188 firstname_dob_set.csv
printf '1890-1899: '; grep -c 189 firstname_dob_set.csv
printf '1900-1909: '; grep -c 190 firstname_dob_set.csv
printf '1910-1919: '; grep -c 191 firstname_dob_set.csv
printf '1920-1929: '; grep -c 192 firstname_dob_set.csv
printf '1930-1939: '; grep -c 193 firstname_dob_set.csv
printf '1940-1949: '; grep -c 194 firstname_dob_set.csv
printf '1950-1959: '; grep -c 195 firstname_dob_set.csv
printf '1960-1969: '; grep -c 196 firstname_dob_set.csv
printf '1970-1970: '; grep -c 197 firstname_dob_set.csv
printf '1980-1989: '; grep -c 198 firstname_dob_set.csv
printf '1990-1999: '; grep -c 199 firstname_dob_set.csv
printf '2000-2009: '; grep -c 200 firstname_dob_set.csv
printf '2010-present: '; grep -c 201 firstname_dob_set.csv