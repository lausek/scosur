<?php

class DataInterface {

    const DB_LOC = "localhost";
    const DB_USER = "root";
    const DB_PW = "";
    const DB_NAME = "eval";

    protected static $pdo;

    public static function get_connection() {
        if(self::$pdo === NULL) {
            self::$pdo = new PDO("mysql:dbname=".DataInterface::DB_NAME.";host=".DataInterface::DB_LOC.";", DataInterface::DB_USER, DataInterface::DB_PW);
        }
        return self::$pdo;
    }

    public static function exec_with($query, $params = NULL) {

        $stat = self::get_connection()->prepare($query);

        if($params !== NULL) {
            foreach($params as $key => $val) {
                if(is_numeric($key)) {
                    $key += 1;
                }
                $stat->bindValue($key, $val);
            }
        }

        if(!$stat->execute()) {
            throw new Exception("Execute failed", 1);
        }

        return $stat;

    }

    public static function exec_with_all($query, $params = NULL) {
        return self::exec_with($query, $params)->fetchAll();
    }

    public static function exec_with_single($query, $params) {
        return self::exec_with($query, $params)->fetch();
    }

}

?>
