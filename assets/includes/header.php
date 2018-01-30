<?php if ($idUsuario) { ?>

    <header>
		<div id='nameUser'>
			<?=$_SESSION['nomeUsuario'] ? $_SESSION['nomeUsuario'] : 'Preencha sue nome de Usuario' ;?>
		</div>
        <nav>
            <a href='cadastro.php' alt='meus dados de cadastro'><img src='../assets/img/icones/cadastro.png' ></a>
            <a href='anuncios.php' alt='anúncios'><img src='../assets/img/icones/anuncio.png' ></a>
            <a href='chat.php' alt='chat de mesagens'><img src='../assets/img/icones/chat.png' ></a>
            <a href='anunciar.php' alt='anúnciar'><img src='../assets/img/icones/anunciar.png' ></a>
            <a href='meusanuncios.php' alt='meus anúncios' ><img src='../assets/img/icones/meusAnuncios.png' ></a>
			<div id='local-notificacoes'>
				<a alt='notificações' >	
					<span id='nNotificacoes'></span>
					<img src='../assets/img/icones/notification.png' >
				</a>
				<div id='notificacoes'>
					<div id='local'></div>
				</div>
			</div>
            <a href='sair.php' alt='sair, encerrar conexão' ><img src='../assets/img/icones/sair.png' ></a>
        </nav>

        <img src='../assets/img/icones/logo.png' id='logoBRIKAO' onclick="window.open('anuncios.php', '_self')";>
    </header>	

<?php } else { ?>

    <header>
        <nav>
            <a href='index.php?CADASTRO' alt='cadastre-se para ter maior acesso'><img src='../assets/img/icones/cadastro.png' ></a>
            <a alt='anúncios' href='anuncios.php' ><img src='../assets/img/icones/anuncio.png' ></a>
            <a href='index.php?CADASTRO' alt='cadastre-se para ter maior acesso'><img src='../assets/img/icones/chat.png' ></a>
            <a href='index.php?CADASTRO' alt='cadastre-se para ter maior acesso'><img src='../assets/img/icones/anunciar.png' ></a>
            <a href='index.php?CADASTRO' alt='cadastre-se para ter maior acesso'><img src='../assets/img/icones/meusAnuncios.png' ></a>
            <a href='index.php?CADASTRO' alt='cadastre-se para ter maior acesso'><img src='../assets/img/icones/notification.png' ></a>
            <a href='index.php?CADASTRO' alt='cadastre-se para ter maior acesso'><img src='../assets/img/icones/sair.png' ></a>
        </nav>

        <img src='../assets/img/icones/logo.png' id='logoBRIKAO' onclick="window.open('index.php', '_self')";>
    </header>	

<?php } ?>