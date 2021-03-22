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
    $sql = "SELECT * FROM post INNER JOIN media ON post.idPost = media.idPost ORDER BY media.creationDate DESC LIMIT 100";
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
 * Read the post by his id
 *
 * @param $idPost
 * @return array|false
 */
function ReadPostById($idPost)
{
    static $ps = null;
    $sql = "SELECT * FROM media as m INNER JOIN post as p ON m.idPost=p.idPost WHERE m.idPost=:IDPOST";
    try {
        if ($ps == null)
            $ps = connectDB()->prepare($sql);

        $ps->bindParam(":IDPOST", $idPost, PDO::PARAM_STR);
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
function InsertPost($textArea): bool
{
    if (LastPost()["commentaire"] === $textArea)
        return false;
    static $ps = null;
    $sql = "INSERT INTO `post` (`commentaire`) VALUES (:COMMENTAIRE)";
    try {
        if ($ps == null)
            $ps = connectDB()->prepare($sql);

        $ps->bindParam(":COMMENTAIRE", $textArea, PDO::PARAM_STR);
        if ($ps->execute())
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
 * Get the name of the posts for unlink in the tmp folder
 *
 * @param $idPost
 * @return array|false
 */
function GetNameOfThePostsUsingIdPost($idPost)
{
    static $ps = null;
    $sql = "SELECT nomMedia FROM media WHERE idPost = :IDPOST";
    try {
        if ($ps == null)
            $ps = connectDB()->prepare($sql);

        $ps->bindParam("IDPOST", $idPost, PDO::PARAM_INT);

        if ($ps->execute())
            return $ps->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

/**
 * Delete the media
 *
 * @param $idPost
 * @return bool
 */
function DeleteMedia($idPost): bool
{
    static $ps = null;
    $sql = "DELETE FROM media WHERE idPost=:IDPOST";
    try {
        if ($ps == null)
            $ps = connectDB()->prepare($sql);

        $ps->bindParam(":IDPOST", $idPost, PDO::PARAM_INT);
        return $ps->execute();
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

/**
 * Delete the post
 *
 * @param $idPost
 * @return bool
 */
function DeletePost($idPost): bool
{
    static $ps = null;
    $sql = "DELETE FROM post WHERE idPost=:IDPOST";
    try {
        if ($ps == null)
            $ps = connectDB()->prepare($sql);

        $ps->bindParam(":IDPOST", $idPost, PDO::PARAM_INT);
        return $ps->execute();
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

/**
 * Delete the file in the folder
 *
 * @param $nameOfPosts
 * @param $uploadsDir
 */
function UnlinkMediaInFolder($nameOfPosts, $uploadsDir)
{
    try {
        for ($i = 0; $i < count($nameOfPosts); $i++)
            unlink($uploadsDir . $nameOfPosts[$i]["nomMedia"]);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

/**
 * Transaction Delete media and post
 *
 * @param $idPost
 * @param $nameOfPosts
 * @param $uploadsDir
 * @return bool
 */
function DeleteMediaAndPost($idPost, $nameOfPosts, $uploadsDir): bool
{
    static $dbh = null;
    if ($dbh == null)
        $dbh = connectDB();
    $dbh->beginTransaction();
    try {
        DeleteMedia($idPost);
        DeletePost($idPost);
        UnlinkMediaInFolder($nameOfPosts, $uploadsDir);
        return $dbh->commit();
    } catch (Exception $e) {
        echo $e->getMessage();;
        return $dbh->rollBack();
    }
}

/**
 * If we have an error with the transaction, remove the media in the folder
 *
 * @param $name
 * @param $uploadsDir
 */
function RemoveLastInsertPostInTheFolder($name, $uploadsDir)
{
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
 * @param $uploadDir
 * @return string
 */
function WriteAllPost($posts, $uploadDir): string
{
    $result = "";
    foreach ($posts as $p) {
        $result .= "<div class='panel panel-default'>"
            . "<div class='panel-heading'>"
            . "<div class='panel panel-default'>"
            . "<div class='panel-thumbnail'>";
        foreach ($p['medias'] as $m) {
            if (strpos($m["type"], "image/") !== false) // image
                $result .= "<img alt='Post' width='640' height='480' src='" . $uploadDir . $m['media'] . "' class='img-responsive'/>";
            else if (strpos($m['type'], "video/") !== false) { // video
                $result .= "<video width='640' height='480' controls autoplay loop muted class='img-responsive'>
                                <source src='" . $uploadDir . $m['media'] . "' type='" . $p['type'] . "'>
                                Your browser does not support the video tag.
                            </video>";
            } else if (strpos($m['type'], "audio/") !== false) { // audio
                $result .= "<audio controls>
                                <source src='" . $uploadDir . $m['media'] . "' type='" . $p['type'] . "'>
                                Your browser does not support the audio element.
                            </audio>";
            }
        }
        $result .= "</div>"
            . "<div class='panel-body'>"
            . "<p class='lead'>" . $p['commentaire'] . "</p>"
            . "<a class='btn btn-link' href='update.php?idPost=" . $p['idPost'] . "'>"
            . "<img alt='modif' src='assets/img/editing.png'/>"
            . "</a>"
            . "<a class='btn btn-link' href='delete.php?&idPost=" . $p['idPost'] . "'>"
            . "<img alt='supp' src='assets/img/delete.png'/>"
            . "</a>"
            . "</div>"
            . "</div>"
            . "</div>"
            . "</div>";
    }
    return $result;
}

/**
 * Create a fill the form for the update
 *
 * @param $posts
 * @param $uploadDir
 * @return string
 */
function CreateUpdateForm($posts, $uploadDir): string
{
    $result = "";

    //textArea
    $result .= "<label for='textArea' class='visually-hidden'>textArea</label>";
    $result .= "<textarea class='mb-3 form-control' id='textArea' name='textArea' required autofocus>" . $posts[0]['commentaire'] . "</textarea>";
    // Checkboxes
    $result .= "<div class='row'>";
    for ($i = 0; $i < count($posts); $i++) {
        $result .= "<div class='col-md-3'>";
        $result .= "<div class='custom-control custom-checkbox image-checkbox'>";
        $result .= "<input type='checkbox' class='custom-control-input' id='ck" . $i . "' checked>";
        $result .= "<label class='custom-control-label' for='ck" . $i . "'>";
        if (strpos($posts[$i]['typeMedia'], "image/") !== false) // image
            $result .= "<img src='" . $uploadDir . $posts[$i]['nomMedia'] . "' alt='" . $posts[$i]['nomMedia'] . "' class='img-fluid'/>";
        else if (strpos($posts[$i]['typeMedia'], "video/") !== false) {
            $result .= "<video class='img-fluid' autoplay loop muted class='img-responsive'>";
            $result .= "<source src='" . $uploadDir . $posts[$i]['nomMedia'] . "' type='" . $posts[$i]['typeMedia'] . "'>";
            $result .= "Your browser does not support the video tag.";
            $result .= "</video>";
        }
        $result .= "</label>";
        $result .= "</div>";
        $result .= "</div>";
    }
    $result .= "</div>";


    // Files
    $result .= "<label for='inputFile' class='visually-hidden'>Media</label>";
    $result .= "<input type='file' name='mediaFiles[]' id='inputFile' class='mb-3 form-control-file' accept='image/*, video/*, audio/*' multiple required/>";
    return $result;
}
