<?php
session_start();

if(isset($_SESSION['idUsuario']) && !$_SESSION['nomeUsuario'] && basename($_SERVER['PHP_SELF']) != 'cadastro.php') {
    header('location:cadastro.php?incompleto');
    exit;
}

include '../controller/anuncios.php';

?>

<!doctype html>
<html lang='pt-br'>
    <head>
        <meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/>
        <title>anúncios</title>
        <link rel="shortcut icon" type="img/x-icon" href="../assets/img/icones/favicon.png">
        <link rel='stylesheet' type="text/css" href='../assets/css/todasPaginas.css'>
        <link rel='stylesheet' type="text/css" href='../assets/css/anuncios.css'>
    </head>
    <body>
        <?php include '../assets/includes/header.php' ?>

        <div id='filtroAnuncio'>
			<h3>Filtros Para Anúncios</h3>
            <form method='GET' >
           
               <?=$selectEstadoCidades?>
				
				<div id='autocomplete-bairro'>
					<input type='text'  name='bairro' value='<?=$bairro?>' placeholder='BAIRRO' autocomplete='off' >
					<div id='ls'></div>
				</div>
				
				<?=$categoriaSelect?>
				
				<select name='anunciante'>
					<option value=''>tipo de anunciante</option>
					<option <?php if ($anunciante == 1) echo"selected='selected'"; ?> value='1'>Particular</option>
					<option <?php if ($anunciante == 2) echo"selected='selected'"; ?> value='2'>Profissional</option>
				</select>
				
                <input type='text' name='titulo' placeholder='Titulo do Anúncio' value='<?=$titulo?>' >
				<div align='center' id='valores'>
					<input type='text' name='valorminimo' placeholder='Valor Minimo' value='<?=numeroDecimal($valorMin)?>' >
					<input type='text' name='valormaximo' placeholder='Valor Maximo' value='<?=numeroDecimal($valorMax)?>' >
				</div>
                <select name='ordem'>
                    <option value=''>ordernar valor</option>
                    <option <?php if ($ordem == 1) echo"selected='selected'"; ?> value='1'>Menor Preço</option>
                    <option <?php if ($ordem == 2) echo"selected='selected'"; ?> value='2'>Maior Preço</option>
                </select>
				
                <?= @$filtroInclude;?>
                <button>filtrar</button>
                <button type="button" >limpar</button>
                <input type='hidden' name='pagina' value='<?=$pagina;?>'>

            </form>
        </div>

        <div id='localAnuncios'>
            <?php
            if (count($anuncios) === 0)
                echo "<div id='semAnuncio'>nenhum anúncio encontrado</div>";
			
            if ($numeroDeAnuncios > 0) {
				echo "<p> Total de Anúncios {$numeroDeAnuncios}";
                foreach ($anuncios as $anuncio) {
                    echo "<a href='anuncio.php?anuncio={$anuncio->getId()}' target='_blanck'>"
                            . "<div id='anuncio'>"
                            . "   <p>{$anuncio->getData()}</p>"
                            . "   <p>{$anuncio->getAnunciante('tipo')}</p>"
                            . "   <p>{$anuncio->getCategoria('nomes')}</p>"
                            . "   <p>{$anuncio->getEstado()} - {$anuncio->getCidade()} - {$anuncio->getBairro()}</p>"
                            . "   <p>{$anuncio->getTitulo(40)}</p>"
                            . "  <div align='center' id='l-img'><img src='../assets/img/anuncios/{$anuncio->getImagens('explode')[0]}'></div>"
                            . "   <p>{$anuncio->getDescricao(44)}</p>";
                               
                    if(get_class($anuncio) === 'Moto'){
                        echo  "<p>Marca: {$anuncio->getMarca('texto')}</p>"
                            . "<p>Cilindrada: {$anuncio->getCilindrada('texto')}</p>"
                            . "<p>Quilometragem: {$anuncio->getQuilometragem('texto')}</p>"
                            . "<p>Ano: {$anuncio->getAno()}</p>";
                    }else
                    if(get_class($anuncio) === 'Carro'){
                        echo  "<p>Marca: {$anuncio->getMarca('texto')}</p>"
                            . "<p>Quilometragem: {$anuncio->getQuilometragem('texto')}</p>"
                            . "<p>Ano: {$anuncio->getAno()}</p>";
                    }
                               
                    echo    " <p>R$ {$anuncio->getValor()}</p>"
                        . " </div>"
                        . "</a>";
                }

                echo  paginalizacao($numeroDeAnuncios,$pagina);
            }
            ?>
        </div>
    </body>
    <script type="text/javascript" src="../assets/js/jquery_code.js"></script>
    <script type="text/javascript" src="../assets/js/todasPaginas.js"></script>
    <script type="text/javascript" src="../assets/js/anuncios.js"></script>
</html>
