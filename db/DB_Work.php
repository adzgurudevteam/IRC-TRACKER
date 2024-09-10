<?php
class DB_Work {
    private static $INSTANCE;
    private $DBH;
    private $CONNECTION;
    private $_ERR_INFO;
    private function __construct() {
        $this->CONNECTION = Connection::getInstance();
        $this->DBH = $this->CONNECTION->getConnection();
        $this->_ERR_INFO = ['error' => false, 'message' => ''];
    }

    public static function getDBInstance() {
        if(DB_Work::$INSTANCE == null) {
            DB_Work::$INSTANCE = new DB_Work();
        }
        return DB_Work::$INSTANCE;
    }

    public function getFormattedDataFromQuery($query, $returnAsJSON = false, $zeroCheckIsOn = false) {
        $con = DB_Work::$INSTANCE->DBH;
        // if(!DB_Work::$INSTANCE->CONNECTION->shouldCallAPI()) {
            if(DB_Work::$INSTANCE->CONNECTION->getEngine() == "pdo") {
                // Perform pdo operation
                try{
                    $res = $con->query($query);
                    
                    if(!$res) {
                        $ret = [];
                        if(DEBUG_APP) {
                            $ee =  $con->errorInfo();
                            $e =$ee[2];
                            $ec = $ee[1];
                            $ret = array('error'=>$ec, 'message' => $e);
                            DB_Work::$INSTANCE->_ERR_INFO = $ret;
                            DB_Work::$INSTANCE->_ERR_INFO['sql'] = $query;
                        }
                        return $returnAsJSON ? json_encode($ret) : $ret;
                    }
                    if($zeroCheckIsOn && $res->rowCount() == 0) {
                        return false;
                    }
                    $rows = [];
                    foreach($res as $r) {
                        $rows[] = $r;
                    }
                    if($returnAsJSON) {
                        return json_encode($rows);
                    }
                    return $rows;
                }catch(Exception $e) {
                    return false;
                }
            } else {
                // Perform mysql operation
                $res = mysqli_query($con, $query);
                if($res == false) {
                    $ret = array();
                    if(DEBUG_APP) {
                        $ret = array('error'=>mysqli_errno($con), 'message' => mysqli_error($con));
                        DB_Work::$INSTANCE->_ERR_INFO = $ret;
                        DB_Work::$INSTANCE->_ERR_INFO['sql'] = $query;
                    } 
                    return $returnAsJSON ? json_encode($ret) : $ret;
                }
                if($zeroCheckIsOn && mysqli_num_rows($res) == 0) {
                    return false;
                }
                $rows = array();
                while($r = mysqli_fetch_array($res)) {
                    $rows[] = $r;
                }
            
                if($returnAsJSON) {
                    return json_encode($rows);
                }
                return $rows;
            }
        // } 
    }

    public function select($table, $columns = array(), $whereClause = array(), $inClause = array(), $betweenAndClause = array(), 
    $orderBy = array(), $orderMode = "ASC", $limit = array()) {
        $query = "SELECT ";
        if(empty($columns)) {
            $query .= "* ";
        } else {
            foreach ($columns as $col) {
                $query .= $col .", ";
            }
            $query = rtrim($query, ", ");
        }
        $query .= " FROM $table";
        if(!empty($whereClause) || !empty($inClause) || !empty($betweenAndClause)) {
            $query .= " WHERE ";
        }
        if(!empty($whereClause)) {
            foreach ($whereClause as $key => $value) {
                $query .= $key . "='" . $value . "' AND ";
            }
        }
        
        if( ! empty($inClause) ) {
            ##IN clause exists
            foreach ($inClause as $col => $clause) {
                $query .= $col . " IN ('" . implode("','", $clause) . "') AND ";
            }
        }
        if( ! empty($betweenAndClause) ) {
            ##BETWEEN AND clause exists
            foreach ($betweenAndClause as $col => $clause) {
                $query .= $col . " BETWEEN ";
                $query .= "'".$clause[0]."' AND '".$clause[1]."' AND ";
            }
        }
        $query = rtrim($query, "AND ");
        
        if(!empty($orderBy)) {
            $query .= " ORDER BY ";
            foreach ($orderBy as $order) {
                $query .= $order . ", ";
            }
            $query = rtrim($query, ", ");
            if(!empty($orderMode)) {
                $query .= ' '.$orderMode . ' ';
            }
        }
        
        if(!empty($limit)) {
            $query .= " LIMIT $limit[0], $limit[1]";
        }
        return DB_Work::$INSTANCE->getFormattedDataFromQuery($query);
    }

