<?php

class ChatDAO{

    private $banco;

    public function __construct($bd) {
        $this->banco = $bd;
    }
	
	public function uploadArquivo($chat){
		
		$FILE = $chat->getArquivo();
		
		$type = $FILE['type'];
		$types[0] = array('image/jpg','image/jpeg','image/pjpeg','image/png','image/gif');
		$types[1] = array('video/ogg','video/mp4','video/webm');
	
		if(in_array($type,$types[0]))
			$pasta = "../assets/img/mensagens/img/";
		else if(in_array($type,$types[1]))
			$pasta = "../assets/img/mensagens/video/";
		else
			return 0;

		$name = date("Ymdhsi").substr($FILE['name'],-5);
		
		if(move_uploaded_file($FILE['tmp_name'], $pasta.$name)){
			$chat->setArquivo($name);
			$this->insertMensagem($chat);
		}
		
	}
	
    public function insertMensagem($chat) {
		
        $idAnuncio = $chat->getIdAnuncio();
        $idUsuario = $chat->getIdUsuario();
        $idUsuarioPara = $chat->getIdUsuarioPara();
        $mensagem = $chat->getMensagem();
        $arquivo = $chat->getArquivo();
		
        $sql = "INSERT INTO chat (id_anuncio, id_usuario_envio, id_usuario_para, mensagem, arquivo, data, visualizacao) VALUES (?,?,?,?,?,NOW(),0)";

        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('iiiss', $idAnuncio, $idUsuario, $idUsuarioPara, $mensagem, $arquivo);
        $stmt->execute();
        $id = $stmt->insert_id;
		$stmt->error;
		
        if ($id > 0) {
            $NotificacoesDAO = new NotificacoesDAO($this->banco);
            $NotificacoesDAO->verficiaAcaoParaCRUD($idAnuncio, $idUsuario, $idUsuarioPara, 4);
        }
		
    }

    public function selecionaPreChat($chat) {

        $idUsuario = $chat->getIdUsuario();
        $idAnuncio = $chat->getIdAnuncio();
		
        $sql = "SELECT id FROM chat WHERE id_anuncio = {$idAnuncio} AND ( id_usuario_envio = {$idUsuario} OR id_usuario_para = {$idUsuario} )";
		
        $num = $this->banco->executaQuery($sql)->num_rows;
        if ($num > 0) return '';
		
		$usuariosBloqueados = "SELECT id_bloqueado FROM bloqueio WHERE id_bloqueou = {$idUsuario} UNION SELECT id_bloqueou FROM bloqueio WHERE id_bloqueado = {$idUsuario}";
			
        $sql = "SELECT A.id,A.id_usuario,A.titulo,A.imagens,U.nome FROM anuncios A INNER JOIN usuarios U 
			ON U.id = A.id_usuario
			WHERE A.id = ? AND A.id_usuario != ? AND A.id_usuario NOT IN({$usuariosBloqueados})";

        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('ii', $idAnuncio, $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows == 0) return '';
        
        $linhas = $result->fetch_array();
        $value = $linhas['id'] . '/' . $linhas['id_usuario'];
        $img = explode('/', $linhas['imagens'])[0];
        $anunciante = $linhas['nome'];
		$negociante = "eu";
		
		$string = $linhas['titulo'] .'/'. $anunciante .'/'. $negociante;
		
        return "<table value='{$value}' string='{$string}' >
					<tr>
						<td rowspan='3' align='center' >
							<img src='../assets/img/anuncios/{$img}'>
						</td>
						<td>
							<span>" . texto($linhas['titulo'], 40) . "</span>
						</td>
					</tr>
					 <tr>
						<td>
							<span><a>Negociante {$negociante}</a></span>
						</td>
					</tr>
					<tr>
						<td>
							<span>começe a negociar  com <a>{$anunciante}</a></span>
						</td>
					</tr>
			</table>";
    }

