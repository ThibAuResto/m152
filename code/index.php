<?php
require_once "assets/php/functions.inc.php";

$posts = ReadAllPost();
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
                <!-- top nav -->
                <div class="navbar navbar-blue navbar-static-top">
                    <nav class="collapse navbar-collapse" role="navigation">
                        <div class="navbar-header">
                            <button class="navbar-toggle" type="button" data-toggle="collapse"
                                    data-target=".navbar-collapse"></button>
                            <a href="index.php" class="navbar-brand logo">b</a>
                        </div>
                        <form class="navbar-form navbar-left">
                            <div class="input-group input-group-sm" style="max-width: 360px">
                                <input class="form-control" placeholder="Search" name="srch-term" id="srch-term"
                                       type="text"/>
                                <div class="input-group-btn">
                                    <button class="btn btn-default" type="submit">
                                        <i class="glyphicon glyphicon-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <ul class="nav navbar-nav">
                            <li>
                                <a href="#"><i class="glyphicon glyphicon-home"></i> Home</a>
                            </li>
                            <li>
                                <a href="post.php" role="button">
                                    <i class="glyphicon glyphicon-plus"></i>Post
                                </a>
                            </li>
                            <li>
                                <a href="#"><span class="badge">badge</span></a>
                            </li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="glyphicon glyphicon-user"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="">More</a></li>
                                    <li><a href="">More</a></li>
                                    <li><a href="">More</a></li>
                                    <li><a href="">More</a></li>
                                    <li><a href="">More</a></li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>

                <!-- /top nav -->
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

                                <?= WriteAllPost($posts) ?>
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