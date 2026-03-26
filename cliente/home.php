<?php
session_start();

require_once('../includes/config.php');
require_once('../includes/funcoes.php');

verificarLogin();

$usuario_id = $_SESSION['user_id'];

// pegar idCliente
$stmt = $pdo->prepare("SELECT idCliente FROM cliente WHERE idUsuario = ?");
$stmt->execute([$usuario_id]);
$cliente = $stmt->fetch();

$idCliente = $cliente['idCliente'];

// 🔥 pegar tatuagens + ver se já salvou
$stmt = $pdo->prepare("
    SELECT p.*, 
    (SELECT COUNT(*) FROM referencia_salva r 
     WHERE r.idportfolio = p.idPortfolio AND r.idCliente = ?) as salvo
    FROM portfolio p
    ORDER BY p.dataPublicacao DESC
");

$stmt->execute([$idCliente]);
$tatuagens = $stmt->fetchAll();
?>

<link rel="stylesheet" href="../assets/css/style.css">

<h1 class="section-title">🔥 Explorar Tatuagens</h1>

<div class="gallery-grid">

    <?php foreach ($tatuagens as $tattoo): ?>

        <div class="gallery-item">
            <img src="../<?= htmlspecialchars($tattoo['imagem_url']) ?>" alt="Tattoo">

            <div class="gallery-item">
                <img src="../cliente/imagens/<?= htmlspecialchars($tattoo['arquivo']) ?>" alt="Tattoo">

                <div class="gallery-overlay">
                    <p class="gallery-title"><?= htmlspecialchars($tattoo['titulo']) ?></p>

                    <?php if ($tattoo['salvo']): ?>
                        <button disabled style="background:gray;">✔ Salvo</button>
                    <?php else: ?>
                        <form method="POST" action="referencias.php">
                            <input type="hidden" name="idPortfolio" value="<?= $tattoo['idPortfolio'] ?>">
                            <button type="submit">❤️ Salvar</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        </div>