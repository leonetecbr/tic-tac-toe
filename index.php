<?php

require 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load(); ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/games/tic-tac-toe/css/index.min.css">
    <link rel="stylesheet" href="/games/tic-tac-toe/css/bootstrap.min.css">
    <link rel="stylesheet" href="/games/tic-tac-toe/css/bootstrap-icons.css">
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $_ENV['PUBLIC_RECAPTCHA_V3']; ?>"></script>
    <link rel="icon" href="/games/tic-tac-toe/favicon.ico">
    <title>Início | Jogo da velha</title>
</head>

<body>
    <main class="mt-3 container">
        <h1 class="display-4 text-center">Jogo da velha</h1>
        <div class="main">
            <p class="text-center">Você pode criar uma partida ou entrar em uma partida criada pelo(a) seu(ua) amigo(a),
                quem cria será o <i class="bi bi-x-lg"></i> e quem entra será <i class="bi bi-circle"></i>. O que você quer
                fazer ?</p>
            <?php require('src/Components/buttons.html'); ?>
    </main>
    <script src="/games/tic-tac-toe/js/jquery.min.js"></script>
    <script src="/games/tic-tac-toe/js/bootstrap.bundle.min.js"></script>
    <script src="/games/tic-tac-toe/js/index.min.js"></script>
</body>

</html>