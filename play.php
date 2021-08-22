<?
require 'vendor/autoload.php';

if (!empty($_GET['create_room']) && $_GET['create_room']==1) {
  require __DIR__.'/src/initialize.php';
  die;
}elseif (!empty($_GET['room_key'])) {
  require __DIR__.'/src/validate.php';
}else {
  header('Location: /');
  die;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/index.css">
  <title>Jogar | Jogo da velha</title>
</head>
<body>
  <main>
  	<h1 class="center">Jogo da velha</h1>
  	<? if ($valid) :?>
  	<div id="game" class="d-none">
    	<div class="center mt">Agora é a vez do <span id="vez"><i class="bi bi-x-lg"></i></span></div>
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
      <div class="center mt-3">Você é o <span id="bexoro"><i class="bi bi-x-lg"></i></span></span></div>
    </div>
    <div class="center" id="loading">
      <div class="c-loader"></div>
      <p>Aguardando adversário ...</p>
    </div>
    <div class="center mt-3">
      <p class="mb-2">Link para a partida:</p>
      <input type="text" disabled="true" id="copy_text" value="https://leone.tec.br/games/hash/play?room_key=">
      <button id="copy" class="mt-2">Copiar</button>
    </div>
  </main>
  <script>
    var be = true;
  </script>
  <? else:?>
    <p class="center mt-3">Código inválido!</p>
    <div class="center mt-3 mb-3">
      <button class="btn" id="join-room">Entrar em uma partida</button>
    </div>
    <div id="join" class="d-none mt-3 mb-3">
      <div class="row">
        <input type="text" placeholder="Digite o código ou o link ..." id="code"><button id="btn-join"><i class="bi bi-door-open-fill"></i></button>
    	</div>
    </div>
  <? endif;?>
  <script src="js/jquery.min.js"></script>
  <script src="js/index.js"></script>
</body>
</html>