<?php

class AnuncioDAO {

    private $banco;

    public function __construct($bd) {
        $this->banco = $bd;
    }

    public function inserirAnuncio($anuncio) {
		
        $idUsuario = $anuncio->getAnunciante();
        $categoria = $anuncio->getCategoria();
        $titulo = $anuncio->getTitulo();
        $FILES = $anuncio->getImagens();
        $descricao = $anuncio->getDescricao();
        $valor = $anuncio->getValor(false);
        $telefone = $anuncio->getTelefone();
        $cep = $anuncio->getCep();
        $estado = $anuncio->getEstado();
        $cidade = $anuncio->getCidade();
        $bairro = $anuncio->getBairro();

        $imgNames = [];
        $numImgs = count($FILES['imagens']['name']);
        for ($i = 0; $i < $numImgs; $i++) {
            $tmp_name = $FILES['imagens']['tmp_name'][$i];
            $name = $FILES['imagens']['name'][$i];
            $newName = 'image' . $i . date("Ymdhsi") . substr($name, -4);
            if (move_uploaded_file($tmp_name, '../assets/img/anuncios/' . $newName))
                $imgNames[] = $newName;
        }

        $imgNames = implode('/', $imgNames);

        $sql = 'INSERT INTO anuncios (id_usuario,categoria,titulo,imagens,descricao,valor,telefone,cep,estado,cidade,bairro,data) VALUES (?,?,?,?,?,?,?,?,?,?,?,now())';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('issssdsisss', $idUsuario, $categoria, $titulo, $imgNames, $descricao, $valor, $telefone, $cep, $estado, $cidade, $bairro);
        $stmt->execute();

        return $stmt->insert_id;
    }

    public function selectAnuncios($id,$tipo = true, $num = 1) { // qual 0 id do anuncio ou 1 id do usuario que postou o anuncio
        if ($tipo)
            $sql = 'SELECT A.*,u.nome FROM anuncios A INNER JOIN usuarios U ON U.id = A.id_usuario WHERE A.id = ?';
        else {
            $num = ($num -1) * 15;
            $sq = 'SELECT A.*,U.nome FROM anuncios A INNER JOIN usuarios U ON U.id = A.id_usuario WHERE A.id_usuario = ?';
            $sql = $sq . " ORDER BY id DESC LIMIT {$num},15";
        }

        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $anuncios = [];

        while ($linhas = $result->fetch_array()) 
            $anuncios[] = new Anuncio($linhas);
      
        if (isset($sq) and count($anuncios) > 0) {
            $stmt = $this->banco->preparaStatement($sq);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $anuncios[] = $result->num_rows;
        }else
            $anuncios[] = 0;
        
        return $anuncios;
    }

    public function selectAnunciosFiltros($filtros) {
	
        $estado = $filtros[0] ? "A.estado = '{$filtros[0]}' AND" : null;
        $cidade = $filtros[1] ? "A.cidade = '{$filtros[1]}' AND" : null;
		$bairro = $filtros[2] ? "A.bairro = '{$filtros[2]}' AND" : null;
		$anunciante = $filtros[3] ? "U.tipo = '{$filtros[3]}' AND" : null;
		$categorias = $filtros[4] ? "A.categoria LIKE '{$filtros[4]}%' AND" : null;
		$titulo = $filtros[5] ? "A.titulo LIKE '{$filtros[5]}%' AND": null;
        $valorMin = $filtros[6] ? "A.valor >= '{$filtros[6]}' AND" : null;
        $valorMax = $filtros[7] ? "A.valor <= '{$filtros[7]}' AND" : null;
		
		
        switch ($filtros[8]) {
            case 1: $order = 'ORDER BY A.valor ASC';
                break;
            case 2: $order = 'ORDER BY A.valor DESC';
                break;
            default: $order = 'ORDER BY A.id DESC';
        }
        
        $paginas = ($filtros[9] - 1) * 15;

        $sql = "SELECT A.* FROM anuncios A WHERE 
                {$estado} {$cidade} {$bairro} {$anunciante} {$categorias} {$titulo} {$valorMin} {$valorMax} A.id > 0 {$order} LIMIT {$paginas},15";

       $query = $this->banco->executaQuery($sql);
        $anuncios = [];
        while ($linhas = $query->fetch_array())
            $anuncios[] = new Anuncio($linhas);
       
		$sql = "SELECT A.id FROM anuncios A WHERE 
                {$estado} {$cidade} {$bairro} {$anunciante} {$categorias} {$titulo} {$valorMin} {$valorMax} A.id > 0";

		$query = $this->banco->executaQuery($sql);
		$anuncios[] = $query->num_rows;
        
        return $anuncios;
    }

