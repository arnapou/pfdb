<?php
/*
 * This file is part of the Arnapou PFDB package.
 *
 * (c) Arnaud Buathier <arnaud@arnapou.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$pages = array(
    'demo1' => 'Conditions',
    'demo2' => 'Updating / Deleting',
    'demo3' => 'Entities',
);

if (isset($_GET['page']) && in_array($_GET['page'], array_keys($pages), true)) {
    $current = $_GET['page'];
}
if (!isset($current)) {
    foreach ($pages as $page => $title) {
        $current = $page;
        break;
    }
}
?><!DOCTYPE html>
<html>
    <head>
        <title>PFDB</title>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="//code.jquery.com/jquery-1.10.1.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="#">PFDB</a>
                    </div>

                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                        <?php foreach ($pages as $page => $title): ?>
                            <li<?= ($current == $page ? ' class="active"' : '') ?>><a href="?page=<?= $page ?>"><?= $title ?></a></li>
                        <?php endforeach; ?>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li>
                                <a href="https://github.com/arnapou/pfdb"><i class="github-icon"></i> Github</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <?php include __DIR__.'/'.$current.'.php' ?>

        </div>
        <script type="text/javascript">
            var currentPage = <?= json_encode($pages[$current]) ?>;
        </script>
    </body>
</html>
