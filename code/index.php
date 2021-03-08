<?php
require_once "assets/php/functions.inc.php";

$posts = ReadAllPost();
$new = array();
$compteur = 0;

// Create the new array with the images connect with the post
for ($i = 0; $i < count($posts); $i++) {
    if (!array_key_exists($posts[$i]["idPost"], $new)) {
        $new[$posts[$i]["idPost"]] = array(
            'commentaire' => $posts[$i]["commentaire"],
            'medias' => array(
                array(
                    'media' => $posts[$i]["nomMedia"],
                    'type' => $posts[$i]["typeMedia"]
                )
            )
        );
    } else
        array_push($new[$posts[$i]["idPost"]]["medias"], array(
            'media' => $posts[$i]["nomMedia"],
            'type' => $posts[$i]["typeMedia"]
        ));
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta charset="utf-8"/>
    <title>M152 - Thibault Capt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <link href="assets/css/bootstrap.css" rel="stylesheet"/>
    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link href="assets/css/style.css" rel="stylesheet"/>
</head>

<body>
<div class="wrapper">
    <div class="box">
        <div class="row row-offcanvas row-offcanvas-left">
            <!-- main right col -->
            <div class="column col-sm-10 col-xs-11" id="main">
                <?php require_once(__DIR__ . "/assets/php/nav.inc.php"); ?>
                <div class="padding">
                    <div class="full col-sm-9">
                        <!-- content -->
                        <div class="row">
                            <!-- main col left -->
                            <div class="col-sm-5">
                                <div class="panel panel-default">
                                    <div class="panel-thumbnail">
                                        <img src="assets/img/bg_5.jpg" class="img-responsive"/>
                                    </div>
                                    <div class="panel-body">
                                        <p class="lead">Thibault Capt</p>
                                        <p>45 Followers, 13 Posts</p>

                                        <p>
                                            <img src="assets/img/uFp_tsTJboUY7kue5XAsGAs28.png" height="28px"
                                                 width="28px"/>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- main col right -->
                            <div class="col-sm-7">
                                <div class="well">
                                    <h1>Welcome</h1>
                                </div>

                                <?= WriteAllPost($new) ?>
                                <!--/row-->

                                <div class="row">
                                    <div class="col-sm-6">
                                        <a href="#">Twitter</a>
                                        <small class="text-muted">|</small>
                                        <a href="#">Facebook</a>
                                        <small class="text-muted">|</small>
                                        <a href="#">Google+</a>
                                    </div>
                                </div>

                                <div class="row" id="footer">
                                    <div class="col-sm-6"></div>
                                    <div class="col-sm-6">
                                        <p>
                                            <a href="#" class="pull-right">Copyright 2013</a>
                                        </p>
                                    </div>
                                </div>
                                <hr/>
                                <h4 class="text-center">
                                    <a href="http://usebootstrap.com/theme/facebook" target="ext">Download this Template
                                        @Bootply</a>
                                </h4>
                                <hr/>
                            </div>
                        </div>
                    </div>

                    <!--post modal-->
                    <div id="postModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">Update
                                        Status
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form class="form center-block">
                                        <div class="form-group">
                                            <textarea class="form-control input-lg" autofocus=""
                                                      placeholder="What do you want to share?"></textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <div>
                                        <button class="btn btn-primary btn-sm" data-dismiss="modal" aria-hidden="true">
                                            Post
                                        </button>
                                        <ul class="pull-left list-inline">
                                            <li>
                                                <a href=""><i class="glyphicon glyphicon-upload"></i></a>
                                            </li>
                                            <li>
                                                <a href=""><i class="glyphicon glyphicon-camera"></i></a>
                                            </li>
                                            <li>
                                                <a href=""><i class="glyphicon glyphicon-map-marker"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script type="text/javascript" src="assets/js/jquery.js"></script>
                    <script type="text/javascript" src="assets/js/bootstrap.js"></script>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $("[data-toggle=offcanvas]").click(function () {
                                $(this).toggleClass("visible-xs text-center");
                                $(this)
                                    .find("i")
                                    .toggleClass("glyphicon-chevron-right glyphicon-chevron-left");
                                $(".row-offcanvas").toggleClass("active");
                                $("#lg-menu").toggleClass("hidden-xs").toggleClass("visible-xs");
                                $("#xs-menu").toggleClass("visible-xs").toggleClass("hidden-xs");
                                $("#btnShow").toggle();
                            });
                        });
                    </script>
</body>

</html>