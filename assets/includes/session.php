<?php 
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header('location:index.php');
    exit;
}

if(!$_SESSION['nomeUsuario'] && basename($_SERVER['PHP_SELF']) != 'cadastro.php') {
    header('location:cadastro.php?incompleto');
    exit;
}