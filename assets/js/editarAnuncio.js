$(document).ready(function(){
	
	var reloadPagina = false
	
	$(window).bind('beforeunload', function() { 
		if(reloadPagina) return true; 
	});
	 
	var arrayImgs = [], principalImg = 0, condicaoImg, indexAlteraImg;
	

	console.log(arrayImgs)
	
	$('#btAnexarImg').click(function(){
		$('[name=inputFiles]').click();
		condicaoImg = 'inseri';
	})
	
	$('[name=inputFiles]').change(function(e){	
		
		if($(this).val().trim() != ''){
			
			var files = e.target.files;

			var types = ['image/jpg','image/jpeg','image/pjpeg','image/png'];
			
			if(types.indexOf(files[0].type) > -1){
			
				var file = URL.createObjectURL(files[0]);	
				
				var veSeAUrlEImagem = "<img src='"+file+"' >";
				
				$(veSeAUrlEImagem).load(function(){
					
					var w = $(this).get(0).naturalWidth; 
					var h = $(this).get(0).naturalHeight;
					
					if(w >= 300 && h >= 300){
						if(condicaoImg == 'inseri'){
							
							$('#localFileImgs #localImg').append("<div id='caixaImg'><label>PRINCIPAL</label>"+veSeAUrlEImagem+"<button type='button' id='removerImg'>remove</button><button type='button' id='alteraImg'>altera</button></div>");
							arrayImgs.push(files[0]);
							if(arrayImgs.length == 6) $('#btAnexarImg').hide();
							
						}else if(condicaoImg == 'altera'){
							
							arrayImgs.splice(indexAlteraImg,1,files[0]);
							$('#localImg #caixaImg').eq(indexAlteraImg).find('img').attr('src',file);
							
						};
					}else
						alert('sua imagem não é grande o suficiente possui as seguintes dimenções '+w+' largura e '+h+' altura e as minimas necessarias são de 300px de largura e 300px de altura');	
				});	
			}else
				alert('arquivo invalido');
		};
	})
	
	$('#localFileImgs').delegate('#removerImg','click',function(){
		var thiss = $(this).parents('#caixaImg');
		var index = thiss.index();
		thiss.remove();
		arrayImgs.splice(index, 1);
		$('#btAnexarImg').show();
		
		if(principalImg == index) principalImg == 0;
		
	}).delegate('#alteraImg','click',function(){
		var thiss = $(this).parents('#caixaImg');
		indexAlteraImg = thiss.index();
		$('[name=inputFiles]').click();
		condicaoImg = 'altera';
		
	}).delegate('#caixaImg img','mouseover mouseout',function(e){
		
		if(e.type == 'mouseover'){
			$(this).prev('label:not([id=true])').show();
		}else{
			$(this).prev('label:not([id=true])').hide();
		}
		
	}).delegate('#caixaImg img','click',function(e){
		$('#caixaImg label[id=true]').hide().removeAttr('id');
		$(this).prev('label').attr('id','true');
		var index = $(this).parents('#caixaImg').index();
		principalImg = index;
	})
	
	$('p #info').tooltip();
	var regex = {
		valor: function(v){
			var r = /^(\d\.?\,?){1,}$/;
			return r.test(v);
		},
		telefone: function(t){
			var r = /^(\(\d{2}\)\s\d{4,5}-\d{4}){0,15}$/;
			return r.test(t);
		},
		cep: function(c){
			var r = /^(\d){8}$/;
			return r.test(c);
		}
	};
	
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
	
	$('input[name=valor]').keyup(function(){
		var thiss = $(this);
		var valor = thiss.val();
		
		if(regex.valor(valor))
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
						selectsEC(dados.uf,dados.localidade)				
						$('input[name=cep]').removeClass();
					}else{
						alert("CEP não encontrado.");
						$('input[name=cep]').addClass('erroInput')
					};
					
				}).fail(function() {
					alert("erro no sistema, CEP não encontrado.");
				})
			}else
				$(this).addClass('erroInput');
		};
	});
	
	$('form').keydown(function(event){
		if(event.keyCode == 13) {
			event.preventDefault();
			return false;
		};
	});
	function upload(arrayImg){
		
		var form = new FormData();

		for(i in arrayImg){
				form.append('imagens['+i+']',arrayImg[i]);
		}
		
	

		$.ajax({
			url: '',
			data:form,
			processData: false,
			contentType: false,
			type: 'POST',
			xhr:function(){
				var xhr = $.ajaxSettings.xhr();
				xhr.upload.onprogress = function(e) {
					var porcent = (Math.floor(e.loaded / e.total * 100) + '%');
					$('#caixaUploadAnuncio #centro #progress div').css('width',porcent);
				};
				return xhr;
			},success:function(x){
				document.write(x)
			
				
			},beforeSend:function(jqXHR){
				$('#btCancelarPublicarAnuncio').click(function(){jqXHR.abort()});
			}
		});
	};
	
	$('#btPublicarAnuncio').click(function(){
		
			upload(arrayImgs)
		
	
	})
	
	
});