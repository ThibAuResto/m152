<?php
// Projet    :   m152
// Desc.     :   Connection to the database
require_once("constantes.inc.php");

/**
 * Connect to the database
 *
 * @return PDO|null PDO if succeed else null
 */
function connectDB(): ?PDO
{
    static $myDb = null;
    try {
        if ($myDb === null) {
            $myDb = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET . "",
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
            );
        }
    } catch (Exception $e) {
        echo $e;
        exit;
    }
    return $myDb;
}
