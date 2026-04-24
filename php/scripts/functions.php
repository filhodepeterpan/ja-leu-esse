<?php
    function verificaLogin(){
        if (isset($_SESSION['logado']) ?? false) return true;

        return false;
    }

    function aplicaRestricao(){
        if (!verificaLogin()) {
            header("Location:../pages/login.php");
        }
    }
?>