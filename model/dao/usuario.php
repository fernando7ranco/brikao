<?php

class UsuarioDAO {

    private $banco;

    public function __construct($bd) {
        $this->banco = $bd;
    }

    public function insertUsuario($usuario) {
		
        $email = $usuario->getEmail();
        $senha = $usuario->getSenha();
        $nome = $usuario->getNome();
        $tipo = $usuario->getTipo();
        $tipo_cadastro = $usuario->getTipoCadastro();
        $identificador_cadastro = $usuario->getIdentificadorCadastro();

        $sql = 'INSERT INTO `usuarios`(`email`, `senha`, `nome`, `tipo`, `data_acesso`, `tipo_cadastro`, `identificador_cadastro`) VALUES (?,SHA2(?, 224),?,?,NOW(),?,?)';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('sssiis', $email, $senha, $nome, $tipo, $tipo_cadastro, $identificador_cadastro);
        $stmt->execute();
		
		if($stmt->affected_rows){
			$this->sessionStart($stmt->insert_id);
			return true;
		}
        return false;
    }

    public function selectUsuarioId($id) {
        $sql = 'SELECT * FROM usuarios WHERE id = ?';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $array = $result->fetch_array();
        return new Usuario($array);
    }

    public function loginUSuario($usuario) {
        $email = $usuario->getEmail();
        $senha = $usuario->getSenha();

        $sql = 'SELECT id,nome FROM usuarios WHERE email = ? and senha = SHA2(?, 224)';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('ss', $email, $senha);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 1) {
            $id = $resultado->fetch_array()['id'];
			$this->sessionStart($id);
			return 1;
        } else {

            $sql = 'SELECT id FROM usuarios WHERE email = ?';
            $stmt = $this->banco->preparaStatement($sql);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $resultado = $stmt->get_result();

            return $resultado->num_rows == 1 ? 'senha' : 'email';
        }  
  
    }
    
    public function manterLogado($id){
        $id = codificacao($id,10,1);
		setcookie("brikao",$id, time()+60*60*24*100, "/");
    }

    public function updateCadastro($usuario) {
        $senha = $usuario->getSenha();
        $email = $usuario->getEmail();
        $nome = $usuario->getNome();
        $tipo = $usuario->getTipo();
        $telefone = $usuario->getTelefone();
        $cep = $usuario->getCep();
        $estado = $usuario->getEstado();
        $cidade = $usuario->getCidade();
        $bairro = $usuario->getBairro();
        $logradouro = $usuario->getLogradouro();
        $id = $usuario->getId();

        if ($senha != '') {
            $stmt = $this->banco->preparaStatement('UPDATE usuarios SET senha = SHA2(?, 224) WHERE id = ? and senha != SHA2(?, 224)');
            $stmt->bind_param('sis', $senha, $id, $senha);
            $stmt->execute();
        }
        if ($email != '') {
            $stmt = $this->banco->preparaStatement('UPDATE usuarios SET email = ? WHERE id = ? and email != ?');
            $stmt->bind_param('sis', $email, $id, $email);
            $stmt->execute();
        }
        if ($nome != '') {
            $stmt = $this->banco->preparaStatement('UPDATE usuarios SET nome = ? WHERE id = ? and nome != ?');
            $stmt->bind_param('sis', $nome, $id, $nome);
			$stmt->execute();
            if($stmt->affected_rows)
				$_SESSION['nomeUsuario'] = $nome;
			
        }
        if ($tipo != '') {
            $stmt = $this->banco->preparaStatement('UPDATE usuarios SET tipo = ? WHERE id = ? and tipo != ?');
            $stmt->bind_param('iii', $tipo, $id, $tipo);
            $stmt->execute();
        }
        if ($telefone != '') {
            $stmt = $this->banco->preparaStatement('UPDATE usuarios SET telefone = ? WHERE id = ? and telefone != ?');
            $stmt->bind_param('sis', $telefone, $id, $telefone);
            $stmt->execute();
        }
        if ($cep != '') {
            $stmt = $this->banco->preparaStatement('UPDATE usuarios SET cep = ? WHERE id = ? and cep != ?');
            $stmt->bind_param('iii', $cep, $id, $cep);
            $stmt->execute();
        }
        if ($estado != '') {
            $stmt = $this->banco->preparaStatement('UPDATE usuarios SET estado = ? WHERE id = ? and estado != ?');
            $stmt->bind_param('sis', $estado, $id, $estado);
            $stmt->execute();
        }
        if ($cidade != '') {
            $stmt = $this->banco->preparaStatement('UPDATE usuarios SET cidade = ? WHERE id = ? and cidade != ?');
            $stmt->bind_param('sis', $cidade, $id, $cidade);
            $stmt->execute();
        }
        if ($bairro != '') {
            $stmt = $this->banco->preparaStatement('UPDATE usuarios SET bairro = ? WHERE id = ? and bairro != ?');
            $stmt->bind_param('sis', $bairro, $id, $bairro);
            $stmt->execute();
        }
        if ($logradouro != '') {
            $stmt = $this->banco->preparaStatement('UPDATE usuarios SET logradouro = ? WHERE id = ? and logradouro != ?');
            $stmt->bind_param('sis', $logradouro, $id, $logradouro);
            $stmt->execute();
        }
    }

	public function sessionStart($id){
		
		$stmt = $this->banco->preparaStatement('UPDATE usuarios SET data_acesso = now() WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $stmt = $this->banco->preparaStatement('SELECT id,nome FROM usuarios WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
		$dados = $result->fetch_array();
		
		$_SESSION['idUsuario'] = $dados['id'];
        $_SESSION['nomeUsuario'] = $dados['nome'];
	}

    public function localizarEmail($email) {
        $sql = 'SELECT id FROM usuarios WHERE email = ?';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->num_rows;

        return $row;
    }

    public function qualCadastro($tipoCadastro, $identificadorCadastro) {
        $sql = 'SELECT id FROM usuarios WHERE tipo_cadastro = ? AND identificador_cadastro = ?';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('is', $tipoCadastro, $identificadorCadastro);
        $stmt->execute();
        $resultado = $stmt->get_result();
		
		if($resultado->num_rows){
			$this->sessionStart($resultado->fetch_array()['id']);
			return 1;
		}

		return 0;
    }

    public function recuperarSenha($email) {
        if ($this->localizarEmail($email) == 0)
            return false;
		
		$data = date( "Y-m-d H:s:i", strtotime( "+1 day" ) );
		$id = $this->banco->executaQuery("SELECT id FROM usuarios WHERE email = '{$email}'")->fetch_array()['id'];
		
		$hash = hash('sha224', $email.$data);
		
		$this->banco->executaQuery("INSERT INTO recuperar_senha (hash, usuario, data) VALUES (SHA2('{$hash}', 224), {$id}, '{$data}')");
		
        $para = $email;
        $assunto = 'Recuperar Senha';
        $mensagem = "<div style='border:1px solid #555;padding:10px';>

                        <div align='center' style='border-bottom:1px solid #555;padding:10px 0px';>
                            <img src='http://www.residencialexcellence.com.br/wp-content/uploads/2014/05/password_icon.png' > 
                        </div>

                        <h2>Recuperação de Senha</h2>
                        <p>Acesse o link para alterar sua senha</p> 
                        <p>Esse link é valido até ".dataExtensa($data)."</p> 
                        <p><a href='http://localhost/brique/recuperarsenha.php?password=true&hash={$hash}' style='text-decoration:none;color:green;font-weight:bold;cursor:pointer'; >BRIKÃO</a></p> 
                    </div>";

        return enviarEmails($para, $assunto, $mensagem);
    }

	public function bloquearUsuario($idBloqueou, $idBloqueado){
		
		$sql = "INSERT INTO bloqueio (id_bloqueado,id_bloqueou,data) VALUES (?,?,NOW())";
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('ii',$idBloqueado, $idBloqueou);
        $stmt->execute();
		
		if($stmt->affected_rows > 0){
			
			$sql = 'DELETE FROM chat WHERE (id_usuario_envio = ? AND id_usuario_para = ?) OR (id_usuario_envio = ? AND id_usuario_para = ?)';
			$stmt = $this->banco->preparaStatement($sql);
			$stmt->bind_param('iiii', $idBloqueou, $idBloqueado, $idBloqueado, $idBloqueou);
			$stmt->execute();
			
			$sql = 'DELETE FROM comentarios WHERE (id_anuncio IN (SELECT id FROM anuncios WHERE id_usuario = ?) AND id_usuario = ?) OR (id_anuncio IN (SELECT id FROM anuncios WHERE id_usuario = ?) AND id_usuario = ?)';
			$stmt = $this->banco->preparaStatement($sql);
			$stmt->bind_param('iiii', $idBloqueou, $idBloqueado, $idBloqueado, $idBloqueou);
			$stmt->execute();
			
			$sql = 'DELETE FROM notificacoes WHERE (id_usuario = ? AND id_usuario_para = ? ) OR (id_usuario = ? AND id_usuario_para = ? )';
			$stmt = $this->banco->preparaStatement($sql);
			$stmt->bind_param('iiii', $idBloqueou, $idBloqueado, $idBloqueado, $idBloqueou);
			$stmt->execute();
			
		}
			
	}
	
    public function excluirUsuario($idUsuario) {
        $sql = 'SELECT id FROM anuncios WHERE id_usuario = ?';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('i', $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();

        $anunciosDAO = new AnunciosDAO($this->banco);

        while ($linhas = $result->fetch_array()) {
            $anuncio = new Anuncios([0 => $linhas['id'], 1 => $idUsuario]);
            $anunciosDAO->excluirAnuncio($anuncio);
        }

        $sql = 'DELETE FROM usuarios WHERE id = ?';
        $stmt = $this->banco->preparaStatement($sql);
        $stmt->bind_param('i', $idUsuario);
        $stmt->execute();
        session_destroy();
    }

}

?>