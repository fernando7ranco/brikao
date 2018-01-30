<?php

include '../model/dataBase/bancoDedados.php';

if(isset($_POST) and count($_POST) ){
    include '../model/domain/usuario.php';
    include '../model/dao/usuario.php';
    include '../assets/arquivos_php/todasFuncoes.php';
}
 
if (isset($_POST['login']) ) {
    
    $dados = $_POST;
    $email = $dados['email'];
    $senha = $dados['senha'];

    $banco = new BancoDedados;
    $usuario = new Usuario([1 => $email, 2 => $senha]);
    $usuarioDAO = new UsuarioDAO($banco);
    $retorno = $usuarioDAO->loginUsuario($usuario);
    $banco->fechaConexao();

    if (!is_numeric($retorno))
        exit($retorno);

	if(isset($dados['manterLogado']))
		$usuarioDAO->manterLogado($retorno);
	
	exit;
    
}

if (isset($_POST['cadastro'])) {

    $dados = $_POST['dados'];
    
    $email = $dados['email'];
    $senha1 = $dados['senha1'];
    $senha2 = $dados['senha2'];
    
    $tipoCadastro = 1;
    $identificadorCadastro = '';

    $usuario = new Usuario([1 => $email, 2 => $senha1, 12 => $tipoCadastro, 13 => $identificadorCadastro]);
    $usuario->validaDados();

    if ($usuario->getEmail() and $usuario->getSenha() and $usuario->getSenha() == $senha2) {

        $banco = new BancoDedados;
        $usuarioDAO = new UsuarioDAO($banco);
        $email = $usuarioDAO->localizarEmail($email);
        if ($email == 0) {       
            $retorno = $usuarioDAO->insertUsuario($usuario);
            if (!$retorno) 
                echo 3;
        } else
            echo 2;
        $banco->fechaConexao();
    } else
        echo 1;
       
    exit;
}


if (isset($_POST['apps'])) {

    $dados = $_POST['dados'];

    $nome = $dados['name'];
    $email = isset($dados['email']) ? $dados['email'] : null;
    $identificadorCadastro = $dados['id'];
    $tipoCadastro = $dados['tipo'];

    $banco = new BancoDedados;
    $usuarioDAO = new UsuarioDAO($banco);

    $jaEsta = $usuarioDAO->qualCadastro($tipoCadastro, $identificadorCadastro);
    
    if ($jaEsta) {
        $banco->fechaConexao();
        exit('1');
    }else{
		$val2 = isset($email) ? $usuarioDAO->localizarEmail($email) : false;
		
		if ($val2) {
			$banco->fechaConexao();
			exit('2');
		}
	}

    $usuario = new Usuario([1 => $email, 3 => $nome, 12 => $tipoCadastro, 13 => $identificadorCadastro]);
    $usuario->validaDados();
    $retorno = $usuarioDAO->insertUsuario($usuario);

    if ($retorno > 0) {
        echo '3';
    }else
        echo '4';

    $banco->fechaConexao();
    exit;
}


if(isset($_COOKIE["brikao"])){
    include '../assets/arquivos_php/todasFuncoes.php';
    $id = codificacao($_COOKIE["brikao"], 12, 2);
    if(is_numeric($id)){
        $_SESSION['idUsuarios'] = $id;
        header('location:anuncios.php');
        exit;
    }
}

$banco = new BancoDedados;

$hoje = date('Y-m-d');

$datas = "data >= '{$hoje} 00:00:00' AND data <= '{$hoje} 23:59:59'";

$numeros['inserido'] = $banco->executaQuery("SELECT id FROM anuncios WHERE {$datas}")->num_rows;
$numeros['vendido'] = $banco->executaQuery("SELECT id FROM feedback_exclusao_anuncio WHERE motivo = 1 AND {$datas}")->num_rows;

include '../model/dataBase/matrizes/matrizEstadosCidades.php';
$listaDeEstados = null;

foreach($estados as $uf => $estado){
	$num = $banco->executaQuery("SELECT id FROM anuncios WHERE estado = '{$uf}'")->num_rows;
	$num = $num ? $num : '';
	$listaDeEstados.= "<div><a href='anuncios.php?estado={$uf}'>{$estado} {$num}</a></div>";
}

$banco->fechaConexao();

$sl = isset($_GET['CADASTRO']) ? "style='display:none'" : null;
$sc = !isset($_GET['CADASTRO']) ? "style='display:none'" : null;
$il = !isset($_GET['CADASTRO']) ? 'foco' : 'semFoco';
$ic = isset($_GET['CADASTRO']) ? 'foco' : 'semFoco';

if (isset($_GET['CADASTRO']))
    $script = '<script>$(document).ready(function(){$("#cadastro input[type=email]").focus()})</script>';
