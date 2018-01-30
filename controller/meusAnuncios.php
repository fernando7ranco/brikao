<?php

$idUsuario = $_SESSION['idUsuario'];

include '../model/dataBase/bancoDeDados.php';
include '../model/domain/anuncio.php';
include '../model/dao/anuncio.php';
include '../assets/arquivos_php/todasFuncoes.php';

$banco = new BancoDeDados;

if (isset($_POST['excluir'])) {
    $dados = $_POST['dados'];

    $idAnuncio = $dados['idAnuncio'];
    $feedback[] = $dados['motivo'];
    $feedback[] = isset($dados['tempoVenda']) ? $dados['tempoVenda'] : 0;

    $anunciosDAO = new AnuncioDAO($banco);
    $anuncio = new Anuncio([0 => $idAnuncio, 1 => $idUsuario]);
    $anunciosDAO->excluirAnuncio($anuncio, $feedback);
    $banco->fechaConexao();
    exit;
}

$pagina = input('GET', 'pagina', 'INT', '+');
$pagina = $pagina ? $pagina : 1;

$anuncioDAO = new AnuncioDAO($banco);

$anuncios = $anuncioDAO->selectAnuncios($idUsuario,false,$pagina);

$ultimoIndice = count($anuncios) - 1;
$numeroDeAnuncios = $anuncios[$ultimoIndice];
array_splice($anuncios, $ultimoIndice, 1);

$banco->fechaConexao();
