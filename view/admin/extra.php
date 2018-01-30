<?php
session_start();
/*
if (!isset($_SESSION['idUsuario'])) {
    header('location:index.php');
    exit;
}
$idUsuario = $_SESSION['idUsuario'];
*/

include '../../model/dataBase/bancoDeDados.php';

$banco = new BancoDeDados;

if(isset($_POST['excluir'])){
	
	$id = $_POST['id'];
	
	switch($_POST['tipo']){
		case 1: $sql = "DELETE FROM categoria_extra WHERE tipo = ?"; 
			break;
		case 2: $sql = "DELETE FROM categoria_extra WHERE tipo_referencia = ?"; 
			break;		
		case 3: $sql = "DELETE FROM categoria_extra WHERE id = ?"; 
			break;
	}
	
	$stmt = $banco->preparaStatement($sql);
    $stmt->bind_param('s', $id);
	$stmt->execute();
	$banco->fechaConexao();
	exit;
}

if(isset($_POST['editar'])){
	
	$id = $_POST['id'];
	$texto = $_POST['texto'];
	
	switch($_POST['tipo']){
		case 1: $sql = "UPDATE categoria_extra SET tipo = ? WHERE tipo = ? and tipo != ?"; 
			break;
		case 2: $sql = "UPDATE categoria_extra SET tipo_referencia = ? WHERE tipo_referencia = ? and tipo_referencia != ?"; 
			break;		
		case 3: $sql = "UPDATE categoria_extra SET nome = ? WHERE id = ? and nome != ?";  
			break;
	}
	
	$stmt = $banco->preparaStatement($sql);
    $stmt->bind_param('sss', $texto, $id, $texto);
    $stmt->execute();
	
	$banco->fechaConexao();
	exit;
}

if(isset($_POST['inserir'])){
	
	$tipo = $_POST['tipo'];
	$tipoReferencia = $_POST['tipoReferencia'];
	$nome = $_POST['nome'];
	
	$sql = "INSERT INTO categoria_extra VALUES (null,?,?,?)"; 
	$stmt = $banco->preparaStatement($sql);
	$stmt->bind_param('sss', $tipo, $tipoReferencia, $nome);

    $stmt->execute();

	$banco->fechaConexao();
	exit;
}


$query1 = $banco->executaQuery("SELECT tipo FROM categoria_extra WHERE tipo_referencia = '' AND nome = '' ORDER BY tipo");

$listaExtra = $findExtra = null;

while($linhas1 = $query1->fetch_array())
$listaExtra.= "<div value='{$linhas1['tipo']}'><a href='?extra={$linhas1['tipo']}'>{$linhas1['tipo']}</a> <span id='editar'>editar</span><span id='excluir' >excluir</span></div>";

$extra = @$_GET['extra'];

if($extra){

	$findExtra.= "<p value='{$extra}'><input type='text' placeholder='adicionar para {$extra}' ><button>add</button></p>";
	
	$query2 = $banco->executaQuery("SELECT tipo_referencia  FROM categoria_extra WHERE tipo = '{$extra}' AND tipo_referencia != '' AND nome = '' ORDER BY tipo_referencia");
	
	while($linhas2 = $query2->fetch_array()){
		
		$findExtra.= "<div>";
		$findExtra.= "<h3 value='{$linhas2['tipo_referencia']}'><a>{$linhas2['tipo_referencia']}</a> <span id='editar'>editar</span><span id='excluir' >excluir</span></h3>";
		$findExtra.= "<p value='{$linhas2['tipo_referencia']}'><input type='text' placeholder='adicionar {$linhas2['tipo_referencia']}' ><button>add</button></p>";
		$findExtra.= "<div id='c-l'>";
		$query3 = $banco->executaQuery("SELECT id,nome FROM categoria_extra WHERE tipo = '{$extra}' AND tipo_referencia = '{$linhas2['tipo_referencia']}' AND nome != '' ORDER BY tipo_referencia");
		while($linhas3 = $query3->fetch_array())
			$findExtra.= "<div value='{$linhas3['id']}'><a>{$linhas3['nome']}</a> <span id='editar'>editar</span><span id='excluir' >excluir</span></div>";
		
		$findExtra.= "</div>";
		$findExtra.= "</div>";
	}

}
	

$banco->fechaConexao();

?>
<!doctype html>
<html lang='pt-br'>
    <head>
        <meta http-equiv="Content-Type" content="text/html" charset="UTF-8" >
        <title>extras</title>
        <link type="img/x-icon" rel="shortcut icon" href="../../assets/img/icones/favicon.png">
        <link type="text/css" rel='stylesheet' href='../../assets/css/extra.css'>
    </head>
    <body>
        <div id='todo-conteudo'>
		
			<h3><a href='categorias.php'>Ir para Categorias</a></h3>
			
			<h2>Lista extra. <small>extra são filtros opcionais e correspondente a heranças dos anuncios exemplo carro e moto </small></h2>
			<h4>qualquer alteração, exclusão ou inserção podera ocasionar algum erro no site</h2>
		
			<div id='lista-extra' >
				<div id='conteudo'>
					<?=$listaExtra;?>
				</div>
			<?php 
			if($extra and $listaExtra){
				echo "<div id='conteudo-find'>
						<h2>{$extra}</h2>
						{$findExtra}
					</div>";
			}
			?>
			</div>
		</div>
    </body>
	<script type="text/javascript" src="../../assets/js/jquery_code.js"></script>
    <script type="text/javascript" src="../../assets/js/extra.js"></script>
   
</html>
