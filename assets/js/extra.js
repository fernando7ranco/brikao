$(function () {
	
	var objetoAuxiliar;
	
	function fObjetoAuxiliar(id, tipo, tipoReferencia, nome){
		return {
			id: id,
			tipo: tipo,
			tipoReferencia: tipoReferencia,
			nome: nome
		}
	};
	
	// parte de editar
	$("div[id^=lista]").delegate("#conteudo div #editar","click",function(){
		var id = $(this).parent('div').attr('value');
		objetoAuxiliar =  fObjetoAuxiliar(id, 1);
		
		var html = "<h3>editar</h3>"
					+"<input type='text' name='editar' value='"+$(this).siblings('a').text()+"'>"
					+"<button id='cancelaFechalightbox' >cancelar</button>"
					+"<button id='confimarEditacao'>editar</button>"
		lightbox(html);

	}).delegate("#conteudo-find div h3 #editar","click",function(){
		
		var id = $(this).parent('h3').attr('value')
		objetoAuxiliar =  fObjetoAuxiliar(id, 2);
		
		var html = "<h3>editar</h3>"
					+"<input type='text' name='editar' value='"+$(this).siblings('a').text()+"'>"
					+"<button id='cancelaFechalightbox' >cancelar</button>"
					+"<button id='confimarEditacao'>editar</button>"
		lightbox(html);

	}).delegate("#conteudo-find div div #editar","click",function(){
		var id = $(this).parent('div').attr('value');
		objetoAuxiliar =  fObjetoAuxiliar(id, 3);
		
		var html = "<h3>editar</h3>"
					+"<input type='text' name='editar' value='"+$(this).siblings('a').text()+"'>"
					+"<button id='cancelaFechalightbox' >cancelar</button>"
					+"<button id='confimarEditacao'>editar</button>"
		lightbox(html);

	});
	
	// parte de excluir
	$("div[id^=lista]").delegate("#conteudo div #excluir","click",function(){
		var id = $(this).parent('div').attr('value');
		objetoAuxiliar =  fObjetoAuxiliar(id, 1);
		
		var html = "<h3>excluir</h3>"
					+"<h2>"+$(this).siblings('a').text()+"</h2>"
					+"<button id='cancelaFechalightbox' >cancelar</button>"
					+"<button id='confimarExclusao'>excluir</button>"
		lightbox(html);

	}).delegate("#conteudo-find div h3 #excluir","click",function(){
		
		var id = $(this).parent('h3').attr('value')
		objetoAuxiliar =  fObjetoAuxiliar(id, 2);
		
		var html = "<h3>excluir</h3>"
					+"<h2>"+$(this).siblings('a').text()+"</h2>"
					+"<button id='cancelaFechalightbox' >cancelar</button>"
					+"<button id='confimarExclusao'>excluir</button>"
		lightbox(html);

	}).delegate("#conteudo-find div div #excluir","click",function(){
		var id = $(this).parent('div').attr('value');
		objetoAuxiliar =  fObjetoAuxiliar(id, 3);
		
		var html = "<h3>excluir</h3>"
					+"<h2>"+$(this).siblings('a').text()+"</h2>"
					+"<button id='cancelaFechalightbox' >cancelar</button>"
					+"<button id='confimarExclusao'>excluir</button>"
		lightbox(html);

	});
	
	$('body').delegate('#confimarExclusao','click',function(){

		$.ajax({
			url:'',
			method:'POST',
			data:{excluir: true, id: objetoAuxiliar.id, tipo: objetoAuxiliar.tipo}
		}).done(function(x){
			location.reload();
		});
		
	}).delegate('#confimarEditacao','click',function(){
		var nome = $(this).siblings('input[name=editar]').val();
		if(nome){
			$.ajax({
				url:'',
				method:'POST',
				data:{editar: true, id: objetoAuxiliar.id, texto: nome, tipo: objetoAuxiliar.tipo}
			}).done(function(x){
				location.reload();
			});
		}
	});
	
	// parte de inserir
	$("#todo-conteudo").delegate("#conteudo-find p:eq(0) button","click",function(){
		var tipoReferencia = $(this).siblings('input').val();
		
		if(tipoReferencia){
			var tipo = $(this).parent('p').attr('value');
			
			$.ajax({
				url:'',
				method:'POST',
				data:{inserir: true, tipo: tipo, tipoReferencia: tipoReferencia, nome: ''}
			}).done(function(x){
				location.reload();
			});
		}
		
	}).delegate("#conteudo-find div p button","click",function(){
		var nome = $(this).siblings('input').val();
		
		if(nome){
			var tipo = $(this).parents('#conteudo-find').find('h2').text();
			var tipoReferencia = $(this).parent('p').attr('value');
			
			$.ajax({
				url:'',
				method:'POST',
				data:{inserir: true, tipo: tipo, tipoReferencia: tipoReferencia, nome: nome}
			}).done(function(x){
				location.reload();
			});
		}
	});
	
	$('body').delegate('#lightbox', 'click', function () {
        $(this).remove();
		
    }).delegate('#cancelaFechalightbox', 'click', function () {
        $("#lightbox").remove();
		
	}).delegate('#lightbox #conteudoLightbox', 'click', function (e) {
        e.stopPropagation();
		
	});
	
	function lightbox(conteudo) {
        var html = "<div id='lightbox'>" +
					"<div id='conteudoLightbox' align='center'>" +
					conteudo +
					"</div>" +
					"</div>";
        $('body').append(html);
    };
});