<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/funcoes.php';

$erro = '';
$linkWhatsapp = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $telefone = limparDados($_POST['telefone']);

    // 🔍 busca usuário pelo telefone
    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE celular = ?");
    $stmt->execute([$telefone]);
    $user = $stmt->fetch();

    if ($user) {

        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $pdo->prepare("
            UPDATE usuario 
            SET reset_token = ?, reset_expira = ?
            WHERE idUsuario = ?
        ");
        $stmt->execute([$token, $expira, $user['idUsuario']]);

        $link = "http://localhost/seuprojeto/resetar-senha.php?token=$token";

        // 🔥 mensagem pro zap
        $mensagem = urlencode("🔑 Recuperação de senha\n\nClique no link:\n$link");

        // 🔥 link WhatsApp
        $linkWhatsapp = "https://wa.me/55$telefone?text=$mensagem";
    } else {
        $erro = "Telefone não encontrado!";
    }
}
?>

<?php require_once 'includes/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon">📱</div>
            <h2 class="auth-title">Recuperar Senha</h2>
            <p class="auth-subtitle">Digite seu telefone</p>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-error"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>Telefone (somente números)</label>
                <input type="text" name="telefone" placeholder="11999999999" required class="form-input">
            </div>

            <button type="submit" class="btn-submit">Enviar via WhatsApp</button>
        </form>

        <?php if ($linkWhatsapp): ?>
            <div class="alert alert-success" style="margin-top:15px;">
                <p><strong>Clique para enviar no WhatsApp:</strong></p>
                <a href="<?php echo $linkWhatsapp; ?>" target="_blank">
                    📲 Abrir WhatsApp
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>