<?php
// project : m152
// desc : All of functions for the php
require_once "database.php";

/**
 * Read all post
 *
 * @return bool
 */
function ReadAllPost()
{
    static $ps = null;
    $sql = "SELECT * FROM post INNER JOIN media ON post.idPost = media.idPost";
    try {
        if ($ps == null)
            $ps = connectDB()->prepare($sql);

        if($ps->execute())
            return $ps->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

/**
 * Insert a new post in the database
 *
 * @param $textArea
 * @return bool
 */
function InsertPost($textArea): bool
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
function InsertMedia($files, $idPost): bool
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

// TODO begin transaction
function RegroupInsert($files, $idPost, $textArea){
    static $dbh = null;
    if($dbh == null)
        $dbh = connectDB()->beginTransaction();

    if($dbh->exec(InsertPost($textArea)) && $dbh->exec(InsertMedia($files, $idPost)))
      return $dbh->commit();
    else
      return $dbh->rollBack();
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

/**
 * Get a random string for the name of the image
 *
 * @param int $lenght
 * @return string
 */
function GetRandomString($lenght = 10): string
{
    $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $maxLenght = strlen($char);
    $randomString = '';
    for ($i = 0; $i < $lenght; $i++)
    {
        $randomString .= $char[rand(0, $maxLenght - 1)];
    }
    return $randomString;
}

/**
 * Get the size of the uploaded images
 *
 * @param $images
 * @return int
 */
function GetSizeOfTheUploadImages($images): int
{
    $result = 0;
    foreach ($images['size'] as $i)
        $result += $i;
    return $result;
}

/**
 * Write all posts
 *
 * @param $posts
 * @return string
 */
function WriteAllPost($posts): string
{
    $result = "";
    for ($i = 0; $i < count($posts); $i++){
    $result .=  "<div class='panel panel-default'>"
        ."<div class='panel-heading'>"
            ."<div class='panel panel-default'>"
                ."<div class='panel-thumbnail'>"
                   ."<img src='assets/uploads/" . $posts[$i]['nomMedia'] . "' class='img-responsive'/>"
                ."</div>"
                ."<div class='panel-body'>"
                    ."<p class='lead'>" . $posts[$i]['commentaire'] . "</p>"
                ."</div>"
            ."</div>"
        ."</div>"
    ."</div>";
    }
    return $result;
}