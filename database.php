<?php
//parametri di connessione
$config = [
    'db_engine' => 'mysql',
    'db_host' => 'localhost',
    'db_name' => 'my_econsent',
    'db_user' => 'econsent',
    'db_password' => '',
];

$db_config = $config['db_engine'] . ":host=".$config['db_host'] . ";dbname=" . $config['db_name'];

//blocco try/catch di gestione delle eccezioni
try {
	// stringa di connessione al DBMS
    $pdo = new PDO($db_config, $config['db_user'], $config['db_password'], 
                   [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
   
    // impostazione dell'attributo per il report degli errori
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // impostazione dell'attributo per usare prepared statement nativi
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    exit("Impossibile connettersi al database: " . $e->getMessage());
}
