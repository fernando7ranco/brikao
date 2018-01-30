$(document).ready(function () {
	
	$('#localOp img').tooltip();
	
    var dadosG, timeOut;

    $('#left').delegate('table', 'click', function () {
		
        var dados = $(this).attr('value').split('/');

        if (JSON.stringify(dados) !== JSON.stringify(dadosG) && typeof dados == 'object')
        {
            $('#loading').show();
            $('#verMensagensAnteriores').hide();

            $(this).attr('id', 'foco').removeClass().siblings('table').removeAttr('id');

            var string = $(this).attr('string').split('/');

            $('#topInfo span:eq(0)').html('Anúncio: <a href="anuncio.php?anuncio='+ dados[0] +'">'+ string[0] +'</a>');
            $('#topInfo span:eq(1)').text('Anúnciante: '+ string[1] +'. Negociante: '+ string[2]);

            $('#right #localMsg #apresentaMsg').html('');

            dadosG = dados;

            if (timeOut)
                clearTimeout(timeOut);

            pegaMensagens(dadosG, '>');
        }
    });
    
    $('#topInfo #localOp img').click(function () {
        $(this).siblings('div').toggle();
    });
    $('#topInfo #localOp').click(function (e) {
        e.stopPropagation();
    });
    $('body').click(function () {
        $('#topInfo #localOp div').hide();
    });

    function atualizaChat() {
        setTimeout(function () {
            $.ajax({
                url: '',
                method: 'POST',
                data: {
                    atualizaChat: true
                }
            }).done(function (re) {

                $('#left #chats').html(re);
                atualizaChat();
                value = dadosG.join('/');
                $("#left #chats table[value='" + value + "']").attr('id', 'foco');
				
				if(!$("#left #chats table#foco").length)
					location.reload();
            });
        }, 8000);
    };
    
    atualizaChat();

    function pegaMensagens(dados, condicao) {

        if (condicao == '>')
            var idCondicao = $('#right #localMsg #divisorDeMensagens:not([class]):eq(-1)').attr('value');
        else
            var idCondicao = $('#right #localMsg #divisorDeMensagens:not([class]):eq(1)').attr('value');

        if (!idCondicao) idCondicao = 0;

        $.ajax({
            url: '',
            method: 'POST',
            dataType: 'JSON',
            data: {
                idAnuncio: dados[0],
                idUsuarioPara: dados[1],
                idCondicao: idCondicao,
                condicao: condicao,
                pegaMsg: true
            }
        }).done(function (re) {
			
            if (re[1] == 20)
                $('#verMensagensAnteriores').show();

            if (re[2] == 0){
                $('#apresentaMsg #imgVisu[class]').html("<img src='../assets/img/icones/visualizada.png' title='mensagem visualizada' >").removeAttr('class');
			}
            if (condicao == '<' && re[1]) {
                $('#right #localMsg #apresentaMsg').prepend(re[0]);
                autoScroll(re[1]);
            } else if (condicao == '>' && re[1]) {

                if ($('#right #localMsg #comece').length)
                    $('#right #localMsg #comece').remove();
				
				$('#localMsg #apresentaMsg .enviandoMsg').remove();
				
                $('#right #localMsg #apresentaMsg').append(re[0]);

                autoScroll();
            } else if ($('#right #localMsg #divisorDeMensagens').length == 0) {
                $('#right #localMsg #apresentaMsg').html("<div align='center' id='comece' ><h4>Começe a Negociar</h2></div>");
                autoScroll();
            }

            loopPegaMsg();
        });
    };

    $('#verMensagensAnteriores').click(function () {
        $(this).hide();
        $('#loading').show();
        pegaMensagens(dadosG, '<');

    });

    $('#enviarMensagem button').click(function () {
        var mensagem = $('#caixaDeMensagem').val().trim();
        var load = $('#localMsg #apresentaMsg .enviandoMsg').length;

        if (mensagem && !load) {
     
            var code = '<div id="divisorDeMensagens" class="enviandoMsg">' +
                    '<div id="alingDivRight">' +
                    '<div class="eu">' +
                    '<span>' + mensagem + '</span>' +
                    '<a id="dataMsg"><img src="../assets/img/icones/load.gif"> enviando mensagem</a>' +
                    '</div>' +
                    '</div>' +
                    '</div>';

            if ($('#right #localMsg #comece').length)
                $('#right #localMsg #comece').remove();

            $('#right #localMsg #apresentaMsg').append(code);

            autoScroll();

            $.ajax({
                url: '',
                method: 'POST',
                data: {
                    idAnuncio: dadosG[0],
                    idUsuarioPara: dadosG[1],
                    mensagem: mensagem,
                    enviarMsg: true
                }
            }).done(function(){
				$('#caixaDeMensagem').val('');
			});
        }
    });

    function loopPegaMsg() {
        timeOut = setTimeout(function () {

            var fim = $('#right #localMsg').prop("scrollHeight") - 680;
            if (fim < $('#right #localMsg').scrollTop())
                pegaMensagens(dadosG, '>');
			else
				loopPegaMsg();

        }, 1500);
    };

    function autoScroll(auto) {
        setTimeout(function () {

            var height = 0;
            var caminho = $('#right #localMsg #divisorDeMensagens');
            var num = caminho.length;

            for (var i = 0; i < num; i++)
            {
                height += parseInt(caminho.eq(i).height()) + 2;

                if (auto && auto < i)
                    break;

                $('#right #localMsg').scrollTop(height);
            }
            $('#loading').slideUp();
        }, 555);
    };

    $('button#excluirChat').click(function () {
        var html = "<div align='center'>" +
                "<h3>Deseja Excluir o Chat</h3>" +
                "<button id='cancelaFechalightbox'>cancelar</button>" +
                "<button id='confimarExcluirChat'>excluir</button>" +
                "</div>";
        lightbox(html);
    });

    $('body').delegate('#confimarExcluirChat', 'click', function () {

        $.ajax({
            url: '',
            method: 'POST',
            data: {
                idAnuncio: dadosG[0],
                idUsuarioPara: dadosG[1],
                excluirChat: true
            }
        }).done(function (re) {
            location.reload();
        });

    });
    
    $('#denunciarUsuario').click(function () {
        var html = "<div>" +
                    "<h3 align='center'>Deseja denunciar esse usuario</h3>" +
                    "<h4 align='center'>Por que você está denunciando esse usuário?</h4>" +
                    "<p><input type='radio' name='denuncia' value='1' >Fraude</p>" +
                    "<p><input type='radio' name='denuncia' value='2' >Comportamento desrespeitoso</p>" +
                    "<p><input type='radio' name='denuncia' value='3' >span</p>" +
                   "<button id='cancelaFechalightbox' >cancelar</button>"+
                   "<button id='confirmaDenunciarUsuario'>denunciar</button>"+
                "</div>";
        lightbox(html);
    });
    
    $('body').delegate('button#confirmaDenunciarUsuario', 'click', function () {

        var tipo = $('#conteudoLightbox p input:checked').val();

        if (tipo) {
            $.ajax({
                url: '',
                method: 'POST',
                data: {
                    denunciar:true,
                    idDenunciado: dadosG[1],
                    tipo: tipo
                }
            }).done(function (re) {
                $('#lightbox').remove();
            });
        } else {
            alert('selecione uma opção de denuncia');
        }
    });
    
    $('#bloquearUsuario').click(function () {
        var html = "<div>" +
                    "<h3 align='center'>Deseja bloquear esse usuario</h3>" +
                   "<button id='cancelaFechalightbox' >cancelar</button>"+
                   "<button id='confirmaBloquearUsuario'>bloquear</button>"+
                "</div>";
        lightbox(html);
    });
    
    $('body').delegate('button#confirmaBloquearUsuario', 'click', function () {

        $.ajax({
            url: '',
            method: 'POST',
            data: {
                bloquear:true,
                idBloqueado: dadosG[1]
            }
        }).done(function (re) {
            location.reload();
        });
       
    });
	
	var arquivoMsg = '';
	var fileArquivo = '';
	
	$('#caixaEnviarMsg #enviarMensagem img').click(function(){
		if(arquivoMsg == '')
			$('#caixaEnviarMsg #enviarMensagem input').click();
		else{
			
			if($('#caixaEnviarMsg #previlarquivomsg:visible').length)
				$(this).addClass('foco');
			else
				$(this).removeClass();
			
			$('#caixaEnviarMsg #previlarquivomsg').toggle();
		}			
	}).tooltip();
	
	$('#caixaEnviarMsg #enviarMensagem input').change(function(e){
		
		if($(this).val() != ''){
			
			var file = e.target.files;
			fileArquivo = file[0];
			var url = URL.createObjectURL(file[0]);
			var tipo = file[0].type;
			var video = new Array('video/ogg','video/mp4','video/webm');
			var img =  new Array('image/jpg','image/jpeg','image/pjpeg','image/png','image/gif');
			
			arquivoMsg = ''; 	
			
			if(jQuery.inArray(tipo,video) != -1)
				arquivoMsg = arquivoMsg = '<video controls ><source src="'+url+'" type="'+tipo+'"></video>';
			else if(jQuery.inArray(tipo,img) != -1)
				arquivoMsg = "<img src="+url+" />";
			else
				alert('arquivo invalido');

			if(arquivoMsg){
				$('#caixaEnviarMsg #previlarquivomsg').show();
				$('#caixaEnviarMsg #localprevilarquivomsg').html(arquivoMsg);
			}
		}
	
	});
	
	function clearUploadArquivoMsg(){
		$('#caixaEnviarMsg #enviarMensagem img').removeClass();
		$('#caixaEnviarMsg #localprevilarquivomsg').html('');
		$('#caixaEnviarMsg #previlarquivomsg').hide();
		$('#caixaEnviarMsg #progressoArquivoMsg div').css('width','0%').text('');
		arquivoMsg = '';
	}
	$('#caixaEnviarMsg #cancelarArquivosDeMsg').click(function(){
		clearUploadArquivoMsg();
	});
	
	$('#caixaEnviarMsg #enviarArquivosDeMsg').click(function(){
	
		if(fileArquivo){
			
			var form = new FormData();
			form.append('idAnuncio',  dadosG[0]);
			form.append('idUsuarioPara', dadosG[1]);
			form.append('arquivo', fileArquivo);
			form.append('uploadArquivo', true);
			
			fileArquivo = '';
			
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
						$('#caixaEnviarMsg #progressoArquivoMsg div').css('width', porcent).text(porcent);
					};
					
					return xhr;
					
				}, success: function() {
					
					clearUploadArquivoMsg();

				}, beforeSend: function (jqXHR) {
					$('#caixaEnviarMsg #cancelarArquivosDeMsg').click(function () {
						jqXHR.abort();
					});
				}
			});
		}
    });
	
    function lightbox(conteudo) {
        var html = "<div id='lightbox'>" +
                "<div id='conteudoLightbox'>" +
                conteudo +
                "</div>" +
                "</div>";
        $('body').append(html);
    };

    $('body').delegate('#lightbox, #cancelaFechalightbox', 'click', function () {
        $('#lightbox').remove();
    }).delegate('#lightbox #conteudoLightbox', 'click', function (e) {
        e.stopPropagation();
    });
});