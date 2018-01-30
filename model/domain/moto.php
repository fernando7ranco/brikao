<?php

class Moto extends Anuncio{

    private $marca;
    private $cilindrada;
    private $quilometragem;
    private $ano;
  

    public function __construct($dados) {

        for ($i = 0; $i <= 17; $i++) {
            if ($i >= 13)
                $dados1[] = isset($dados[$i]) ? $dados[$i] : null;
            else
                $dados2[] = isset($dados[$i]) ? $dados[$i] : null;
        }

        $this->marca = $dados1[0];
        $this->cilindrada = $dados1[1];
        $this->quilometragem = $dados1[2];
        $this->ano = $dados1[3];

        parent::__construct($dados2);
		
    }

    public function validaDados() {
        parent::validaDados();

        if (!is_string($this->marca))
            $this->marca = null;

        if (!is_numeric($this->cilindrada))
            $this->cilindrada = null;

      
        if (!is_numeric($this->quilometragem))
            $this->quilometragem = null;

        if (!is_numeric($this->ano))
            $this->ano = null;
    }

    public function getMarca($qual = null) {
		if($qual == 'texto'){
			$banco = new BancoDeDados;

			$nome = $banco->executaQuery("SELECT nome FROM categoria_extra WHERE id = '{$this->marca}' ")->fetch_array()['nome'];
			$banco->fechaConexao();
			return $nome;
		}
        return $this->marca;
    }

    public function getCilindrada() {
        return $this->cilindrada;
    }

    public function getQuilometragem($tipo = null) {
        if($tipo === 'texto')
            return preg_replace('/(\d+)(\d{3})/i', "$1.$2", $this->quilometragem) . ' KM';
      
        return $this->quilometragem;
    }

    public function getAno($tipo = null) {
        return $this->ano;
    }

    public function setMarca($marca) {
        $this->marca = $marca;
    }

    public function setCilindrada($cilindrada) {
        $this->cilindrada = $cilindrada;
    }

    public function setQuilometragem($quilometragem) {
        $this->quilometragem = $quilometragem;
    }

    public function setAno($ano) {
        $this->ano = $ano;
    }
}
