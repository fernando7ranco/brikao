 $(document).ready(function () {
	 
	$('select').change(function(){
		$('form').submit();
		
	}).each(function (){
		
		if(!$(this).find('option').length)
			$(this).hide();
	});
	
	$('input[name=valorminimo], input[name=valormaximo]').keyup(function () {

		var valor = $(this).val();
		
		valor = numeroParaReal(valor);
		
		$(this).val(valor);

	});
	
	var timeOutBairro; 
	function FtimeOutBairro(bairro){
		timeOutBairro = setTimeout(function(){
			
			var cidade = $('select[name=cidade] option:selected').val();
			
			if(cidade){
				var estado = $('select[name=estado] option:selected').val();
				$.ajax({
					url:'../assets/arquivos_php/requisicoesAjax.php',
					method:'POST',
					data:{acao:'autocompleteBairro', estado: estado, cidade: cidade, bairro: bairro}
				}).done(function(x){
					$('#autocomplete-bairro div').html(x);
				});
			}else{
				$('#autocomplete-bairro div').html("<span>escolha uma cidade primeiro</span>");
				$('input[name=bairro]').val('');
			}
		},800);
	}
	
	$('input[name=bairro]').keyup(function(){
		clearInterval(timeOutBairro);

		var bairro = $(this).val();
		if(bairro){
			FtimeOutBairro(bairro);
			$('#autocomplete-bairro div').show();
			$('#autocomplete-bairro div').html("<span>carregando a lsita ...</span>");
		}else
			$('#autocomplete-bairro div').hide();
		
	}).click(function(e){
		e.stopPropagation();
	});
	
	$('body').click(function(){
		$('#autocomplete-bairro div').hide();
	});
	
	$('#autocomplete-bairro div').delegate("a",'click',function(){
		$('input[name=bairro]').val($(this).text());
	});

	$('#paginalizacao a').click(function () {
		var num = $(this).text();

		$('#filtroAnuncio form input[name=pagina]').val(num);
		$('#filtroAnuncio form').submit();
	});
	
	$('#filtroAnuncio form button[type=button]').click(function () {
		location.href = 'anuncios.php';
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

	}
});