<?php
include_once(BASE_DIR.'includes/menu-options.php');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Jyotirmoy Saha">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?=WEBSITE_TITLE?></title>
    <?php include_once(BASE_DIR . 'includes/admin_url_to_css.php'); ?>
</head>

<body>

    <div class="wrapper">
        <!-- Sidebar  start-->
        <?php include_once(BASE_DIR . 'includes/sidebar.php'); ?>
        <!-- Sidebar  end-->
        
        <!-- Page Content  -->
        <div id="content">
        <!-- Main Menu  start-->
        <?php include_once(BASE_DIR . 'includes/main-menu.php'); ?>
        <!-- Main Menu  end-->
            