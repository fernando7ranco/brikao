<?php
$idUsuario = $_SESSION['idUsuario'];

include '../assets/arquivos_php/todasFuncoes.php';
include '../model/dataBase/bancoDeDados.php';
include '../model/domain/anuncio.php';
include '../model/dao/anuncio.php';
include '../model/domain/moto.php';
include '../model/dao/moto.php';
include '../model/domain/carro.php';
include '../model/dao/carro.php';

$banco = new BancoDeDados;

if (isset($_POST['upload'])) {
	
    $POST = $_POST;
	
    $dados = [null, $idUsuario];
	$dados[] = $POST['categoria'];
	$dados[] = $POST['titulo'];
	$dados[] = $_FILES;
	$dados[] = $POST['descricao'];
	$dados[] = $POST['valor'];
	$dados[] = $POST['telefone'];
	$dados[] = $POST['cep'];
	$dados[] = $POST['estado'];
	$dados[] = $POST['cidade'];
	$dados[] = $POST['bairro'];
	$dados[] = null; // seria a data
	
	if(is_categoria($POST['categoria'],'carro')){
		
		$dados[] = $POST['marca'];
		$dados[] = $POST['quilometragem'];
		$dados[] = $POST['ano'];
		$dados[] = $POST['portas'];
		$dados[] = $POST['cambio'];
		$dados[] = $POST['combustivel'];
		$dados[] = $POST['tipo'];
		$dados[] = $POST['opcionais'];
		
		$carro = new Carro($dados);
		$carroDAO = new CarroDAO($banco);
		echo $carroDAO->inserirCarro($carro);
		
	}else if(is_categoria($POST['categoria'],'moto')){
		
		$dados[] = $POST['marca'];
		$dados[] = $POST['cilindrada'];
		$dados[] = $POST['quilometragem'];
		$dados[] = $POST['ano'];
		
		$moto = new Moto($dados);
		$motoDAO = new MotoDAO($banco);
		echo $motoDAO->inserirMoto($moto);
		
	}else{

		$anuncio = new Anuncio($dados);
		$anuncioDAO = new AnuncioDAO($banco);
		echo $anuncioDAO->inserirAnuncio($anuncio);
	}
    

    $banco->fechaConexao();
    exit;
}

include '../model/domain/usuario.php';
include '../model/dao/usuario.php';
include '../assets/arquivos_php/funcoesDeFiltros.php';

$usuariosDAO = new UsuarioDAO($banco);
$infoUsu = $usuariosDAO->selectUsuarioId($idUsuario);

$banco->fechaConexao();