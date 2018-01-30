<?php

function ordenaValores($a,$b,$qual = false){
	
	if($qual){
		$a =  numeroDecimal($a, false);
		$b =  numeroDecimal($b, false);
	}
	
	if($a and $b and $b < $a){
		$aux = $a;
		$a = $b;
		$b = $aux;
	}
	
	return [$a,$b];
}

function numeroDecimal($valor, $condicao = true) {

	if(!$valor) return null;
	
	$valor = preg_replace('/\D/i', '', $valor);

	if (strlen($valor) > 3)
		$valor = preg_replace('/^(0+)(\d)/i', "$2", $valor);

	$num = strlen($valor);
	if ($num < 3) {
		for ($i = 0; $i < (3 - $num); $i++) {
			$valor = '0' . $valor;
		}
	}

	if ($condicao) {
		$valor = preg_replace('/(\d+)(\d{2})/i', "$1,$2", $valor);
		$valor = preg_replace('/(\d+)(\d{3})(\,\d{2})/i', "$1.$2$3", $valor);
		$valor = preg_replace('/(\d+)(\d{3})(\.\d{3}\,\d{2})/i', "$1.$2$3", $valor);
		$valor = preg_replace('/(\d+)(\d{3})(\.\d{3}\.\d{3}\,\d{2})/i', "$1.$2$3", $valor);
	} else {
		$valor = preg_replace('/(\d)(\d{2})$/i', "$1.$2", $valor);
	}

	return $valor;
}
	
function defineTimezone($paramentro) {
    $timezones = [
        'AC' => 'America/Rio_branco', 'AL' => 'America/Maceio',
        'AP' => 'America/Belem', 'AM' => 'America/Manaus',
        'BA' => 'America/Bahia', 'CE' => 'America/Fortaleza',
        'DF' => 'America/Sao_Paulo', 'ES' => 'America/Sao_Paulo',
        'GO' => 'America/Sao_Paulo', 'MA' => 'America/Fortaleza',
        'MT' => 'America/Cuiaba', 'MS' => 'America/Campo_Grande',
        'MG' => 'America/Sao_Paulo', 'PR' => 'America/Sao_Paulo',
        'PB' => 'America/Fortaleza', 'PA' => 'America/Belem',
        'PE' => 'America/Recife', 'PI' => 'America/Fortaleza',
        'RJ' => 'America/Sao_Paulo', 'RN' => 'America/Fortaleza',
        'RS' => 'America/Sao_Paulo', 'RO' => 'America/Porto_Velho',
        'RR' => 'America/Boa_Vista', 'SC' => 'America/Sao_Paulo',
        'SE' => 'America/Maceio', 'SP' => 'America/Sao_Paulo',
        'TO' => 'America/Araguaia',
    ];
    $timezone = isset($timezones[$paramentro]) ? $timezones[$paramentro] : $paramentro;

    date_default_timezone_set($timezone);
}

function codificacao($string, $vezes, $tipo) {

    $str_r[] = ['=', '+', '/'];
    $str_r[] = ['laugi', 'roiam', 'arrab'];

    if ($tipo == 1) {
        for ($i = 0; $i < $vezes; $i++)
            $string = strrev(base64_encode($string));

        $string = str_replace($str_r[0], $str_r[1], $string);
    } else {
        $string = str_replace($str_r[1], $str_r[0], $string);

        for ($i = 0; $i < $vezes; $i++)
            $string = base64_decode(strrev($string));
    }
    return $string;
}

function dataExtensa($data) {
    setlocale(LC_ALL, 'pt-br');

    $date = substr($data, 0, 10);
    $hora = substr($data, 10);

    if ($date == date("Y-m-d"))
        $data = 'hoje ás ' . strftime('%Hh%M', strtotime($hora));
    else if ($date == date("Y-m-d", strtotime('- 1 day')))
        $data = 'Ontem ás ' . strftime('%Hh%M', strtotime($hora));
    else
        $data = strftime('%d de %B de %Y ás %Hh%M', strtotime($data));

    return $data;
}

