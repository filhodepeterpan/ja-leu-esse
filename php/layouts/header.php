<?php
    if(isset($_POST['login'])){    
        header("Location:../pages/login.php");
    }

    if(isset($_POST['logout'])){
        session_destroy();
        header("Location:../pages/home.php");
    }

?>

<header>
    <nav>
        <div class="logo">
            <a href="../index.php"><img src="../assets/logo.png" alt="Logo"></a>
        </div>

        <div class="nav-links">
            <a href="../pages/home.php">Início</a>
            <a href="../pages/perfil.php">Meu perfil</a>
        </div>

        <div class="nav-usuario">
            <?php if (!isset($_SESSION['logado'])): ?>
                <form action="#" method="POST" id="form-login">
                    <input type="hidden" value="login" name="login">
                    <button type="submit" id="login">Login</button>
                </form>
                
            <?php else: ?>
                <form action="#" method="POST" id="form-logout">
                    <p><?=@$_SESSION['user']?>!</p>
                    <input type="hidden" value="logout" name="logout">
                    <button type="submit" id="logout">Logout</button>
                </form>
            <?php endif; ?>
        </div>
    </nav>
</header>