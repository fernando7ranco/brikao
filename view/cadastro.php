<?php

include '../assets/includes/session.php';

include '../controller/cadastro.php';
?>
<!doctype html>
<html lang='pt-br'>
    <head>
        <meta http-equiv="Content-Type" content="text/html"; charset="UTF-8"/>
        <title>cadastro</title>
        <link rel="shortcut icon" type="img/x-icon" href="../assets/img/icones/favicon.png">
        <link rel='stylesheet' type="text/css" href='../assets/css/todasPaginas.css'>
        <link rel='stylesheet' type="text/css" href='../assets/css/cadastro.css'>
    </head>
    <body>
        <?php include '../assets/includes/header.php' ?>
        <div id='localFinrmacoesDeCadastro'>

            <div id='dadosUsuario'>
                <?= @$sms; ?>
                <h2>Meus dados</h2>
				<p>Respeitamos a sua privacidade. Asseguramos que seus dados não serão fornecidos a terceiros.</p>
                <h5>Você pode editar seu dados de usuario, campos com (*) são obrigatorios</h5> 
                <form action='?update=true' method='POST'>
				
					<h3>Quem sou</h3>
                    <p>
                        <label>Nome *:</label>
                        <input type='text' name='nome' value='<?= $infoUsu->getNome(); ?>' maxlength='35' placeholder='NOME' >
                        <img src='../assets/img/icones/inf.png' alt='campo nome é obrigatorio, somente letra de A-Z e espaços, de 3 à 35 caracteres' >
                    </p>
                    <p>
                        <label>Eu sou *:</label>
                            <?php
                            $selected = [null, null, null];
							$tipo = $infoUsu->getTipo() == 0 ? 1 :$infoUsu->getTipo();
                            $selected[$tipo] = "checked='true'";
                            ?>
                        <span id='inputsTipo'>
                            <input type='radio' name='tipo' value='1' <?= $selected[1]; ?> >Particular 
                            <input type='radio' name='tipo' value='2' <?= $selected[2]; ?> >Profissional
                        </span>
                    </p>
					
					<h3>Meus contatos</h3>
                    <p>
                        <label>Email:</label>
                        <span id='dadoEmail'><?= $infoUsu->getEmail(); ?></span>
                    </p>	

                    <p>
                        Digite o numero de area e os  8 ou 9 digitos do telefone, que no final é ajustado.<br>
                        <label>Telefone:</label>
                        <input type='text' name='telefone' value='<?= $infoUsu->getTelefone(); ?>' maxlength='15' placeholder='(xx) xxxxx-xxxx' >
                        <img src='../assets/img/icones/inf.png' alt='campo telefone , somente numeros de 0-9, 11 caracteres' >
                    </p>
					
					<h3>Meu endereço</h3>
                    <p>
                        Digite o CEP e pressione ENTER para completar seu dados de enderço.<br>
                        <label>Cep:</label>
                        <input type='text' name='cep' value='<?= $infoUsu->getCep(); ?>' maxlength='8' placeholder='SOMENTE NUMEROS'>
                        <img src='../assets/img/icones/inf.png' alt='campo cep, somente numeros de 0-9, 8 caracteres' >
                    </p>

                    <p>
                        <label>Estado:</label>
                        <select name='estado' id='getEstados' alt='<?= $infoUsu->getEstado(); ?>' ></select>
                    </p>
                    <p>
                        <label>Cidade:</label>
                        <select name='cidade' id='getCidades' alt='<?= $infoUsu->getCidade(); ?>' ></select>
                    </p>

                    <p>
                        <label>Bairro:</label>
                        <input type='text' name='bairro' value='<?= $infoUsu->getBairro(); ?>' maxlength='50' placeholder='BAIRRO'>
                        <img src='../assets/img/icones/inf.png' alt='campo bairro, somente letras de A-Z, de 0 á 50 caracteres' >
                    </p>

                    <p>
                        <label>logradouro:</label>
                        <input type='text' name='logradouro' value='<?= $infoUsu->getLogradouro(); ?>' maxlength='80' placeholder='RUA NUMERO COMPLEMENTO'>
                        <img src='../assets/img/icones/inf.png' alt='campo logradouro, somente letras de A-Z e numeros de 0-9, de 0 á 80 caracteres' >
                    </p>
                    <input type='submit' value='salvar alterações'>
                </form>
            </div>
			
            <div id='dadosConta'>

                <h2>Dados da conta</h2>
                <h4>Você pode alterar seu email e senha de usuario</h4> 
                <p>
                    <label>email:</label>
                    <input type='email' maxlength='60' id='emailLogado' placeholder='insira um email valido'>
                    <img src='../assets/img/icones/inf.png' alt='insira um email valido, que você use atualmente, limite de 60 caracteres' >
                </p>
                <p>
                    <label>senha:</label>
                    <input type='password' maxlength='16' id='senhaLogado' placeholder='insira uma senha valida' >
                    <img src='../assets/img/icones/inf.png' alt='somente letras de [A-Z] e numeros de [0-9], de 6 á 16 caracteres' >
                </p>
                <p>
                    <label>excluir conta:</label>
                    <input type='checkbox' id='exclurLogado'>

                </p>

                <?php
                if ($infoUsu->getSenha() || $infoUsu->getTipoCadastro() == 1) {
                    echo"<p><label>senha logada:</label><input type='password' id='senhaUsuario' maxlength='16' placeholder='insira sua senha para fazer alterações' >
                            <button id='fazerAlteracoes'>fazer alterações</button></p>";
                    $script = null;
                }
                if ($infoUsu->getTipoCadastro() == 2) {
                    echo "<div class='iconeWebSite' id='facebookApp'><img src='../assets/img/icones/face.jpg' id='icone' > fazer alterações </div>";
                    $script = '<script type="text/javascript" src="../assets/js/appFacebook.js"></script>';
					
                }else if ($infoUsu->getTipoCadastro() == 3) {
                    echo "<div class='iconeWebSite' id='googleApp'><img src='../assets/img/icones/google+.jpg' id='icone' > fazer alterações </div>";
                    $script = '<script type="text/javascript" src="https://apis.google.com/js/api:client.js"></script><script type="text/javascript" src="../assets/js/appGoogle.js"></script>';
                }
                ?>
            </div>
        </div>
    </body>
    <script type="text/javascript" src="../assets/js/jquery_code.js"></script>
    <script type="text/javascript" src="../assets/js/todasPaginas.js"></script>
    <script type="text/javascript" src="../assets/js/cidadesEstados.js"></script>
    <?= $script ?>
    <script type="text/javascript" src="../assets/js/cadastro.js"></script>
</html>
