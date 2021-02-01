<?php
// project : m152
// desc : All of functions for the php
require_once "database.php";

/**
 * Insert a new post in the database
 *
 * @param $textArea
 * @return bool
 */
function InsertPost($textArea)
{
    static $ps = null;
    $sql = "INSERT INTO `post` (`commentaire`) VALUES (:COMMENTAIRE)";
    try {
        if ($ps == null)
            $ps = connectDB()->prepare($sql);

            $ps->bindParam(":COMMENTAIRE", $textArea, PDO::PARAM_STR);
        return $ps->execute();
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

/**
 * Insert a new media in the database
 *
 * @param $files
 * @param $idPost
 * @return bool
 */
function InsertMedia($files, $idPost)
{
    static $ps = null;
    $sql = "INSERT INTO `media` (`typeMedia`, `nomMedia`, `idPost`) VALUES (:TYPEMEDIA, :NOMMEDIA, :IDPOST)";
    try {
        if ($ps == null)
            $ps = connectDB()->prepare($sql);

        foreach ($files as $value) {
            $ps->bindParam(":TYPEMEDIA", $value['type'], PDO::PARAM_STR);
            $ps->bindParam(":NOMMEDIA", $value['name'], PDO::PARAM_STR);
            $ps->bindParam(":IDPOST", $idPost['idPost'], PDO::PARAM_INT);
        }
        return $ps->execute();
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

/**
 * Get the last entry in the table post
 * @return false|mixed
 */
function GetLastPost()
{
    static $ps = null;
    $sql = "SELECT idPost FROM post ORDER BY idPost DESC LIMIT 1";
    try {
        if ($ps == null)
            $ps = connectDB()->prepare($sql);

        if($ps->execute())
            return $ps->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}