
$(function () {

	var dadosF = function(id,tipo){
		return {
			acao: 'categorias',
			id: id,
			tipo: tipo
		}
	};
	
    $('select#primaria').change(function () {
		
		if($(this).val()){
			
			var dados = dadosF($(this).val(), 1);
			var selected = $(this).find('option:selected').text();
		
			$.ajax({
				url: '../assets/arquivos_php/requisicoesAjax.php',
				method: 'POST',
				data: dados
			}).done(function (re) {
				re = "<option value=''> >> mais "+selected+"</option>" + re;
				$('select#segundaria').show().html(re);
				$('select#terciaria').hide();
			});
			
		}else{
			$('select#segundaria').hide().html();
			$('select#terciaria').hide().html();
		}
    });   
	
	$('select#segundaria').change(function () {
		
		if($(this).val()){
			
			var dados = dadosF($(this).val(), 2);
			var selected = $(this).find('option:selected').text();
			$.ajax({
				url: '../assets/arquivos_php/requisicoesAjax.php',
				method: 'POST',
				data: dados
			}).done(function (re) {
				re = "<option value=''> >> mais "+selected+"</option>" + re;
				$('select#terciaria').show().html(re);
			});
			
		}else{
			$('select#terciaria').hide().html('');
		}
    });
	
	$('select[name="categorias[]"]').each(function (){
		
		if(!$(this).find('option').length)
			$(this).hide();
	});
});