    public function selecionaChat($chat) {
		
        $idUsuario = $chat->getIdUsuario();

        $sql = 'SELECT C.*, A.titulo, A.imagens, U.nome FROM chat C INNER JOIN anuncios A INNER JOIN usuarios U 
                ON A.id = C.id_anuncio 
                AND U.id = C.id_usuario_envio
			WHERE
				C.id IN( SELECT max(id) FROM chat WHERE id_usuario_envio = ? OR id_usuario_para = ? GROUP BY (id_usuario_envio + id_usuario_para), id_anuncio ORDER BY id )  ORDER BY C.id DESC';

        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('ii', $idUsuario, $idUsuario );
        $stmt->execute();
        $result = $stmt->get_result();
        $retorno = null;

        while ($linhas = $result->fetch_array()){
			
            $ide = $linhas['id_usuario_envio'] == $idUsuario ? $linhas['id_usuario_para'] : $linhas['id_usuario_envio'];
            $value = $linhas['id_anuncio'] . '/' . $ide;

            $img = explode('/', $linhas['imagens'])[0];

            $nomeMsg = $linhas['id_usuario_envio'] == $idUsuario ? 'eu' : texto($linhas['nome'], 15);
            $titulo = texto($linhas['titulo'], 40);
            $data = dataExtensa($linhas['data']);
			
			if($this->banco->executaQuery("SELECT id FROM anuncios WHERE id = {$linhas['id_anuncio']} AND id_usuario = {$idUsuario}")->num_rows){
				$negociante = $linhas['nome'];
				$anunciante = "eu";
			}else{
				$negociante = "eu";
				$anunciante = $this->banco->executaQuery("SELECT U.nome FROM anuncios A INNER JOIN usuarios U ON U.id = A.id_usuario WHERE A.id = {$linhas['id_anuncio']}")->fetch_array()['nome'];
			}
			
			if($linhas['mensagem'])
				$mensagem = texto($linhas['mensagem'], 20);
			else
				$mensagem = "enviou um arquivo";

            if ($linhas['visualizacao'] == 0 AND $linhas['id_usuario_envio'] != $idUsuario)
                $id = "class='novisu'";
            else
                $id = null;
			
			$string = $linhas['titulo'] .'/'. $anunciante .'/'. $negociante;
		
			$retorno.= "<table value='{$value}' string='{$string}' >
		
                        <tr>
                            <td rowspan='3' align='center' >
                                <img src='../assets/img/anuncios/{$img}'>
                            </td>
                            <td>
                                <span>{$titulo}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span><a>Anunciante {$anunciante}</a> </span>
                                <span><a>Negociante {$negociante}</a></span>
                            </td>
						</tr>
						<tr>
							<td>
                                <span>{$data}</span>
                                <span><a>{$nomeMsg}</a> : {$mensagem}</span>
                            </td>
                        </tr>
                    </table>";
        }

        return $retorno;
    }

    public function selecionaMsgChat($chat, $condicao, $idCondicao) {
		
        $idAnuncio = $chat->getIdAnuncio();
        $idUsuario = $chat->getIdUsuario();
        $idUsuarioPara = $chat->getIdUsuarioPara();

        if ($condicao == '>')
            $this->visualizarMensagens($chat);

        $sql = "SELECT * FROM chat WHERE 
			id_anuncio = ? 
			AND ((id_usuario_envio = ? AND id_usuario_para = ?) OR (id_usuario_envio = ? AND id_usuario_para = ?))  
			AND id {$condicao} ? 
			ORDER BY id DESC LIMIT 20";

        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('iiiiis', $idAnuncio, $idUsuario, $idUsuarioPara, $idUsuarioPara, $idUsuario, $idCondicao);
        $stmt->execute();
        $result = $stmt->get_result();
        $msg = [];
        while ($linhas = $result->fetch_array()) {
			
            $id = $linhas['id_usuario_envio'] == $idUsuario ? 'alingDivRight' : 'alingDivLeft';
            $class = $linhas['id_usuario_envio'] == $idUsuario ? 'eu' : 'ele';
			
			if($linhas['mensagem'])
				$mensagem = texto($linhas['mensagem']);
			else{
				$arquivo = $linhas['arquivo'];
				
				$type = substr($arquivo,strripos($arquivo,'.')+1);
			
				$types[0] = array('jpg','jpeg','pjpeg','png','gif');
				$types[1] = array('ogg','mp4','webm');
			
				if(in_array($type,$types[0]))
					$mensagem = "<img src='../assets/img/mensagens/img/{$arquivo}'>";
				else if(in_array($type,$types[1]))
					$mensagem = "<video controls ><source src='../assets/img/mensagens/video/{$arquivo}' type='video/{$type}'></video>";
			}
			
            $data = dataExtensa($linhas['data']);

            if ($linhas['id_usuario_envio'] == $idUsuario) {
				
                if ($linhas['visualizacao'] == 1)
                    $visualizacao = "<label id='imgVisu' ><img src='../assets/img/icones/visualizada.png' title='mensagem visualizada' ></label>";
                else
                    $visualizacao = "<label id='imgVisu' class='naoViu' ></label>";
				
            }else
                $visualizacao = null;

            $msg[] = "<div id='divisorDeMensagens' value='{$linhas['id']}'>
                        <div id='{$id}'>
                            <div class='{$class}'>
                                    <span>{$mensagem}</span>
                                    <a id='dataMsg'>{$visualizacao} {$data}</a>
                            </div>
                        </div>
                    </div>";
        }


        $num = count($msg);
        $msg = implode('', array_reverse($msg));

        $nVisu = $this->verficaVisualizacoes($chat);

        return json_encode([$msg, $num, $nVisu]);
    }

