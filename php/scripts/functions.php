<?php
    function verificaLogin(){
        return isset($_SESSION['logado']) ?? false;
    }

    function aplicaRestricao(){
        if (!verificaLogin()) header("Location:../pages/login.php");
    }
?>