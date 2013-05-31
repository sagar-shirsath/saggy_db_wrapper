<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "SaggyDbWrapper.php";

$dbObj = SaggyDbWrapper::getInstance();

//List all orgsanisaions
print "#All Organziations";
$orgList = $dbObj->select('name')
->from('organizations')
->get();
$dbObj->getQuery();
print_r($orgList);

// List 10 organization whose id is greater than 10
print "#Organisation with id > 10";
$grOrgList =$dbObj->select('name')
    ->from('organizations')
    ->where(array('id,>'=>10))
    ->get();
$dbObj->getQuery();
print_r($grOrgList);

// List Organization whose id is greater than 10 and less than equal to 50
print '#List Organization whose id is greater than 10 and less than equal to 50';
$grOrgList =$dbObj->select('name')
    ->from('organizations')
    ->where(array('id,>'=>10,'id,<'=>50))
    ->get();
$dbObj->getQuery();
print_r($grOrgList);

//LIst all organization who has bee created after 2013-02-10 00:00:00
print "#LIst all organization who has bee created after 2013-02-10 00:00:00";
$grOrgList =$dbObj->select('name')
    ->from('organizations')
    ->where(array('created_on,>'=>"2013-02-10 00:00:00"))
    ->get();
$dbObj->getQuery();
print_r($grOrgList);

// display informations about organization whose id is 70
print "# display informations about organization whose id is 70";
$org =$dbObj->select()
    ->from('organizations')
    ->where(array('id'=>70))
    ->get();
$dbObj->getQuery();
print_r($org);

//display informations about organization whose name is "Org Name 30"
print "#display informations about organization whose name is Org Name 30";

$org =$dbObj->select()
    ->from('organizations')
    ->where(array('name'=>'Org Name 30'))
    ->get();
$dbObj->getQuery();
print_r($org);

//display all the users of organization_id 30
print "#display all the users of organization_id 30";
$result = $dbObj->select(array('fname','lname'))
->from(array('users','organizations'))
->where(array('organizations.id'=>30))
->get();
$dbObj->getQuery();
print_r($result);

//return a count of users per organization with organization name
print "#return a count of users per organization with organization name";
$result = $dbObj->select(array('organizations.name','COUNT(users.id)'))
    ->from(array('users','organizations'))
    ->where(array('users.organisation_id'=>'organizations.id'))
    ->groupBy('users.id')
    ->get();
$dbObj->getQuery();
print_r($result);


//update users table fname = 'abc' and lname = 'xyz' of user whose id is 20
print "#update users table fname = 'abc' and lname = 'xyz' of user whose id is 20 ";
$isSaved = $dbObj->save('users',array('fname'=>'abc','lname'=>'xyz'),array('id'=>20));
if($isSaved){
    print "Record updated";
}
$dbObj->getQuery();

// delete all users who lives in city "City7"
print "#delete all users who lives in city City7";
$isDeleted = $dbObj->delete('users',array('city'=>'City7'));
$dbObj->getQuery();


//List all organizations who has id between 10 to 50 and its orders should be descending by name
print "#List all organizations who has id between 10 to 50 and its orders should be descending by name";
$org =$dbObj->select()
    ->from('organizations')
    ->where(array('id>'=>10,'id<'=>50))
    ->orderBy('name')
    ->get();

$dbObj->getQuery();
print_r($org);

//
//
//print_r($result->getQuery());
//
//print $query->getQuery();
//print "\n";
//$result = $query->get();

//$insert = $dbObj->save('users',array('fname'=>'Sagar2','lname'=>'Shirsath2','organisation_id'=>2),array('id'=>1));
//$insert = $dbObj->save('users',array('fname'=>'Sagar3','lname'=>'Shirsath3','organisation_id'=>2));
//$delete = $dbObj->delete('users',array('id'=>45,'lname'=>'lname3'));
//print_r($delete);
?>

