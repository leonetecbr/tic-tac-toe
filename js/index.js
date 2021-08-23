var vez = true, tag, networkError = 0;
var x = '<i class="bi bi-x-lg"></i>';
var o = '<i class="bi bi-circle"></i>';

function sendServer(id){
  $.ajax({
    url: 'moveBoard.php',
    dataType: 'json',
    data: {
      room_key: room_key,
      position: id,
      per: be
    },
    type: 'POST'
  }).done(function (data) {
    if (data.success) {
      vez = data.dados.vez;
      drawGame(data.dados);
      startGame();
    }else{
      setTimeout(function() {sendServer(id)}, 2000);
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
  $.ajax({
    url: 'waitOponent.php',
    data: {room_key: room_key},
    dataType: 'json',
    type: 'GET'
  }).done(function (data) {
    if (data.connect) {
      vez = data.dados.vez;
      drawGame(data.dados);
      startGame();
    }else{
      setTimeout(function() {waitOponent()}, 1000);
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

function mudarVez(){
  vez = !vez;
  if (vez) {
    $('#vez').html(x);
  }else{
    $('#vez').html(o);
  }
}

function marcarCasa(id) {
  $('#item-'+id).html(tag);
  mudarVez();
  sendServer(id);
  waitOponent();
}

$('.item').click(function(){
  if (vez===be) {
    var id = $(this).attr('id').replace('item-', '');
    marcarCasa(id);
  }else{
    alert('Aguarde sua vez!');
  }
});

function startGame(){
  $('#loading').hide();
  $('#game').removeClass('d-none');
  
  if (be) {
    tag = x;
  }else{
    tag = o;
  }
  
  $('#bexoro').html(tag);
  
  if (vez!==be) {
    setTimeout(function() {waitOponent();}, 1000);
  }
}

$('#copy').click(function(){
  $('#copy_text').attr('disabled', false);
  $('#copy_text').select();
  document.execCommand('copy');
  $('#copy_text').attr('disabled', true);
  alert('Texto copiado!');
});

$('#join-room').click(function() {
  $('#create-room').hide();
  $('#join').removeClass('d-none');
  $('#join-room').css({'background-color':'#ff4c4c'});
  window.location.href = '#join';
});

$('#btn-join').click(function(){
  var code = $('#code').val();
  if (code == '') {
    alert('Digite um código ou link!');
  }else if (code.indexOf('https://leone.tec.br/games/tic-tac-toe/play')===0){
    window.location.href = code;
  }else{
    if (code.length==42) {
      window.location.href = 'play?room_key='+code;
    }else{
      alert('Código inválido!');
    }
  }
});