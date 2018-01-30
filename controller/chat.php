<?php
$idUsuario = $_SESSION['idUsuario'];

include '../model/dataBase/bancoDeDados.php';
include '../model/domain/chat.php';
include '../model/dao/chat.php';
include '../model/dao/notificacoes.php';
include '../assets/arquivos_php/todasFuncoes.php';

$banco = new BancoDeDados;

if (isset($_POST['bloquear'])) {

    $idBloqueado = $_POST['idBloqueado'];

	include '../model/domain/usuario.php';
	include '../model/dao/usuario.php';
	
	$usuariosDAO = new UsuariosDAO($banco);
	$usuariosDAO->bloquearUsuario($idUsuario, $idBloqueado);
	$banco->fechaConexao();
	exit;
}

$chatDAO = new ChatDAO($banco);

if (isset($_POST['denunciar'])) {
	echo $chatDAO->denunciarUsuario($_POST['idDenunciado'], $idUsuario, $_POST['tipo']);
	$banco->fechaConexao();
	exit;
}

if (isset($_POST['pegaMsg'])) {
    $dados[1] = $_POST['idAnuncio'];
    $dados[2] = $idUsuario;
    $dados[3] = $_POST['idUsuarioPara'];

    $idCondicao = $_POST['idCondicao'];
    $condicao = $_POST['condicao'];

    $chat = new Chat($dados);
    echo $chatDAO->selecionaMsgChat($chat, $condicao, $idCondicao);
    $banco->fechaConexao();
    exit;
}

if (isset($_POST['enviarMsg'])) {
    $dados[1] = $_POST['idAnuncio'];
    $dados[2] = $idUsuario;
    $dados[3] = $_POST['idUsuarioPara'];
    $dados[4] = $_POST['mensagem'];

    $chat = new Chat($dados);
	$chatDAO->insertMensagem($chat);
    $banco->fechaConexao();
    exit;
}

if (isset($_POST['uploadArquivo'])) {
    $dados[1] = $_POST['idAnuncio'];
    $dados[2] = $idUsuario;
    $dados[3] = $_POST['idUsuarioPara'];
    $dados[5] = $_FILES['arquivo'];
	
    $chat = new Chat($dados);
    $chatDAO->uploadArquivo($chat);
    $banco->fechaConexao();
    exit;
}

if (isset($_POST['excluirChat'])) {
    $dados[1] = $_POST['idAnuncio'];
    $dados[2] = $idUsuario;
    $dados[3] = $_POST['idUsuarioPara'];

    $chat = new Chat($dados);
    echo $chatDAO->excluirChat($chat);
    $banco->fechaConexao();
    exit;
}
$chats = null;

if (input('GET', 'anuncio', 'INT', '+')) {
    $dados[1] = $_GET['anuncio'];
    $dados[2] = $idUsuario;

    $chat = new Chat($dados);
    $chats = $chatDAO->selecionaPreChat($chat);

    if ($chats === null)
        unset($idAnuncio);
}

$chats.= $chatDAO->selecionaChat(new Chat([2 => $idUsuario]));

$banco->fechaConexao();

if (isset($_POST['atualizaChat'])) {
    echo $chats;
    exit;
}
