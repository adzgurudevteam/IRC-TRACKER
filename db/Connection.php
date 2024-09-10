<?php
class Connection {
    private $CON;
    private static $INSTANCE;
    private $ENGINE;
    private $API;
    private function __construct() {
        global $db_cfg;
        $this->API = $db_cfg['api'];
        if(!$db_cfg['api']) {
            $host = $db_cfg['db_config']['host'];
            $username = $db_cfg['db_config']['username'];
            $password = $db_cfg['db_config']['password'];
            $db = $db_cfg['db_config']['database'];
            $engine = $db_cfg['engine'];
            $this->ENGINE = $engine;
            if(strtolower($engine) == 'pdo') {
                $server = "mysql:host=$host;dbname=$db;charset=utf8";
                $this->CON = new PDO($server, $username, $password);
                $this->CON->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } else {
                $this->CON = mysqli_connect($host, $username, $password, $db);
                mysqli_set_charset( $this->CON, 'utf8');
            }
        } else {
            $this->CON = null;
        }
    }

    public static function getInstance() {
        if(Connection::$INSTANCE == null) {
            Connection::$INSTANCE = new Connection();
        }
        return Connection::$INSTANCE;
    }

    public function getConnection() {
        return $this->CON;        
    }

    public function getEngine() {
        return strtolower($this->ENGINE);   
    }
    public function shouldCallAPI() {
        return $this->API;
    }

    public function destroyInstance() {
        global $db_cfg;
        if(!$db_cfg['api']) {
            if($db_cfg['engine'] == 'pdo') {
                $this->CON = null;
            } else {
                mysqli_close($this->CON);
            }
        }
        Connection::$INSTANCE = null;
    }
}
?>