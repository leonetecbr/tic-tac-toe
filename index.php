<?
require 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/index.css">
  <script src="https://www.google.com/recaptcha/api.js?render=<?echo $_ENV['PUBLIC_RECAPTCHA_V3'];?>"></script>
  <title>Início | Jogo da velha</title>
</head>
<body>
  <main>
  	<h1 class="center">Jogo da velha</h1>
  	<div class="main">
  	  <p>Você pode criar uma partida ou entrar em uma partida criada pelo(a) seu(ua) amigo(a), quem cria será o X e quem entra será O. O que você quer fazer ?</p>
  	  <div class="center mb-3">
    	  <a href="play?create_room=1"><button class="btn" id="create-room">Criar partida</button></a>
    	  <p class="small mb-3">Este botão é protegido pelo Google reCAPTCHA para garantir que você não é um robô. <a target="_blank" rel="nofollow" href="https://policies.google.com/privacy">Políticas de Privacidade</a> e <a target="_blank" rel="nofollow" href="https://policies.google.com/terms">Termos de Serviço</a> do Google são aplicáveis.<p>
    	  <button class="btn" id="join-room">Entrar em uma partida</button>
    	</div>
    	<div id="join" class="d-none">
    	  <div class="row row-center">
    	    <input type="text" placeholder="Digite o código ou o link ..." id="code" class="ms-auto"><button id="btn-join"><i class="bi bi-door-open-fill"></i></button>
    	  </div>
    	</div>
  	</div>
  </main>
  <script src="js/jquery.min.js"></script>
  <script src="js/index.js"></script>
</body>
</html>