    /**
     * Insert function
     */
    function insert($table, $data, $onDuplicate = []) {
        $conection = DB_Work::$INSTANCE->CONNECTION;
        $con = DB_Work::$INSTANCE->DBH;
        if(gettype($table) != 'string') {
            throw new Exception("Error table data type missmatch.");
        }
        if(gettype($data) != 'array') {
            throw new Exception("Error \$data data type missmatch. Found " . gettype($data) . " expected array.");
        }
    
        
    
        if($conection->getEngine() == 'pdo') {
            $query = "INSERT INTO $table (";
            $datas = [];
            $qts = "(";
            foreach($data as $col => $v) {
                if(is_numeric($col)) {
                    throw new Exception("Error: Unknown column key.");
                }
                $query .= "$col,";
                $qts .= "?,";
                $datas[] = $v;
            }
            $query = rtrim($query, ",");
            $qts = rtrim($qts, ",");
            $qts .= ")";
            $query .= ") VALUES $qts";
    
            try{
            $stmt = $con->prepare($query);

            $res = $stmt->execute($datas);
            if($res) {
                $response['res'] = true;
                $response['error'] = '';
                $response['id'] = $con->lastInsertId();
            } else {
                $err = $con->errorInfo();
                $e = $err[2];
                $er = $err[1];
                $response['res'] = false;
                $response['error'] = $e;
                DB_Work::$INSTANCE->_ERR_INFO['error'] = true;
                DB_Work::$INSTANCE->_ERR_INFO['message'] = $e;
                DB_Work::$INSTANCE->_ERR_INFO['sql'] = $query;
            }
        } catch(Exception $e) {
            DB_Work::$INSTANCE->_ERR_INFO['error'] = true;
            DB_Work::$INSTANCE->_ERR_INFO['message'] = $e->getMessage();
            DB_Work::$INSTANCE->_ERR_INFO['sql'] = $query;
            $response['res'] = false;
            $response['error'] = $e->getMessage();
        }
        } else {
    
            $query = "INSERT INTO $table SET ";
            foreach($data as $col => $value) {
                $query .= $col . "='$value', ";
            }
    
            $query = rtrim($query, ", ");
    
            if(!empty($onDuplicate) && count($onDuplicate) > 0) {
                $query .= " ON DUPLICATE KEY UPDATE ";
                foreach($onDuplicate as $col => $v) {
                    $query .= " $col='$v', ";
                }
                $query = rtrim($query, ', ');
            }
            $res = mysqli_query($con, $query);
            if($res) {
                $response['res'] = true;
                $response['error'] = '';
                $response['id'] = mysqli_insert_id($con);
            } else {
                $response['res'] = false;
                $response['error'] = mysqli_error($con);
                DB_Work::$INSTANCE->_ERR_INFO['error'] = true;
                DB_Work::$INSTANCE->_ERR_INFO['message'] = mysqli_error($con);
                DB_Work::$INSTANCE->_ERR_INFO['sql'] = $query;
            }
        }
        return $response;
    }

