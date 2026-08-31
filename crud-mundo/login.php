<?php
session_start();
require_once 'config/database_pdo.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos!';
    } else {
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();
            
            $stmt = $pdo->prepare("SELECT * FROM tb_usuarios WHERE email = :email AND ativo = 1");
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch();
            
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['primeiro_login'] = $usuario['primeiro_login'];
                
                // Registrar login
                require_once 'config/log.php';
                registrarLog($usuario['id_usuario'], 'Login realizado', 'tb_usuarios', $usuario['id_usuario']);
                
                if ($usuario['primeiro_login']) {
                    header('Location: alterar_senha.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $erro = 'Email ou senha inválidos!';
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao fazer login: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CRUD Mundo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
        }
        .login-card {
            border-radius: 15px;
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card shadow-lg">
                    <div class="login-header text-white text-center">
                        <i class="fas fa-globe-americas fa-3x mb-3"></i>
                        <h3>CRUD Mundo</h3>
                        <p class="mb-0">Sistema de Gerenciamento Geográfico</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($erro): ?>
                            <div class="alert alert-danger"><?php echo $erro; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="Digite seu email" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="senha" name="senha" 
                                           placeholder="Digite sua senha" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Entrar
                            </button>
                        </form>
                    </div>
                    <div class="card-footer text-center text-muted bg-dark">
                        <small>Desenvolvido por Thiago Gouveia Faria Teodoro</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
