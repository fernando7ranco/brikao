<?php

if (isset($_GET['bemvindo']))
    $sms = "<h4>Bem-vindo ao Brinkão, Complete seus dados para melhor navegação entre anuncios</h4>";
else if (isset($_GET['alteracao']))
    $sms = "<h4><font color='green'>cadastro alterado</font></h4>";
else if (isset($_GET['incompleto']))
    $sms = "<h4><font color='red'>Preencha ao menos seu nome para ter maior acesso !</font></h4>";

$idUsuario = $_SESSION['idUsuario'];

include '../model/dataBase/bancoDeDados.php';
include '../model/domain/usuario.php';
include '../model/dao/usuario.php';
include '../assets/arquivos_php/todasFuncoes.php';

$banco = new BancoDeDados;
$usuarioDAO = new UsuarioDAO($banco);

if (isset($_GET['update'])) {
    $nome = isset($_POST['nome']) ? $_POST['nome'] : null;
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : 0;
    $telefone = isset($_POST['telefone']) ? $_POST['telefone'] : null;
    $cep = isset($_POST['cep']) ? $_POST['cep'] : null;
    $estado = isset($_POST['estado']) ? $_POST['estado'] : null;
    $cidade = isset($_POST['cidade']) ? $_POST['cidade'] : null;
    $bairro = isset($_POST['bairro']) ? $_POST['bairro'] : null;
    $logradouro = isset($_POST['logradouro']) ? $_POST['logradouro'] : null;
    $id = $idUsuario;

    $usuario = new Usuario([$id, null, null, $nome, $sexo, $telefone, $cep, $estado, $cidade, $bairro, $logradouro]);
    $usuario->validaDados();

    if ($usuario->getNome())
        $usuarioDAO->updateCadastro($usuario);

    $banco->fechaConexao();
    header('location:cadastro.php?alteracao');
    exit;
}

if (isset($_POST['conta'])) {
	
    $dados = $_POST['dados'];
    $id = $idUsuario;

    if (isset($_POST['brikao'])) {
        $query = $banco->executaQuery("SELECT id FROM usuarios WHERE id = {$id} AND tipo_cadastro = 0 AND senha = '{$dados['identificador']}'");
        if ($query->num_rows == 0) {
            $banco->fechaConexao();
            exit('1');
        }
    }
    if (isset($_POST['appFacebook'])) {
        $query = $banco->executaQuery("SELECT id FROM usuarios WHERE id = {$id} AND tipo_cadastro = 2 AND identificador_cadastro = '{$dados['identificador']}'");
        if ($query->num_rows == 0) {
            $banco->fechaConexao();
            exit('1');
        }
    }
    if (isset($_POST['appGoogle'])) {
        $query = $banco->executaQuery("SELECT id FROM usuarios WHERE id = {$id} AND tipo_cadastro = 3 AND identificador_cadastro = '{$dados['identificador']}'");
        if ($query->num_rows == 0) {
            $banco->fechaConexao();
            exit('1');
        }
    }
    if ($dados['excluir']) {
        $usuarioDAO->excluirUsuario($id);
        $banco->fechaConexao();
        exit;
    }

    $email = $dados['email'];
    $senha = $dados['senha'];
    $usuario = new Usuario([$id, $email, $senha]);
    $usuario->validaDados();

    $val = $usuario->getEmail() ? $usuariosDAO->localizarEmail($usuario->getEmail()) : false;

    if ($val)
        $usuario->setEmail('');
    if ($usuario->getEmail() OR $usuario->getSenha())
        $usuariosDAO->updateCadastro($usuario);
	
    $banco->fechaConexao();
    exit;
}

$infoUsu = $usuarioDAO->selectUsuarioId($idUsuario);
$banco->fechaConexao();