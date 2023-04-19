<?php
//Script written by Jorge D. Mendez Rios
//Email jdmendez at infomedicint.com
//Licence GNU. April 2023.


//MySQL configuration.
$dbconfig['host']='localhost';
$dbconfig['user']='';
$dbconfig['pass']='';



$test=','.implode(',',$dbconfig).','; 
if (!isset($argv[1]) || !isset($argv[2]) || strstr($test,',,')) {
echo 'Use: php -f '.basename(__FILE__).' <database name1> <database name2>'."\n";
echo 'Remember to modify the mysql credentials inside the script.';
exit;}



//Arguments provided (mysql database names).
$db1=$argv[1];
$db2=$argv[2];
$verbose=false;


class comparedb {


public $tables1 = array();
public $tables2 = array();
public $fields1 = array();

}

function cdb($sql,$forced_db = null)
{
global $dbconfig;
    $result=false;
    if ($forced_db == null)
$mysqli = new mysqli($dbconfig['host'],$dbconfig['user'],$dbconfig['pass'],$dbconfig['db']);
    else
        $mysqli = new mysqli($dbconfig['host'],$dbconfig['user'],$dbconfig['pass'],$forced_db);

if ($mysqli->connect_errno) {
echo "Sorry, this website is experiencing problems.";
    echo "Error: Failed to make a MySQL connection, here is why: \n";
    echo "Errno: " . $mysqli->connect_errno . "\n";
    echo "Error: " . $mysqli->connect_error . "\n";
 // You might want to show them something nice, but we will simply exit
    exit;
}
else
{
if ($result = $mysqli->query($sql)) {
    }
}
 //$result->free();
$mysqli->close();
  return $result;

}







$c = new comparedb();

//--------------------------
//Get tables from database 1
//--------------------------
$q=cdb('show tables;',$db1);
while ($qq=$q->fetch_assoc()) {
foreach ($qq as $key => $value) { $tables1[]=$value;
} }
//--------------------------




//--------------------------
//Get tables from database 2
//--------------------------
$q=cdb('show tables;',$db2);
while ($qq=$q->fetch_assoc()) {
foreach ($qq as $key => $value) { $tables2[]=$value;
} }
//--------------------------




//--------------------------
//Generating array with the information of the fields for each table in table 1.
//--------------------------

	for ($i=0;$i<count($tables1);$i++) {
	$table=$tables1[$i];
	$q=cdb('show fields from '.$table.';',$db1);
		while ($qq=$q->fetch_assoc()) {
		$fields1[$db1][$table][$qq['Field']]=$qq; 
		}// end of for
	}
//--------------------------


//--------------------------
//Generating array with the information of the fields for each table in table 2.
//--------------------------
	for ($i=0;$i<count($tables2);$i++) {
	$table=$tables2[$i];
	$q=cdb('show fields from '.$table.';',$db2);
		while ($qq=$q->fetch_assoc()) {
		$fields2[$db2][$table][$qq['Field']]=array_values($qq);
		}// end of for

	}
//--------------------------




//--------------------------
//Checking if tables in database 1 are found in database 2.  
//If found, an array of tables will be used to further compare.
//--------------------------
$table2_list=','.implode(',',$tables2).',';
	for ($i=0;$i<count($tables1);$i++) {
		if (strstr($table2_list,','.$tables1[$i].',')) { 
		if ($verbose) echo 'table found:'.$tables1[$i]."\n";$tocompare[]=$tables1[$i];
		} 
		else 
		{echo 'Table not found:'.$tables1[$i].' in database '.$db2."\n";}

	}
//--------------------------



//--------------------------
//Comparing fields from tables in database 1 with those fields on each table of database2.
//The output will suggest which command to run to create the missing fields.
//--------------------------
	for ($i=0;$i<count($tocompare);$i++) {
	if ($verbose) echo 'Comparing tables:'.$tocompare[$i]."\n\n";
	$first=$fields1[$db1][$tocompare[$i]];
	$second=$fields2[$db2][$tocompare[$i]];
		foreach ($first as $key => $value) {
		if (isset($second[$key])) 
		{} else 
		{echo 'Field '.$key.' does not exists.'."\n".'Run:'."alter table `$tocompare[$i]` add column $key ".$value['Type'].';'."\n";}
		}
	}
