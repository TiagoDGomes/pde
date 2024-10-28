<?php

require_once 'config.php';
require_once 'database.php';

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
            num $TEXT NOT NULL 
        );",
    "CREATE TABLE IF NOT EXISTS loan (
            id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD, 
            tstamp $TIMESTAMP NOT NULL DEFAULT $CURRENT_TIMESTAMP,
            user_id $INT NOT NULL,
            model_id $INT NOT NULL,
            patrimony_id $INT DEFAULT NULL,
            closed $BYTE DEFAULT 0 NOT NULL
        );",   
    "CREATE TABLE IF NOT EXISTS log_loan (
            id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD, 
            tstamp $TIMESTAMP NOT NULL DEFAULT $CURRENT_TIMESTAMP,
            loan_id $INT NOT NULL,
            diff $INT DEFAULT 1 NOT NULL,
            details $TEXT            
        );",   
    
);

Database::executeQueries($queries);
exit(header('Location: .'));

