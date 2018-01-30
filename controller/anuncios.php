<?php

$idUsuario = isset($_SESSION['idUsuario']) ? $_SESSION['idUsuario'] : 0;

include '../model/dataBase/bancoDeDados.php';
include '../model/domain/anuncio.php';
include '../model/dao/anuncio.php';
include '../model/dao/notificacoes.php';
include '../assets/arquivos_php/todasFuncoes.php';
include '../assets/arquivos_php/funcoesDeFiltros.php';

$banco = new BancoDeDados;

if (!isset($_GET['estado']) and ! isset($_GET['cidade']) and $idUsuario) {
	include '../model/dao/usuario.php';
	include '../model/domain/usuario.php';
    $usuarioDAO = new UsuarioDAO($banco);
    $info = $usuarioDAO->selectUsuarioId($idUsuario);
    $estado = $info->getEstado();
    $cidade = $info->getCidade();
} else {
    $estado = input('GET', 'estado');
	$cidade = input('GET', 'cidade');

}

$selectEstadoCidades = implode(' ',selectEstadoCidades($estado,$cidade));

$bairro = $cidade ? input('GET', 'bairro') : null;
$anunciante = input('GET', 'anunciante');

$categoriaSelect = implode(' ',selectCategorias(@$_GET['categorias']));

$categorias = is_array(@$_GET['categorias']) ? implode('-',array_filter($_GET['categorias'])) : null;
 
$titulo = input('GET', 'titulo');

$val = ordenaValores(input('GET', 'valorminimo'), input('GET', 'valormaximo'), true);

$valorMin = $val[0];
$valorMax = $val[1];

$ordem = input('GET', 'ordem', 'INT', '+');
$pagina = input('GET', 'pagina', 'INT', '+');
$pagina = $pagina ? $pagina : 1;


$filtros = [$estado, $cidade, $bairro, $anunciante, $categorias, $titulo, $valorMin, $valorMax, $ordem, $pagina];
if(is_categoria($categorias, 'carro')){
    include '../model/domain/carro.php';
    include '../model/dao/carro.php';
    
	$filtros[] = $marca = input('GET','marca');
	
    $val = ordenaValores(input('GET', 'quilometragemMinima', 'INT', '+'), input('GET', 'quilometragemMaxima', 'INT', '+'));
	$filtros[] = $quilometragemMinima = $val[0];
	$filtros[] = $quilometragemMaxima = $val[1];
	
    $val = ordenaValores(input('GET', 'anoMinimo', 'INT', '+'), input('GET', 'anoMaximo', 'INT', '+'));
	$filtros[] = $anoMinimo = $val[0];
	$filtros[] = $anoMaximo = $val[1];
	
    $filtros[] = $cambios = @$_GET['cambios'];
    $filtros[] = $combustiveis = @$_GET['combustiveis'];
    $filtros[] = $tipos = @$_GET['tipos'];
    $filtros[] = $opcionais = @$_GET['opcionais'];
	
	
	$filtroInclude = 
        montaFiltro($marca,'carro','MARCAS','marcas','select').''.
		selectQuilometragem($quilometragemMinima,1).''.
        selectQuilometragem($quilometragemMaxima,2).''.
        selectAnos($anoMinimo,2).''.
        selectAnos($anoMaximo,3).
        "<div>CAMBIOS<br>".montaFiltro($cambios,'carro','CAMBIOS','cambios','checkbox').'</div>'.
        "<div>COMBUSTIVEIS<br>".montaFiltro($combustiveis,'carro','COMBUSTIVEIS','combustiveis','checkbox').'</div>'.
        "<div>TIPOS<br>".montaFiltro($tipos,'carro','TIPOS','tipos','checkbox').'</div>'.
        "<div>OPCIONAIS<br>".montaFiltro($opcionais,'carro','OPCIONAIS','opcionais','checkbox').'</div>';
	
    $anuncioDAO = new CarroDAO($banco);
	
}else if(is_categoria($categorias, 'moto')){
    include '../model/dao/moto.php';
    include '../model/domain/moto.php';
    
    $filtros[] = $marca = input('GET','marca');
	
	$val = ordenaValores(input('GET', 'cilindradaMinima', 'INT', '+'), input('GET', 'cilindradaMaxima', 'INT', '+'));
	$filtros[] = $cilindradaMinima = $val[0];
	$filtros[] = $cilindradaMaxima = $val[1];
	
    $val = ordenaValores(input('GET', 'quilometragemMinima', 'INT', '+'), input('GET', 'quilometragemMaxima', 'INT', '+'));
	$filtros[] = $quilometragemMinima = $val[0];
	$filtros[] = $quilometragemMaxima = $val[1];
	
    $val = ordenaValores(input('GET', 'anoMinimo', 'INT', '+'), input('GET', 'anoMaximo', 'INT', '+'));
	$filtros[] = $anoMinimo = $val[0];
	$filtros[] = $anoMaximo = $val[1];

    $anuncioDAO = new MotoDAO($banco);
	$filtroInclude = 
        montaFiltro($marca,'moto','MARCAS','marcas','select').''.
        selectCilindrada($cilindradaMinima,2).''.
        selectCilindrada($cilindradaMaxima,3).''.
		selectQuilometragem($quilometragemMinima,1).''.
        selectQuilometragem($quilometragemMaxima,2).''.
        selectAnos($anoMinimo,2).''.
        selectAnos($anoMaximo,3);
	
}else 
	$anuncioDAO = new AnuncioDAO($banco);

$anuncios = $anuncioDAO->selectAnunciosFiltros($filtros);
$ultimoIndice = count($anuncios) - 1;
$numeroDeAnuncios = $anuncios[$ultimoIndice];
array_splice($anuncios, $ultimoIndice, 1);
$banco->fechaConexao();
