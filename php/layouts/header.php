<?php
    include "../scripts/functions.php";
    $logado = verificaLogin();

    if(isset($_POST['login'])){    
        header("Location:../pages/login.php");
    }

    if(isset($_POST['logout'])){
        header("Location:../scripts/logout.php");
    }

?>

<header>
    <nav>
        <div class="logo">
            <a href="../../index.php"><img src="../../assets/img/logo.png" alt="Logo" width="50"></a>
            <a href="../../index.php"><h1 class="logo-titulo">Já leu esse?</h1></a>
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
                    <p>@<?=$_SESSION['user']?></p>
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