function texto($texto, $limite = 0) {
    $texto = htmlspecialchars($texto);

    if ($limite > 0)
        $texto = strlen($texto) > $limite ? substr($texto, 0, $limite) . '...' : $texto;

    return $texto;
}

function input($method, $name, $type = 'STRING', $p = null) {
    switch ($type) {
        case 'INT':
            $type = FILTER_VALIDATE_INT;
            break;
        case 'FLOAT':
            $type = FILTER_VALIDATE_FLOAT;
            break;
        case 'STRING':
            $type = FILTER_SANITIZE_STRING;
            break;
        case 'ARRAY':
            $type = FILTER_REQUIRE_ARRAY;
            break;
    }

    switch ($method) {
        case 'GET':
            $input = filter_input(INPUT_GET, $name, $type);
            break;
        case 'POST':
            $input = filter_input(INPUT_POST, $name, $type);
            break;
    }

    if ($p == '+' and $input < 0)
        $input = false;

    return $input;
}

function enviarEmails($para, $assunto, $mensagem) {
    
    require '../assets/arquivos_php/PHPMailer/class.phpmailer.php';
    require '../assets/arquivos_php/PHPMailer/class.smtp.php';

    $mail = new PHPMailer;
    $mail->setLanguage('br');
    // Configura para envio de e-mails usando SMTP
    $mail->isSMTP();
    // debugador 
    //$mail->SMTPDebug = 2;
    // Servidor SMTP
    $mail->Host = 'smtp.live.com';
    // Usar autenticação SMTP
    $mail->SMTPAuth = true;
    // Usuário da conta
    $mail->Username = 'brikao-site@hotmail.com';
    // Senha da conta
    $mail->Password = 'adminbrikaosite2016';
    // Tipo de encriptação que será usado na conexão SMTP
    $mail->SMTPSecure = 'tls';
    // Porta do servidor SMTP
    $mail->Port = 587;
    // Informa se vamos enviar mensagens usando HTML
    $mail->IsHTML(true);
    // Email do Remetente
    $mail->From = 'brikao-site@hotmail.com';
    // Nome do Remetente
    $mail->FromName = 'brikao';
    // Endereço do e-mail do destinatário
    $mail->addAddress($para);
    //Aceitar carasteres especiais
    $mail->CharSet = "UTF-8";
    // Assunto do e-mail
    $mail->Subject = $assunto;
    // Mensagem que vai no corpo do e-mail
    $mail->Body = $mensagem;
    // Envia o e-mail e captura o sucesso ou erro
    if ($mail->Send())
        return 'Enviado com sucesso !';
    else
        return 'Erro ao enviar Email:' . $mail->ErrorInfo;
}

function paginalizacao($numeroDeAnuncios,$pagina) 
{
    $rows = round($numeroDeAnuncios / 15);
    $total = $rows < 1 ? 1 : $rows;
    $atual = $pagina > $total ? 0 : $pagina;
    $inicio = $atual - 8;
    $inicio = $inicio < 1 ? 1 : $atual - 7;
    $contador = 1;

    $paginas = null;
    for ($i = $inicio; ($contador <= 14 AND $i <= $total); $i++) {
        $id = $i == $atual ? 'id=foco' : null;
        $paginas.= "<a {$id} >{$i}</a>";
        $contador++;
    }

    if ($atual > 8 and $total > 10)
        $paginas = "<a>1</a>..." . $paginas;

    if (($total - $atual) > 6 and $total > 10)
        $paginas.= "...<a>{$total}</a>";

    return "<div><div id='paginalizacao' >{$paginas}</div></div>";
}

function is_categoria($categoria, $tipo){
	
	switch($tipo){
		case 'carro': $retorno = $categoria == '9-89-641' ? true : false;
			break;
		case 'moto': $retorno = $categoria == '9-89-645' ? true : false;
			break;
		default: $retorno = false;
	}
	
	return $retorno;
}

?>						
