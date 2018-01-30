$(function () {

    var reloadPagina = true;
	
    $(window).bind('beforeunload', function () {
        if (reloadPagina)
            return true;
    });

    var arrayImgs = [], principalImg = 0, condicaoImg, indexAlteraImg;

    $('#localImg #caixaImg img').each(function (i, value) {
        arrayImgs.push($(this).attr('src').split('/').pop());
    });
	
	$('select[name="categorias[]"]').change(function(){
		var id = $(this).attr('id');
		var categoria = $(this).val(); 
		
		$('#parteCarro, #parteMoto').hide();
			
		if(id == 'terciaria' && categoria == '641')
			$('#parteCarro').slideDown();
		else if(id == 'terciaria' && categoria == '645')
			$('#parteMoto').slideDown();
	
	});
    

    $('#btAnexarImg').click(function () {
        $('[name=inputFiles]').click();
        condicaoImg = 'inseri';
    });

    $('[name=inputFiles]').change(function (e) {

        if ($(this).val().trim() !== '') {

            var files = e.target.files;

            var types = ['image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png'];

            if (types.indexOf(files[0].type) > -1) {

                var file = URL.createObjectURL(files[0]);

                var veSeAUrlEImagem = "<img src='" + file + "' >";

                $(veSeAUrlEImagem).load(function () {

                    var w = $(this).get(0).naturalWidth;
                    var h = $(this).get(0).naturalHeight;

                    if (w >= 300 && h >= 300) {
                        if (condicaoImg == 'inseri') {

                            $('#localFileImgs #btAnexarImg').before("<div id='caixaImg'><div><a>PRINCIPAL</a>" + veSeAUrlEImagem + "</div><button type='button' id='removerImg'>remove</button><button type='button' id='alteraImg'>altera</button></div>");
                            arrayImgs.push(files[0]);
                            if (arrayImgs.length == 6)
                                $('#btAnexarImg').hide();

                        } else if (condicaoImg == 'altera') {

                            arrayImgs.splice(indexAlteraImg, 1, files[0]);
                            $('#localImg #caixaImg').eq(indexAlteraImg).find('img').attr('src', file);

                        };
                        
                    } else
                        alert('sua imagem não é grande o suficiente possui as seguintes dimenções ' + w + ' largura e ' + h + ' altura e as minimas necessarias são de 300px de largura e 300px de altura');
                });
            } else
                alert('arquivo invalido');
        }
  
    });

    $('#localFileImgs').delegate('#removerImg', 'click', function () {
        var thiss = $(this).parents('#caixaImg');
        var index = thiss.index();
        thiss.remove();
        arrayImgs.splice(index, 1);
        $('#btAnexarImg').show();

        if (principalImg === index)
            principalImg = 0;

    }).delegate('#alteraImg', 'click', function () {
        var thiss = $(this).parents('#caixaImg');
        indexAlteraImg = thiss.index();
        $('[name=inputFiles]').click();
        condicaoImg = 'altera';

    }).delegate('#localImg #caixaImg div:not(.principal)', 'mouseover mouseout', function (e) {

        if (e.type === 'mouseover') {
            $(this).find('a').show();
        } else {
            $(this).find('a').hide();
        }

    }).delegate('#localImg #caixaImg div:not(.principal)','click', function () {
		
        $('#localImg  #caixaImg div').removeAttr('class');
        $('#localImg  #caixaImg div a').hide();
        $(this).attr('class','principal');
        $(this).find('a').show();
        var index = $(this).parent('#caixaImg').index();
        principalImg = index;
		
    });

    $('div #info').tooltip();
    
    var regex = {
        valor: function (v) {
            var r = /^(\d\.?\,?){1,}$/;
            return r.test(v);
        },
        telefone: function (t) {
            var r = /^(\(\d{2}\)\s\d{4,5}-\d{4}){0,15}$/;
            return r.test(t);
        },
        cep: function (c) {
            var r = /^(\d){8}$/;
            return r.test(c);
        }
    };

    $('input[name=telefone]').keyup(function () {
        var thiss = $(this);
        var tel = $(this).val();
        tel = tel.replace(/[^\d]{1,}/g, '').replace(/(\d{2})(\d{4,5})(\d{4})/g, "($1) $2-$3");
        thiss.val(tel);

        if (regex.telefone(tel))
            thiss.removeClass();
        else
            thiss.addClass('erroInput');
    });
    
    function numeroParaReal(valor){
        valor = valor.replace(/\D/g, "");

        if(valor.length === 1)
            valor = '00'+valor;

        if(valor.length > 3 )
            valor = valor.replace(/^(0{1})(\d)/g,"$2");

        valor = valor.replace(/(\d+)(\d{2})/, "$1,$2");
        valor = valor.replace(/(\d+)(\d{3})(\,\d{2})/, "$1.$2$3");
        valor = valor.replace(/(\d+)(\d{3})(\.\d{3}\,\d{2})/, "$1.$2$3");
        valor = valor.replace(/(\d+)(\d{3})(\.\d{3}\.\d{3}\,\d{2})/, "$1.$2$3");

        return valor;

    };
    
    $('input[name=valor]').keyup(function () {
        
        var valor = $(this).val();
        
        valor = numeroParaReal(valor);

        $(this).val(valor);

    });

    $('input[name=cep]').keyup(function () {
        var thiss = $(this);
        var cep = $(this).val();

        if (regex.cep(cep) || cep.length === 0)
            thiss.removeClass();
        else
            thiss.addClass('erroInput');
    });

    $('input[name=cep]').keydown(function (event) {

        if (event.keyCode === 13) {

            var cep = $(this).val();

            if (regex.cep(cep)) {

                $('input[name=cep]').addClass('loadInput');
                //Consulta o webservice viacep.com.br/
                $.getJSON("//viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {

                    if (!("erro" in dados)) {
                        selectsEC(dados.uf, dados.localidade);
                        $('input[name=bairro]').val(dados.bairro);
                        $('input[name=cep]').removeClass();
                    } else {
                        alert("CEP não encontrado.");
                        $('input[name=cep]').addClass('erroInput');
                    }

                }).fail(function () {
                    alert("erro no sistema, CEP não encontrado.");
                });
            } else
                $(this).addClass('erroInput');
        }
    });

    $('form').keydown(function (event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            return false;
        }
        
    });
    function upload(dados) {

        var form = new FormData();
       
        form.append('imagens[0]', dados.imagens[principalImg]);
		delete dados.imagens[principalImg];
		
        var index = 1;
        for (var i in dados.imagens) {
            form.append('imagens[' + index + ']', dados.imagens[i]);
            index++;
        }
        delete dados.imagens;
        
        for (var key in dados) {
            form.append(key, dados[key]);
        }
      
        form.append('upload', true);
		
		$('#caixaUploadAnuncio').show();
		
        $.ajax({
            url: '',
            data: form,
            processData: false,
            contentType: false,
            type: 'POST',
            xhr: function () {
                var xhr = $.ajaxSettings.xhr();
                xhr.upload.onprogress = function (e) {
                    var porcent = (Math.floor(e.loaded / e.total * 100) + '%');
                    $('#caixaUploadAnuncio #centro #progress div').css('width', porcent);
                };
                return xhr;
            }, success: function (re) {
				
                reloadPagina = false;
                var title = $('title').text();

                if (title === 'anúnciar') {
                    var html = "<center><h3>Parabéns seu anúncio foi publicado com sucesso!</h3>" +
                            "<h4><a href=''>Atualizar pagina</a></h4>" +
                            "<h4><a href='anuncio.php?anuncio=" + re + "'>Visualizar anuncio</a></h4></center>";
                } else {
                    var html = "<center><h3>Parabéns seu anúncio foi editado com sucesso!</h3>" +
                            "<h4><a href=''>Atualizar Pagina</a></h4>" +
                            "<h4><a href='anuncio.php?anuncio=" + re + "'>Visualizar Anúncio</a></h4></center>";
                }

                $('#caixaUploadAnuncio #centro').html(html);

            }, beforeSend: function (jqXHR) {
                $('#btCancelarPublicarAnuncio').click(function () {
                    jqXHR.abort();
                });
            }
        });
    };
    
    function eachInput(input){
        var dados = [];
        $(input).each(function() {
			var val = $(this).val();
			if(val) dados.push(val); 
        });
       
        return dados;
    };

    $('#btPublicarAnuncio').click(function () {
        var dados = {};

        dados['categoria'] = eachInput('select[name="categorias[]"]');
        dados['titulo'] = $('input[name=titulo]').val();
        dados['imagens'] = arrayImgs;
        dados['descricao'] = $('textarea[name=descricao]').val();
        dados['valor'] = $('input[name=valor]').val();
        dados['telefone'] = $('input[name=telefone]').val();
        dados['cep'] = $('input[name=cep]').val();
        dados['estado'] = $('select[name=estado] option:selected').val();
        dados['cidade'] = $('select[name=cidade] option:selected').val();
        dados['bairro'] = $('input[name=bairro]').val();
           
        if (dados.categoria.length != 3)
            alert('selecione as tres categorias primaria, segundaria e terciaria');
        else if (!dados.titulo)
            alert('preencha o titulo');
        else if (!dados.imagens.length)
            alert('anexe uma imagem');
        else if (!dados.descricao)
            alert('preencha a descrição');
        else if (!regex.valor(dados.valor))
            alert('preencha o valor');
        else if (!regex.telefone(dados.telefone))
            alert('preencha o telefone');
        else if (!regex.cep(dados.cep))
            alert('preencha o cep');
        else if (!dados.estado)
            alert('selecione um estado');
        else if (!dados.cidade)
            alert('selecione uma cidade');
        else {
			dados.categoria = dados.categoria.join('-');
			
            if(dados.categoria == '9-89-641'){
                 
                dados['marca'] = $('#parteCarro select[name=marca] option:selected').val();
                dados['quilometragem'] = $('#parteCarro input[name=quilometragem]').val();
                dados['ano'] = $('#parteCarro select[name=ano] option:selected').val();
                dados['portas'] = $('#parteCarro select[name=portas] option:selected').val(); 
                dados['cambio'] = $('#parteCarro select[name=cambio] option:selected').val(); 
                dados['combustivel'] = $('#parteCarro select[name=combustivel] option:selected').val(); 
                dados['tipo'] = $('#parteCarro select[name=tipo] option:selected').val(); 
                dados['opcionais'] = eachInput('#parteCarro input[name="opcionais[]"]:checked').join('-');
               
                if(!dados.marca)
                    alert('selecione uma marca');
                else if(!regex.valor(dados.quilometragem) || dados.quilometragem < 1 || dados.quilometragem > 500000)
                    alert('selecione uma quilometragem');
                else if(!(dados.ano >= 1950 && dados.ano <= $('select[name=ano] option:eq(1)').val()))
                    alert('selecione um ano');
                else if(!dados.portas)
					alert('selecione quantidade de portas');  
				else if(!dados.cambio)
					alert('selecione o cambio'); 
				else if(!dados.combustivel)
					alert('selecione o combustivel');
				else
                    upload(dados);
                
            }else if(dados.categoria == '9-89-645'){
                
                dados['marca'] = $('#parteMoto select[name=marca] option:selected').val();
                dados['cilindrada'] = $('#parteMoto select[name=cilindrada] option:selected').val();
                dados['quilometragem'] = $('#parteMoto input[name=quilometragem]').val();
                dados['ano'] = $('#parteMoto select[name=ano] option:selected').val();
        
                if(!dados.marca)
                    alert('selecione uma marca');
                else if(!regex.valor(dados.cilindrada) || dados.cilindrada < 50 || dados.cilindrada > 1001)
                    alert('selecione uma cilindrada');
                else if(!regex.valor(dados.quilometragem) || dados.quilometragem < 1 || dados.quilometragem > 500000)
                    alert('selecione uma quilometragem');
                else if(!(dados.ano >= 1950 && dados.ano <= $('#parteMoto select[name=ano] option:eq(1)').val()))
                    alert('selecione um ano');
                else
                    upload(dados);
             
            }else
                upload(dados);
            
        }

    });

});