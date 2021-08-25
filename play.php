<?
require 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_name('TicTacToe');
session_start();

$session_id = session_id()??null;

if (!empty($_GET['create_room']) && $_GET['create_room']==1) {
  require __DIR__.'/src/initialize.php';
  if (!$isRobot) {
    die;
  }
}elseif (!empty($_GET['room_key'])) {
  require __DIR__.'/src/validate.php';
  $isRobot = false;
}else {
  header('Location: /games/tic-tac-toe/');
  die;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/index.css">
  <?php if ($isRobot): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <?php else: ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?echo $_ENV['PUBLIC_RECAPTCHA_V3'];?>" async></script>
  <?php endif; ?>
  <title>Jogar | Jogo da velha</title>
</head>
<body>
  <main class="col">
  	<h1 class="center">Jogo da velha</h1>
  	<? if ($isRobot):?>
  	<form id="form" action="?">
  	  <p class="center">Não conseguimos confirmar que você não é um robô, por favor marque a caixa de verificação abaixo:</p>
  	  <div class="g-recaptcha" data-sitekey="<?echo $_ENV['PUBLIC_RECAPTCHA_V2'];?>" data-callback="submit"></div>
  	  <input type="hidden" name="create_room" value="1">
  	  <input type="hidden" name="type" value="v2">
  	</form>
  	<script>
  	  function submit() {
  	    document.getElementById('form').submit();
      }
  	</script>
  	<? elseif ($valid) :?>
  	<div id="game" class="d-none">
    	<div class="center mt" id="xo-vez">Agora é a vez do <span id="vez"><i class="bi bi-x-lg"></i></span></div>
    	<div class="center mt-2 mb-2 d-none" id="result"></div>
    	<div id="tabuleiro">
      	<div class="row">
      	  <div class="item" id="item-1"></div>
      	  <div class="item" id="item-2"></div>
      	  <div class="item" id="item-3"></div>
      	</div>
      	<div class="row">
      	  <div class="item" id="item-4"></div>
      	  <div class="item" id="item-5"></div>
      	  <div class="item" id="item-6"></div>
      	</div>
      	<div class="row">
      	  <div class="item" id="item-7"></div>
      	  <div class="item" id="item-8"></div>
      	  <div class="item" id="item-9"></div>
      	</div>
      </div>
      <div class="center mt-3">Você é o <span id="bexoro"></span></div>
    </div>
    <div class="center" id="loading">
      <div class="c-loader"></div>
      <p id="t-loader">Aguardando adversário ...</p>
    </div>
    <div class="center">
      <p class="text-danger mb-0 d-none" id="network-error">Estamos enfretando problemas para conectar ao servidor ...</p>
    </div>
    <div class="center mt-2" id="link-game">
      <p class="mb-2">Link para a partida:</p>
      <input type="text" disabled="true" id="copy_text" value="https://leone.tec.br/games/tic-tac-toe/play?room_key=<? echo $room_key; ?>"><br/>
      <button id="copy" class="mt-2">Copiar</button>
    </div>
    <div id="join" class="d-none mt-3 mb-3">
      <div class="row row-center">
        <input type="text" placeholder="Digite o código ou o link ..." id="code"><br/><button id="btn-join"><i class="bi bi-door-open-fill"></i></button>
    	</div>
    </div>
  </main>
  <script src="js/jquery.min.js"></script>
  <script src="js/index.js"></script>
  <script>
    var be = <?echo $be;?>, room_key = '<?echo $room_key;?>';
    waitOponent();
  </script>
  <? else:?>
    <p class="center mt-3">Código inválido! Esse código não existe, já foi usado por alguém, essa partida já acabou ou expirou!</p>
    <div class="center mt-3 mb-3">
      <a href="play?create_room=1"><button class="btn" id="create-room">Criar partida</button></a>
      <p class="small mb-3">Este botão é protegido pelo Google reCAPTCHA para garantir que você não é um robô. <a target="_blank" rel="nofollow" href="https://policies.google.com/privacy">Políticas de Privacidade</a> e <a target="_blank" rel="nofollow" href="https://policies.google.com/terms">Termos de Serviço</a> do Google são aplicáveis.<p>
      <button class="btn" id="join-room">Entrar em uma partida</button>
    </div>
    <div id="join" class="d-none mt-3 mb-3">
      <div class="row row-center">
        <input type="text" placeholder="Digite o código ou o link ..." id="code"><br/><button id="btn-join"><i class="bi bi-door-open-fill"></i></button>
    	</div>
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/index.js"></script>
  <? endif;?>
</body>
</html>