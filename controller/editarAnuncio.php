<?php
include '../assets/arquivos_php/todasFuncoes.php';

$idAnuncio = input('GET', 'anuncio', 'INT', '+');

if (!$idAnuncio) exit;

$idUsuario = $_SESSION['idUsuario'];

include '../model/dataBase/bancoDeDados.php';
include '../model/domain/anuncio.php';
include '../model/dao/anuncio.php';
include '../model/domain/moto.php';
include '../model/dao/moto.php';
include '../model/domain/carro.php';
include '../model/dao/carro.php';

$banco = new BancoDeDados;
$anuncioDAO = new AnuncioDAO($banco);

if (isset($_POST['upload'])) {
    
    $POST = $_POST;

    $query = $banco->executaQuery("SELECT id,imagens FROM anuncios WHERE id = {$idAnuncio} AND id_usuario = {$idUsuario}");
    
    if ($query->num_rows == 1) {
        
		$imgs['atuais'] = explode('/', $query->fetch_array()['imagens']);
		$imgs['agora'] = isset($POST['imagens']) ? $POST['imagens'] : [];
		$imgs['novas'] = isset($_FILES) ? $_FILES : [];
		
		$dados = [$idAnuncio, null];
        $dados[] = $POST['categoria'];
        $dados[] = $POST['titulo'];
        $dados[] = $imgs;
        $dados[] = $POST['descricao'];
        $dados[] = $POST['valor'];
		$dados[] = $POST['telefone'];
        $dados[] = $POST['cep'];
        $dados[] = $POST['estado'];
        $dados[] = $POST['cidade'];
        $dados[] = $POST['bairro'];
		$dados[] = null; // seria data
           
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
            echo $carroDAO->updateCarro($carro);
			
        }else if(is_categoria($POST['categoria'],'moto')){
            
            $dados[] = $POST['marca'];
            $dados[] = $POST['cilindrada'];
            $dados[] = $POST['quilometragem'];
            $dados[] = $POST['ano'];
            
            $moto = new Moto($dados);
            $motoDAO = new MotoDAO($banco);
			echo $motoDAO->updateMoto($moto);
			
		}else{

			$anuncio = new Anuncio($dados);
            $anuncioDAO = new AnuncioDAO($banco);
            echo $anuncioDAO->updateAnuncio($anuncio);
        }
    }

    $banco->fechaConexao();
    exit;
}

$anuncio = $anuncioDAO->getAnuncio(new Anuncio([$idAnuncio, $idUsuario]));

if(!$anuncio){
    header('location:meusanuncios.php');
    exit;
}

include '../assets/arquivos_php/funcoesDeFiltros.php';

if(is_categoria($anuncio->getCategoria(),'moto')){
    $motoDAO = new MotoDAO($banco);
    $moto = $motoDAO->selecionaMotoId($anuncio->getId()); 
}else
	$moto = new Moto([]);

if(is_categoria($anuncio->getCategoria(),'carro')){
    $carrosDAO = new CarroDAO($banco);
    $carro = $carrosDAO->selecionaCarroId($anuncio->getId()); 
}else
	$carro = new Carro([]);

$banco->fechaConexao();
?>
