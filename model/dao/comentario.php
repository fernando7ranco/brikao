<?php

class ComentarioDAO {

    private $banco;

    public function __construct($bd) {
        $this->banco = $bd;
    }

    public function selecionarComentarios($comentario, $qtd) {
		
        $id = $comentario->getId();
		
		if($qtd == 0){
			$sql = 'SELECT C.id FROM comentarios C INNER JOIN usuarios U ON U.id = C.id_usuario WHERE C.id_anuncio = ? AND id_comentario_resposta = 0';
			$stmt = $this->banco->preparaStatement($sql);
			$stmt->bind_param('i', $id);
			$stmt->execute();
			$result = $stmt->get_result(); 
			
			$return['numero'] = $result->num_rows;
		}
		
		$sql = 'SELECT C.*,U.nome FROM comentarios C INNER JOIN usuarios U ON U.id = C.id_usuario WHERE C.id_anuncio = ? AND id_comentario_resposta = 0 ORDER BY id DESC LIMIT ?,10';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('ii', $id, $qtd);
        $stmt->execute();
        $result = $stmt->get_result();
		
        $idUsuario = $comentario->getIdUsuario();
        $return['comentarios'] = null;
        while ($linhas = $result->fetch_array()) {
			
			$botoes = $this->botoes($linhas['id_anuncio'], $idUsuario , $linhas['id_usuario']);

			$repostas = $this->pegaResPostas($linhas['id'], $idUsuario);

            $return['comentarios'].= "<div id='comentario' value='{$linhas['id']}'> 
                            <div id='headComentario'><a>{$linhas['nome']}</a> " . dataExtensa($linhas['data']) . " {$botoes['excluir']} {$botoes['bloquear']}</div>
                            <span>{$linhas['comentario']}</span>
                            {$repostas}
                            {$botoes['responder']}
						</div>";
        }

        if ($result->num_rows == 10)
            $return['comentarios'].= "<div id='carregarMaisComentarios' class='carregarMaisComentarios'>carregar mais comentarios</div>";

        return $return;
    }

    public function inserirComentario($comentario) {
		
        $idAnu = $comentario->getIdAnuncio();
        $idUsu = $comentario->getIdUsuario();
        $comen = $comentario->getComentario();

        $sql = 'INSERT INTO comentarios (id_anuncio,id_usuario,comentario,data) VALUES (?,?,?,now())';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('iis', $idAnu, $idUsu, $comen);
        $stmt->execute();
        $num = $stmt->affected_rows;

        if ($num == 1) {
            $idC = $stmt->insert_id;

            $NotificacoesDAO = new NotificacoesDAO($this->banco);
            $idPara = $this->banco->executaQuery("SELECT id_usuario FROM anuncios WHERE id = {$idAnu}")->fetch_array()['id_usuario'];
            $NotificacoesDAO->verficiaAcaoParaCRUD($idAnu, $idUsu, $idPara, 2);

            $sql = "SELECT C.*,U.nome FROM comentarios C INNER JOIN usuarios U ON U.id = C.id_usuario WHERE C.id = {$idC}";
            $query = $this->banco->executaQuery($sql);
            $linhas = $query->fetch_array();
		
			$botoes = $this->botoes($linhas['id_anuncio'], $idUsu , $linhas['id_usuario']);

			$repostas = $this->pegaResPostas($linhas['id'], $idUsu);

            return "<div id='comentario' value='{$linhas['id']}'> 
						<div id='headComentario'><a>{$linhas['nome']}</a> " . dataExtensa($linhas['data']) . " {$botoes['excluir']} {$botoes['bloquear']}</div>
						<span>{$linhas['comentario']}</span>
						{$repostas}
						{$botoes['responder']}
					</div>";
        
        } else
            return $num;
    }

    public function inserirResposta($comentario) {
		
        $idAnu = $comentario->getIdAnuncio();
        $idUsu = $comentario->getIdUsuario();
        $idCom = $comentario->getIdComentarioResposta();
        $comen = $comentario->getComentario();

        $sql = 'INSERT INTO comentarios (id_anuncio,id_usuario,id_comentario_resposta,comentario,data) VALUES (?,?,?,?,now())';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('iiis', $idAnu, $idUsu, $idCom, $comen);
        $stmt->execute();

        $num = $stmt->affected_rows;
        if ($num == 1) {

            $NotificacoesDAO = new NotificacoesDAO($this->banco);
            $idPara = $this->banco->executaQuery("SELECT id_usuario FROM comentarios WHERE id = {$idCom}")->fetch_array()['id_usuario'];
            $NotificacoesDAO->verficiaAcaoParaCRUD($idAnu, $idUsu, $idPara, 3);

			return $this->pegaResPostas($stmt->insert_id, $idUsu, false);
        
        } else
            return $num;
    }
	
	private function botoes($idAnuncio, $idUsuario1 , $idUsuario2){
		$botoes = ['bloquear' => null, 'excluir' => null, 'responder' => null];
		
		 $num = $this->banco->executaQuery("SELECT id FROM anuncios WHERE id = {$idAnuncio} AND id_usuario = {$idUsuario1}")->num_rows;
	
		if($num && $idUsuario1 != $idUsuario2)
			$botoes['bloquear'] = "<span title='bloquear usuario' id='bloquearUsuario' value='{$idUsuario2}'>bloquear</span>";
		
		if ($idUsuario1 == $idUsuario2 || $num) {
			$botoes['excluir'] = "<img src='../assets/img/icones/close.png' id='excluirComentario' title='excluir'>";
			$botoes['responder'] = "<a id='btResponderComentario'>Responder</a>";
		}
		return $botoes;
	}
	
	private function pegaResPostas($id,$idUsuario, $qual = true){
		if($qual)
			$sql = "SELECT C.*,U.nome FROM comentarios C INNER JOIN usuarios U ON U.id = C.id_usuario WHERE C.id_comentario_resposta = {$id}";
		else
			$sql = "SELECT C.*,U.nome FROM comentarios C INNER JOIN usuarios U ON U.id = C.id_usuario WHERE C.id = {$id}";
		
        $query = $this->banco->executaQuery($sql);
		$return = null;
		while ($linhas = $query->fetch_array()) {
			
			$botoes = $this->botoes($linhas['id_anuncio'], $idUsuario , $linhas['id_usuario']);
			
			$return.= "<div id='comentarioResposta' value='{$linhas['id']}'> 
						<div><a>{$linhas['nome']}</a> " . dataExtensa($linhas['data']) . " {$botoes['excluir']} {$botoes['bloquear']}</div>
						<span>{$linhas['comentario']}</span>
					</div>";
		}
		return $return;
	}
	
    public function deletarComentarios($id) {
        $sql = 'DELETE FROM comentarios WHERE id = ?  OR id_comentario_resposta = ?';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('ii', $id, $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

}

?>