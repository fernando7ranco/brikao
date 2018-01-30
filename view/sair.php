<?php
session_start();
session_destroy();
setcookie("brikao",$_COOKIE['brikao'], time()-60*60*24*100, "/");
unset($_COOKIE["brikao"]);
header('location:index.php');
?>