    public function visualizaAnuncio($idAnu, $identificador) {
        $identificador = $identificador ? $identificador : $_SERVER['REMOTE_ADDR'];

        $sql = "SELECT A.id FROM visualizacoes_anuncios V INNER JOIN anuncios A ON A.id = V.id_anuncio WHERE V.identificador = '{$identificador}' AND V.id_anuncio = {$idAnu}";

        if ($this->banco->executaQuery($sql)->num_rows == 0) {

            $sql = "INSERT INTO visualizacoes_anuncios (id_anuncio,identificador) VALUES ({$idAnu},'{$identificador}')";
            $this->banco->executaQuery($sql);
        }
    }

    public function getAnuncio($anuncio) {
        
        $idAnuncio = $anuncio->getId();
        $idUsuario = $anuncio->getAnunciante();
        
        $sql = 'SELECT * FROM anuncios WHERE id = ? AND id_usuario = ?';

        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('ii', $idAnuncio, $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 1)
            return new Anuncio($result->fetch_array());
        else
            return null;
    }

    public function updateAnuncio($anuncio) {
        
        $idAnuncio = $anuncio->getId();
        $categoria = $anuncio->getCategoria();
        
        if(!is_categoria($categoria,'moto')){
            $sql = 'DELETE FROM motos WHERE id_anuncio = ?';
            $stmt = $this->banco->preparaStatement($sql);
            $stmt->bind_param('i', $idAnuncio);
            $stmt->execute();
        }
		
        if(!is_categoria($categoria,'carro')){
            $sql = 'DELETE FROM carros WHERE id_anuncio = ?';
            $stmt = $this->banco->preparaStatement($sql);
            $stmt->bind_param('i', $idAnuncio);
            $stmt->execute();
        }
        
        $titulo = $anuncio->getTitulo();
        $imagens = $anuncio->getImagens();
        $descricao = $anuncio->getDescricao();
        $valor = $anuncio->getValor(false);
        $telefone = $anuncio->getTelefone();
        $cep = $anuncio->getCep();
        $estado = $anuncio->getEstado();
        $cidade = $anuncio->getCidade();
        $bairro = $anuncio->getBairro();
        
        $stmt = $this->banco->preparaStatement('UPDATE anuncios SET categoria = ? WHERE id = ? AND categoria != ?');
        $stmt->bind_param('sis', $categoria, $idAnuncio, $categoria);
        $stmt->execute();

        $stmt = $this->banco->preparaStatement('UPDATE anuncios SET titulo = ? WHERE id = ? AND titulo != ?');
        $stmt->bind_param('sis', $titulo, $idAnuncio, $titulo);
        $stmt->execute();
		
	
		for ($i = 0; $i < 6; $i++) {
            $antiga = isset($imagens['atuais'][$i]) ? $imagens['atuais'][$i] : 0;
            $atual = isset($imagens['agora'][$i]) ? $imagens['agora'][$i] : 0;

            if ($antiga AND !in_array($antiga, $imagens['agora']) AND file_exists("../assets/img/anuncios/{$antiga}")){
                unlink("../assets/img/anuncios/{$antiga}");
            }

            if (isset($imagens['novas']['imagens']['name'][$i])) {
                $tmp_name = $imagens['novas']['imagens']['tmp_name'][$i];
                $name = $imagens['novas']['imagens']['name'][$i];
                $newName = 'image' . $i . date("Ymdhsi") . substr($name, -4);
                if (move_uploaded_file($tmp_name, '../assets/img/anuncios/' . $newName)){
                    $imgNames[] = $newName;
                }
            }else if ($atual){
                $imgNames[] = $atual;
            }
        }
		$imagens = implode('/', $imgNames);
		
        $stmt = $this->banco->preparaStatement('UPDATE anuncios SET imagens = ? WHERE id = ? AND imagens != ?');
        $stmt->bind_param('sis', $imagens, $idAnuncio, $imagens);
        $stmt->execute();

        $stmt = $this->banco->preparaStatement('UPDATE anuncios SET descricao = ? WHERE id = ? AND descricao != ?');
        $stmt->bind_param('sis', $descricao, $idAnuncio, $descricao);
        $stmt->execute();

        $stmt = $this->banco->preparaStatement('UPDATE anuncios SET valor = ? WHERE id = ? AND valor != ?');
        $stmt->bind_param('did', $valor, $idAnuncio, $valor);
        $stmt->execute();

        $stmt = $this->banco->preparaStatement('UPDATE anuncios SET telefone = ? WHERE id = ? AND telefone != ?');
        $stmt->bind_param('sis', $telefone, $idAnuncio, $telefone);
        $stmt->execute();

        $stmt = $this->banco->preparaStatement('UPDATE anuncios SET cep = ? WHERE id = ? AND cep != ?');
        $stmt->bind_param('sis', $cep, $idAnuncio, $cep);
        $stmt->execute();

        $stmt = $this->banco->preparaStatement('UPDATE anuncios SET estado = ? WHERE id = ? AND estado != ?');
        $stmt->bind_param('sis', $estado, $idAnuncio, $estado);
        $stmt->execute();

        $stmt = $this->banco->preparaStatement('UPDATE anuncios SET cidade = ? WHERE id = ? AND cidade != ?');
        $stmt->bind_param('sis', $cidade, $idAnuncio, $cidade);
        $stmt->execute();

        $stmt = $this->banco->preparaStatement('UPDATE anuncios SET bairro = ? WHERE id = ? AND bairro != ?');
        $stmt->bind_param('sis', $bairro, $idAnuncio, $bairro);
        $stmt->execute();

        return $idAnuncio;
    }

