$(function () {

    $.fn.tooltip = function () {

        this.hover(function () {
            var texto = $(this).attr('alt');

            $('body').append("<span id='titleTooltip'>" + texto + "</span>");
            $('#titleTooltip').fadeIn(555);
        }, function () {
            $('#titleTooltip').remove();
        }).mousemove(function (e) {
            var mousex = e.pageX + 20;
            var mousey = e.pageY + 20;

            $('#titleTooltip').css({top: mousey, left: mousex});
        });

    };

    $('svg a').tooltip();
	
	$('svg a').hover(function () {
            var href = $(this).attr('href');
			$('#direita #lista-estados a[href="'+href+'"]').addClass('this-hover');
		}, function () {
            var href = $(this).attr('href');
			$('#direita #lista-estados a[href="'+href+'"]').removeClass();
	});
	
	$('#direita #lista-estados a').hover(function () {
            var href = $(this).attr('href');
			$('svg a[href="'+href+'"]').attr('id','this-hover');
		}, function () {
            var href = $(this).attr('href');
			$('svg a[href="'+href+'"]').removeAttr('id');
	});
	
    $('#forms .iconeWebSite').tooltip();

    $('body').delegate('#semFoco', 'click', function () {

        $(this).removeAttr('id');
        $(this).siblings('span').attr('id', 'semFoco');

        if($(this).index())
            $('#login').hide().siblings('#cadastro').fadeIn();
        else
            $('#cadastro').hide().siblings('#login').fadeIn();
        
    })

    var regex = {
        email: function (e) {
            var r = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/;
            return r.test(e);
        },
        senha: function (s) {
            var r = /^([a-zA-Z0-9]){6,16}$/;
            return r.test(s);
        }
    };

    $('#fazerLogin').click(function () {
  
        var email = $('#login input[name=email]').val();
        var senha = $('#login input[name=senha]').val();
        var mLogado = $('#Mlogado:checked').length;
        
        if (regex.email(email) && regex.senha(senha)) {

            $.ajax({
                url: '',
                method: 'POST',
                data: {
                    login: true,
                    email: email,
                    senha: senha,
                    manterLogado: mLogado
                }
            }).done(function (re) {
                var retorno = re.trim();
				console.log(re)
                if (retorno == 'email') {
                    textoInput('#login input[name=email]','este email de usuario não foi encontrado em nosso sistema');
                } else if (retorno == 'senha') {
                    textoInput('#login input[name=senha]','esta senha não corresponde ao email logado em nosso sistema');
                }else if(retorno == ''){
                    location.href = 'anuncios.php';
                }
            });

        } else {
            if (!regex.email(email)){
                textoInput('#login input[name=email]','insira seu email de usuario');
            } else if (!regex.senha(senha)){
                textoInput('#login input[name=senha]','insira sua senha de usuario');
            }
        }
    });
    
    $('#login input[placeholder]').focusout(function(){
        $(this).parent('p').find("#textoInput").remove();
    });
    
    function textoInput(input,texto){
        $(input).parent('p').append("<a id='textoInput' >"+texto+"</a>");
        $(input).focus();
    };
    
});