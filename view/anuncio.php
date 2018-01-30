<?php
session_start();

if(isset($_SESSION['idUsuario']) && !$_SESSION['nomeUsuario'] && basename($_SERVER['PHP_SELF']) != 'cadastro.php') {
    header('location:cadastro.php?incompleto');
    exit;
}

include '../controller/anuncio.php';
?>
<!doctype html>
<html lang='pt-br'>
    <head>
        <meta http-equiv="Content-Type" content="text/html" charset="UTF-8" >
        <title>anúncio</title>
        <link rel="shortcut icon" type="img/x-icon" href="../assets/img/icones/favicon.png" >
        <link rel="stylesheet" type="text/css" href='../assets/css/todasPaginas.css' >
        <link rel="stylesheet" type="text/css" href='../assets/css/anuncio.css'>
    </head>
    <body>
        <?php include '../assets/includes/header.php' ?>
        <div id='localAnuncio'>
            <div id='anuncioUnico' value='<?= $anuncio->getId(); ?>'>

				<?php
                if ($idUsuario and $idUsuario !== $anuncio->getAnunciante())
                    echo "<a href='chat.php?anuncio={$anuncio->getId()}'><button id='irChat'>Negociar Por Chat</button></a>";
                if ($idUsuario !== $anuncio->getAnunciante())
                    echo "<button id='denunciarAnuncio'>Denunciar</button>";
				?>
                <p><?= $anuncio->getCategoria('nomes'); ?></p>
                <p>Anúnciante <?= $anuncio->getAnunciante('nome'); ?></p>
                <p>Anúnciado <?= $anuncio->getData(); ?></p>
                <p><?= $anuncio->getTitulo(); ?></p>
                <div id='localImgs' >
                <?php
                $imgs = null;
                $imagens = $anuncio->getImagens('explode');
                foreach ($imagens as $idx => $img) {
                    if (!$idx) {
                        echo "<div id='localImgPrincipal' >
                                <span class='zoom'>
                                    <img id='imgPrincipal' src='../assets/img/anuncios/$img'>
                                </span>
                             </div>";
                        $imgs .= "<img id='imgInferiores' class='foco' src='../assets/img/anuncios/$img'>";
                    } else
                        $imgs .= "<img id='imgInferiores' src='../assets/img/anuncios/$img'>";
                }
                echo "<div id='locaImgInferiores'>{$imgs}</div>";
                ?>
                </div>
                <p><?=$anuncio->getDescricao(); ?></p>
                <p>R$ <?=$anuncio->getValor(); ?></p>
                <p>Telefone de contato: <?=$anuncio->getTelefone(); ?></p>
                <p>Cep: <?=$anuncio->getCep(); ?></p>
                <p>Estado: <?=$anuncio->getEstado('texto'); ?></p>
                <p>Cidade: <?=$anuncio->getCidade(); ?></p>
                <p>Bairro: <?=$anuncio->getBairro(); ?></p>
                <?php
                if(get_class($anuncio) == 'Moto'){
                    echo  "<p>Marca: {$anuncio->getMarca('texto')}</p>"
                        . "<p>Cilindrada: {$anuncio->getCilindrada('texto')}</p>"
                        . "<p>Quilometragem: {$anuncio->getQuilometragem('texto')}</p>"
                        . "<p>Ano: {$anuncio->getAno()}</p>";
						
                }else if(get_class($anuncio) == 'Carro'){
                        echo  "<p>Marca: {$anuncio->getMarca('texto')}</p>"
                            . "<p>Quilometragem: {$anuncio->getQuilometragem('texto')}</p>"
                            . "<p>Ano: {$anuncio->getAno()}</p>";
                        
                        if($anuncio->getCambio())
                            echo "<p>Cambio: {$anuncio->getCambio('texto')}</p>";
                        if($anuncio->getCombustivel())
                            echo "<p>Combustivel: {$anuncio->getCombustivel('texto')}</p>";
                        if($anuncio->getTipo())
                            echo "<p>Tipo: {$anuncio->getTipo('texto')}</p>";
                        if($anuncio->getOpcionais())
                            echo "<p>Opcionais: {$anuncio->getOpcionais('texto')}</p>";
                }
                ?>
                <div id='localComentarios'>
                 <?php 
			
                    if ($idUsuario){
                        echo  "<textarea maxlength='400' placeholder='comentario...' id='caixaAddComentarios'></textarea>"
                            ."<button id='btAddComentarios' >inserir comentario</button>";
                    }

                    echo "<h2><b>Comentários</b> • {$comentarios['numero']}</h2>";
                    echo $comentarios['comentarios'];
                    
                ?>
                </div>

            <div>
        </div>
    </body>
    <script type="text/javascript" src="../assets/js/jquery_code.js"></script>
    <script type="text/javascript" src="../assets/js/todasPaginas.js"></script>
    <script type="text/javascript" src='../assets/js/jquery.zoom.min.js'></script>
    <?= $idUsuario ? "<script type='text/javascript' src='../assets/js/anuncio.js'></script>" : ''; ?>
    <script type="text/javascript" src='../assets/js/denunciar.js'></script>
</html>
