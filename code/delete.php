<?php
require_once __DIR__ . "/assets/php/functions.inc.php";
$idPost = filter_input(INPUT_GET, 'idPost', FILTER_VALIDATE_INT);

if(isset($idPost) && !empty($idPost)){
    $idMedias = GetIdMediaUsingIdPost($idPost);
    $namePosts = GetNameOfThePostsUsingIdPost($idPost);

    if (DeleteMediaAndPost($idMedias, $idPost, $namePosts, "assets/uploads/"))
        header("Location: index.php");
    else
        die("Une erreur est survenue lors de la suppression du post.");
} else die("Vous n'êtes pas autorisé à accéder à cette page.");