<?php
// project : m152
// desc : All of functions for the php
require_once "database.php";

/**
 * Read all post
 *
 * @return array|false
 */
function ReadAllPost()
{
    static $ps = null;
    $sql = "SELECT * FROM post INNER JOIN media ON post.idPost = media.idPost";
    try {
        if ($ps == null)
            $ps = connectDB()->prepare($sql);

        if ($ps->execute())
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
function InsertPost($textArea) : bool
{
    if (LastPost()["commentaire"] === $textArea)
        return false;
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
 * Check if the post has been already created
 *
 * @return array|bool
 */
function LastPost()
{
    static $ps = null;
    $sql = "SELECT commentaire FROM post ORDER BY idPost DESC LIMIT 1";
    try {
        if ($ps == null)
            $ps = connectDB()->prepare($sql);

        if ($ps->execute())
            return $ps->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

/**
 * Insert a new media in the database
 *
 * @param $files
 * @param $id
 * @return bool
 */
function InsertMedia($files, $id): bool
{
    static $ps = null;
    $sql = "INSERT INTO `media` (`typeMedia`, `nomMedia`, `idPost`) VALUES (:TYPEMEDIA, :NOMMEDIA, :IDPOST)";
    try {
        if ($ps == null)
            $ps = connectDB()->prepare($sql);

        foreach ($files as $value) {
            $ps->bindParam(":TYPEMEDIA", $value['type'], PDO::PARAM_STR);
            $ps->bindParam(":NOMMEDIA", $value['name'], PDO::PARAM_STR);
            $ps->bindParam(":IDPOST", $id, PDO::PARAM_INT);
        }
        return $ps->execute();
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

/**
 * Transaction of the two query
 *
 * @param $files
 * @param $textArea
 * @return mixed
 */
function Transaction($files, $textArea)
{
    static $dbh = null;
    if ($dbh == null)
        $dbh = connectDB();
    $dbh->beginTransaction();
    try {
        InsertPost($textArea);
        InsertMedia($files, GetLastPost());
        return $dbh->commit();
    } catch (Exception $e) {
        echo $e->getMessage();
        return $dbh->rollBack();
    }
}

/**
 * Get the last entry in the table post
 * @return int|bool
 */
function GetLastPost()
{
        static $ps = null;
        $sql = "SELECT idPost FROM post ORDER BY idPost DESC LIMIT 1";
        try {
            if ($ps == null)
                $ps = connectDB()->prepare($sql);

            if ($ps->execute())
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
    for ($i = 0; $i < $lenght; $i++) {
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
    for ($i = 0; $i < count($images); $i++) {
        if (strpos($images["type"][$i], "image/") !== false)
            $result += $images["size"][$i];
    }
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
    foreach ($posts as $p) {
        $result .= "<div class='panel panel-default'>"
            . "<div class='panel-heading'>"
            . "<div class='panel panel-default'>"
            . "<div class='panel-thumbnail'>";
        foreach ($p['img'] as $img)
            $result .= "<img alt='Post' src='assets/uploads/" . $img . "' class='img-responsive'/>";
        $result .= "</div>"
            . "<div class='panel-body'>"
            . "<p class='lead'>" . $p['commentaire'] . "</p>"
            . "</div>"
            . "</div>"
            . "</div>"
            . "</div>";
    }
    return $result;
}