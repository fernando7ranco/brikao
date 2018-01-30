<?php

class Usuario{

    private $id;
    private $email;
    private $senha;
    private $nome;
    private $tipo;
    private $telefone;
    private $cep;
    private $estado;
    private $cidade;
    private $bairro;
    private $logradouro;
    private $dataAcesso;
    private $tipoCadastro;
    private $identificadorCadastro;

    public function __construct($dados) {
        for ($i = 0; $i <= 13; $i++)
            $dados[$i] = isset($dados[$i]) ? $dados[$i] : null;

        $this->id = $dados[0];
        $this->email = $dados[1];
        $this->senha = $dados[2];
        $this->nome = ucwords($dados[3]);
        $this->tipo = $dados[4];
        $this->telefone = $dados[5];
        $this->cep = $dados[6];
        $this->estado = $dados[7];
        $this->cidade = $dados[8];
        $this->bairro = $dados[9];
        $this->logradouro = $dados[10];
        $this->dataAcesso = $dados[11];
        $this->tipoCadastro = $dados[12];
        $this->identificadorCadastro = $dados[13];
    }

    public function validaDados() {
        if (!preg_match("/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/", $this->email))
            $this->email = '';

        if (!preg_match("/^([a-zA-Z0-9]){6,16}$/", $this->senha))
            $this->senha = '';

        if (!preg_match("/^([A-Z a-zÀ-ú]){3,35}$/", $this->nome))
            $this->nome = '';

        if (!preg_match("/^(\(\d{2}\) (\d{4,5})-\d{4}){0,15}$/", $this->telefone))
            $this->telefone = '';

        if (!preg_match("/^([0-9]){1}$/", $this->tipo) and ( $this->tipo >= 0 and $this->tipo <= 2))
            $this->tipo = 0;

        if (!preg_match("/^([0-9]){8}$/", $this->cep))
            $this->cep = '';

        if (!preg_match("/^([A-Z]){2}$/", $this->estado))
            $this->estado = '';

        if (!preg_match("/^([A-Z a-zÀ-ú]){0,40}$/", $this->cidade))
            $this->cidade = '';

        if (!preg_match("/^([A-Z a-zÀ-ú]){0,50}$/", $this->bairro))
            $this->bairro = '';

        if (!preg_match("/^([A-Z a-zÀ-ú](\d{1,6})?){0,80}$/", $this->logradouro))
            $this->logradouro = '';
    }

    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getTipo($qual = null) {
		
		if($qual === 'texto'){
			switch($this->tipo){
				case 1: $texto = 'Particular';break;
				case 1: $texto = 'Profissional';break;
				default: $texto =  null;
			}
			return $texto;
		}
			
        return $this->tipo;
    }

    public function getTelefone() {
        return $this->telefone;
    }

    public function getCep() {
        return $this->cep;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function getCidade() {
        return $this->cidade;
    }

    public function getBairro() {
        return $this->bairro;
    }

    public function getLogradouro() {
        return $this->logradouro;
    }

    public function getDataAcesso() {
        return $this->dataAcesso;
    }

    public function getTipoCadastro() {
        return $this->tipoCadastro;
    }

    public function getIdentificadorCadastro() {
        return $this->identificadorCadastro;
    } 
	
	public function setId($id) {
        $this->id = $id;
    }

}
