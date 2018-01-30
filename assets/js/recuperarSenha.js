$(function(){

	var regex = {
		email: function(e){
			var r = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/;
			return r.test(e);
		},
		senha: function(s) {
            var r = /^([a-zA-Z0-9]){6,16}$/;
            return r.test(s);
        }
	};

	$('#recuperarSenha #localizarUsuario').click(function(){
		var email = $('#recuperarSenha input[name=email]').val().trim();
		if(regex.email(email)){
			$.ajax({
				url:'../assets/arquivos_php/requisicoesAjax.php',
				method:'POST',
				data:{
					acao:'localizarEmail',
					email:email
					}
			}).done(function(re){
				
				if(re > 0){
					var html = "<div id='form'>"+
									"<input type='hidden' name='emailPRS' value='"+email+"' >"+
									"<button id='sendEmail' >enviar email para: "+email+"</button>"+
								"</div>";
				}else
					var html = "<h4>usuario não encontrado<h4>";
				
				$('#localResultado').html(html);
			})
		}else
			$('#localResultado').html('<h4>email inválido</h4>');
	});

	$('body').delegate('#sendEmail','click',function(){

		var email = $('#form input[name=emailPRS]').val().trim();
		$(this).removeAttr('id');
		$('#enviandoEmail #centro').html("enviando email para: "+email+" <img src='../assets/img/icones/load.gif'>");
		$('#enviandoEmail').show();
		
		var reloadPagina = true

		$(window).bind('beforeunload',function(){ 
			if(reloadPagina) return true; 
		});

		$.ajax({
			url:'',
			method:'POST',
			data:{emailPRS:email}
		}).done(function(re){
			document.write(re)
			reloadPagina = false;
			$('#enviandoEmail #centro').html(
				"<div align='center'>"+
					"email enviado com sucesso! <img src='../assets/img/icones/ok.png'>"+
					"<h4>"+
						"<p><a href=''>atualizar pagina</a></p>"+
						"<p><a href='index.php'>efetuar login</a></p>"+
					"</h4>"+
				"</div>"
			);
		})
	});
	
	function textoInput(input, texto) {
        $(input).parent('p').append("<a id='textoInput' >" + texto + "</a>");
    };

    $('form input[placeholder]').focusout(function () {
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
	
	$('form input[name=senha1]').keyup(function () {
     
        var senha = $(this).val();

        if (regex.senha(senha))
            valido(this);
        else
            erro(this);

        thiis = $('form input[name=senha2]');
        var senha2 = thiis.val();

        if (regex.senha(senha) && senha == senha2)
            valido(thiis[0]);
        else
            erro(thiis[0]);

    }).focus(function () {
        textoInput(this, 'somente letras de [A-Z] e numeros de [0-9], de 6 á 16 caracteres');
    });

    $('form input[name=senha2]').keyup(function () {
       
        var senha1 = $('form input[name=senha1]').val();
        var senha2 = $(this).val();

        if (regex.senha(senha2) && senha1 == senha2)
            valido(this);
        else
            erro(this);
        
    }).focus(function () {
        textoInput(this, 'confirme sua senha, deve ser igual sua senha anterior');
    });

    $('form #vsenha').click(function () {

        if ($(this).is(':checked'))
            $('form input[name^=senha]').attr('type', 'text');
        else
            $('form input[name^=senha]').attr('type', 'password');
    });
	
	
	$('form button').click(function () {
        var dados = {
            senha1: $('form input[name=senha1]').val(),
            senha2: $('form input[name=senha2]').val()
        };

        if (regex.senha(dados.senha1) && regex.senha(dados.senha2) && dados.senha1 == dados.senha2)
			$('form').submit();
	});
	
});