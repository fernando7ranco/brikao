<?php

include '../assets/includes/session.php';

include '../controller/editarAnuncio.php';

?>
<!doctype html>
<html lang='pt-br'>
    <head>
        <meta http-equiv="Content-Type" content="text/html" charset="UTF-8" >
        <title>editar anúncio</title>
        <link rel="shortcut icon" type="img/x-icon" href="../assets/img/icones/favicon.png">
        <link rel='stylesheet' type="text/css" href='../assets/css/todasPaginas.css'>
        <link rel='stylesheet' type="text/css" href='../assets/css/anunciar.css'>
    </head>
    <body>
        <?php include '../assets/includes/header.php' ?>
        <div id='localAnunciar'>
            <form>
                      <div id='div'>
                    <label>Categorias *</label>
					<a class='div'>
						 <?=implode('<br>',selectCategorias($anuncio->getCategoria('array')));?>
					</a>
                </div>
               
                <?php

                $display = is_categoria($anuncio->getCategoria(),'carro')? null: "style='display:none;'";
                echo"<div {$display} id='parteCarro'>
				
					 <div id='div'>
						<label>Marca *</label>
						".montaFiltro($carro->getMarca(),'carro','MARCAS','marca','select')."                       
					</div id='div'>  
                   
                    <div id='div'>
                        <label>Quilometragem *</label>
						<input type='text' name='quilometragem' value='{$carro->getQuilometragem()}' placeholder='entre 0  a 1.000.000'>
                    </div>
                  
                    <div id='div'>
                        <label>Ano *</label>
                       ".selectAnos($carro->getAno(), 1)."
                    </div>
					<div id='div'>
                        <label>Portas *</label>
						".montaFiltro($carro->getPortas(),'carro','PORTAS','portas','select')."
                    </div>
                    
                    <div id='div'>
                        <label>Cambio *</label>
                        ".montaFiltro($carro->getCambio(),'carro','CAMBIOS','cambio','select')."
                    </div>
                    
                    <div id='div'>
                        <label>Combustivel *</label>
                        ".montaFiltro($carro->getCombustivel(),'carro','COMBUSTIVEIS','combustivel','select')."
                    </div>
                    
					<div id='div'>
						<label>Tipo</label>
						".montaFiltro($carro->getTipo(),'carro','TIPOS','tipo','select')."
                    </div>
                    
                    <div id='div'>
						<label>Opcionais</label>
                        <div class='div'>
                            ".montaFiltro($carro->getOpcionais('array'),'carro','OPCIONAIS','opcionais','checkbox')."
                        </div>
                    </div>
              
                </div>";
				
                $display = is_categoria($anuncio->getCategoria(),'moto')? null: "style='display:none;'";
                echo "<div {$display} id='parteMoto'>
                    
                    <div id='div'>
                        <label>Marca *</label>
                        ".montaFiltro($moto->getMarca(),'moto','MARCAS','marca','select')."                       
                    </div id='div'>
                    
                    <div id='div'>
                        <label>Cilindrada *</label>
                        ". selectCilindrada($moto->getCilindrada(), 1)."
                    </div>
                    
					<div id='div'>
                        <label>Quilometragem *</label>
						<input type='text' name='quilometragem' value='{$moto->getQuilometragem()}' placeholder='entre 0  a 1.000.000'>
                    </div>
					
                    <div id='div'>
                        <label>Ano *</label>
                        ".selectAnos($moto->getAno(), 1)."
                    </div>
                </div>";
		
                ?>
                <div id='div'>
                    <label>Titulo *</label>
                    <input type='text' name='titulo' value='<?=$anuncio->getTitulo();?>' placeholder='titulo do anuncio' maxlength='100'>
                    <img src='../assets/img/icones/inf.png' id='info' alt='campo titulo ,no minimo 1 caracter e no maximo 100 caracteres' >
                </div>

                <div id='div'>
                    <label>Adicione até seis imagens . JPG, JPEG, PJPEG e PNG somente *</label>
					<a class='div'>Clique em cima da imagem para definer a imagem principal</a>
					<div class='div' id='localFileImgs'>
                        <input type='file' name='inputFiles' accept="image/jpg,image/jpeg,image/pjpeg,image/png" />
                        <div id='localImg'>
                            <?php
							$imagens = $anuncio->getImagens('explode');
                       
							foreach ($imagens as $img) {
								echo"<div id='caixaImg'>
										<div>
											<a>PRINCIPAL</a>
											<img src='../assets/img/anuncios/{$img}'>
										</div>
										<button type='button' id='removerImg'>remove</button>
										<button type='button' id='alteraImg'>altera</button>
									</div>";
							}
							
							$display = (count($imagens) < 6) ? '' : 'style="display:none"';
							
							echo "<div id='btAnexarImg' {$display} ></div>";
                            ?>
						</div>
					</div>
                </div>

                <div id='div'>
                    <label>Descrição *</label>
                    <textarea name='descricao' placeholder='descrição do anúncio' maxlength='500'  ><?=$anuncio->getDescricao();?></textarea>
                    <img src='../assets/img/icones/inf.png' id='info' alt='campo descrição ,no minimo 1 caracter e no maximo 500 caracteres' >
                </div>

                <div id='div'>
                    <label>Valor (R$) *</label>
                    <input type='text' name='valor' value='<?=$anuncio->getValor();?>' placeholder='R$ 0.00'>
                    <img src='../assets/img/icones/inf.png' id='info' alt='campo valor , somente numeros de 0-9 e pontos e virgular' >
                </div>

                <div id='div'>
                    <a class='div'>Digite apenas os numeros do telefone que no final é ajustado.</a>
                    <label>Telefone *</label>
                    <input type='text' name='telefone' value='<?=$anuncio->getTelefone();?>' maxlength='15' placeholder='(xx) xxxxxxxx' >
                    <img src='../assets/img/icones/inf.png' id='info' alt='campo telefone , somente numeros de 0-9, 10 caracteres' >
                </div>

                <div id='div'>
                    <a class='div'>Digite o CEP e pressione ENTER para completar seu dados de enderço.</a>
                    <label>Cep *</label>
                    <input type='text' name='cep' value='<?=$anuncio->getCep();?>' maxlength='8' placeholder='SOMENTE NUMEROS'>
                    <img src='../assets/img/icones/inf.png' id='info' alt='campo cep, somente numeros de 0-9, 8 caracteres' >
                </div>

                <div id='div'>
                    <label>Estado *</label>
                    <select name='estado' id='getEstados' alt='<?=$anuncio->getEstado();?>' ></select>
                </div>

                <div id='div'>
                    <label>Cidade *</label>
                    <select name='cidade' id='getCidades' alt='<?=$anuncio->getCidade();?>' ></select>
                </div>
				
				<div id='div'>
					<label>Bairro *</label>
					<input type='text' name='bairro' value='<?=$anuncio->getBairro();?>' maxlength='50' placeholder='BAIRRO'>
					<img src='../assets/img/icones/inf.png' id='info' alt='campo bairro, somente letras de A-Z, de 0 á 50 caracteres' >
				</div>
				
                <div id='div'>
                    <button type='button' id='btPublicarAnuncio' >publicar anúncio</button>
                </div>
            </form>

            <div id='caixaUploadAnuncio'>
                <div id='centro'>
                    <button type='button' id='btCancelarPublicarAnuncio' >cancelar publicação</button>
                    <div id='progress'>
                        <div></div>
                        <span></span>
                    </div>
                </div>
            </div>

        </div>

    </body>
    <script type="text/javascript" src="../assets/js/jquery_code.js"></script>
    <script type="text/javascript"src="../assets/js/todasPaginas.js"></script>
    <script type="text/javascript" src="../assets/js/cidadesEstados.js"></script>
    <script type="text/javascript" src="../assets/js/selectCategorias.js"></script>
    <script type="text/javascript" src="../assets/js/anunciar.js"></script>
</html>
