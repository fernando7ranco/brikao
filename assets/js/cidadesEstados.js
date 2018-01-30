$(function () {
	var dados = {};
	
	$.ajax({
		url:'../assets/arquivos_php/requisicoesAjax.php',
		method:'POST',
		dataType:'JSON',
		data:{acao:'cidadesEstados'}
	}).done(function(re){

		dados['estados'] = re[0];
		dados['cidades'] = re[1];

        var E = $('#getEstados').attr('alt');
        var C = $('#getCidades').attr('alt');
        selectsEC(E, C);
    });

    window.selectsEC = function (E, C) {
        var keyE , estados = '', cidades = '', selected;
		
		estados = "<option value=''>ESTADOS</option>";
		
        $.each(dados.estados, function (key, value) {
			selected = '';
            if (E === key) {
                selected = "selected='selected'";
                keyE = key;
				cidades = "<option value=''>Cidades de "+value+"</option>";
            }
			
            estados += "<option value='" + key +"' " + selected + " >" + value+ "</option>";
        });

        $('#getEstados').html(estados);


		if(keyE){
			$.each(dados.cidades[keyE], function (key, value) {
				
				selected = C === value ? "selected='selected'" : '';
			
				cidades += "<option value='" + value + "' " + selected + " >" + value + "</option>";
			});
			$('#getCidades').html(cidades);
		}else
			$('#getCidades').hide().html('');
		
    };

    $('#getEstados').change(function () {

        var keyE = $(this).val();
		var options = "<option value=''>Cidades em "+ $(this).find('option:selected').text() +"</option>";
		
		if(keyE){
			$.each(dados.cidades[keyE], function (key, value) {
				options += "<option value='" + value + "' >" + value + "</option>";
			});
			$('#getCidades').html(options).show();
		}else
			$('#getCidades').hide().html('');
			
		
    });


});