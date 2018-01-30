<?php
class Chat{
	
    private $id;
    private $idAnuncio;
    private $idUsuario;
    private $idUsuarioPara;
    private $mensagem;
    private $arquivo;
    private $data;
    private $visualizacao;

    public function __construct($dados) {
        for ($i = 0; $i <= 7; $i++)
            $dados[$i] = isset($dados[$i]) ? $dados[$i] : '';

        $this->id = $dados[0];
        $this->idAnuncio = $dados[1];
        $this->idUsuario = $dados[2];
        $this->idUsuarioPara = $dados[3];
        $this->mensagem = $dados[4];
        $this->arquivo = $dados[5];
        $this->data = $dados[6];
        $this->visualizacao = $dados[7];
    }

    public function getId() {
        return $this->id;
    }

    public function getIdAnuncio() {
        return $this->idAnuncio;
    }

    public function getIdUsuario() {
        return $this->idUsuario;
    }

    public function getIdUsuarioPara() {
        return $this->idUsuarioPara;
    }

    public function getMensagem() {
        return $this->mensagem;
    } 
	
	public function getArquivo() {
        return $this->arquivo;
    }

    public function getData() {
        return dataExtensa($this->data);
    }

    public function getVisualizacao() {
        return $this->visualizacao;
    }
	
	public function setArquivo($arquivo){
		$this->arquivo = $arquivo;
	}
}