<?php

require_once 'config.php';
require_once 'classes/Database.php';

Database::startInstance($CONFIG_PDO_CONN,$CONFIG_PDO_USER, $CONFIG_PDO_PASS);

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
            usable $BYTE NOT NULL DEFAULT 1,
            found $BYTE NOT NULL DEFAULT 1,
            loan_block $BYTE NOT NULL DEFAULT 0,
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
            closed $BYTE DEFAULT 0 NOT NULL,
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
    
);

$queries[] = file_get_contents('legacy/sql/01-user.sql');
$queries[] = file_get_contents('legacy/sql/02-model.sql');
$queries[] = file_get_contents('legacy/sql/03-patrimony.sql');

Database::executeQueries($queries);
exit(header('Location: .'));

