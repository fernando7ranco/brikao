$(document).ready(function () {

    $('#btAddComentarios').click(function () {
        var idAnuncio = $('#anuncioUnico').attr('value');
        var comentario = $('#caixaAddComentarios').val().trim();

        if (comentario) {
            $.ajax({
                url: '',
                method: 'POST',
                data: {
                    idAnuncio: idAnuncio,
                    comentario: comentario,
                    insertComentario: true
                }
            }).done(function (re) {

                if (re != 0) {
                    $('#caixaAddComentarios').val('');

                    if ($('#localComentarios h2').length === 1)
                        $('#localComentarios h2').after(re);
                    else
                        $('#localComentarios').append('<h2>comentarios</h2>' + re);
                }
            });
        }
    });

	$('#bloquearUsuario').click(function () {
		var id = $(this).attr('value');
        var html = "<div>" +
                    "<h3 align='center'>Deseja bloquear esse usuario</h3>" +
                   "<button id='cancelaFechalightbox' >cancelar</button>"+
                   "<button id='confirmaBloquearUsuario' value='"+id+"'>bloquear</button>"+
                "</div>";
        lightbox(html);
    });
    
    $('body').delegate('button#confirmaBloquearUsuario', 'click', function () {
		
		var idBloqueado = $(this).attr('value');

        $.ajax({
            url: '',
            method: 'POST',
            data: {
                bloquear:true,
                idBloqueado: idBloqueado
            }
        }).done(function (re) {
            document.write(re)
        });
       
    });
	
	function lightbox(conteudo) {
        var html = "<div id='lightbox'>" +
                "<div id='conteudoLightbox' align='center'>" +
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
	
    var numComentarios = 10;
    $('body').delegate('#btResponderComentario', 'click', function () {
        $(this).after("<div id='caixaResponderComentario' align='right'><textarea placeholder='Responder'></textarea><button id='can' >cancelar</button><button id='res'>reponder</button></div>");
        $(this).remove();

    }).delegate('#caixaResponderComentario button#can', 'click', function () {
        var comentario = $(this).parents('#comentario');
        $(this).parent('#caixaResponderComentario').remove();
       comentario.append('<a id="btResponderComentario">Responder</a>');
    }).delegate('#caixaResponderComentario button#res', 'click', function () {

        var $thiss = $(this);
        var comentario = $thiss.siblings('textarea').val().trim();

        if (comentario) {
            var idAnuncio = $('#anuncioUnico').attr('value');
            var idComentario = $thiss.parents('#comentario').attr('value');
           $thiss.siblings('textarea').val('');

            $.ajax({
                url: '',
                method: 'POST',
                data: {
                    idAnuncio: idAnuncio,
                    idComentario: idComentario,
                    comentario: comentario,
                    inserirComentarioResposta: true
                }
            }).done(function (re) {
                if (re !== 0)
                    $thiss.parent('#caixaResponderComentario').before(re);
            });
        }else
            $thiss.siblings('textarea').focus();

    }).delegate('.carregarMaisComentarios', 'click', function () {
        var thiss = $(this);
        thiss.removeClass();
        thiss.text('carregando comentarios ...');

        var idAnuncio = $('#anuncioUnico').attr('value');
        var comentario = $('#caixaAddComentarios').val().trim();

        $.ajax({
            url: '',
            method: 'POST',
            data: {
                idAnuncio: idAnuncio,
                numComentarios: numComentarios,
                carregarMaisComentarios: true
            }
        }).done(function(re) {
            thiss.remove();
            numComentarios += 10;
            $('#localComentarios').append(re);
        });

    }).delegate('#excluirComentario', 'click', function () {
        var comentario = $(this).parents('div[id^=comentario]:eq(0)');
        var idComentario = comentario.attr('value');
        comentario.remove();
        $.ajax({
            url: '',
            method: 'POST',
            data: {
                idComentario: idComentario,
                deleteComentarios: true
            }
        });
    });
});