SAGGY DB WRAPPER
================

This is the db wrapper that will help to plain php programmer to interact with the databases


#NOTE :
 - Class  follow Singleton design pattern.
 - Used PDO class to connect to the DB drivers.
 - Have proper exception handling
 - Handles SQL injections.

# Methods :
 - function select (array $Fields)
         : It should accept either a single variable or an array.
 - function from (array $tableNames)
         : It should accept either a single variable or an array.

    //$conditions format of where condition is like array('first_name'=>'sagar','last_name'=>'shirsath','OR'=>array('id'=>2,'name'=>'sagar'))
    // specify < , > , <= , >= , <> after the comma in the key e.g. arra('id,<='=>20 , 'salary,>'=>'20000')
 - function where (array $conditions)
         : Proper handling of 'OR' and 'AND' condition
         : ( Create your own array structure to be parsed in a way, that it should handle OR, AND conditions. By default, every condition would be AND.)
 - function limit (integer $limit , null | integer $offset )
 - function orderBy (string $fieldName , null | enum (DESC , ASC))
 - function getInstance(string $hostName , string $userName , string $password , string $databaseName)
        : Should return the singleton object of the class.
 - function get ()
        : Should build the query which have been created by the above mentioned methods.
 - function query (string $query)
        : Can directly pass the query as a string and should return the proper result set. Also, every select type query should use this method.
   //give the condition same as in where clause
 - function save (string $tableName , array $setParameters , null | array $conditions)
       : This method should perform the Insert as well as the update operations on to the database. Update would rely on the third parameter being passed or not.
 - function delete (string $tableName , null | array $conditions)

