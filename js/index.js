var vez = true, tag, networkError = 0, ended = false, win = null;
var x = '<i class="bi bi-x-lg"></i>';
var o = '<i class="bi bi-circle"></i>';

$('#create-room').click(function(e) {
  e.preventDefault();
  isNotRobot();
});

function isNotRobot(to = 'play?create_room=1') {
  grecaptcha.ready(function() {
    grecaptcha.execute('6LePcSEcAAAAAPGLHLV91ZMtH0Bkxkr47aiF4toJ', {action: 'start_tictactoe'})
      .then(function(token) {
        window.location.href = to+'&g-recaptcha-response='+token;
      });
  });
}

function joinRoom() {
  $('#create-room').hide();
  $('#join').removeClass('d-none');
  $('#join-room').css({'background-color':'#ff4c4c'});
  window.location.href = '#join';
}

function checkEnd() {
  var casas = $('.item'), marked = 0, markedPer = [];
  
  for (var i = 0;i<casas.length;i++) {
    if (casas[i].innerHTML != '') {
      marked++;
      if (casas[i].innerHTML == x) {
        markedPer[i+1] = true;
      }else{
        markedPer[i+1] = false;
      }
    }
  }
  
  if ((markedPer[1]===true && markedPer[2]===true && markedPer[3]===true) || (markedPer[4]===true && markedPer[5]===true && markedPer[6]===true) || (markedPer[7]===true && markedPer[8]===true && markedPer[9]===true)) {
    ended = true;
    win = true;
  } else if ((markedPer[1]===false && markedPer[2]===false && markedPer[3]===false) || (markedPer[4]===false && markedPer[5]===false && markedPer[6]===false) || (markedPer[7]===false && markedPer[8]===false && markedPer[9]===false)){
    ended = true;
    win = false;
  } else if ((markedPer[1]===true && markedPer[4]===true && markedPer[7]===true) || (markedPer[2]===true && markedPer[5]===true && markedPer[8]===true) || (markedPer[3]===true && markedPer[6]===true && markedPer[9]===true)){
    ended = true;
    win = true;
  } else if ((markedPer[1]===false && markedPer[4]===false && markedPer[7]===false) || (markedPer[2]===false && markedPer[5]===false && markedPer[8]===false) || (markedPer[3]===false && markedPer[6]===false && markedPer[9]===false)){
    ended = true;
    win = false;
  } else if ((markedPer[1]===true && markedPer[5]===true && markedPer[9]===true) || (markedPer[3]===true && markedPer[5]===true && markedPer[7]===true)){
    ended = true;
    win = true;
  } else if ((markedPer[1]===false && markedPer[5]===false && markedPer[9]===false) || (markedPer[3]===false && markedPer[5]===false && markedPer[7]===false)){
    ended = true;
    win = false;
  } else if (marked==9) {
    ended = true;
  }
  
  if (ended) {
    $('#xo-vez').hide();
    $('#result').removeClass('d-none');
    $('#link-game').html('<button class="btn link" id="create-room" onclick="isNotRobot(\'play?create_room=1&hash=\'+next)">Jogar de novo</button><p class="small mb-3">Este botão é protegido pelo Google reCAPTCHA para garantir que você não é um robô. <a target="_blank" rel="nofollow" href="https://policies.google.com/privacy">Políticas de Privacidade</a> e <a target="_blank" rel="nofollow" href="https://policies.google.com/terms">Termos de Serviço</a> do Google são aplicáveis.<p>');
    if (win === null) {
      $('#result').html('Empate!');
      $('#result').css({'color':'#ff8c00'});
    }else if(win == be){
      $('#result').html('Vitória!');
      $('#result').css({'color':'#080'});
    }else{
      $('#result').html('Derrota!');
      $('#result').css({'color':'#f33'});
    }
  }
}

function sendServer(id){
  if (!ended) {
    $.ajax({
      url: 'moveBoard',
      dataType: 'json',
      data: {
        room_key: room_key,
        position: id,
        per: be
      },
      type: 'POST'
    }).done(function (data) {
      if (data.success) {
        startGame();
      }else if (!ended){
        alert(data.message);
      }
      networkError = 0;
      $('#network-error').addClass('d-none');
    }).fail(function() {
      networkError++;
      if (networkError < 5) {
        $('#network-error').removeClass('d-none');
        setTimeout(function() {sendServer(id)}, 2000);
      }else{
        $('#loading').hide();
        $('#network-error').show();
        $('#network-error').html('Parece que você está sem internet!');
      }
    });
  }
}

function drawGame(dados){
  if (vez) {
    $('#vez').html(x);
  }else{
    $('#vez').html(o);
  }
  
  for (var i=1;i<10; i++) {
    if (dados[i] != null) {
      if (dados[i]==1) {
        $('#item-'+i).html(x);
      }else{
        $('#item-'+i).html(o);
      }
    }
  }
}

function waitOponent(){
  if (!ended) {
    $.ajax({
      url: 'waitOponent',
      data: {room_key: room_key},
      dataType: 'json',
      type: 'GET'
    }).done(function (data) {
      if (data.success) {
        if (data.connect) {
          vez = data.dados.vez;
          drawGame(data.dados);
          startGame();
        }else{
          setTimeout(function() {waitOponent()}, 2000);
        }
      }else if(!ended){
        alert(data.message);
      }
      networkError = 0;
      $('#network-error').addClass('d-none');
    }).fail(function() {
      networkError++;
      if (networkError < 5) {
        $('#network-error').removeClass('d-none');
        setTimeout(function() {waitOponent()}, 2000);
      }else{
        $('#loading').hide();
        $('#game').hide();
        $('#network-error').show();
        $('#network-error').html('Parece que você está sem internet!');
      }
    });
  }
}

function mudarVez(){
  vez = !vez;
  if (vez) {
    $('#vez').html(x);
  }else{
    $('#vez').html(o);
  }
}

function marcarCasa(id) {
  if ($('#item-'+id).html()=='') {
    $('#item-'+id).html(tag);
    mudarVez();
    sendServer(id);
    
    checkEnd();
    if (!ended){
      waitOponent();
    }
  }else{
    alert('Casa já marcada!');
  }
}

$('.item').click(function(){
  if (!ended) {
    if (vez===be) {
      var id = $(this).attr('id').replace('item-', '');
      marcarCasa(id);
    }else{
      alert('Aguarde sua vez!');
    }
  }else{
    alert('Essa partida já acabou!');
  }
});

function redirect($to){
  window.location.href = $to;
}

function startGame(){
  $('#loading').hide();
  $('#game').removeClass('d-none');
  
  if (be) {
    tag = x;
  }else{
    tag = o;
  }
  
  $('#bexoro').html(tag);
  
  checkEnd();
  
  if (!ended){
    if (vez!==be) {
      setTimeout(function() {waitOponent();}, 2000);
    }
  }
}

$('#copy').click(function(){
  $('#copy_text').attr('disabled', false);
  $('#copy_text').select();
  document.execCommand('copy');
  $('#copy_text').attr('disabled', true);
  alert('Link copiado!');
});

$('#join-room').click(function() {
  joinRoom();
});

$('#btn-join').click(function(){
  var code = $('#code').val();
  if (code == '') {
    alert('Digite um código ou link!');
  }else if (code.indexOf('https://leone.tec.br/games/tic-tac-toe/play')===0){
    window.location.href = code;
  }else if (code.length==42) {
    window.location.href = 'play?room_key='+code;
  }else{
    alert('Código inválido!');
  }
});