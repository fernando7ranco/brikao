<?php
$idUsuario = isset($_SESSION['idUsuario']) ? $_SESSION['idUsuario'] : 0;

include '../model/dataBase/bancoDeDados.php';
include '../assets/arquivos_php/todasFuncoes.php';

$banco = new BancoDeDados;

if (isset($_POST['bloquear'])) {

    $idBloqueado = $_POST['idBloqueado'];

	include '../model/domain/usuario.php';
	include '../model/dao/usuario.php';

	$usuarioDAO = new UsuarioDAO($banco);
	$usuarioDAO->bloquearUsuario($idUsuario, $idBloqueado);
	$banco->fechaConexao();
	exit;
}

include '../model/domain/anuncio.php';
include '../model/dao/anuncio.php';
include '../model/domain/comentario.php';
include '../model/dao/comentario.php';
include '../model/dao/notificacoes.php';

if (isset($_POST['denunciar'])) {
    $idAnuncio = $_GET['anuncio'];
    $tipo = $_POST['tipo'];
    $descricao = $_POST['descricao'];

    $anuncioDAO = new AnuncioDAO($banco);
    $anuncio = $anuncioDAO->denuncairAnuncio($idAnuncio, $tipo, $descricao);
    $banco->fechaConexao();
    exit;
}

if (isset($_POST['insertComentario']) and $idUsuario) {
	
    $comentarioDAO = new ComentarioDAO($banco);
    $dados = [1 => $_POST['idAnuncio'], 2 => $idUsuario, 4 => $_POST['comentario']];

    $comentario = new Comentario($dados);
    $comentario = $comentarioDAO->inserirComentario($comentario);
    $banco->fechaConexao();
    echo $comentario;
    exit;
}

if (isset($_POST['inserirComentarioResposta']) and $idUsuario) {

    $comentarioDAO = new ComentarioDAO($banco);
    $dados = [1 => $_POST['idAnuncio'], 2 => $idUsuario, 3 => $_POST['idComentario'], 4 => $_POST['comentario']];
    $comentario = new Comentario($dados);
    $comentario = $comentarioDAO->inserirResposta($comentario);
    $banco->fechaConexao();
    echo$comentario;
    exit;
}

if (isset($_POST['deleteComentarios']) and $idUsuario) {

    $comentarioDAO = new ComentarioDAO($banco);
    $comentario = $comentarioDAO->deletarComentarios($_POST['idComentario']);
    $banco->fechaConexao();
    echo$comentario;
    exit;
}
if (isset($_POST['carregarMaisComentarios']) and $idUsuario) {

    $comentarioDAO = new ComentarioDAO($banco);
    $comentario = new Comentario([0 => $_POST['idAnuncio'], 2 => $idUsuario]);
    $comentarios = $comentarioDAO->selecionarComentarios($comentario, $_POST['numComentarios']);
    $banco->fechaConexao();
    foreach ($comentarios as $idx => $comentario)
        echo $comentario;
    exit;
}

if (!isset($_GET['anuncio']) or ! is_numeric($_GET['anuncio'])) {
    header('location:anuncios.php');
    exit;
}
$idAnuncio = $_GET['anuncio'];


$anuncioDAO = new AnuncioDAO($banco);
$anuncio = $anuncioDAO->selectAnuncios($idAnuncio)[0];

$bloqueado = $banco->executaQuery("SELECT id FROM bloqueio WHERE (id_bloqueado = {$idUsuario} AND id_bloqueou = {$anuncio->getAnunciante()}) or (id_bloqueado = {$anuncio->getAnunciante()} AND id_bloqueou = {$idUsuario} )")->num_rows;

if (!is_object($anuncio) or $bloqueado){
	$banco->fechaConexao();
    header('location:anuncios.php');
	exit;
}

if(is_categoria($anuncio->getCategoria(),'moto')){
    include '../model/domain/moto.php';
    include '../model/dao/moto.php';
	
    $motoDAO = new MotoDAO($banco);
    $anuncio = $motoDAO->selecionaMotoId($anuncio->getId());           
}

if(is_categoria($anuncio->getCategoria(),'carro')){
    include '../model/domain/carro.php';
    include '../model/dao/carro.php';

    $carroDAO = new CarroDAO($banco);
    $anuncio = $carroDAO->selecionaCarroId($anuncio->getId());           
}

$anuncioDAO->visualizaAnuncio($anuncio->getId(), $idUsuario);

$comentarioDAO = new ComentarioDAO($banco);
$comentarios = new Comentario([0 => $anuncio->getId(), 2 => $idUsuario]);
$comentarios = $comentarioDAO->selecionarComentarios($comentarios, 0);
$banco->fechaConexao();
