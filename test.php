<?php

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

$insert = $dbObj->save('users',array('fname'=>'Sagar2','lname'=>'Shirsath2','organisation_id'=>2),array('id'=>1));
$insert = $dbObj->save('users',array('fname'=>'Sagar3','lname'=>'Shirsath3','organisation_id'=>2));
print_r($insert);
?>

