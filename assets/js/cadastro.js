$(function(){
	
	var reloadPagina = true;
	$(window).bind('beforeunload', function() { 
		if(reloadPagina) return true; 
	});
	
	var regex = {
		nome: function(n){
			var r = /^([a-z A-Z\u00C0-\u00FF]){3,35}$/;
			return r.test(n);
		},
		telefone: function(t){
			var r = /^(\(\d{2}\)\s\d{4,5}-\d{4}){0,15}$/;
			return r.test(t);
		},
		cep: function(c){
			var r = /^(\d){8}$/;
			return r.test(c);
		},
		bairro: function(b){
			var r = /^([a-z A-Z\u00C0-\u00FF]){0,50}$/;
			return r.test(b);
		},
		logradouro: function(e){
			var r = /^([a-z A-Z\u00C0-\u00FF](\d{1,6})?){0,80}$/;
			return r.test(e);
		},	
		email: function(e){
			var r = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/;
			return r.test(e);
		},
		senha: function(s){
			var r = /^([a-zA-Z0-9]){6,16}$/;
			return r.test(s);
		}
	}
	
	$('#localFinrmacoesDeCadastro p img').tooltip();
		
	$('input[name=nome]').keyup(function(){
		var thiss = $(this);
		thiss.val(thiss.val().replace(/ /g,' '));
		var nome = thiss.val().trim();
		
		if(regex.nome(nome))
			thiss.removeClass();
		else
			thiss.addClass('erroInput')
		
	})
	
	$('input[name=telefone]').keyup(function(){
		
		var thiss = $(this);
		var tel = $(this).val();
		tel = tel.replace(/[^\d]{1,}/g,'').replace(/(\d{2})(\d{4,5})(\d{4})/g,"($1) $2-$3");
		thiss.val(tel);
		
		if(regex.telefone(tel))
			thiss.removeClass();
		else
			thiss.addClass('erroInput')
	})
	
	$('input[name=cep]').keyup(function(){
		var thiss = $(this);
		var cep = $(this).val();
		
		if(regex.cep(cep) || cep.length == 0)
			thiss.removeClass();
		else
			thiss.addClass('erroInput')
	})	
	
	$('input[name=cep]').keydown(function(event){
		
		if(event.keyCode == 13){
		
			var cep = $(this).val();
			
			if(regex.cep(cep)){
				
				$('input[name=cep]').addClass('loadInput')
				//Consulta o webservice viacep.com.br/
				$.getJSON("//viacep.com.br/ws/"+ cep +"/json/?callback=?",function(dados){
					
					if(!("erro" in dados)){
						$("#localFinrmacoesDeCadastro input[name=logradouro]").val(dados.logradouro);
						$("#localFinrmacoesDeCadastro input[name=bairro]").val(dados.bairro);
						selectsEC(dados.uf,dados.localidade)				
						$('input[name=cep]').removeClass();
					}else{
						alert("CEP não encontrado.");
						$('input[name=cep]').addClass('erroInput')
					}
					
				}).fail(function() {
					alert("erro no sistema, CEP não encontrado.");
				})
			}else
				$(this).addClass('erroInput');
		}
	});
	
	$('input[name=bairro]').keyup(function(){
		var thiss = $(this);
		var bairro = $(this).val();
		
		if(regex.bairro(bairro))
			thiss.removeClass();
		else
			thiss.addClass('erroInput')
	})	
	
	$('input[name=logradouro]').keyup(function(){
		var thiss = $(this);
		var logradouro = $(this).val();
		
		if(regex.logradouro(logradouro))
			thiss.removeClass();
		else
			thiss.addClass('erroInput')
	})	
	
	
	$('#localFinrmacoesDeCadastro form').submit(function(){
		var campoNome = $('input[name=nome]');
		if(!regex.nome(campoNome.val())){
			alert('erro campo nome obrigatorio, somente letra de A-Z');
			campoNome.select();
			return false;
		}else
			reloadPagina = false;
		
	}).keydown(function(event){
		if(event.keyCode == 13) {
			event.preventDefault();
			return false;
		}
	});
	
	$('#localFinrmacoesDeCadastro #exclurLogado').click(function(){
		
		if($(this).is(':checked'))
			$(this).parent('p').append("<span>essa opção é para excluir sua conta, seus dados serão todos deletados</span>");
		else
			$(this).parent('p').find('span').remove();
	})
	
	function contaDadosInput(){
		dados = {
			email: function(){
				var email = $('#emailLogado').val();
				if(regex.email(email))
					return email;
				else 
					return null;
			},		
			senha: function(){
				var senha = $('#senhaLogado').val();
				if(regex.senha(senha))
					return senha;
				else 
					return null;
			},
			excluir: function(){
				var conta = $('#exclurLogado');
				if(conta.is(':checked'))
					return true;
				else 
					return null;
			}
		};
		
		return {
			email:dados.email(),
			senha:dados.senha(),
			excluir:dados.excluir()
		};
	};
	
	$('#fazerAlteracoes').click(function(){
		
		var senha = $('#senhaUsuario').val();
		if(regex.senha(senha)){
			
			var dados = contaDadosInput();
			if(dados.email || dados.senha || dados.excluir ){
				dados['identificador'] = senha;
				$.ajax({
					url:'',
					method:'POST',
					data:{
						conta:true,
						brikao:true,
						dados:dados
					}
				}).done(function(retorno){
					if(retorno == 1)
						alert('erro na autentificação do usuario');
					else{
						reloadPagina = false;
						location.href = '';
					}
				})		
			}
		}else 
			$('#senhaUsuario').focus();
	})
	
	window.functionAppFacebook = function(appDados,imgLoad){
		
		var dados = contaDadosInput();
	
		if(dados.email || dados.senha || dados.excluir){
			dados['identificador'] = appDados.id;
			$.ajax({
				url:'',
				method:'POST',
				data:{
					conta:true,
					appFacebook:true,
					dados:dados
				}
			}).done(function(re) {
				var retorno = re.trim();
				imgLoad(false);
				if(retorno == '1')
					alert('erro na autentificação do usuario');
				else{
					reloadPagina = false;
					location.href = '';
				}
			})	
		}else
			imgLoad(false);		
	}
	
	window.functionAppGoogle = function(appDados,imgLoad){
		var dados = contaDadosInput();
		
		if(dados.email || dados.senha || dados.excluir){
			dados['identificador'] = appDados.id;
			
			$.ajax({
				url:'',
				method:'POST',
				data:{
					conta:true,
					appGoogle:true,
					dados:dados
				}
			}).done(function(re) {
				var retorno = re.trim();
				imgLoad(false);
				if(retorno == '1')
					alert('erro na autentificação do usuario');
				else{
					reloadPagina = false;
					location.href = '';
				}
			})	
		}else
			imgLoad(false);
	}
	
});