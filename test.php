<?php
$text = 1;
require_once 'classes/dbconfig.php';
require_once 'classes/mysql.php';
require_once 'classes/tools.class.php';
require_once 'classes/data_generator.class.php';
require_once 'classes/data_dummy.class.php';


$dataGenerator = new DataGenerator();
$schema = 'attune_portal';
$tables = array("TransportRequest");
//number of records
$size = 1000;
echo "Loading data\n";
$qa = array();
foreach ($tables as $tablename)
{
    $table = $dataGenerator->scanTable($schema.'.'.$tablename);
    $data = $dataGenerator->populate($table,$size);
    $q = $dataGenerator->buildSQL($data, $tablename);
    $qa = array_merge($qa, $q);
}
//if only to dump
//file_put_contents('dummy.sql', implode("\n",$q));
//else
echo "Inserting ".count($qa)."\n";
//$dataGenerator->runSQL($q);
echo "Complete\n";