<?php
// Database{Mysql} wrapper using php
require_once "config.php";

class SaggyDbWrapper {
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
                            if (($counter == 0) and (($innerCounter == 0) or ($innerCounter == sizeof($value))))
                                $whereString .= "";
                            else
                                $whereString .= " OR ";
                            $whereString .= $orKey . '="' . $orValue . '"';
                            $innerCounter++;

                        }
                    } else {
                        if (($counter == 0) or ($counter == sizeof($conditions)))
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

    public function orderBy($fieldName, $enum = "ASC") {
        if (!empty($fieldName)) {
            $this->query .= "ORDER BY " . $fieldName . " " . $enum;
        }
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
        $pdoStmt = self::$pdo->query($query);
        if (!empty($pdoStmt)) {
            $result = $pdoStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $result;
    }

    public function getQuery() {
        return $this->query;
    }

    public function save($tableName, $setParameters, $conditions = null) {
        $params = "";
        $this->query = "";
        if (!empty($tableName) or !empty($setParameters)) {
            if (empty($conditions)) {

                if (is_array($setParameters)) {
                    $keys = join(",", array_keys($setParameters));
                    $values = "";
                    foreach (array_values($setParameters) as $key => $val) {
                        if ($key == sizeof($setParameters) - 1)
                            $values .= "'" . $val . "'";
                        else
                            $values .= "'" . $val . "',";
                    }
                    $params = "(" . $keys . ") VALUES (" . $values . ")";
                } else {
                    $params = $setParameters;
                }
                $this->query = "INSERT INTO " . $tableName . " " . $params;
            } else {
                $condition = "Where ";
                $cnt = 0 ;
                if (is_array($setParameters)) {
                    foreach ($setParameters as $key => $val) {
                        if($cnt++ == sizeof($setParameters)-1)
                            $params .= $key . "='" . $val."' ";
                        else
                            $params .= $key . "='" . $val . "',";
                    }
                }else{
                    $params = $setParameters;
                }
                $cndCnt = 0;
                foreach ($conditions as $key => $val) {
                    if($cndCnt++ == sizeof($conditions)-1)
                        $condition .= $key . "='" . $val . "' ";
                    else
                        $condition .= $key . "='" . $val . "',";
                }
                $this->query = "UPDATE " . $tableName . " SET " . $params . ' ' . $condition;
            }
            $pdoStmt = self::$pdo->prepare($this->query);
            $pdoStmt->execute();

        }
        return false;
    }

    public function delete($tableName, $conditions) {
        return $this;
    }
}

?>