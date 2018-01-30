<?php

include '../assets/includes/session.php';

include '../controller/meusAnuncios.php';

?>

<!doctype html>
<html lang='pt-br'>
    <head>
        <meta http-equiv="Content-Type" content="text/html"; charset="UTF-8"/>
        <title>meus anúncios</title>
        <link rel="shortcut icon" type="img/x-icon" href="../assets/img/icones/favicon.png">
        <link rel='stylesheet' type="text/css" href='../assets/css/todasPaginas.css'>
        <link rel='stylesheet' type="text/css" href='../assets/css/meusAnuncios.css'>
    </head>
    <body>
        <?php include '../assets/includes/header.php' ?>

        <div id='localAnuncios'>
            <?php
            if (count($anuncios) === 0)
                echo "<div id='semAnuncio'>você não publicou nenhum anúncio ainda começe agora <a href='anunciar.php'>anunciar</a></div>";
            if ($numeroDeAnuncios > 0) {
		
                foreach ($anuncios as $anuncio) {

                    echo "<div id='anuncio' value='{$anuncio->getId()}' >
                            <a id='button' class='excluirAnuncio' >excluir</a>
                            <a id='button' href='editaranuncio.php?anuncio={$anuncio->getId()}'>editar</a>
							<a href='anuncio.php?anuncio={$anuncio->getId()}' target='_blanck'>
								<p>{$anuncio->pegaVisualizacoes()}</p>
								<p>{$anuncio->getData()}</p>
								<p>{$anuncio->getCategoria('nomes')}</p>
								<p>{$anuncio->getEstado()} - {$anuncio->getCidade()} - {$anuncio->getBairro()}</p>
								<p>{$anuncio->getTitulo(40)}</p>
								<div align='center' id='l-img'><img src='../assets/img/anuncios/{$anuncio->getImagens('explode')[0]}'></div>
								<p>{$anuncio->getDescricao(44)}</p>
								<p>R$ {$anuncio->getValor()}</p>
                            </a>
                    </div>";
                }
                echo  paginalizacao($numeroDeAnuncios,$pagina);
            }
            ?>
        </div>
    </body>
    <script type="text/javascript" src="../assets/js/jquery_code.js"></script>
    <script type="text/javascript" src="../assets/js/todasPaginas.js"></script>
    <script type="text/javascript" src="../assets/js/cidadesEstados.js"></script>
    <script type="text/javascript" src="../assets/js/meusAnuncios.js"></script>
</html>
