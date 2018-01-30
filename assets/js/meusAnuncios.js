
$(document).ready(function () {
	$('body').delegate('#anuncio .excluirAnuncio', 'click', function (e) {
		e.stopPropagation();
		var excluir = "<div id='localExcluir'>" +
				"<div align='center' >" +
				"<h4>Qual motivo da exclusão deste anúncio</h4>" +
				"</div>" +
				"<div>" +
				"<input type='radio' name='excluir' id='vendi' value='1' > Vendi pelo BRIKÃO. <br>" +
				"<span id='vendiOp'>" +
				"<p> Em quanto tempo você vendeu? </p>" +
				"<input type='radio' name='vendi' value='1' > Em 24h<br>" +
				"<input type='radio' name='vendi' value='2' > Em 1 semana<br>" +
				"<input type='radio' name='vendi' value='3' > Em 1 mês<br>" +
				"<input type='radio' name='vendi' value='4' > Mais de um mês<br>" +
				"<input type='radio' name='vendi' value='5' > Não me lembro<br>" +
				"</span>" +
				"</div>" +
				"<div><input type='radio' name='excluir' value='2' > Vendi por outro meio.</div>" +
				"<div><input type='radio' name='excluir' value='3' > Desisti de vender.</div>" +
				"<div><input type='radio' name='excluir' value='4' > Outro motivo.</div>" +
				"<button id='confirmaExclusao'>excluir</button>" +
				"<button id='cancelarExclusao'>cancelar</button>" +
				"</div>";
		$(this).parent('#anuncio').append(excluir);

	}).delegate('#localExcluir', 'click', function (e) {
		e.stopPropagation();
	}).click(function () {
		$('#localExcluir').remove();
	}).delegate('#anuncio #cancelarExclusao', 'click', function () {
		$('#localExcluir').remove();
	}).delegate('#anuncio #confirmaExclusao', 'click', function () {

		var caminho = $(this).parents('#anuncio');

		dados = {
			idAnuncio: caminho.attr('value'),
			motivo: $("#localExcluir div input[name=excluir]:checked").val(),
			tempoVenda: $('#localExcluir div span#vendiOp input[name=vendi]:checked').val()
		};

		if (dados.motivo) {
			if ((dados.motivo == 1 && dados.tempoVenda) || dados.motivo > 1) {
				caminho.fadeOut(500, function () {
					$(this).remove();
				});
				$.ajax({
					url: '',
					method: 'POST',
					data: {
						excluir: true,
						dados
					}
				});
			} else
				alert('selecione o tempo de venda por favor');
		} else
			alert('selecione um motivo para exclusão por favor');

	}).delegate("#localExcluir div input[name=excluir]", 'change', function () {
		var id = $(this).attr('id');

		if (id === 'vendi')
			$('#localExcluir div span#vendiOp').slideDown();
		else {
			$('#localExcluir div span#vendiOp').slideUp();
			$('#localExcluir div span#vendiOp input[name=vendi]:checked').removeAttr('checked');
		}
	});
	
	$('#paginalizacao a').click(function () {
		var num = $(this).text();
		location.href = '?pagina='+num;
	});
});