<?php

if (!isset($_POST['acao']))
    exit;

$acao = $_POST['acao'];

if ($acao == 'cidadesEstados') {

    include '../../model/dataBase/matrizes/matrizEstadosCidades.php';

    echo json_encode([$estados, $cidades]);
    exit;
}


include '../../model/dataBase/bancoDeDados.php';
include 'todasFuncoes.php';

$banco = new BancoDedados;

if ($acao == 'categorias') {
	
	$id = $_POST['id'];
	$tipo = $_POST['tipo'];
	
	if($tipo == 1)
		$query = $banco->executaQuery("SELECT id,nome FROM categoria_segundaria WHERE id_primaria = {$id} ORDER BY nome");
	else if($tipo == 2)
		$query = $banco->executaQuery("SELECT id,nome FROM categoria_terciaria WHERE id_segundaria = {$id} ORDER BY nome");
		
	
	$options = null;
	while($linhas = $query->fetch_array()){
		$options.= "<option value='{$linhas['id']}' >{$linhas['nome']}</option>";	
	}
	
	echo $options;
	$banco->fechaConexao();
    exit;
}

if ($acao == 'autocompleteBairro') {
	
	$estado = $_POST['estado'];
	$cidade = $_POST['cidade'];
	$bairro = $_POST['bairro'];
	
	$sql = "SELECT DISTINCT bairro FROM anuncios WHERE estado = '{$estado}' AND cidade = '{$cidade}' AND bairro LIKE '{$bairro}%' ORDER BY bairro";
	$query = $banco->executaQuery($sql);
	$return = null;
	while($linha = $query->fetch_array()){
		$return.= "<a>{$linha['bairro']}</a>";	
	}
	echo $return ? $return : "<span>nenhum bairro encontrado na cidade atual<span>";
    $banco->fechaConexao();
    exit;
}

if ($acao == 'localizarEmail') {
    $email = $_POST['email'];
    include '../../model/dao/usuario.php';
    $usuariosDAO = new UsuarioDAO($banco);
    echo $usuariosDAO->localizarEmail($email);
    $banco->fechaConexao();
    exit;
}

session_start();
if (!isset($_SESSION['idUsuario']))
    exit;

$idUsuario = $_SESSION['idUsuario'];

if ($acao == 'notificacoes'){
	
    include '../../model/dao/notificacoes.php';
    $NotificacoesDAO = new NotificacoesDAO($banco);

    if ($_POST['qual'] == 1)
        echo $NotificacoesDAO->selecionaNotificacoes($idUsuario);
    else
        echo $NotificacoesDAO->numeroNotificacoes($idUsuario);

    $banco->fechaConexao();
    exit;
}
?>