<?php
$logado = verificaLogin();

if (isset($_POST['login'])) {
    header("Location: ../pages/login.php");
    exit();
}

if (isset($_POST['logout'])) {
    header("Location: ../scripts/logout.php");
    exit();
}

// Caminho da foto relativo às páginas/layouts (ambos ficam 2 níveis abaixo da raiz)
$fotoPerfil = !empty($_SESSION['foto'])
    ? "../../{$_SESSION['foto']}"
    : null;
?>

<header>
    <nav>
        <div class="logo">
            <a href="../../index.php"><img src="../../assets/img/logo.png" alt="Logo" width="50"></a>
            <a href="../../index.php">
                <h1 class="logo-titulo">Já leu esse?</h1>
            </a>
        </div>

        <div class="nav-links">
            <a href="../pages/home.php">Início</a>

            <?php if ($logado): ?>
                <a href="../pages/trocas.php">Trocas</a>
                <a href="../pages/perfil.php">Meu perfil</a>
            <?php endif; ?>
        </div>

        <div class="nav-usuario">
            <?php if ($logado): ?>
                <form action="#" method="POST" id="form-user-logout" class="form-user">

                    <!-- Foto do usuário -->
                    <?php if ($fotoPerfil): ?>
                        <img src="<?= htmlspecialchars($fotoPerfil) ?>" alt="Foto de perfil" class="header-foto-perfil">
                    <?php else: ?>
                        <svg class="header-foto-perfil header-foto-placeholder" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 40 40">
                            <circle cx="20" cy="20" r="20" fill="#e0e0e0" />
                            <circle cx="20" cy="15" r="7" fill="#aaa" />
                            <path d="M5 38 Q5 27 20 27 Q35 27 35 38Z" fill="#aaa" />
                        </svg>
                    <?php endif; ?>

                    <p><?= htmlspecialchars($_SESSION['nome']) ?></p>
                    <input type="hidden" value="logout" name="logout">
                    <button type="submit" id="logout" class="session-button">Logout</button>
                </form>
            <?php else: ?>
                <form action="#" method="POST" id="form-user-login" class="form-user">
                    <input type="hidden" value="login" name="login">
                    <button type="submit" id="login" class="session-button">Login</button>
                </form>
            <?php endif; ?>
        </div>
    </nav>
</header>