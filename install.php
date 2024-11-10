<?php isset($PDE) or die('Nope');

require_once 'config.php';
require_once 'classes/Database.php';

Database::startInstance($CONFIG_PDO_CONN,$CONFIG_PDO_USER, $CONFIG_PDO_PASS);

$dsn_type = explode(":", $CONFIG_PDO_CONN)[0];
if ($dsn_type == 'sqlite'){
    $AUTO_INCREMENT_KEYWORD = 'AUTOINCREMENT';
    $TEXT = 'TEXT';   
    $LONG_TEXT = 'TEXT';
    $BYTE = 'INTEGER';
    $INT = 'INTEGER';
    $LONG_INT = 'INTEGER';
    $TIMESTAMP = 'TEXT';
    $CURRENT_TIMESTAMP = "(datetime('now','localtime'))";
    $QUERY_NORMALIZE = 'SELECT 1';
} else {
    $AUTO_INCREMENT_KEYWORD = 'AUTO_INCREMENT';
    $TEXT = 'VARCHAR(255)';   
    $LONG_TEXT = 'TEXT';
    $BYTE = 'INT';
    $INT = 'INT';
    $LONG_INT = 'LONGINT';
    $TIMESTAMP = 'TIMESTAMP';
    $CURRENT_TIMESTAMP = "CURRENT_TIMESTAMP";
    $QUERY_NORMALIZE =  "
        DROP FUNCTION IF EXISTS normalize;
        DELIMITER //

        CREATE FUNCTION normalize (txt TEXT)
            RETURNS TEXT

            BEGIN
            RETURN txt;
            END //
        DELIMITER ;";
}


$queries = array(

    "CREATE TABLE IF NOT EXISTS user (
            id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD, 
            name $TEXT NOT NULL,
            code1 $TEXT DEFAULT NULL,
            code2 $TEXT DEFAULT NULL
        );",
    "CREATE TABLE IF NOT EXISTS model (
            id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD, 
            name $TEXT NOT NULL,
            code $TEXT NOT NULL,
            has_patrimony $BYTE DEFAULT 0 NOT NULL
        );",
    "CREATE TABLE IF NOT EXISTS patrimony (
            id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD, 
            model_id $INT NOT NULL,
            number1 $TEXT NOT NULL,
            number2 $TEXT,
            serial_number $TEXT,
            usable $BYTE NOT NULL DEFAULT 1 CHECK (usable IN (0, 1)),
            found $BYTE NOT NULL DEFAULT 1 CHECK (found IN (0, 1)),
            loan_block $BYTE NOT NULL DEFAULT 0 CHECK (loan_block IN (0, 1)),
            obs $TEXT,
            FOREIGN KEY (model_id)
                REFERENCES model (id)
        );",
    "CREATE TABLE IF NOT EXISTS loan (
            id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD, 
            tstamp $TIMESTAMP NOT NULL DEFAULT $CURRENT_TIMESTAMP,
            user_id $INT NOT NULL,
            model_id $INT NOT NULL,
            patrimony_id $INT DEFAULT NULL,
            closed $BYTE DEFAULT 0 NOT NULL CHECK (closed IN (0, 1)),
            original_count $INT DEFAULT 1 NOT NULL,
            FOREIGN KEY (model_id)
                REFERENCES model (id),
            FOREIGN KEY (user_id)
                REFERENCES user (id)
        );",   
    "CREATE TABLE IF NOT EXISTS log_loan (
            id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD, 
            tstamp $TIMESTAMP NOT NULL DEFAULT $CURRENT_TIMESTAMP,
            loan_id $INT NOT NULL,
            diff $INT DEFAULT 1 NOT NULL,
            details $TEXT,
            FOREIGN KEY (loan_id)
                REFERENCES loan (id)      
        );",  
      "CREATE TABLE IF NOT EXISTS kit (
            id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD,             
            description $TEXT,
            code $INT NOT NULL
        );",  
       "CREATE TABLE IF NOT EXISTS kit_model (
            id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD,
            model_id $INT NOT NULL,
            patrimony_id $INT DEFAULT NULL,
            FOREIGN KEY (model_id)
                REFERENCES model (id),
            FOREIGN KEY (patrimony_id)
                REFERENCES patrimony (id)                
        );",  
        "ALTER TABLE model ADD COLUMN model_obs $TEXT",
        "ALTER TABLE model ADD COLUMN model_location $TEXT",
        "ALTER TABLE patrimony ADD COLUMN patrimony_location $TEXT",
        "ALTER TABLE model ADD COLUMN model_loan_block $BYTE NOT NULL DEFAULT 0 CHECK (model_loan_block IN (0, 1))",
        "ALTER TABLE model ADD COLUMN icon_set $TEXT DEFAULT NULL", 
    $QUERY_NORMALIZE
);


if (isset($CONFIG_ADDITIONAL_SQL_PATH)){
    if (is_dir($CONFIG_ADDITIONAL_SQL_PATH)){
        foreach (new DirectoryIterator($CONFIG_ADDITIONAL_SQL_PATH) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            $queries[] = file_get_contents($CONFIG_ADDITIONAL_SQL_PATH . "/" . $fileInfo->getFilename());
        }
    }
}



$errors = Database::executeQueries($queries, false);


