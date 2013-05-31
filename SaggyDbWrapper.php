<?php
// Database{Mysql} wrapper using php
require_once "config.php";

class SaggyDbWrapper {

    private $query;
    private $params;
    private $cnt=0;
    private static $hostName;
    private static $userName;
    private static $password;
    private static $databaseName;
    private static $pdo;
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
        $this->query = "";
        $this->params = array();
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
                foreach ($conditions as $key => $value) {
                    if (strtoupper($key) == "OR") {
                        $innerCounter = 0;
                        foreach ($value as $orKey => $orValue) {
                            if (($counter == 0) and (($innerCounter == 0) or ($innerCounter == sizeof($value))))
                                $whereString .= "";
                            else
                                $whereString .= " OR ";

                            $whereString .= $this->handleArithmeticConditions($orKey, $orValue);
                            $innerCounter++;

                        }
                    } else {
                        if (($counter == 0) or ($counter == sizeof($conditions)))
                            $whereString .= "";
                        else
                            $whereString .= " AND ";
                        $whereString .= $this->handleArithmeticConditions($key, $value);
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

    public function handleArithmeticConditions($key, $value) {

        $op = explode(",", $key);
        if (sizeof($op) == 2) {
            $whereString = $op[0] . " " . $op[1] . ':' . $op[0].$this->cnt." ";
            $this->params[":" . $op[0].$this->cnt] =$value;
        } elseif(strpos($key,".")) {
            $whereString = $key. '= ' . $value;
        }else{
            $whereString = $key . '= :' . $key.$this->cnt." ";
            $this->params[":" . $key.$this->cnt] = $value;
        }
        $this->cnt++;
        return $whereString;
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
        $prepStmt = self::$pdo->prepare($this->query);
        $prepStmt->execute($this->params);
        $result =  $prepStmt->fetchAll(PDO::FETCH_ASSOC);
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
        print "\n" . $this->query . "\n";
        return $this->query;
    }

    public function save($tableName, $setParameters, $conditions = null) {
        $params = "";
        $this->query = "";
        $this->params = array();
        if (!empty($tableName) or !empty($setParameters)) {
            if (empty($conditions)) {

                if (is_array($setParameters)) {
                    $keys = join(",", array_keys($setParameters));

                    $values = $this->formatterSqlInjection($setParameters);

                    $params = $values;
                } else {
                    $params = $setParameters;
                }
                $this->query = "INSERT INTO " . $tableName . " " . $params;
            } else {
                $condition = "Where ";
                if (is_array($setParameters)) {
                    $values = $this->formatterSqlInjection($setParameters);
                    $params = $values;
                } else {
                    $params = $setParameters;
                }
                foreach ($conditions as $key => $val) {
                    $condition .= $this->handleArithmeticConditions($key, $val);
                }
                $this->query = "UPDATE " . $tableName . " SET " . $params . ' ' . $condition;

            }
            $pdoStmt = self::$pdo->prepare($this->query);
            $pdoStmt->execute($this->params);
            return true;

        }
        return false;
    }

    function formatterSqlInjection($conditions, $separator = ",") {
        $cndCnt = 0;
        $condition = "";
        foreach ($conditions as $key => $val) {
            if ($cndCnt++ == sizeof($conditions) - 1)
                $condition .= $key . " = :" . $key." ";
            else
                $condition .= $key . " = :" . $key." ".$separator;
            $this->params[':' . $key] = $val;
        }

        return $condition;
    }

    function formatter($conditions, $separator = ",") {
        $cndCnt = 0;
        $condition = "";
        foreach ($conditions as $key => $val) {
            if ($cndCnt++ == sizeof($conditions) - 1)
                $condition .= $key . "='" . $val . "' ";
            else
                $condition .= $key . "='" . $val . "' " . $separator;
        }
        return $condition;
    }

    public function delete($tableName, $conditions = null) {
        $this->query = "";
        if (!empty($conditions)) {
            $params = $this->formatter($conditions, " AND ");
            $this->query = "DELETE FROM " . $tableName . " WHERE" . " " . $params;
        } else if (!empty($tableName)) {
            $this->query = "DROP TABLE " . $tableName;

        }
        try {
            $pdoStmt = self::$pdo->prepare($this->query);
            $pdoStmt->execute();
        } catch (Exception $e) {
            throw new Exception($e);

        }
        return true;
    }

    public function groupBy($field = "") {
        if (!empty($field)) {
            $this->query .= "GROUP BY " . $field;
        }
        return $this;
    }


}

?>