<?php

include '../assets/includes/session.php';

include '../controller/chat.php';
?>
<!doctype html>
<html lang='pt-br'>
    <head>
        <meta http-equiv="Content-Type" content="text/html"; charset="UTF-8"/>
        <title>chat</title>
        <link rel="shortcut icon" type="img/x-icon" href="../assets/img/icones/favicon.png">
        <link rel='stylesheet' type="text/css" href='../assets/css/todasPaginas.css'>
        <link rel='stylesheet' type="text/css" href='../assets/css/chat.css'>
    </head>
    <body>
        <?php include '../assets/includes/header.php' ?>
        <div id='localChat'>
        <?php if ($chats) { ?>
            
            <div id='chat'>
                
                <div id='left'>
                    <div>
                        <h3>Meus Chats de Negocios</h3>
                    </div>
                    <div id='chats'>
                         <?= $chats; ?>
                    </div>
                 </div>
                
                <div id='right'>
				
                    <div id='loading'></div>
					
                    <div id='topInfo'> 
                        <span>...</span>
                        <span>...</span>

                        <div id='localOp'>
                            <img src='../assets/img/icones/settings.png' alt='opções do chat'>
                            <div>
                                <button id='excluirChat' >excluir</button>
                                <button id='bloquearUsuario' >bloquear</button>
                                <button id='denunciarUsuario' >denunciar</button>
                            </div>
                        </div>

                    </div>

                    <div id='localMsg'>
                        <div id='verMensagensAnteriores'> visualizar mensagens anteriores</div>
                        <div id='apresentaMsg'></div>
                    </div>
        
                    <div id='caixaEnviarMsg'>
                        <textarea id='caixaDeMensagem' placeholder='Digite uma Mensagem ...' maxlength='200' ></textarea>
						
						<div id='enviarMensagem'> 
							<input type='file' name='arquivo' accept='image/*,video/ogg,video/mp4,video/webm' />
							<img src='../assets/img/icones/anexo.png' id='anexarArquivoMsg' alt='anexe uma imagem ou um video' >
							<button>ENVIAR</button>
						</div>
						
						<div id='previlarquivomsg'>
							<div id='localprevilarquivomsg' align='center' ></div>
							<div id='progressoArquivoMsg'><div></div></div>
							<div id='localDeEnviarArquivosMsg' align='center' ><button type='button' id='enviarArquivosDeMsg'>enviar</button><button type='button' id='cancelarArquivosDeMsg'>cencelar</button></div>
						</div>
                    </div>
                    
                </div>
                
            </div>
    <?php
        } else
            echo "<div id='semChat'> você não possui nenhuma mensagem, vá até um anúncio para iniciar um chat</div>";
    ?>
        </div>
    </body>
    <script type="text/javascript" src="../assets/js/jquery_code.js"></script>
    <script type="text/javascript" src="../assets/js/todasPaginas.js"></script>
    <?=($chats) ? '<script type="text/javascript" src="../assets/js/chat.js"></script>' : '';?>
    <script type="text/javascript" >
        $(document).ready(function () {
            <?php
			if ($chats){ 
				if (isset($idAnuncio))
					echo "$('#left table[value^={$idAnuncio}]').click().removeClass();";
				else
					echo "$('#left table:eq(0)').click().removeClass();";
			}
			?>
        });
    </script>

</html>
