<?php

class CarroDAO extends AnuncioDAO{

    private $banco;

    public function __construct($bd) {
        $this->banco = $bd;
        parent::__construct($bd);
    }

    public function inserirCarro($carro) {

        $idAnuncio = parent::inserirAnuncio($carro);

        if (!$idAnuncio)
            return false;

        $marca = $carro->getMarca();
        $quilometragem = $carro->getQuilometragem();
        $ano = $carro->getAno();
        $portas = $carro->getPortas();
        $cambio = $carro->getCambio();
        $combustivel = $carro->getCombustivel();
        $tipo = $carro->getTipo();
        $opcionais = $carro->getOpcionais();

        $sql = 'INSERT INTO carros (id_anuncio, marca, quilometragem, ano, portas, cambio, combustivel, tipo, opcionais) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('isiisssss', $idAnuncio, $marca, $quilometragem, $ano, $portas, $cambio, $combustivel, $tipo, $opcionais);
        $stmt->execute();

        if ($stmt->affected_rows == 1)
            return $idAnuncio;

        return false;
    }

    public function selecionaCarroId($id) {
	
        $sql = "SELECT A.*,
                    C.marca,
                    C.quilometragem,
                    C.ano,
                    C.portas,
                    C.cambio,
                    C.combustivel,
                    C.tipo,
                    C.opcionais
                FROM anuncios A INNER JOIN carros C ON C.id_anuncio = A.id WHERE A.id = ?";

        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $anuncio = null;
		
        while ($linhas = $result->fetch_array())
            $anuncio = new Carro($linhas);
        
        return $anuncio;
    }

    public function updateCarro($carro) {

        parent::updateAnuncio($carro);

        $idAnuncio = $carro->getId();
        $marca = $carro->getMarca();
        $quilometragem = $carro->getQuilometragem();
        $ano = $carro->getAno();
        $portas = $carro->getPortas();
        $cambio = $carro->getCambio();
        $combustivel = $carro->getCombustivel();
        $tipo = $carro->getTipo();
        $opcionais = $carro->getOpcionais();

        if ($this->selecionaCarroId($idAnuncio) === null) {
            $sql = 'INSERT INTO carros (id_anuncio,marca,quilometragem,ano,portas,cambio,combustivel,tipo,opcionais) VALUES (?,?,?,?,?,?,?,?)';
            $stmt = $this->banco->preparaStatement($sql);
            $stmt->bind_param('isiisssss', $idAnuncio, $marca, $quilometragem, $ano, $portas, $cambio, $combustivel, $tipo, $opcionais);
            $stmt->execute();
        } else {

            $stmt = $this->banco->preparaStatement('UPDATE carros SET marca = ? WHERE id_anuncio = ? AND marca != ?');
            $stmt->bind_param('isi', $marca, $idAnuncio, $marca);
            $stmt->execute();

            $stmt = $this->banco->preparaStatement('UPDATE carros SET quilometragem = ? WHERE id_anuncio = ? AND quilometragem != ?');
            $stmt->bind_param('iii', $quilometragem, $idAnuncio, $quilometragem);
            $stmt->execute();

            $stmt = $this->banco->preparaStatement('UPDATE carros SET ano = ? WHERE id_anuncio = ? AND ano != ?');
            $stmt->bind_param('iii', $ano, $idAnuncio, $ano);
            $stmt->execute();           

			$stmt = $this->banco->preparaStatement('UPDATE carros SET portas = ? WHERE id_anuncio = ? AND portas != ?');
            $stmt->bind_param('sis', $portas, $idAnuncio, $portas);
            $stmt->execute();

            $stmt = $this->banco->preparaStatement('UPDATE carros SET cambio = ? WHERE id_anuncio = ? AND cambio != ?');
            $stmt->bind_param('sis', $cambio, $idAnuncio, $cambio);
            $stmt->execute();

            $stmt = $this->banco->preparaStatement('UPDATE carros SET combustivel = ? WHERE id_anuncio = ? AND combustivel != ?');
            $stmt->bind_param('sis', $combustivel, $idAnuncio, $combustivel);
            $stmt->execute();

            $stmt = $this->banco->preparaStatement('UPDATE carros SET tipo = ? WHERE id_anuncio = ? AND tipo != ?');
            $stmt->bind_param('sis', $tipo, $idAnuncio, $tipo);
            $stmt->execute();

            $stmt = $this->banco->preparaStatement('UPDATE carros SET opcionais = ? WHERE id_anuncio = ? AND opcionais != ?');
            $stmt->bind_param('sis', $opcionais, $idAnuncio, $opcionais);
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
        $quilometragemMinima =  $filtros[11] ? "C.quilometragem >= {$filtros[11]} AND" : null;
        $quilometragemMaxima = $filtros[12] ? "C.quilometragem <= {$filtros[12]} AND" : null;
        $anoMinimo = $filtros[13] ? "C.ano >= {$filtros[13]} AND" : null;
        $anoMaximo = $filtros[14] ? "C.ano <= {$filtros[14]} AND" : null;
        $cambio = $filtros[15] ? "C.cambio REGEXP '" . implode('|', $filtros[15] ) . "' AND" : null;
        $combustivel = $filtros[16] ? "C.combustivel REGEXP '" . implode('|', $filtros[16]) . "' AND" : null;
        $tipo = $filtros[17]  ? "C.tipo REGEXP '" . implode('|', $filtros[17]) . "' AND" : null;
		$opcionais = $filtros[18]? "C.opcionais REGEXP '" . implode('|', $filtros[18]) . "' AND" : null;

        $sql = "SELECT A.*,
                    C.marca,
                    C.quilometragem,
                    C.ano,
                    C.portas,
                    C.cambio,
                    C.combustivel,
                    C.tipo,
                    C.opcionais
                FROM anuncios A INNER JOIN carros C ON C.id_anuncio = A.id WHERE 
					{$estado} {$cidade} {$bairro} {$categorias} {$titulo} {$valorMin} {$valorMax} {$marca} {$quilometragemMinima} 
					{$quilometragemMaxima} {$anoMinimo} {$anoMaximo} {$cambio} {$combustivel} {$tipo} {$opcionais}
					A.id > 0 {$order} LIMIT {$paginas},15";

        
		$query = $this->banco->executaQuery($sql);
        $anuncios = [];
        while ($linhas = $query->fetch_array()) {
            $anuncios[] = new Carro($linhas);
        }

        $sql = "SELECT A.id FROM anuncios A INNER JOIN carros C ON C.id_anuncio = A.id WHERE 
            {$estado} {$cidade} {$bairro} {$categorias} {$titulo} {$valorMin} {$valorMax} {$marca} {$quilometragemMinima} 
			{$quilometragemMaxima} {$anoMinimo} {$anoMaximo} {$cambio} {$combustivel} {$tipo} {$opcionais} A.id > 0";

		$query = $this->banco->executaQuery($sql);
        $anuncios[] = $query->num_rows;

        return $anuncios;
    }

}

?>