<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "SaggyDbWrapper.php";

$dbObj = SaggyDbWrapper::getInstance();
//$query = $dbObj->select()
//->from('users')
//->where()
//->orderBy('id','DESC');
//
//print $query->getQuery();
//print "\n";
//$result = $query->get();

//$insert = $dbObj->save('users',array('fname'=>'Sagar2','lname'=>'Shirsath2','organisation_id'=>2),array('id'=>1));
//$insert = $dbObj->save('users',array('fname'=>'Sagar3','lname'=>'Shirsath3','organisation_id'=>2));
$delete = $dbObj->delete('users',array('id'=>45,'lname'=>'lname3'));
print_r($delete);
?>

