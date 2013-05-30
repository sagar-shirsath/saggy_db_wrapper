<?php

require_once "SaggyDbWrapper.php";

$dbObj = SaggyDbWrapper::getInstance();
$query = $dbObj->select()
->from('users')
->where(array('id'=>1));

print $query->getQuery();
print "\n";
$result = $query->get();
print_r($result);
?>

