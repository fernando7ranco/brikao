$(function () {
	
	var objetoAuxiliar;
	
	function fObjetoAuxiliar(id, tipo, caminho){
		console.log(id)
		return {
			id: id,
			tipo: tipo,
			caminho: caminho
		}
	}
	
	// parte de editar
	$("div[id^=lista]").delegate("#conteudo-p div #editar","click",function(){
		var id = $(this).parent('div').attr('value');
		objetoAuxiliar =  fObjetoAuxiliar(id, 1, $(this).siblings('a'));
		
		var html = "<h3>editar nome da categoria primaria</h3>"
					+"<input type='text' name='editar' value='"+$(this).siblings('a').text()+"'>"
					+"<button id='cancelaFechalightbox' >cancelar</button>"
					+"<button id='confimarEditacao'>editar</button>"
		lightbox(html);

	}).delegate("#conteudo-s #conteudo-t h3 #editar","click",function(){
		
		var id = $(this).parent('h3').attr('value')
		objetoAuxiliar =  fObjetoAuxiliar(id, 2, $(this).siblings('a'));
		
		var html = "<h3>editar nome da categoria segundaria</h3>"
					+"<input type='text' name='editar' value='"+$(this).siblings('a').text()+"'>"
					+"<button id='cancelaFechalightbox' >cancelar</button>"
					+"<button id='confimarEditacao'>editar</button>"
		lightbox(html);

	}).delegate("#conteudo-t div #editar","click",function(){
		var id = $(this).parent('div').attr('value');
		objetoAuxiliar =  fObjetoAuxiliar(id, 3, $(this).siblings('a'));
		
		var html = "<h3>editar nome da categoria terciaria</h3>"
					+"<input type='text' name='editar' value='"+$(this).siblings('a').text()+"'>"
					+"<button id='cancelaFechalightbox' >cancelar</button>"
					+"<button id='confimarEditacao'>editar</button>"
		lightbox(html);

	});
	
	// parte de excluir
	$("div[id^=lista]").delegate("#conteudo-p div #excluir","click",function(){
		var id = $(this).parent('div').attr('value');
		objetoAuxiliar =  fObjetoAuxiliar(id, 1, $(this).parent('div'));
		
		var html = "<h3>excluir categoria primaria</h3>"
					+"<h2>"+$(this).siblings('a').text()+"</h2>"
					+"<button id='cancelaFechalightbox' >cancelar</button>"
					+"<button id='confimarExclusao'>excluir</button>"
		lightbox(html);

	}).delegate("#conteudo-s #conteudo-t h3 #excluir","click",function(){
		
		var id = $(this).parent('h3').attr('value')
		objetoAuxiliar =  fObjetoAuxiliar(id, 2, $(this).parents('#conteudo-t'));
		
		var html = "<h3>excluir categoria segundaria</h3>"
					+"<h2>"+$(this).siblings('a').text()+"</h2>"
					+"<button id='cancelaFechalightbox' >cancelar</button>"
					+"<button id='confimarExclusao'>excluir</button>"
		lightbox(html);

	}).delegate("#conteudo-t div #excluir","click",function(){
		var id = $(this).parent('div').attr('value');
		objetoAuxiliar =  fObjetoAuxiliar(id, 3, $(this).parent('div'));
		
		var html = "<h3>excluir categoria terciaria</h3>"
					+"<h2>"+$(this).siblings('a').text()+"</h2>"
					+"<button id='cancelaFechalightbox' >cancelar</button>"
					+"<button id='confimarExclusao'>excluir</button>"
		lightbox(html);
		console.log(3)
	});
	
	$('body').delegate('#confimarExclusao','click',function(){

		$.ajax({
			url:'',
			method:'POST',
			data:{excluir: true, id: objetoAuxiliar.id, tipo: objetoAuxiliar.tipo}
		}).done(function(x){
			objetoAuxiliar.caminho.remove();
			$("#lightbox").remove();
		});
		
	}).delegate('#confimarEditacao','click',function(){
		var nome = $(this).siblings('input[name=editar]').val();
		if(nome){
			$.ajax({
				url:'',
				method:'POST',
				data:{editar: true, id: objetoAuxiliar.id, nome: nome, tipo: objetoAuxiliar.tipo}
			}).done(function(x){
				objetoAuxiliar.caminho.text(nome);
				$("#lightbox").remove();
			});
		}
	});
	
	// parte de inserir
	$("#todo-conteudo").delegate("#lista-primaria p button","click",function(){
		var input = $(this).siblings('input');
		var nome = input.val();
		
		if(nome){
			var id;
			var caminho = $(this).parent('p').siblings('#conteudo-p');
			
			$.ajax({
				url:'',
				method:'POST',
				data:{inserir: true, nome: nome, id: id, tipo: 1}
			}).done(function(x){
				console.log(x)
				var html = "<div value='"+x+"'><a href='?p="+x+"'>"+nome+"</a> <span id='editar'>editar</span><span id='excluir' >excluir</span></div>";
				caminho.prepend(html);
				input.val('');
			});
		}
	}).delegate("#lista-segundaria-terciaria p:eq(0) button","click",function(){
		var input = $(this).siblings('input');
		var nome = input.val();
		
		if(nome){
			var id = $(this).parent('p').attr('value');
			var caminho = $(this).parent('p').siblings('#conteudo-s');
			
			$.ajax({
				url:'',
				method:'POST',
				data:{inserir: true, nome: nome, id: id, tipo: 2}
			}).done(function(x){
				var html = "<div id='conteudo-t' >"
						+"<div value='"+x+"'><h3><a href='?p="+x+"'>"+nome+"</a> <span id='editar'>editar</span><span id='excluir' >excluir</span></h3></div>"
						+"<div value="+x+"><input type='text' placeholder='terciaria'><button>add</button></div>"
						+"</div>";
						
				caminho.prepend(html);
				input.val('');
			});
		}
	}).delegate("#lista-segundaria-terciaria #conteudo-t p button","click",function(){
		var input = $(this).siblings('input');
		var nome = input.val();
		
		if(nome){
			var id = $(this).parent('p').attr('value');
			var caminho = $(this).parent('p')
			
			$.ajax({
				url:'',
				method:'POST',
				data:{inserir: true, nome: nome, id: id, tipo: 3}
			}).done(function(x){
				var html = "<div value='"+x+"'><a href='?p="+x+"'>"+nome+"</a> <span id='editar'>editar</span><span id='excluir' >excluir</span></div>";
				caminho.after(html);
				input.val('');
			});
		}
	})
	

	
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