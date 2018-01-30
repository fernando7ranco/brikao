<?php

class Anuncio{

    protected $id;
    protected $anunciante;
    protected $categoria;
    protected $titulo;
    protected $imagens;
    protected $descricao;
    protected $valor;
    protected $telefone;
    protected $cep;
    protected $estado;
    protected $cidade;
	protected $bairro;
    protected $data;
	
    public function __construct($dados) {
        for ($i = 0; $i <= 12; $i++)
            $dados[$i] = isset($dados[$i]) ? $dados[$i] : null;

        $this->id = $dados[0];
        $this->anunciante = $dados[1];
        $this->categoria = $dados[2];
        $this->titulo = $dados[3];
        $this->imagens = $dados[4];
        $this->descricao = $dados[5];
        $this->valor = $dados[6];
        $this->telefone = $dados[7];
        $this->cep = $dados[8];
        $this->estado = $dados[9];
        $this->cidade = $dados[10];
        $this->bairro = $dados[11];
        $this->data = $dados[12];
    
    }
    
    public function validaDados() {
      
        if (!preg_match("/^(.){1,100}$/", $this->titulo))
            $this->titulo = '';

        if (!preg_match("/^(.){1,500}$/", $this->descricao))
            $this->descricao = '';

        if (!$this->validaValor($this->valor))
            $this->valor = null;

        if (!preg_match("/^(\(\d{2}\) (\d{4})-\d{4})$/", $this->telefone))
            $this->telefone = null;

        if (!preg_match("/^([0-9]){8}$/", $this->cep))
            $this->cep = null;

        if (!preg_match("/^([A-Z]){2}$/", $this->estado))
            $this->estado = '';

        if (!preg_match("/^([A-Z a-zÀ-ú]){2,40}$/", $this->cidade))
            $this->cidade = '';
        
        if (!$this->validaValor($this->valorMinimo))
            $this->valorMinimo = null;
        
        if (!$this->validaValor($this->valorMaximo))
            $this->valorMaximo = null;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getAnunciante($tipo = null) {
		
		if($tipo === 'tipo'){
			$banco = new BancoDeDados;
			
			$tipo = $banco->executaQuery("SELECT tipo FROM usuarios WHERE id = {$this->anunciante}")->fetch_array()['tipo'];
			$banco->fechaConexao();
			return $tipo == 1 ? 'Particular' : 'Profissional';
		}
		
		if($tipo === 'nome'){
			$banco = new BancoDeDados;
			
			$nome = $banco->executaQuery("SELECT nome FROM usuarios WHERE id = {$this->anunciante}")->fetch_array()['nome'];
			$banco->fechaConexao();
			return $nome;
		}
			
        return $this->anunciante;
    }

    public function getCategoria($tipo = null) {
        if($tipo === 'texto')
           return is_array($this->categoria) ? implode('-',array_filter($this->categoria)) : $this->categoria;
		
        if($tipo === 'selects')
            return $this->selectsCategorias();
		
        if($tipo == 'array')
			return is_array($this->categoria) ? $this->categoria : explode('-',$this->categoria);
		
		if($tipo == 'nomes')
			return $this->categoriasNomes();
		
        return $this->categoria;
    }

    public function getTitulo($limite = null) {
        if($limite)
            return texto($this->titulo,$limite);
        
        return texto($this->titulo);
    }

    public function getImagens($tipo = null) {
        if($tipo === 'explode')
            return explode('/',$this->imagens);
		
          if($tipo === 'implode')
            return implode('/',$this->imagens);
        
        return $this->imagens;
    }

    public function getDescricao($limite = null) {
        if($limite)
            return texto($this->descricao,$limite);
        
        return texto($this->descricao);
    }

    public function getValor($condicao = true) {
        return numeroDecimal($this->valor,$condicao);
    }

    public function getTelefone() {
        return $this->telefone;
    }

    public function getCep() {
        return $this->cep;
    }

    public function getEstado($tipo = null) {
		
		if($tipo === 'texto'){
			include '../model/dataBase/matrizes/matrizEstadosCidades.php';
			
			return isset($estados['RS']) ? $estados[$this->estado] : null;
		}
		
        return $this->estado;
    }

    public function getCidade() {
        return $this->cidade;
    }

    public function getBairro() {
        return $this->bairro;
    }

    public function getData() {
        return dataExtensa($this->data);
    }
	
    public function setId($id) {
        $this->id = $id;
    }

    public function setIdUsuario($anunciante) {
        $this->anunciante = $anunciante;
    }

    public function setCategoria($categoria) {
        $this->categoria = $categoria;
    }

    public function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    public function setImagens($imagens) {
        $this->imagens = $imagens;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function setValor($valor,$condicao = false) {
        $this->valor = $this->numeroDecimal($valor,$condicao);
    }

    public function setTelefone($telefone) {
        $this->telefone = $telefone;
    }

    public function setCep($cep) {
        $this->cep = $cep;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function setCidade($cidade) {
        $this->cidade = $cidade;
    }

    public function setBairro($bairro) {
        $this->bairro = $bairro;
    }

    public function setData($data) {
        $this->data = $data;
    }
	
	protected function categoriasNomes(){
		
		$categorias = $this->getCategoria('array');
		$nomes = null;
		
		if(isset($categorias[0])){
			$banco = new BancoDeDados;
			$nomes[] = $banco->executaQuery("SELECT nome FROM categoria_primaria WHERE id = {$categorias[0]}")->fetch_array()['nome'];
			if(isset($categorias[1])){
				$nomes[] = $banco->executaQuery("SELECT nome FROM categoria_segundaria WHERE id = {$categorias[1]}")->fetch_array()['nome'];
				if(isset($categorias[2]))
					$nomes[] = $banco->executaQuery("SELECT nome FROM categoria_terciaria WHERE id = {$categorias[2]}")->fetch_array()['nome'];
			}
			$banco->fechaConexao();
		}
		
		return is_array($nomes) ? implode(' > ',$nomes) : $nomes;
	}
 
	public function pegaVisualizacoes(){
		
		$banco = new BancoDeDados;
		
		$quantidade = $banco->executaQuery("SELECT count(id) as qtd FROM visualizacoes_anuncios WHERE id_anuncio = {$this->id} GROUP BY id_anuncio")->fetch_array()['qtd'];
		$banco->fechaConexao();
		
		$datetime1 = date_create(date('y-m-d'));
		$datetime2 = date_create($this->data);
		$interval = date_diff($datetime1, $datetime2);
		$diffDays = $interval->format('%a');
		
		$diffDays = $diffDays ? "em {$diffDays} dias" : 'hoje';
		
		return "{$quantidade} visitas {$diffDays}";
	}
   
}