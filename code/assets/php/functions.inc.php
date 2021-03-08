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
    $sql = "SELECT * FROM post INNER JOIN media ON post.idPost = media.idPost ORDER BY media.creationDate asc";
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
 * @return int|bool
 */
function InsertPost($textArea) : bool
{
    if (LastPost()["commentaire"] === $textArea)
        return false;
    static $ps = null;
    $sql = "INSERT INTO `post` (`commentaire`) VALUES (:COMMENTAIRE)";
    try {
        if ($ps == null)
            $ps = connectDB()->prepare($sql);

        $ps->bindParam(":COMMENTAIRE", $textArea, PDO::PARAM_STR);
        if($ps->execute())
            return intval(connectDB()->lastInsertId());
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
            $ps->bindParam(":IDPOST", $id["idPost"], PDO::PARAM_INT);
        }
        return $ps->execute();
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

/**
 * Transaction of the query of insert a media and insert a post
 *
 * @param $files
 * @param $textArea
 * @param $nameOfPost
 * @param $uploadsDir
 * @return mixed
 */
function InsertMediaAndPost($files, $textArea, $nameOfPost, $uploadsDir)
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
        RemoveLastInsertPostInTheFolder($nameOfPost, $uploadsDir);
        return $dbh->rollBack();
    }
}

/**
 * If we have an error with the transaction, remove the media in the folder
 *
 * @param $name
 * @param $uploadsDir
 */
function RemoveLastInsertPostInTheFolder($name, $uploadsDir){
    $path = $uploadsDir . DIRECTORY_SEPARATOR . $name;
    try {
        unlink($path);
    } catch (Exception $e) {
        echo $e->getMessage();
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
 * @param $medias
 * @return int
 */
function GetSizeOfTheUpload($medias): int
{
    $result = 0;
    for ($i = 0; $i < count($medias["name"]); $i++)
        $result += $medias["size"][$i];
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
        foreach ($p['medias'] as $m) {
                if (strpos($m["type"], "image/") !== false) {
                    $result .= "<img alt='Post' src='assets/uploads/" . $m['media'] . "' class='img-responsive'/>";
                } else if (strpos($m['type'], "video/") !== false) {
                    $result .= "<video controls autoplay loop>
                            <source src='assets/uploads/" . $m['media']. "' type='" . $p['type'] . "'>
                            Your browser does not support the video tag.
                        </video>";
            }
        }
        $result .= "</div>"
            . "<div class='panel-body'>"
            . "<p class='lead'>" . $p['commentaire'] . "</p>"
            . "<a class='btn btn-link' href='#'>"
            . "<img alt='modif' src='assets/img/editing.png'/>"
            . "</a>"
            . "<a class='btn btn-link' href='#'>"
            . "<img alt='supp' src='assets/img/delete.png'/>"
            . "</a>"
            . "</div>"
            . "</div>"
            . "</div>"
            . "</div>";
    }
    return $result;
}
