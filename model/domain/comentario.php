<?php


class Comentario{

    private $id;
    private $idAnuncio;
    private $idUsuario;
    private $idComentarioResposta;
    private $comentario;
    private $data;

    public function __construct($dados) {
        for ($i = 0; $i <= 5; $i++)
            $dados[$i] = isset($dados[$i]) ? $dados[$i] : null;

        $this->id = $dados[0];
        $this->idAnuncio = $dados[1];
        $this->idUsuario = $dados[2];
        $this->idComentarioResposta = $dados[3];
        $this->comentario = $dados[4];
        $this->data = $dados[5];
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

    public function getIdComentarioResposta() {
        return $this->idComentarioResposta;
    }

    public function getComentario() {
        return $this->comentario;
    }

    public function getData() {
        return $this->data;
    }

}