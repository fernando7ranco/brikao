$(function () {

    var valores = [0, 0, 0];

    function textoInput(input, texto) {
        $(input).parent('p').append("<a id='textoInput' >" + texto + "</a>");
    };

    $('#cadastro input[placeholder]').focusout(function () {
        $(this).parent('p').find("#textoInput").remove();
    });

    function erro(thiss) {
        $(thiss).siblings('img').remove();
        $(thiss).parent('p').append("<img id='cf' src='../assets/img/icones/error.png'>");
    }
    function valido(thiss) {
        $(thiss).siblings('img').remove();
        $(thiss).parent('p').append("<img id='cf' src='../assets/img/icones/ok.png'>");
    }
	
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

    $('#cadastro input[name=email]').keyup(function () {
        
        var email = $(this).val();

        if (regex.email(email))
            valido(this);
        else
            erro(this);

    }).focus(function () {
        textoInput(this, 'insira um email valido, que você use atualmente, limite de 60 caracteres');
    });

    $('#cadastro input[name=senha1]').keyup(function () {
     
        var senha = $(this).val();

        if (regex.senha(senha))
            valido(this);
        else
            erro(this);

        thiis = $('#cadastro input[name=senha2]');
        var senha2 = thiis.val();

        if (regex.senha(senha) && senha == senha2)
            valido(thiis[0]);
        else
            erro(thiis[0]);

    }).focus(function () {
        textoInput(this, 'somente letras de [A-Z] e numeros de [0-9], de 6 á 16 caracteres');
    });

    $('#cadastro input[name=senha2]').keyup(function () {
       
        var senha1 = $('#cadastro input[name=senha1]').val();
        var senha2 = $(this).val();

        if (regex.senha(senha2) && senha1 == senha2)
            valido(this);
        else
            erro(this);
        
    }).focus(function () {
        textoInput(this, 'confirme sua senha, deve ser igual sua senha anterior');
    });

    $('#cadastro #vsenha').click(function () {

        if ($(this).is(':checked'))
            $('#cadastro input[name^=senha]').attr('type', 'text');
        else
            $('#cadastro input[name^=senha]').attr('type', 'password');
    });

    function getDadosCadastro() {

        return {
            email: $('#cadastro input[name=email]').val(),
            senha1: $('#cadastro input[name=senha1]').val(),
            senha2: $('#cadastro input[name=senha2]').val()
        };
    };

    $('#cadastro #fazerCadastro').click(function () {
        var dados = getDadosCadastro();

        if (regex.email(dados.email) && regex.senha(dados.senha1) && regex.senha(dados.senha2) && dados.senha1 == dados.senha2) {
            $.ajax({
                url: 'index.php',
                method: 'POST',
                data: {
                    cadastro: true,
                    dados
                }
            }).done(function (re) {
				retorno = re.trim();
				
                if (retorno == 1)
                    valErro();
                else if (retorno == 2)
                    $('#cadastro input[name=email]').siblings('font').text("esse e-mail já esta logado ao nosso site");
                else if (retorno == 3)
                    alert('erro no sistema, recarregue a pagina e tente novamente');
                else if (retorno == '')
                    location.href = 'cadastro.php?bemvindo';
            });
        } else
            valErro();
    });

    function valErro() {

        var dados = getDadosCadastro();

        if (!regex.email(dados.email)) {
            erro('#cadastro input[name=email]');
        }
        if (!regex.senha(dados.senha1)) {
            erro('#cadastro input[name=senha1]');
        }
        if (!regex.senha(dados.senha2)) {
            erro('#cadastro input[name=senha2]');
        }
        if (dados.senha1 != dados.senha2) {
            erro('#cadastro input[name=senha1]');
        }
    };

    window.functionApps = function (dados, imgLoad) { // facebook e google+
        $.ajax({
            url: '',
            method: 'POST',
            data: {
                apps: true,
                dados: dados
            }
        }).done(function (re) {
			var retorno = re.trim();
            imgLoad(false);
            if (retorno == '1')
                location.href = 'anuncios.php';
            else if (retorno == '2')
                alert('este email já esta logado á uma conta, esqueceu sua senha? tente recupera-la, na opção Recuperar Senha');
            else if (retorno == '3')
                location.href = 'cadastro.php?bemvindo';
            else if (retorno == '4') {
                location.href = 'index.php?CADASTRO';
                alert('erro no sistema, desculpe atualize a pagina e tente novamente');
            }
        });
    };

});