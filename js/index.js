var vez = true, tag, networkError = 0;
var x = '<i class="bi bi-x-lg"></i>';
var o = '<i class="bi bi-circle"></i>';

function sendServer(id){
  //to do
}

function waitOponent(room_key){
  $.ajax({
    url: 'waitOponent.php?room_key='+room_key,
    dataType: 'json',
    contentType: 'application/json',
    type: 'POST'
  }).done(function (data) {
    if (data.connect) {
      startGame();
    }else{
      setTimeout(function() {waitOponent(room_key)}, 1000);
    }
    networkError = 0;
    $('#network-error').addClass('d-none');
  }).fail(function() {
    networkError++;
    if (networkError < 5) {
      $('#network-error').removeClass('d-none');
      setTimeout(function() {waitOponent(room_key)}, 2000);
    }else{
      $('.c-loader').hide();
      $('#t-loader').hide();
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
  
  $('.item').click(function(){
    if (vez===be) {
      var id = $(this).attr('id').replace('item-', '');
      marcarCasa(id);
    }else{
      alert('Aguarde sua vez!');
    }
  });
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