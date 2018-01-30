$(function () {

    $('#denunciarAnuncio').click(function () {
        var html = "<div id='localFazerDenunciaAnuncio' >" +
                "<h3 align='center'>Deseja denunciar esse anúncio</h3>" +
                "<h4 align='center'>Qual o problema deste anúncio?</h4>" +
                "<p><input type='radio' name='denunciarAnuncio' value='1' checked >Fraude ou span</p>" +
                "<p><input type='radio' name='denunciarAnuncio' value='2' >Duplicado</p>" +
                "<p><input type='radio' name='denunciarAnuncio' value='3' >Categoria errada</p>" +
                "<p><input type='radio' name='denunciarAnuncio' value='4' >Já foi vendido</p>" +
                "<p><input type='radio' name='denunciarAnuncio' value='5' >Dados incorretos</p>" +
                "<p><input type='radio' name='denunciarAnuncio' value='6' >Itens não permitidos</p>" +
                "<div id='sendDenuncia'>" +
                    "<textarea maxlength='500' placeholder='Descreva sua denuncia por favor. limite de 500 caracteres'></textarea>" +
					"<button id='cancelaFechalightbox' >cancelar</button>"+
                    "<button id='enviarDenuncia'>enviar</button>" +
                "</div>" +
                "</div>";
        lightbox(html);
    });

    function lightbox(conteudo) {
        var html = "<div id='lightbox'>" +
                "<div id='conteudoLightbox'>" +
                conteudo +
                "</div>" +
                "</div>";
        $('body').append(html);
    }

    $('body').delegate('#lightbox, #cancelaFechalightbox', 'click', function () {
        $(this).remove();
    }).delegate('#lightbox #conteudoLightbox', 'click', function (e) {
        e.stopPropagation();
    }).delegate('#localFazerDenunciaAnuncio #enviarDenuncia', 'click', function () {

        var valNum = $('#localFazerDenunciaAnuncio p input:checked').val();
        var descricao = $('#localFazerDenunciaAnuncio div textarea').val();

        if (valNum && descricao) {
            $.ajax({
                url: '',
                method: 'POST',
                data: {
                    denunciar: true,
                    tipo: valNum,
                    descricao: descricao
                }
            }).done(function (re) {
                $('#lightbox').remove();
            });
        } else {
            alert('selecione uma denuncia e faça sua descrição');
        }
    });
});