     /**
    * Insert multiple rows in a table.
    * @global type $con mysqli connect resource
    * @param type $table Table name
    * @param type $data data to be insert Structure:- $data = array(array('col'=>value, 'col'=>value..), ..)
    */
    public function multipleInsert($table, $data, $onDuplicateDoUpdate = false, $updatedColList = []) {
       $conection = DB_Work::$INSTANCE->CONNECTION;
       $con = DB_Work::$INSTANCE->DBH;
       if(gettype($table) != 'string') {
           throw new Exception("Error table data type missmatch.");
       }
       if(gettype($data) != 'array') {
           throw new Exception("Error \$data data type missmatch. Found " . gettype($data) . " expected array.");
       }
      
       $column = "`".implode("`, `",array_keys($data[0]))."`";
       
       $query = "INSERT INTO `".$table."` (".$column.") VALUES ";

       if($conection->getEngine() == 'pdo') {
        $question_mark = [];
        $values = [];
            foreach($data as $d) {
                $question_mark[] = "(" . DB_Work::$INSTANCE->placeholder("?", count($d), ",") . ")";
                $values = array_merge($values, array_values($d));
            }
            $query .= implode(",", $question_mark);
            $stmt = $con->prepare($query);
            try{
            $res = $stmt->execute($values);
            $return['res'] = $res;
            $return['error'] = '';
            if(!$res) {
                $err = $con->errorInfo();
                $e = $err[2];
                $return['error'] = $e;
                $return['id'] = 0;
                DB_Work::$INSTANCE->_ERR_INFO['error'] = true;
                DB_Work::$INSTANCE->_ERR_INFO['message'] = $e;
                DB_Work::$INSTANCE->_ERR_INFO['sql'] = $query;
            } else {
                $return['id'] = $con->lastInsertId();
            }
            
            }catch(Exception $e) {
                $return['res'] = false;
                $return['error'] = $e->getMessage();
                $return['id'] = 0;
                DB_Work::$INSTANCE->_ERR_INFO['error'] = true;
                DB_Work::$INSTANCE->_ERR_INFO['message'] = $e->getMessage();
                DB_Work::$INSTANCE->_ERR_INFO['sql'] = $query;
            }
       } else {
        foreach ($data as $key => $min_arr) {
            $query .= "(";
            foreach ($min_arr as $col => $val) {
                $query .= "'".$val."', ";
            }
            $query = rtrim($query, ', ');
            $query .= "), ";
        }
        $query = rtrim($query, ', ');
        if($onDuplicateDoUpdate) {
            $query .= " ON DUPLICATE KEY UPDATE ";
            foreach($updatedColList as $col) {
                $query .= " `$col`=VALUES(`$col`), ";
            }
            $query = rtrim($query, ', ');
        }
        $flag = mysqli_query($con, $query);
        $return = array();
        $id = $flag ? mysqli_insert_id($con) : 0;
        $return['res'] = $flag;
        $return['id'] = $id;
        $return['error'] = mysqli_error($con) . mysqli_errno($con);
        if(!$flag) {
            DB_Work::$INSTANCE->_ERR_INFO['error'] = true;
            DB_Work::$INSTANCE->_ERR_INFO['message'] = mysqli_error($con);
            DB_Work::$INSTANCE->_ERR_INFO['sql'] = $query;
        }
       }
       return $return;
   }

   private function placeholder($str, $count=0, $separator=",") {
       $result = [];

       if($count > 0) {
           for($i = 0; $i < $count; $i++) {
               $result[] = $str;
           }
       }
       return implode($separator, $result);
   }

   public function update($table, $data, $where = array(), $inclause = array()) {
        $conection = DB_Work::$INSTANCE->CONNECTION;
        $con = DB_Work::$INSTANCE->DBH;
        if(gettype($table) != 'string') {
            throw new Exception("Error table data type missmatch.");
        }
        if(gettype($data) != 'array') {
            throw new Exception("Error \$data data type missmatch. Found " . gettype($data) . " expected array.");
        }
        if(gettype($where) != 'array') {
            throw new Exception("Error \$where data type missmatch. Found " . gettype($where) . " expected array.");
        }
        if(gettype($inclause) != 'array') {
            throw new Exception("Error \$inclause data type missmatch. Found " . gettype($inclause) . " expected array.");
        }
        $query = "UPDATE $table SET ";
        foreach($data as $col => $value) {
            $query .= $col . "='$value', ";
        }

        $query = rtrim($query, ", ");

        if(count($where) > 0 || count($inclause) > 0) {
            $query .= " WHERE ";
        }

        if(count($where) > 0) {
            foreach($where as $col => $val) {
                $query .= "$col='$val' AND ";
            }
        }

        if(count($inclause) > 0) {
            foreach($inclause as $col => $val) {
                $query .= "$col IN (". implode(",", $val) .") AND ";
            }
        }
        $query = rtrim($query, "AND ");
        $response = [];
        if($conection->getEngine() == 'pdo') {
            $stmt = $con->prepare($query);
            try{
            $res = $stmt->execute();
            if($res) {
                $response['res'] = true;
                $response['error'] = '';
            } else {
                $err = $con->errorInfo();
                $e = $err[2];
                $return['error'] = $e;
                $response['res'] = false;
                DB_Work::$INSTANCE->_ERR_INFO['error'] = true;
                DB_Work::$INSTANCE->_ERR_INFO['message'] = $e;
                DB_Work::$INSTANCE->_ERR_INFO['sql'] = $query;
            }
        }catch(Exception $e) {
            $response['res'] = false;
            $response['error'] = $e->getMessage();
            DB_Work::$INSTANCE->_ERR_INFO['error'] = true;
            DB_Work::$INSTANCE->_ERR_INFO['message'] = $e->getMessage();
            DB_Work::$INSTANCE->_ERR_INFO['sql'] = $query;
        }
        } else {
            $res = mysqli_query($con, $query);
            $response = array();
            if($res) {
                $response['res'] = true;
                $response['error'] = '';
            } else {
                $response['res'] = false;
                $response['error'] = mysqli_error($con);
                DB_Work::$INSTANCE->_ERR_INFO['error'] = true;
                DB_Work::$INSTANCE->_ERR_INFO['message'] = mysqli_error($con);
                DB_Work::$INSTANCE->_ERR_INFO['sql'] = $query;
            }
        }
        return $response;
    }

