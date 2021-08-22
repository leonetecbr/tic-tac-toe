var vez = true, tag;
var x = '<i class="bi bi-x-lg"></i>';
var o = '<i class="bi bi-circle"></i>';

function sendServer(id){
  //to do
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
  }else if (code.indexOf('https://leone.tec.br/games/hash/play')===0){
    window.location.href = code;
  }else{
    if (code.length==42) {
      window.location.href = 'play?room_key='+code;
    }else{
      alert('Código inválido!');
    }
  }
});