<?php
isset($PDE) or die('Nope');

function normalize($str){
    return Filter::normalize($str);
}
function php_concat(...$str){
    $ret = '';
    foreach ($str as $s) {
        $ret .= $s;
    }
    return $ret;
}

class Database {
    public static PDO $db_file;
    public static string $key;

    public static function startInstance($dsn, $username, $password) {
        Database::$db_file = new PDO($dsn, $username, $password);
        $dsn_split = explode(":",$dsn);
        if ($dsn_split[0] == 'sqlite'){
            Database::$db_file->sqliteCreateFunction('normalize', 'normalize');
            Database::$db_file->sqliteCreateFunction('concat', 'php_concat');
        } else if ($dsn_split[0] == 'mysql'){
            #Database::execute('DROP FUNCTION IF EXISTS normalize',array());
            Database::execute('CREATE FUNCTION IF NOT EXISTS normalize (str VARCHAR(255)) 
                                        RETURNS VARCHAR(255)
                                        RETURN str;',array());
        }       
    }

    public static function beginTransaction() {
        Database::$db_file->beginTransaction();
    }

    public static function commit() {
        Database::$db_file->commit();
    }

    public static function fetch($query, $param, $mode = PDO::FETCH_ASSOC) {
        $stmt = Database::$db_file->prepare($query);
        $stmt->execute($param);
        return $stmt->fetch($mode);
    }

    public static function fetchOne($query, $param) {
        $stmt = Database::$db_file->prepare($query);
        if (is_array($param)) {
            $stmt->execute($param);
        } else {
            $stmt->execute(array($param));
        }
        $f = $stmt->fetch();
        if ($f) {
            return $f[0];
        }
        return NULL;
    }

    public static function fetchAll($query, $param, $mode = PDO::FETCH_ASSOC) {
        $stmt = Database::$db_file->prepare($query);
        $stmt->execute($param);
        return $stmt->fetchAll($mode);
    }

    public static function execute($query, $param) {
        $stmt = Database::$db_file->prepare($query);
        return $stmt->execute($param);
    }

    public static function executeQueries($queries, $raise_on_error=TRUE) {
        $sql_errors = array();
        foreach ($queries as $query) {
            if ($raise_on_error){
                Database::execute($query, []);
            } else {
                try{
                    Database::execute($query, []);
                }catch(Exception $e){
                    $sql_errors[]['query'] = $query;
                    $sql_errors[]['message'] = $e->getMessage();
                    
                }
            }
            
        }
        return array("sql_errors" => $sql_errors);
    }
}

