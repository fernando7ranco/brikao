<?php
class BancoDeDados
{
	const SERVIDOR = 'localhost';
	const USUARIO = 'root';
	const SENHA = null;
	const BANCO = 'brique';
	private $conexao;
	
	public function __construct()
	{
		$this->conexao = new mysqli(self::SERVIDOR,self::USUARIO,self::SENHA,self::BANCO);
		
		if($this->conexao->connect_errno)
			die('erro de conexão com mysqli '. $this->conexao->error);
		
		if (!$this->conexao->set_charset("utf8")) 
			die("Error loading character set utf8: ". $mysqli->error);
				
		if(isset($_SESSION['timezone']) and $_SESSION['timezone'])
			$this->defineTimezone($_SESSION['timezone']);
	}
	
	public function executaQuery($sql)
	{
		$query = $this->conexao->query($sql);
		if(!$query)
			die('erro de conexão com mysqli '. $this->conexao->error);
	
		return $query;	
	}
	
	public function preparaStatement($sql)
	{
        $statement = $this->conexao->prepare($sql);
        if(!$statement)
            die('Erro ao preparar statement: '. $this->conexao->error);
        
        return $statement;
    }
	
	public function getConexao()
	{
		return $this->conexao;
	}
	
	public function getErro()
	{
		return $this->conexao->error;
	}
	
	public function fechaConexao(){
		$this->conexao->close();
	}
	
	private function defineTimezone($paramentro)
	{
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

}
?>