    public function excluirAnuncio($anuncio, $feedback = false) {
        $id = $anuncio->getId();
        $idUsuario = $anuncio->getAnunciante();

        if (is_array($feedback)) {
            $motivo = $feedback[0];
            $tempoVenda = $feedback[1];
            $sql = 'INSERT INTO feedback_exclusao_anuncio (id_usuario,motivo,tempo_venda,data) VALUES (?,?,?,NOW())';
            $stmt = $this->banco->preparaStatement($sql);
            $stmt->bind_param('iii', $idUsuario, $motivo, $tempoVenda);
            $stmt->execute();
        }

        $sql = 'SELECT imagens FROM anuncios WHERE id = ? AND id_usuario = ?';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('ii', $id, $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $imgs = explode('/', $result->fetch_array()['imagens']);

        foreach ($imgs as $img)
            unlink('img/anuncios/' . $img);

        $sql = 'DELETE FROM anuncios WHERE id = ? AND id_usuario = ?';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('ii', $id, $idUsuario);
        $stmt->execute();
    }

    public function denuncairAnuncio($idAnuncio, $tipo, $descricao) {
        $sql = "INSERT INTO denuncia_anuncios (id_anuncio,tipo,descricao,data) VALUES (?,?,?,NOW())";
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('iis', $idAnuncio, $tipo, $descricao);
        $stmt->execute();
    }
    
}

?>