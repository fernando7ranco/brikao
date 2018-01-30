<?php

session_start();

if(isset($_SESSION['idUsuario'])){
	header('location:anuncios.php');
	exit;
}

if(isset($_POST['emailPRS'])){

	include '../model/dataBase/bancoDeDados.php';
	include '../model/domain/usuario.php';
	include '../model/dao/usuario.php';
	include '../assets/arquivos_php/todasFuncoes.php';
	
	$banco = new BancoDeDados;
	$usuarioDAO = new UsuarioDAO($banco);
	echo $usuarioDAO->recuperarSenha($_POST['emailPRS']);
	$banco->fechaConexao();
	exit;
	
}

if(isset($_POST['hash'])){

	include '../model/dataBase/bancoDeDados.php';
	include '../model/domain/usuario.php';
	include '../model/dao/usuario.php';
	
	$hash = $_POST['hash'];
	$senha1 = $_POST['senha1'];
    $senha2 = $_POST['senha2'];
	
	$banco = new BancoDeDados;
	$usuario = new Usuario([2 => $senha1]);
    $usuario->validaDados();

    if ($usuario->getSenha() and $usuario->getSenha() == $senha2){
		$id = $banco->executaQuery("SELECT usuario FROM recuperar_senha WHERE hash = '{$hash}'")->fetch_array()['usuario'];
		if($id){
			$usuario->setId($id);
			$banco->executaQuery("DELETE FROM `recuperar_senha` WHERE hash = '{$hash}'");
			$usuarioDAO = new UsuarioDAO($banco);
			$usuarioDAO->updateCadastro($usuario);
			$usuarioDAO->sessionStart($id);
		}
	}

	$banco->fechaConexao();
	header('location:anuncios.php');
	exit;
	
}

if(isset($_GET['password']) and $_GET['hash'] ){
	
	$hash = $_GET['hash'];
	
	include '../model/dataBase/bancoDeDados.php';
	
	$banco = new BancoDeDados;
	
	$banco->executaQuery("DELETE FROM `recuperar_senha` WHERE data <= NOW()");
	
	$nr = $banco->executaQuery("SELECT id FROM recuperar_senha WHERE hash = '{$hash}'")->num_rows;
	$banco->fechaConexao();
	
}

?>
<!doctype html>
<html lang='pt-br'>
	<head>
		<meta http-equiv="Content-Type" content="text/html"; charset="UTF-8"/>
		<title>Recuperação de Senha</title>
		<link rel="shortcut icon" type="img/x-icon" href="../assets/img/icones/favicon.png">
		<link rel='stylesheet' type="text/css" href='../assets/css/recuperarSenha.css'>
	</head>
	<body>
		<header>
			<a href='index.php' ><img src='../assets/img/icones/logo.png' id='logoBRIKAO'></a>
			Esqueceu a senha?
		</header>
		<div id='caixaCentro'>
		
			<?php if(!isset($hash)){ ?>
			
			<div id='recuperarSenha'>
				<p>
					Para recuperar sua senha é fácil e rapido, basta apenas você informar seu EMAIL de usuario, 
					que nós lhe enviaremos uma nova senha para seu endereço de email logado no BRIKÃO.
				</p>
				<label>Encontre sua conta</label><br>
				<input type='email' name='email' placeholder='email de usuario'>
				
				<button id='localizarUsuario' >pesquisar</button>
				<button id='cancelarLU' onclick="window.open('index.php','_self')" >cancelar</button>
				
				<div id='localResultado'></div>
			</div>
			
			<?php }else{ 
					if($nr){
			?>
				<p> altere sua senha </p>
				<form method='POST'>
					<input type='hidden' name='hash' value='<?=$hash?>'>
					<p><input type='password' name='senha1' placeholder='senha' maxlength='16' autocomplete='off' ></p>
					<p><input type='password' name='senha2' placeholder='corfimar senha' maxlength='16' autocomplete='off' ></p>
					<p><input type='checkbox' id='vsenha' title='visualizar senha'> visualizar senha</p>
					<button type='button'>alterar senha</button>
				</form>
			<?php 
					}else{
						echo "<h2>esse link expirou</h2>";
					}
				}
			?>
		</div>
		
		<div id='enviandoEmail'>
			<div id='centro'>
			</div>
		</div>
		
	</body>
	<script type="text/javascript" src="../assets/js/jquery_code.js"></script>
	<script type="text/javascript" src="../assets/js/recuperarSenha.js"></script>
	
</html>
