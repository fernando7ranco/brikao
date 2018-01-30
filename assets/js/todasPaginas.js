$(function(){
	
	$.fn.tooltip = function(){
		
		this.hover(function(){
			var texto = $(this).attr('alt');
	
			$('body').append("<span id='titleTooltip'>"+texto+"</span>");
			$('#titleTooltip').fadeIn(555);
		},function(){
			$('#titleTooltip').remove();
		}).mousemove(function(e){
			 var mousex = e.pageX + 20;
			 var mousey = e.pageY + 10; 
			
			$('#titleTooltip').css({top:mousey ,left: mousex });
		})
					
	};
	
	$('header nav a').tooltip();
	
	var dir = window.location.pathname.split('/').pop();

	switch(dir)
	{
		case 'cadastro.php': $('header nav a:eq(0)').addClass('thisPage');
			break;	
		case 'anuncio.php':
		case 'anuncios.php': 
				$('header nav a:eq(1)').addClass('thisPage');
			break;		
		case 'chat.php': $('header nav a:eq(2)').addClass('thisPage');
			break;
		case 'anunciar.php': $('header nav a:eq(3)').addClass('thisPage');
			break;	
		case 'meusanuncios.php': 
		case 'editaranuncio.php': 
				$('header nav a:eq(4)').addClass('thisPage');
			break;
	};
	
	$('header nav a:eq(5),#notificacoes').click(function(e){
		e.stopPropagation();
	})
	$('header nav a:eq(5)').click(function(){
		
		$('#notificacoes').stop(true,true).slideToggle(200,function(){
			
			$(this).find('#local').html("<div id='load' >carregando <img src='../assets/img/icones/load.gif'></div>");
			
			if($(this).is(":visible")){
	
				$.ajax({
					url:'../assets/arquivos_php/requisicoesAjax.php',
					method: 'POST',
					data:{
						acao:'notificacoes',
						qual:1
					}
				}).done(function(re){
					re = re.trim();
			
					if(!re) re = '<div>você não possui novas notificações</div>';
					
					$('.carregarMaisNotificacoes, #load').remove();
					
					$('#notificacoes #local').append(re);
					$('#nNotificacoes').text('').hide();
				})
			}
		
		})
	})

	function verNumeroNotificacoes(){
		$.ajax({
			url:'../assets/arquivos_php/requisicoesAjax.php',
			method: 'POST',
			data:{
				acao:'notificacoes',
				qual:2
			}
		}).done(function(re){
			
			if(re > 0)
				$('#nNotificacoes').text(re).show();
			else
				$('#nNotificacoes').text('').hide();
			
			timeOutNN();
		})
	}
	
	function timeOutNN(){
		setTimeout(function(){
			verNumeroNotificacoes();
		},10000)
	}
	
	if($('header nav a:eq(5)').length){
		verNumeroNotificacoes();
		$('body').click(function(){
			$('#notificacoes').slideUp();
		});
	}
	
	function getTimeZoneData() {
		var today = new Date();
		var jan = new Date(today.getFullYear(), 0, 1);
		var jul = new Date(today.getFullYear(), 6, 1);
		var dst = today.getTimezoneOffset() < Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
		return {
			offset: -today.getTimezoneOffset() / 60, 
			dst: +dst
		};
	}
		
	$.ajax({
		url: '../assets/arquivos_php/difineFusoHorario.php',
		method:'POST',
		data:getTimeZoneData()
	});
  
})