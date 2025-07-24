<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

session_start();
$currentPage = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['user']) && $currentPage !== 'login.php') {
    header('Location: /login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
<style>
  html.loading #mainContent {
    visibility: hidden;
  }

  #mainPreloader {
    position: fixed;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    font-family: sans-serif;
    font-size: 1.2em;
    background-color: #fff;
    color: #333;
  }

  html.dark.loading #mainPreloader {
    background-color: #121212;
    color: white;
  }

  html.light.loading #mainPreloader {
    background-color: #ffffff;
    color: #333;
  }
</style>

<script>
  (function () {
    const theme = localStorage.getItem("theme");
    const html = document.documentElement;
    html.classList.add("loading");

    if (theme === "dark") {
      html.classList.add("dark");
    } else {
      html.classList.add("light");
    }
  })();
</script>


    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="./images/favicon.png">
    <!-- Page Title  -->
    <title>Cerene</title>
<script>
(function () {
  const theme = localStorage.getItem("theme");
  const html = document.documentElement;
  if (theme === "dark") {
    html.classList.add("dark-mode-init");
  } else {
    html.classList.remove("dark-mode-init");
  }
})();
</script>

    <!-- StyleSheets  -->
    <link rel="stylesheet" href="./assets/css/dashlite.css?ver=3.3.0">
    <link id="skin-default" rel="stylesheet" href="./assets/css/theme.css?ver=3.3.0">
</head>

<body class="nk-body ui-clean" theme="">
<div id="mainPreloader" style="
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: inherit;
  z-index: 9999;
  font-family: sans-serif;
  font-size: 1.2em;">
  <div class="js-preloader">
    <div class="loading-animation tri-ring"></div>
</div>
</div>

    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main " id="mainContent">
            <!-- sidebar @s -->
<?php
include_once 'navigation.php';
?>