    public function visualizarMensagens($chat) {
        $idAnuncio = $chat->getIdAnuncio();
        $idUsuario = $chat->getIdUsuario();
        $idUsuarioPara = $chat->getIdUsuarioPara();
		
		$sql = "DELETE FROM notificacoes WHERE id_usuario = ? AND id_usuario_para = ? AND tipo_acao = 4";
		$stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('ii', $idUsuarioPara, $idUsuario);
		$stmt->execute();
		
        $sql = 'UPDATE chat SET visualizacao = 1 WHERE visualizacao = 0 AND id_usuario_para = ? AND id_usuario_envio = ? AND id_anuncio = ?';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('iii', $idUsuario, $idUsuarioPara, $idAnuncio);
        $stmt->execute();
    }

    public function verficaVisualizacoes($chat) {
        $idAnuncio = $chat->getIdAnuncio();
        $idUsuario = $chat->getIdUsuario();
        $idUsuarioPara = $chat->getIdUsuarioPara();

        $sql = 'SELECT id FROM chat WHERE visualizacao = 0 AND id_usuario_envio = ? AND id_usuario_para = ? AND id_anuncio = ?';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('iii', $idUsuario, $idUsuarioPara, $idAnuncio);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows;
    }
	
	public function denunciarUsuario($idDenunciado,$idDenunciou,$tipo){
		$sql = "INSERT INTO denuncia_usuarios (id_denunciado,id_denunciou,tipo,data) VALUES (?,?,?,NOW())";
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('iii',$idDenunciado, $idDenunciou, $tipo);
        $stmt->execute();
		return $stmt->affected_rows;
	}
	
    public function excluirChat($chat) {
        $idAnuncio = $chat->getIdAnuncio();
        $idUsuario = $chat->getIdUsuario();
        $idUsuarioPara = $chat->getIdUsuarioPara();
		
		$sql = "SELECT arquivo FROM chat WHERE id_anuncio = ? AND arquivo != '' AND ( (id_usuario_envio = ? AND id_usuario_para = ?) OR (id_usuario_para = ? AND id_usuario_envio = ?) )";
		$stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('iiiii', $idAnuncio, $idUsuario, $idUsuarioPara, $idUsuario, $idUsuarioPara);
        $stmt->execute();
		$result = $stmt->get_result();
		
		while($linhas = $result->fetch_array()){
			$arquivo = $linhas['arquivo'];
				
			$type = substr($arquivo,strripos($arquivo,'.')+1);
		
			$types[0] = array('jpg','jpeg','pjpeg','png','gif');
			$types[1] = array('ogg','mp4','webm');
		
			if(in_array($type,$types[0]))
				unlink("../assets/img/mensagens/img/{$arquivo}");
			else if(in_array($type,$types[1]))
				unlink("../assets/img/mensagens/video/{$arquivo}");
		}
		
        $sql = 'DELETE FROM chat WHERE id_anuncio = ? AND ( (id_usuario_envio = ? AND id_usuario_para = ?) OR (id_usuario_para = ? AND id_usuario_envio = ?) )';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('iiiii', $idAnuncio, $idUsuario, $idUsuarioPara, $idUsuario, $idUsuarioPara);
        $stmt->execute();
        return $stmt->affected_rows;
    }

}

?>