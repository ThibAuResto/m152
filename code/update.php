<?php
require_once "assets/php/functions.inc.php";

$submit = filter_input(INPUT_POST, 'submit', FILTER_SANITIZE_STRING);
$textArea = filter_input(INPUT_POST, 'textArea', FILTER_SANITIZE_STRING);
$updatedImages = filter_input(INPUT_POST, 'updatedImages', FILTER_SANITIZE_STRING);


$idPost = filter_input(INPUT_GET, "idPost", FILTER_VALIDATE_INT);
if (empty($idPost))
    die("Vous n'êtes pas autorisé à accéder à cette page");
$posts = ReadPostById($idPost);

$medias = $_FILES['mediaFiles'];
define("UPLOAD_DIR", "assets/uploads/");

$tmpMedias = array();
$tmpSize = 0;
$result = "";
$error = "";

/*
// Press Submit
if (isset($submit) && !empty($submit)) {
    // A file is detected?
    if (isset($medias) && !empty($medias)) {
        // Add the size to know when the size is 70 mega
        $tmpSize = GetSizeOfTheUpload($medias);

        // Create temporary array with the name, the type and the size
        for ($i = 0; $i < count($medias['name']); $i++) {
            // The media is under 3 megabytes, is it an image and the total is under 70 mega?
            if ($medias['size'][$i] < 3 * pow(10, 6) && strpos($medias["type"][$i], "image/") !== false || strpos($medias["type"][$i], "video/") !== false || strpos($medias["type"][$i], "audio/") !== false && $tmpSize <= 70 * pow(10, 6)) {
                $name = GetRandomString();
                // if the file has been move
                if (move_uploaded_file($medias['tmp_name'][$i], UPLOAD_DIR . $name)) {
                    array_push($tmpMedias, array(
                        'name' => $name,
                        'type' => $medias['type'][$i]
                    ));
                    InsertMediaAndPost($tmpMedias, $textArea, $name, UPLOAD_DIR);
                    $tmpMedias = array();
                    // Result
                    $result = "Le post a bien été pris en compte.";
                }
            } else // If an error is detected
                $error = "Une erreur a été détectée lors de l'ajout du/des média(s).";
        }
    }
}*/
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Post - Thibault Capt</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"/>

    <!-- Custom styles for this template -->
    <link href="assets/css/post.css" rel="stylesheet"/>
</head>
<body class="text-center">
<main class="form-signin">
    <form action="update.php?idPost=<?= $idPost ?>" method="post" enctype="multipart/form-data">

        <!-- header -->
        <img class="mb-4" src="assets/img/image.svg" alt="Image" width="96" height="96"/>
        <h1 class="h3 mb-3 fw-normal">Post</h1>

        <?= CreateUpdateForm($posts, UPLOAD_DIR) ?>

        <!-- Buttons -->
        <input class="w-100 mb-1 btn btn-lg btn-success" type="submit" name="submit" value="Soumettre"/>
        <a class="w-100 btn btn-lg btn-danger" type="submit" href="index.php">Retour à l'accueil</a>
        <p class="text-success"><?= $result ?></p>
        <p class="text-danger"><?= $error ?></p>
    </form>
</main>
</body>
</html>
