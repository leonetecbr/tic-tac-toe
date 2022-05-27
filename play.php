<?php

require 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_name('TicTacToe');
session_start();

$session_id = session_id() ?? null;

$person = ($_GET['person'] ?? '' === 'nobot');

if (!empty($_POST['create_room']) && $_POST['create_room'] == 1) {
    require __DIR__ . '/src/Components/initialize.php';
    if (!$isRobot) {
        die;
    }
    $person = true;
} elseif (!empty($_GET['room_key'])) {
    require __DIR__ . '/src/Components/validate.php';
    $isRobot = false;
} else {
    header('Location: /games/tic-tac-toe/');
    die;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/games/tic-tac-toe/css/bootstrap.min.css">
    <link rel="stylesheet" href="/games/tic-tac-toe/css/index.css">
    <?php if ($isRobot) : ?>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php else : ?>
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $_ENV['PUBLIC_RECAPTCHA_V3']; ?>" async></script>
    <?php endif; ?>
    <title>Jogar | Jogo da velha</title>
</head>

<body>
    <main class="mt-3 container">
        <h1 class="display-4 text-center">Jogo da velha</h1>
        <?php if (!$person) : ?>
            <div class="text-center mt-4">
                <button class="btn btn-danger btn-lg" id="join-room">Entrar nessa partida</button>
            </div>
            <script>
                document.getElementById('join-room').addEventListener('click', function() {
                    if (document.cookie.indexOf('TicTacToe') !== -1) {
                        window.location.href = window.location.href + '&person=nobot';
                    } else {
                        alert('Ative os cookies primeiro');
                    }
                });
            </script>
        <?php elseif ($isRobot) : ?>
            <form id="form" method="post">
                <p class="text-center">Não conseguimos confirmar que você não é um robô, por favor marque a caixa de verificação abaixo:</p>
                <div class="g-recaptcha" data-sitekey="<?php echo $_ENV['PUBLIC_RECAPTCHA_V2']; ?>" data-callback="submit"></div>
                <input type="hidden" name="create_room" value="<?php echo $_POST['create_room'] ?? ''; ?>">
                <input type="hidden" name="hash" value="<?php echo $_POST['hash'] ?? ''; ?>">
                <input type="hidden" name="type" value="v2">
            </form>
            <script>
                function submit() {
                    document.getElementById('form').submit();
                }
            </script>
        <?php elseif ($valid) : ?>
            <div class="position-fixed p-3 top-0 end-0">
                <div class="toast align-items-center text-white bg-danger border-0" id="error-alert" data-bs-autohide="true" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-exclamation-circle-fill"></i> <span id="error-text"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="game" class="d-none">
                <div class="text-center mb-3" id="xo-vez">Agora é a vez do <span id="vez"><i class="bi bi-x-lg"></i></span></div>
                <div class="text-center mb-3 d-none" id="result"></div>
                <div id="tabuleiro">
                    <div class="line d-flex">
                        <div class="item" id="item-1"></div>
                        <div class="item" id="item-2"></div>
                        <div class="item" id="item-3"></div>
                    </div>
                    <div class="line d-flex">
                        <div class="item" id="item-4"></div>
                        <div class="item" id="item-5"></div>
                        <div class="item" id="item-6"></div>
                    </div>
                    <div class="line d-flex">
                        <div class="item" id="item-7"></div>
                        <div class="item" id="item-8"></div>
                        <div class="item" id="item-9"></div>
                    </div>
                </div>
                <div class="text-center mt-3">Você é o <span id="bexoro"></span></div>
            </div>
            <div class="text-center" id="loading">
                <div class="c-loader"></div>
                <p id="t-loader"><?php echo ($be == 'true') ? 'Aguardando adversário ...' : 'Conectando ...'; ?></p>
            </div>
            <div class="text-center">
                <div class="alert alert-warning my-3 d-none mx-auto" id="network-error"><i class="bi bi-exclamation-triangle-fill"></i> <span id="network-error-text">Estamos com problemas de conexão ...</span></div>
            </div>
            <div class="d-none text-center mt-2" id="try-again">
                <form action="play" method="post" id="create-form">
                    <input type="hidden" name="create_room" value="1">
                    <input type="hidden" name="g-recaptcha-response" value="" id="token">
                    <input type="hidden" name="hash" value="<?php echo $game['next']; ?>">
                    <button class="btn btn-primary mb-3 w-100" id="create-room">Jogar de novo</button>
                </form>
                <div class="small text-muted">Este botão é protegido pelo Google reCAPTCHA para garantir que você não é um robô. <a target="_blank" rel="nofollow" href="https://policies.google.com/privacy" class="text-decoration-none small">Políticas de Privacidade</a> e <a target="_blank" rel="nofollow" href="https://policies.google.com/terms" class="text-decoration-none small">Termos de Serviço</a> do Google são aplicáveis.</div>
            </div>
            <div class="text-center mt-2" id="link-game">
                <div class="position-fixed p-3 bottom-0 end-0">
                    <div class="toast align-items-center text-white bg-success border-0" id="copy-alert" data-bs-autohide="true" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bi bi-check-circle-fill"></i> Texto copiado!
                            </div>
                        </div>
                    </div>
                </div>
                <p class="mb-2">Link para a partida:</p>
                <div class="mx-auto d-flex justify-content-between col-lg-9">
                    <div class="mx-auto col-10">
                        <input type="text" disabled id="copy-text" class="form-control" value="http<?php echo (!empty($_SERVER['HTTPS']))?'s':''; ?>://<?php echo $_SERVER['SERVER_NAME']?>/games/tic-tac-toe/play?room_key=<?php echo $room_key; ?>"><br />
                    </div>
                    <div class="col-2">
                        <button id="copy" class="btn btn-secondary"><i class="bi bi-clipboard fs-6"></i></button>
                    </div>
                </div>
            </div>
    </main>
    <script src="/games/tic-tac-toe/js/jquery.min.js"></script>
    <script src="/games/tic-tac-toe/js/bootstrap.bundle.min.js"></script>
    <script src="/games/tic-tac-toe/js/index.js"></script>
    <script>
        const be = <?php echo $be; ?>,
            room_key = '<?php echo $room_key; ?>',
            next = '<?php echo $game['next']; ?>';
        waitOpponent();
    </script>
<?php else : ?>
    <p class="text-center mt-3">Código inválido! Esse código não existe, já foi usado por alguém ou essa partida já acabou!</p>
    <?php require('src/Components/buttons.html'); ?>
    <script src="/games/tic-tac-toe/js/jquery.min.js"></script>
    <script src="/games/tic-tac-toe/js/bootstrap.bundle.min.js"></script>
    <script src="/games/tic-tac-toe/js/index.js"></script>
<?php endif; ?>
</body>

</html>