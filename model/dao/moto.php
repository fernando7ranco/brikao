<?php

class MotoDAO extends AnuncioDAO {

    private $banco;

    public function __construct($bd) {
        $this->banco = $bd;
        parent::__construct($bd);
    }

    public function inserirMoto($motos) {

        $idAnuncio = parent::inserirAnuncio($motos);

        if (!$idAnuncio)
            return false;
            
        $marca = $motos->getMarca();
        $cilindrada = $motos->getCilindrada();
        $quilometragem = $motos->getQuilometragem();
        $ano = $motos->getAno();
            
        $sql = 'INSERT INTO motos (id_anuncio,marca,cilindrada,quilometragem,ano)VALUES(?,?,?,?,?)';
        
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('isiii', $idAnuncio, $marca, $cilindrada, $quilometragem, $ano);
        $stmt->execute();
        
        if ($stmt->affected_rows == 1)
            return $idAnuncio;

        return false;
    }
    
    public function selecionaMotoId($id){

        $sql = "SELECT A.*,
                    M.marca,
                    M.cilindrada,
                    M.quilometragem,
                    M.ano
                FROM anuncios A INNER JOIN motos M ON M.id_anuncio = A.id WHERE A.id = ?";

        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $anuncio = null;
        while ($linhas = $result->fetch_array())
			$anuncio = new Moto($linhas);
		
        return $anuncio;
    }
    
    public function updateMoto($motos) {

        parent::updateAnuncio($motos);
        
        $idAnuncio = $motos->getId();
        $marca = $motos->getMarca();
        $cilindrada = $motos->getCilindrada();
        $quilometragem = $motos->getQuilometragem();
        $ano = $motos->getAno();
        
        if($this->selecionaMotoId($idAnuncio) === null){
            $sql = 'INSERT INTO motos (id_anuncio,marca,cilindrada,quilometragem,ano) VALUES (?,?,?,?,?)';
            $stmt = $this->banco->preparaStatement($sql);
            $stmt->bind_param('isiii', $idAnuncio, $marca, $cilindrada, $quilometragem, $ano);
            $stmt->execute();
        }else{

            $stmt = $this->banco->preparaStatement('UPDATE motos SET marca = ? WHERE id_anuncio = ? AND marca != ?');
            $stmt->bind_param('isi', $marca, $idAnuncio, $marca);
            $stmt->execute();

            $stmt = $this->banco->preparaStatement('UPDATE motos SET cilindrada = ? WHERE id_anuncio = ? AND cilindrada != ?');
            $stmt->bind_param('iii', $cilindrada, $idAnuncio, $cilindrada);
            $stmt->execute();

            $stmt = $this->banco->preparaStatement('UPDATE motos SET quilometragem = ? WHERE id_anuncio = ? AND quilometragem != ?');
            $stmt->bind_param('iii', $quilometragem, $idAnuncio, $quilometragem);
            $stmt->execute();

            $stmt = $this->banco->preparaStatement('UPDATE motos SET ano = ? WHERE id_anuncio = ? AND ano != ?');
            $stmt->bind_param('iii', $ano, $idAnuncio, $ano);
            $stmt->execute();
        }
        
        return $idAnuncio;
    }
    
    public function selectAnunciosFiltros($filtros) {
      
        $estado = $filtros[0] ? "A.estado = '{$filtros[0]}' AND" : null;
        $cidade = $filtros[1] ? "A.cidade = '{$filtros[1]}' AND" : null;
		$bairro = $filtros[2] ? "A.bairro = '{$filtros[2]}' AND" : null;
		$anunciante = $filtros[3] ? "U.tipo = '{$filtros[3]}' AND" : null;
		$categorias = $filtros[4] ? "A.categoria LIKE '{$filtros[4]}%' AND" : null;
		$titulo = $filtros[5] ? "A.titulo LIKE '{$filtros[5]}%' AND": null;
        $valorMin = $filtros[6] ? "A.valor >= {$filtros[6]} AND" : null;
        $valorMax = $filtros[7] ? "A.valor <= {$filtros[7]} AND" : null;
		
        switch ($filtros[8]) {
            case 1: $order = 'ORDER BY A.valor ASC';
                break;
            case 2: $order = 'ORDER BY A.valor DESC';
                break;
            default: $order = 'ORDER BY A.id DESC';
        }
        
        $paginas = ($filtros[9] - 1) * 15;
		
        $marca = $filtros[10] ? "M.marca = '{$filtros[10]}' AND" : null;
        $cilindradaMinima = $filtros[11] ? "M.cilindrada >= {$filtros[11]} AND" : null;
        $cilindradaMaxima = $filtros[12] ? "M.cilindrada <= {$filtros[12]} AND" : null;
        $quilometragemMinima = $filtros[13] ? "M.quilometragem >= {$filtros[13]} AND" : null;
        $quilometragemMaxima = $filtros[14] ? "M.quilometragem <= {$filtros[14]} AND" : null;
        $anoMinimo = $filtros[15] ? "M.ano >= {$filtros[15]} AND" : null;
        $anoMaximo = $filtros[16] ? "M.ano <= {$filtros[16]} AND" : null;
        

        $sql = "SELECT A.*,
                    M.marca,
                    M.cilindrada,
                    M.quilometragem,
                    M.ano
                FROM anuncios A INNER JOIN motos M ON M.id_anuncio = A.id WHERE 
					{$estado} {$cidade} {$bairro} {$categorias} {$titulo} {$valorMin} {$valorMax}
					{$marca} {$cilindradaMinima} {$cilindradaMaxima} {$quilometragemMinima} {$quilometragemMaxima} {$anoMinimo} {$anoMaximo}
					A.id > 0 {$order} LIMIT {$paginas},15";

        $query = $this->banco->executaQuery($sql);
        $anuncios = [];
        while ($linhas = $query->fetch_array()) {
            $anuncios[] = new Moto($linhas);
        }

       $sql = "SELECT A.id FROM anuncios A INNER JOIN motos M ON M.id_anuncio = A.id WHERE 
				{$estado} {$cidade} {$bairro} {$categorias} {$titulo} {$valorMin} {$valorMax}
				{$marca} {$cilindradaMinima} {$cilindradaMaxima} {$quilometragemMinima} {$quilometragemMaxima} {$anoMinimo} {$anoMaximo}
				A.id > 0";

        $query = $this->banco->executaQuery($sql);
        $anuncios[] = $query->num_rows;
        
        return $anuncios;
    }
}
?>