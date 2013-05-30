<?php
// Database{Mysql} wrapper using php
require_once "config.php";

interface DbWrapper {
    public static function getInstance();

    public function select($fields);

    public function from($tableName);

    public function where($conditions);

    public function  limit($limit, $offset);

    public function  orderBy($fieldName, $enum);

    public function get();

    public function query($query);

    public function save($tableName, $setParameters, $conditions);

    public function delete($tableName, $conditions);
}

class SaggyDbWrapper implements DbWrapper {
    private static $hostName;
    private static $userName;
    private static $password;
    private static $databaseName;
    private static $pdo;
    private $query;
    private static $instance;

    function __construct() {
    }

    public static function getInstance() {
        if (!is_object(self::$instance)) {
            $confObj = new Config();
            $conf = $confObj->getConf();
            self::$hostName = $conf['host'];
            self::$userName = $conf['user'];
            self::$password = $conf['password'];
            self::$databaseName = $conf['dbName'];
            self::$pdo = self::getDbObj();
            self::$instance = new SaggyDbWrapper();
        }
        return self::$instance;

    }


    public static function getDbObj() {
        $pdo = new PDO("mysql:host=" . self::$hostName . ";dbname=" . self::$databaseName . ";", self::$userName, self::$password);
        return $pdo;
    }

    public function select($fields = '*') {

        $fieldsString = $fields;
        if (is_array($fields)) {
            $fieldsString = join(",", $fields);
        } else if ($fields == null) {
            $fieldsString = '*';
        }

        $fieldsString = "select " . $fieldsString . " ";
        $this->query = $fieldsString;
        return $this;
    }

    public function from($tableNames = null) {
        $tablesString = $tableNames;
        if (is_array($tableNames)) {
            $tablesString = join(",", $tableNames);
        } else if ($tableNames == null) {
            $tablesString = '';
        }

        $tablesString = "from " . $tablesString . " ";
        $this->query .= $tablesString;
        return $this;
    }


    //Array format of where condition is like array('first_name'=>'sagar','last_name'=>'shirsath','OR'=>array(''))
    public function where($conditions = null) {
        $whereString = null;
        $counter = 0;
        if (!empty($conditions)) {
            if (is_array($conditions)) {
                print_r($conditions);
                foreach ($conditions as $key => $value) {
                    if (strtoupper($key) == "OR") {
                        $innerCounter = 0;
                        foreach ($value as $orKey => $orValue) {
                            if (($counter ==0) and (($innerCounter ==0) or ($innerCounter == sizeof($value))))
                                $whereString .= "";
                            else
                                $whereString .= " OR ";
                            $whereString .= $orKey . '="' . $orValue . '"';
                            $innerCounter++;

                        }
                    } else {
                        if (($counter ==0) or ($counter == sizeof($conditions)))
                            $whereString .= "";
                        else
                            $whereString .= " AND ";
                        $whereString .= $key . '="' . $value . '"';
                    }
                    $counter++;
                }
            } else {
                $whereString = $conditions;
            }
            $whereString = "where " . $whereString;
            $this->query .= $whereString . " ";
        }
        return $this;
    }

    public function  limit($limit, $offset) {
        $this->query .= 'LIMIT ' . $limit . " " . $offset;
        return $this;
    }

    public function  orderBy($fieldName, $enum = "ASC") {
        return $this;
    }

    public function get() {
        $result = array();
        $pdoStmt = self::$pdo->query($this->query);
        if (!empty($pdoStmt)) {
            $result = $pdoStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $result;
    }

    public function query($query) {
        return $this;
    }

    public function getQuery(){
        return $this->query;
    }
    public function save($tableName, $setParameters, $conditions) {
        return $this;
    }

    public function delete($tableName, $conditions) {
        return $this;
    }
}

?>