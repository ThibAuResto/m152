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
            $ps->bindParam(":IDPOST", $id, PDO::PARAM_INT);
            $ps->execute();
        }
        return true;
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
 * @param $uploadsDir
 * @return mixed
 */
function InsertMediaAndPost($files, $textArea, $uploadsDir)
{
    static $dbh = null;
    if ($dbh == null)
        $dbh = connectDB();
    $dbh->beginTransaction();
    try {
        InsertPost($textArea);
        InsertMedia($files, GetLastPost()["idPost"]);
        return $dbh->commit();
    } catch (Exception $e) {
        echo $e->getMessage();
        RemoveLastInsertPostInTheFolder($files, $uploadsDir);
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
 * Delete a media by his name
 *
 * @param $name
 * @return bool
 */
function DeleteMediaByName($name): bool
{
    static $ps = null;
    $sql = "DELETE FROM media WHERE nomMedia=:NOMMEDIA";
    try {
        if ($ps == null)
            $ps = connectDB()->prepare($sql);

        $ps->bindParam(":NOMMEDIA", $name, PDO::PARAM_STR);
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
 * @param $files
 * @param $uploadsDir
 */
function RemoveLastInsertPostInTheFolder($files, $uploadsDir)
{
    try {
        foreach($files['name'] as $f) {
            $path = $uploadsDir . DIRECTORY_SEPARATOR . $f;
            unlink($path);
        }
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
 * Insert a new media in the database
 *
 * @param $commentaire
 * @param $idPost
 * @return bool
 */
function UpdatePost($commentaire, $idPost): bool
{
    $date = date_format(date_create(), "Y-m-d H:m:s");

    static $ps = null;
    $sql = "UPDATE post SET commentaire = :COMMENTAIRE, modificationDate = :MODIFICATIONDATE WHERE idPost = :IDPOST";
    try {
        if ($ps == null)
            $ps = connectDB()->prepare($sql);

            $ps->bindParam(":COMMENTAIRE", $commentaire, PDO::PARAM_STR);
            $ps->bindParam(":MODIFICATIONDATE", $date, PDO::PARAM_STR);
            $ps->bindParam(":IDPOST", $idPost, PDO::PARAM_INT);

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
 * @param $uploadsDir
 * @param $idPost
 * @return mixed
 */
function UpdateMediaAndPost($files, $textArea, $uploadsDir, $idPost)
{
    static $dbh = null;
    if ($dbh == null)
        $dbh = connectDB();
    $dbh->beginTransaction();
    try {
        UpdatePost($textArea, $idPost);
        InsertMedia($files, $idPost);
        return $dbh->commit();
    } catch (Exception $e) {
        echo $e->getMessage();
            RemoveLastInsertPostInTheFolder($files, $uploadsDir);
        return $dbh->rollBack();
    }
}

/**
 * Compare the two arays to find out if the user has uncheck images
 *
 * @param $uploaded
 * @param $posts
 * @return array
 */
function CompareMedias($uploaded, $posts): array
{
    $tmpArray = array();
    for($i = 0; $i < count($posts); $i++)
        array_push($tmpArray, $posts[$i]['nomMedia']);

    if (count($uploaded) < count($tmpArray)) {
        foreach ($tmpArray as $tmp) {
            for ($j = 0; $j < count($uploaded); $j++) {
                if ($tmp === $uploaded[$j])
                    unset($tmpArray[$j]);
            }
        }
    } else $tmpArray = array();
    return $tmpArray;
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
        $result .= "<input type='checkbox' class='custom-control-input' id='ck" . $i . "' value='" . $posts[$i]['nomMedia'] . "' name='updatedImages[]' checked>";
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
    $result .= "<input type='file' name='mediaFiles[]' id='inputFile' class='mb-3 form-control-file' accept='image/*, video/*, audio/*' multiple/>";
    return $result;
}
