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
	
	switch($_POST['tipo']){
		case 1: $sql = "DELETE FROM categoria_primaria WHERE id = ?"; 
			break;
		case 2: $sql = "DELETE FROM categoria_segundaria WHERE id = ?"; 
			break;		
		case 3: $sql = "DELETE FROM categoria_terciaria WHERE id = ?"; 
			break;
	}
	
	$id = $_POST['id'];
	$stmt = $banco->preparaStatement($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
	
	$banco->fechaConexao();
	exit;
}

if(isset($_POST['editar'])){
	
	switch($_POST['tipo']){
		case 1: $sql = "UPDATE categoria_primaria SET nome = ? WHERE id = ? and nome != ?"; 
			break;
		case 2: $sql = "UPDATE categoria_segundaria SET nome = ? WHERE id = ? and nome != ?"; 
			break;		
		case 3: $sql = "UPDATE categoria_terciaria SET nome = ? WHERE id = ? and nome != ?";  
			break;
	}
	
	$id = $_POST['id'];
	$nome = $_POST['nome'];
	$stmt = $banco->preparaStatement($sql);
    $stmt->bind_param('sis', $nome, $id, $nome);
    $stmt->execute();
	
	$banco->fechaConexao();
	exit;
}

if(isset($_POST['inserir'])){
	
	$nome = $_POST['nome'];
	$tipo = $_POST['tipo'];
	
	if($tipo == 1){
		$sql = "INSERT INTO categoria_primaria VALUES (null,?)"; 
		$stmt = $banco->preparaStatement($sql);
		$stmt->bind_param('s', $nome);
	}else if($tipo == 2){
		$sql = "INSERT INTO categoria_segundaria VALUES (null,?,?)"; 
		$stmt = $banco->preparaStatement($sql);
		$stmt->bind_param('is', $_POST['id'], $nome);
	}else if($tipo == 3){
		$sql = "INSERT INTO categoria_terciaria VALUES (null,?,?)"; 
		$stmt = $banco->preparaStatement($sql);
		$stmt->bind_param('is', $_POST['id'], $nome);
	}
	
    $stmt->execute();
	
	echo $stmt->insert_id;
	
	$banco->fechaConexao();
	exit;
}

$listaCategorias = $banco->executaQuery("SELECT * FROM categoria_primaria");

if(isset($_GET['p']) and is_numeric($_GET['p'])){
	$idPrimaria = $_GET['p'];
	
	$query = $banco->executaQuery("SELECT nome FROM categoria_primaria WHERE id = '{$idPrimaria}' ORDER BY id DESC");
	if($query->num_rows){
		
		$nomePrimaria = $query->fetch_array()['nome'];
	
		$listaSegundariaTercearia = null;
		
		$query1 = $banco->executaQuery("SELECT * FROM categoria_segundaria WHERE id_primaria = '{$idPrimaria}' ORDER BY id DESC");
		
		while($linhas1 = $query1->fetch_array()){
			$listaSegundariaTercearia.= "<div>";
			
			$listaSegundariaTercearia.= "<div id='conteudo-t'>";
			$listaSegundariaTercearia.= "<h3 value='{$linhas1['id']}'><a>{$linhas1['nome']}</a> <span id='editar'>editar</span><span id='excluir' >excluir</span></h3>";
			
			$listaSegundariaTercearia.= "<p value='{$linhas1['id']}'><input type='text' placeholder='terciaria'><button>add</button></p>";
			$query2 = $banco->executaQuery("SELECT * FROM categoria_terciaria WHERE id_segundaria = '{$linhas1['id']}' ORDER BY id DESC");

			while($linhas2 = $query2->fetch_array())
				$listaSegundariaTercearia.= "<div value='{$linhas2['id']} '><a>{$linhas2['nome']}</a> <span id='editar'>editar</span><span id='excluir' >excluir</span></div>";
				
			$listaSegundariaTercearia.= "</div>";
			$listaSegundariaTercearia.= "</div>";
		}
	}
}

$banco->fechaConexao();

?>
<!doctype html>
<html lang='pt-br'>
    <head>
        <meta http-equiv="Content-Type" content="text/html" charset="UTF-8" >
        <title>categorias</title>
        <link type="img/x-icon" rel="shortcut icon" href="../../assets/img/icones/favicon.png">
        <link type="text/css" rel='stylesheet' href='../../assets/css/categorias.css'>
    </head>
    <body>
        <div id='todo-conteudo'>
		
			<h3><a href='extra.php'>Ir para Categorias Extras de Herança</a></h3>
			
			<div id='lista-primaria'>
				<p><input type='text' placeholder='primaria' ><button>add</button></p>
				<div id='conteudo-p'>
					<?php
					while($linhas = $listaCategorias->fetch_array())
						echo "<div value='{$linhas['id']}'><a href='?p={$linhas['id']}'>{$linhas['nome']}</a> <span id='editar'>editar</span><span id='excluir' >excluir</span></div>";
					
					?>
				</div>
			</div>
			
			<?php
			if(isset($nomePrimaria)){
		 
				echo "<div id='lista-segundaria-terciaria'>";
				echo 	"<h2>".$nomePrimaria."</h2>";
				echo 	"<p value='{$idPrimaria}'><input type='text' placeholder='segundaria' ><button>add</button></p>";
				echo	"<div id='conteudo-s'>";
				echo 		$listaSegundariaTercearia;
				echo	 "<div>";
				echo "<div>";
		
			}else{
				echo "<h3>Selecione Uma Categoria Primaria para Visualizar suas Cetgorias Segundarias e Terciarias</h3>";
			}
			?>

		</div>
    </body>
	<script type="text/javascript" src="../../assets/js/jquery_code.js"></script>
    <script type="text/javascript" src="../../assets/js/categorias.js"></script>
   
</html>
