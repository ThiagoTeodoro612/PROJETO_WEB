<?php
session_start();
require_once 'config/database_pdo.php';
require_once 'config/log.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha_atual = $_POST['senha_atual'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_senha)) {
        $erro = 'Preencha todos os campos!';
    } elseif ($nova_senha !== $confirmar_senha) {
        $erro = 'As senhas não coincidem!';
    } elseif (strlen($nova_senha) < 6) {
        $erro = 'A nova senha deve ter pelo menos 6 caracteres!';
    } else {
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();
            
            $stmt = $pdo->prepare("SELECT senha FROM tb_usuarios WHERE id_usuario = :id");
            $stmt->execute([':id' => $_SESSION['usuario_id']]);
            $usuario = $stmt->fetch();
            
            if (!password_verify($senha_atual, $usuario['senha'])) {
                $erro = 'Senha atual incorreta!';
            } else {
                $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE tb_usuarios SET senha = :senha, primeiro_login = 0 WHERE id_usuario = :id");
                $stmt->execute([
                    ':senha' => $nova_senha_hash,
                    ':id' => $_SESSION['usuario_id']
                ]);
                
                $_SESSION['primeiro_login'] = false;
                
                registrarLog($_SESSION['usuario_id'], 'Alteração de senha (primeiro login)', 'tb_usuarios', $_SESSION['usuario_id']);
                
                $sucesso = 'Senha alterada com sucesso!';
                echo "<script>
                    Swal.fire({
                        title: 'Sucesso!',
                        text: 'Senha alterada com sucesso!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                </script>";
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao alterar senha: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha - CRUD Mundo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg">
                    <div class="card-header bg-warning text-dark text-center">
                        <h4><i class="fas fa-key me-2"></i>Alterar Senha</h4>
                        <p class="mb-0">Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></p>
                        <small>Por segurança, você precisa alterar sua senha no primeiro acesso</small>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($erro): ?>
                            <div class="alert alert-danger"><?php echo $erro; ?></div>
                        <?php endif; ?>
                        <?php if ($sucesso): ?>
                            <div class="alert alert-success"><?php echo $sucesso; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" id="formAlterarSenha">
                            <div class="mb-3">
                                <label for="senha_atual" class="form-label">Senha Atual</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="senha_atual" name="senha_atual" 
                                           placeholder="Digite sua senha atual" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="nova_senha" class="form-label">Nova Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    <input type="password" class="form-control" id="nova_senha" name="nova_senha" 
                                           placeholder="Digite a nova senha (mínimo 6 caracteres)" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="confirmar_senha" class="form-label">Confirmar Nova Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-check"></i></span>
                                    <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" 
                                           placeholder="Confirme a nova senha" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning w-100 py-2">
                                <i class="fas fa-save me-2"></i>Alterar Senha
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    document.getElementById('formAlterarSenha').addEventListener('submit', function(e) {
        const novaSenha = document.getElementById('nova_senha').value;
        const confirmarSenha = document.getElementById('confirmar_senha').value;
        
        if (novaSenha !== confirmarSenha) {
            e.preventDefault();
            Swal.fire({
                title: 'Erro!',
                text: 'As senhas não coincidem!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        }
        
        if (novaSenha.length < 6) {
            e.preventDefault();
            Swal.fire({
                title: 'Erro!',
                text: 'A nova senha deve ter pelo menos 6 caracteres!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        }
    });
    </script>
</body>
</html>
