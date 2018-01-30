<?php

session_start();
if (isset($_SESSION['idUsuario'])) {
    header('location:anuncios.php');
    exit;
}

include '../controller/index.php';
?>
<!doctype html>
<html lang='pt-br'>
    <head>
        <meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/>
        <title>BRIKÃO</title>
        <link rel="shortcut icon" type="img/x-icon" href="../assets/img/icones/favicon.png">
        <link rel='stylesheet' type="text/css" href='../assets/css/index.css'>
        <link rel='stylesheet' type="text/css" href='../assets/css/mapa.css'>
    </head>
    <body>
        <header>
            <img src='../assets/img/icones/logo.png' id='logoBRIKAO'>
			<div>
				<span>Bem-vindo ao BRIKÃO! Aqui você desapega e comprar de maneira fácil e simples.</span>
				<a><?=$numeros['inserido']?> anuncios novos hoje, talvez tenhamos algo que interessa você, ai pertinho de você mesmo.</a>
				<a><?=$numeros['vendido']?> anuncios vendidos hoje, anuncie você também e faça seu próprio negócio, aqui é facil e rapido.</a>
			</div>
        </header>
        <div id='caixaCentro'>

            <div id='esquerda' >
                <div>
                    <span id='<?= $il ?>'>logar</span>
                    <span id='<?= $ic ?>'>cadastrar-se</span>
                </div>

                <div id='forms'>

                    <div <?= $sl ?> id='login' align='center' >

                        <h2>Acessar meus anúncios</h2>
                        <form>
                            <p><input type='email' name='email' placeholder='email' ></p>
                            <p><input type='password' name='senha' placeholder='senha' ></p>
                            <p>
                                <input type='checkbox' value='true' id='Mlogado' > Me mantenha logado 
                                <a href='recuperarsenha.php' >Recuperar Senha</a>
                            </p>
                            <button type='button' id='fazerLogin'>logar</button>
                        </form>

                    </div>

                    <div <?= $sc ?> id='cadastro' align='center' >
                        <h2>Ainda não tenho cadastro</h2>
                        <form method='POST'>
                            <p>
                                <input type='email' name='email' placeholder='email'  maxlength='60' autocomplete='off' >
                                <br><font color='red'></font>
                            </p>
                            <p><input type='password' name='senha1' placeholder='senha' maxlength='16' autocomplete='off' ></p>
                            <p><input type='password' name='senha2' placeholder='corfimar senha' maxlength='16' autocomplete='off' ></p>
                            <p><input type='checkbox' id='vsenha' title='visualizar senha'> visualizar senha</p>
                            <button type='button' id='fazerCadastro'>cadastre-se</button>
                        </form>

                    </div>
                    <div class='iconeWebSite' alt='acessar via facebook' id='facebookApp'>
                        <img src='../assets/img/icones/face.jpg' id='icone'>
                        facebook
                    </div>
                    <div class='iconeWebSite' alt='acessar via google+' id='googleApp'>
                        <img src='../assets/img/icones/google+.jpg' id='icone' >
                        google+
                    </div>
                </div>
            </div>

            <div id='direita'>
                <span>Anuncie ou acesse anuncios ai pertinho de você mesmo</span>
				
				<div id='lista-estados'>
					<?=$listaDeEstados?>
				</div>
                <?php include '../assets/includes/mapa.html'; ?>
            </div>
        </div>
    </body>
    <script type="text/javascript" src="../assets/js/jquery_code.js"></script>
    <script type="text/javascript" src="../assets/js/index.js"></script>
    <script type="text/javascript" src="../assets/js/preCadastro.js"></script>
    <script type="text/javascript" src="../assets/js/appFacebook.js"></script>
    <script type="text/javascript" src="https://apis.google.com/js/api:client.js"></script>
    <script type="text/javascript" src="../assets/js/appGoogle.js"></script>
    <?php echo isset($script) ? $script : null; ?>
</html>