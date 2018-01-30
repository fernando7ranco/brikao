<?php

function selectEstadoCidades($uf,$ci){
	
	include '../model/dataBase/matrizes/matrizEstadosCidades.php';
	
	$select[0] = "<option value=''>Estados</option>";
	$select[1] = null;
	$keyUf = null; 
	
	foreach($estados as $idx => $value){
		$selected = null;
		
		if($idx == $uf){
			$selected = "selected='selected'";
			$keyUf = $idx;
			$select[1] = "<option value=''>cidade em {$value}</option>";
		}
		
		$select[0].= "<option value='{$idx}' {$selected} >{$value}</option>";
	}
	
	if($keyUf){

		foreach($cidades[$keyUf] as $value){
			$selected = null;
			
			if($value == $ci)
				$selected = "selected='selected'";
		
			$select[1].= "<option value='{$value}' {$selected} >{$value}</option>";
		}
	}
	
	$select[0] = "<select name='estado' >{$select[0]}</select>";
	$select[1] = "<select name='cidade' >{$select[1]}</select>";
	
	return $select;
}

function selectCategorias($categorias){
	$banco = new BancoDeDados;
	
	$select[0] = "<option value=''>CATEGORIAS</option>";
	$select[1] = null;
	$select[2] = null;
	
	$query = $banco->executaQuery("SELECT id,nome FROM categoria_primaria ORDER BY nome");
	
	while($linhas = $query->fetch_array()){
		$selected = null;
		if(isset($categorias[0]) and $categorias[0] == $linhas['id']){
			$selected = "selected='selected'";
			$select[1] = "<option value=''> >> mais {$linhas['nome']}</option>";
		}
		
		$select[0].= "<option value='{$linhas['id']}' {$selected}>{$linhas['nome']}</option>";	
	}
	
	if(isset($categorias[0]) and is_numeric($categorias[0])){

		$query = $banco->executaQuery("SELECT id,nome FROM categoria_segundaria WHERE id_primaria = {$categorias[0]} ORDER BY nome");
	
		while($linhas = $query->fetch_array()){
			$selected = null;
			if(isset($categorias[1]) and $categorias[1] == $linhas['id']){
				$selected = "selected='selected'";
				$select[2] = "<option value=''> >> mais {$linhas['nome']}</option>";
			}
			
			$select[1].= "<option value='{$linhas['id']}' {$selected}>{$linhas['nome']}</option>";	
		}
	}
	
	if(isset($categorias[1]) and is_numeric($categorias[1])){

		$query = $banco->executaQuery("SELECT id,nome FROM categoria_terciaria WHERE id_segundaria = {$categorias[1]} ORDER BY nome");
	
		while($linhas = $query->fetch_array()){
			$selected = null;
			if(isset($categorias[2]) and $categorias[2] == $linhas['id'])
				$selected = "selected='selected'";
			
			$select[2].= "<option value='{$linhas['id']}' {$selected}>{$linhas['nome']}</option>";	
		}
	}
	
	$banco->fechaConexao();
	
	$select[0] = "<select id='primaria' name='categorias[]' >{$select[0]}</select>";
	$select[1] = "<select id='segundaria' name='categorias[]' >{$select[1]}</select>";
	$select[2] = "<select id='terciaria' name='categorias[]' >{$select[2]}</select>";
	
	return $select;
}

function  montaFiltro($valor, $tipo, $referencia, $name, $qual){
	
	$banco = new BancoDeDados;
	
	$sql = "SELECT id, nome FROM categoria_extra WHERE tipo = '{$tipo}' AND tipo_referencia = '{$referencia}' AND nome != '' ORDER BY nome";
	$query = $banco->executaQuery($sql);
	$return = null;
	
	if($qual == 'select'){
		$return.= "<select name='{$name}'><option value=''>{$referencia}</option>";
		
		while($linhas = $query->fetch_array()){
			$selected = null;
			if ($linhas['id'] == $valor)
				$selected = "selected='selected'";

			$return.= "<option value='{$linhas['id']}' {$selected} >{$linhas['nome']}</option>";
		}
		$return.= "</select>";
		
	}else if($qual == 'checkbox'){
		
		if(!is_array($valor)) $valor = [];
		
		while($linhas = $query->fetch_array()){
			$checked = null;
			if (in_array($linhas['id'], $valor))
				$checked = "checked='checked'";

			$return.= "<span><input name='{$name}[]' type='checkbox' {$checked} value='{$linhas['id']}'>{$linhas['nome']}</span>";
		}
	}
	
	$banco->fechaConexao();
	return $return;
}

function selectQuilometragem($k = null,$idx){
	if(!is_numeric($k)) $k = -1;

	$i = 0;
	
	if ($idx === 1)
		$optionsQuilometragem = "<select name='quilometragemMinima' ><option value=''>QUILOMETRAGEM MINIMA</option>";
	else if ($idx === 2)
		$optionsQuilometragem = "<select name='quilometragemMaxima' ><option value=''>QUILOMETRAGEM MAXIMA</option>";

	while ($i <= 500000) {

		$selected = null;
		if ($k == $i)
			$selected = "selected='selected'";
		
		$quilo = preg_replace('/(\d+)(\d{3})/i', "$1.$2", $i);

		$optionsQuilometragem .= "<option {$selected} value='{$i}'>{$quilo}</option>";

		if ($i >= 0 and $i < 10000)
			$i += 5000;
		else if ($i >= 10000 and $i < 100000)
			$i += 10000;
		else
			$i += 50000;
	}

	return $optionsQuilometragem . "</select>";
}

function selectAnos($ano = null,$idx){

	$i = 1950;
	$maximo = date('Y') + 1;

	if ($idx === 1)
		$optionsAnos = "<select name='ano'><option value=''>ANO</option>";
	if ($idx === 2)
		$optionsAnos = "<select name='anoMinimo'><option value=''>ANO MINIMO</option>";
	if ($idx === 3)
		$optionsAnos = "<select name='anoMaximo'><option value=''>ANO MAXIMO</option>";

	$anosOptios = null;
	while ($i <= $maximo) {
		$selected = null;

		if ($ano == $i)
			$selected = "selected='selected'";

		$anosOptios = "<option {$selected} value='{$i}'>{$i}</option>" . $anosOptios;

		if ($i <= 1990)
			$i += 5;
		else
			$i++;
	}
	return $optionsAnos . $anosOptios . "</select>";
}

function selectCilindrada($c = null, $idx){
        
	$cilindradas = [50 => 50, 100 => 100, 125 => 125, 150 => 150, 200 => 200, 250 => 250, 300 => 300, 350 => 350, 400 => 400, 450 => 450, 500 => 500, 
		550 => 550, 600 => 600, 650 => 650, 700 => 700, 750 => 750, 800 => 800, 850 => 850, 900 => 900, 950 => 950, 1000 => 1000, 1001 => 'acima de 1000' ];
		
	if($idx === 1)
	   $optionsCilindrada = "<select name='cilindrada'><option value=''>CILINDRADA</option>";
	if($idx === 2)
	   $optionsCilindrada = "<select name='cilindradaMinima'><option value=''>CILINDRADA MINIMA</option>";
	if($idx === 3)
	   $optionsCilindrada = "<select name='cilindradaMaxima'><option value=''>CILINDRADA MAXIMA</option>";    

	foreach($cilindradas as $cilindrada){

		$selected = null;
		if($c == $cilindrada)
			$selected = "selected='selected'";

		$optionsCilindrada.= "<option {$selected} value='{$cilindrada}'>{$cilindrada}</option>";
	}

	return $optionsCilindrada ."</select>";

}
