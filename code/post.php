<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Post - Thibault Capt</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    <!-- Custom styles for this template -->
    <link href="assets/css/post.css" rel="stylesheet" />
  </head>
  <body class="text-center">
    
    <main class="form-signin">
      <form>
        <img
          class="mb-4"
          src="assets/img/image.svg"
          alt="Image"
          width="96"
          height="96"
        />
        <h1 class="h3 mb-3 fw-normal">Post</h1>
        <label for="textArea" class="visually-hidden">textArea</label>
        <textarea
          class="mb-3 form-control"
          id="textArea"
        ></textarea>

        <label for="inputFile" class="visually-hidden">Image</label>
        <input
          type="file"
          name="image"
          id="inputFile"
          class="mb-3 form-control-file"
          accept="image/*"
          multiple
        />
        <button class="w-100 mb-1 btn btn-lg btn-success" type="submit">
          Soumettre
        </button>
        <a class="w-100 btn btn-lg btn-danger" type="submit" href="index.html">
          Retour à l'accueil
        </a>
      </form>
    </main>
  </body>
</html>