    public function delete($table, $where, $inclause = array()) {
        $conection = DB_Work::$INSTANCE->CONNECTION;
        $con = DB_Work::$INSTANCE->DBH;
        $return = [];
        if(gettype($table) != 'string') {
            throw new Exception("Error table data type missmatch.");
        }
        
        if(gettype($where) != 'array') {
            throw new Exception("Error \$where data type missmatch. Found " . gettype($where) . " expected array.");
        }
        if(gettype($inclause) != 'array') {
            throw new Exception("Error \$inclause data type missmatch. Found " . gettype($inclause) . " expected array.");
        }
        $query = "DELETE FROM $table ";
        if(count($where) > 0 || count($inclause) > 0) {
            $query .= " WHERE ";
        }
        if(!empty($where) && count($where) > 0) {
            foreach ($where as $key => $value) {
                $query .= $key . "='" . $value . "' AND ";
            }
            
        }
        if(!empty($inclause) && count($inclause) > 0) {
            foreach ($inclause as $key => $v) {
                $query .= $key . " IN ('". implode("','", $v) . "') AND ";
            }
        }
        $query = rtrim($query, "AND ");
        if($conection->getEngine() == 'pdo') {
            $stmt = $con->prepare($query);
            try{
                $res = $stmt->execute();
                if($res) {
                    $return['res'] = true;
                    $return['error'] = '';
                } else {
                    $err = $con->errorInfo();
                    $e = $err[2];
                    $return['error'] = $e;
                    $return['res'] = false;
                    DB_Work::$INSTANCE->_ERR_INFO['error'] = true;
                    DB_Work::$INSTANCE->_ERR_INFO['message'] = $e;
                    DB_Work::$INSTANCE->_ERR_INFO['sql'] = $query;
                }
            }catch(Exception $e) {
                $return['res'] = false;
                $return['error'] = $e->getMessage();
                DB_Work::$INSTANCE->_ERR_INFO['error'] = true;
                DB_Work::$INSTANCE->_ERR_INFO['message'] = $e->getMessage();
                DB_Work::$INSTANCE->_ERR_INFO['sql'] = $query;
            }
        } else {
            $res =  mysqli_query($con, $query);
            $return = array('res' => $res, 'error' => mysqli_error($con));
            if(!$res) {
                DB_Work::$INSTANCE->_ERR_INFO['error'] = true;
                DB_Work::$INSTANCE->_ERR_INFO['message'] = mysqli_error($con);
                DB_Work::$INSTANCE->_ERR_INFO['sql'] = $query;
            }
        }
        return $return;
    }

    public function errInfo() {
        return DB_Work::$INSTANCE->_ERR_INFO;
    }
    public function destroy() {
        DB_Work::$INSTANCE->CONNECTION->destroyInstance();
        DB_Work::$INSTANCE->CONNECTION=null;
        DB_Work::$INSTANCE->DBH=null;
        DB_Work::$INSTANCE = null;
    }
}
?>