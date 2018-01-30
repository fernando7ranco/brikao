<?php

class Carro extends Anuncio{

    private $marca;
    private $quilometragem;
    private $ano;
    private $portas;
    private $cambio;
    private $combustivel;
    private $tipo;
    private $opcionais;
   

    public function __construct($dados) {

        for ($i = 0; $i <= 20; $i++) {
            if ($i >= 13)
                $dados1[] = isset($dados[$i]) ? $dados[$i] : null;
            else
                $dados2[] = isset($dados[$i]) ? $dados[$i] : null;
        }

        $this->marca = $dados1[0];
        $this->quilometragem = $dados1[1];
        $this->ano = $dados1[2];
        $this->portas = $dados1[3];
        $this->cambio = $dados1[4];
        $this->combustivel = $dados1[5];
        $this->tipo = $dados1[6];
        $this->opcionais = $dados1[7];

        
        parent::__construct($dados2);
		
    }

    public function validaDados() {
        parent::validaDados();
    }

    public function getMarca($qual = null) {
		if($qual == 'texto'){
			$banco = new BancoDeDados;
			$nome = $banco->executaQuery("SELECT nome FROM categoria_extra WHERE id = {$this->marca}")->fetch_array()['nome'];
			$banco->fechaConexao();
			return $nome;
		}
        return $this->marca;
    }

    public function getQuilometragem($tipo = null) {
        if ($tipo === 'texto')
            return preg_replace('/(\d+)(\d{3})/i', "$1.$2", $this->quilometragem) .' KM';
		
        return $this->quilometragem;
    }

    public function getAno() {
        return $this->ano;
    }    
	
	public function getPortas($qual = null) {
		if($qual == 'texto'){
			$banco = new BancoDeDados;
			$nome = $banco->executaQuery("SELECT nome FROM categoria_extra WHERE id = {$this->portas}")->fetch_array()['nome'];
			$banco->fechaConexao();
			return $nome;
		}
        return $this->portas;
    }

    public function getCambio($qual = null) {
		if($qual == 'texto'){
			$banco = new BancoDeDados;
			$nome = $banco->executaQuery("SELECT nome FROM categoria_extra WHERE id = {$this->cambio}")->fetch_array()['nome'];
			$banco->fechaConexao();
			return $nome;
		}
        return $this->cambio;
    }

    public function getCombustivel($qual = null) {
		if($qual == 'texto'){
			$banco = new BancoDeDados;
			$nome = $banco->executaQuery("SELECT nome FROM categoria_extra WHERE id = {$this->combustivel}")->fetch_array()['nome'];
			$banco->fechaConexao();
			return $nome;
		}
        return $this->combustivel;
    }

    public function getTipo($qual = null) {
		if($qual == 'texto'){
			$banco = new BancoDeDados;
			$nome = $banco->executaQuery("SELECT nome FROM categoria_extra WHERE id = {$this->tipo}")->fetch_array()['nome'];
			$banco->fechaConexao();
			return $nome;
		}
        return $this->tipo;
    }

    public function getOpcionais($qual = null) {
        if ($qual === 'array')
            return explode('-', $this->opcionais);

        if ($qual === 'texto'){
            $opcionais = explode('-', $this->opcionais);
			$nome = [];
			$banco = new BancoDeDados;
			
			foreach($opcionais as $opcional)
				$nome[] = $banco->executaQuery("SELECT nome FROM categoria_extra WHERE id = {$opcional}")->fetch_array()['nome'];
		
			$banco->fechaConexao();
			return implode('. ',$nome);
		}
        return $this->opcionais;
    }

    public function setMarca($marca) {
        $this->marca = $marca;
    }

    public function setQuilometragem($quilometragem) {
        $this->quilometragem = $quilometragem;
    }

    public function setAno($ano) {
        $this->ano = $ano;
    }   
	
	public function setPortas($portas) {
        $this->portas = $portas;
    }

    public function setCambio($cambio) {
        $this->cambio = $cambio;
    }

    public function setCombustivel($combustivel) {
        $this->combustivel = $combustivel;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function setOpcionais($opcionais) {
        $this->opcionais = $opcionais;
	}
}