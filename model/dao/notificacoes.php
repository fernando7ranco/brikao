<?php

class NotificacoesDAO
{
	private $banco;
	
	public function __construct($bd)
	{
		$this->banco = $bd;
	}
		
	public function verficiaAcaoParaCRUD($idAnuncio,$idUsuario,$idPara,$tipoAcao)
	{
		if($idUsuario == $idPara) return  null;
		
		$sql = "SELECT id FROM notificacoes WHERE id_anuncio = ? AND id_usuario = ? AND id_usuario_para = ? AND  tipo_acao = ?";
				
		$stmt = $this->banco->preparaStatement($sql);
		$stmt->bind_param('iiii',$idAnuncio,$idUsuario,$idPara,$tipoAcao);
		$stmt->execute();
		$result = $stmt->get_result();

		if($result->num_rows == 0 )
			return $this->inseriNotificacao($idAnuncio,$idUsuario,$idPara,$tipoAcao);
		else
			return $this->atualizaNotificacao($result->fetch_array()['id']);

	}	
	
	public function inseriNotificacao($idAnuncio,$idUsuario,$idPara,$tipoAcao)
	{
		$sql = "INSERT INTO notificacoes (id_anuncio,id_usuario,id_usuario_para,tipo_acao,data) VALUES (?,?,?,?,NOW())";
				
		$stmt = $this->banco->preparaStatement($sql);
		$stmt->bind_param('iiii',$idAnuncio,$idUsuario,$idPara,$tipoAcao);
		$stmt->execute();
		$retorno = $stmt->affected_rows;

		return $retorno;
	}	
	
	public function atualizaNotificacao($id)
	{
		$sql = "UPDATE notificacoes SET data = NOW() WHERE id = {$id}";
		$this->banco->executaQuery($sql);
		$retorno = $this->banco->getConexao()->affected_rows;
		return $retorno;
	}
	
	public function exluirNotificacoes($ids)
	{
		$sql = "DELETE FROM notificacoes WHERE id IN ({$ids})";
		$this->banco->executaQuery($sql);
		$retorno = $this->banco->getConexao()->affected_rows;
		return $retorno;
	}
	
	public function selecionaNotificacoes($idUsuario)
	{
		$sql = 'SELECT 
					U.nome,
					A.titulo,
					N.id,
					N.data,
					N.tipo_acao,
					N.id_anuncio
				FROM usuarios U INNER JOIN anuncios A INNER JOIN notificacoes N
					ON  N.id_anuncio = A.id AND U.id = N.id_usuario
				WHERE 
					N.id_usuario != N.id_usuario_para
					AND N.id_usuario_para = ? 
		
				ORDER BY N.data DESC';
				
		$stmt = $this->banco->preparaStatement($sql);
		$stmt->bind_param('i',$idUsuario);
		$stmt->execute();
		$result = $stmt->get_result();
		$acoes = null;

		while($linhas = $result->fetch_array()){
			
			switch($linhas['tipo_acao'])
			{
				case 2: 
					$tipoAcao = 'comentou um anuncio seu';
					$link = 'anuncio.php?anuncio='.$linhas['id_anuncio'];
					break;
				case 3: 
						$tipoAcao = 'respondeu um comentario seu';
						$link = 'anuncio.php?anuncio='.$linhas['id_anuncio'];
					break;
				case 4: 
						$tipoAcao = 'envio uma mensagem';
						$link = 'chat.php?anuncio='.$linhas['id_anuncio'];
					break;
			}
	
			$ids[] = $linhas['id'];
			
			$data = dataExtensa($linhas['data']);
			$titulo = texto($linhas['titulo'],30);
	
			$acoes.= "<a href='{$link}' >
							<div> 
								<span>{$data}</span>
								<span>Anúncio: {$titulo}</span>
								<span>{$linhas['nome']}: {$tipoAcao}</span>
							</div>
						</a>";
						
		}
		if(isset($ids))
			$this->exluirNotificacoes(implode(',',$ids));
		
		return $acoes;
	}
	
	public function numeroNotificacoes($idUsuario)
	{
		$sql = 'SELECT 
				N.id	
			FROM usuarios U INNER JOIN anuncios A INNER JOIN notificacoes N
				ON  N.id_anuncio = A.id AND U.id = N.id_usuario
			WHERE 
				N.id_usuario != N.id_usuario_para
				AND N.id_usuario_para = ?';
				
		$stmt = $this->banco->preparaStatement($sql);
		$stmt->bind_param('i',$idUsuario);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result->num_rows;
	}
}
?>