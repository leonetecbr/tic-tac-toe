let vez = true, tag, networkError = 0, ended = false, win = null;
let x = '<i class="bi bi-x-lg"></i>', o = '<i class="bi bi-circle"></i>';
let createRoom = $('#create-room'), joinRoomButton = $('#join-room'), casas = $('.item');

createRoom.on('click', function(e) {
    e.preventDefault();
    createRoom.attr('disabled', true);
    createRoom.html('Verificando ....');
    isNotRobot();
});

function isNotRobot(to = null) {
    grecaptcha.ready(function() {
        grecaptcha.execute('6LePcSEcAAAAAPGLHLV91ZMtH0Bkxkr47aiF4toJ', {action: 'start_tictactoe'})
            .then(function(token) {
                if (to === null) {
                    $('#token').val(token);
                    $('#create-form').trigger('submit');
                } else window.location.href = to+'&g-recaptcha-response='+token;
            });
    });
}

function joinRoom() {
    createRoom.hide();
    $('#join').removeClass('d-none');
    $('.buttons').removeClass('justify-content-around').addClass('justify-content-center');
    joinRoomButton.css({'background-color':'#ff4c4c'}).addClass('col-10');
    window.location.href = '#join';
}

function checkEnd() {
    let marked = 0, markedPer = [];

    for (let i = 0;i<casas.length;i++) {
        let content = casas[i].innerHTML;
        if (content !== '') {
            marked++;
            markedPer[i + 1] = content === x;
        }
    }

    if (typeof markedPer[3] === 'boolean' && ((markedPer[1] === markedPer[3] && markedPer[2] === markedPer[3]) || (markedPer[6] === markedPer[3] && markedPer[9] === markedPer[3]))) {
        ended = true;
        win = markedPer[3];
    } else if (typeof markedPer[7] === 'boolean' && ((markedPer[1] === markedPer[7] && markedPer[4] === markedPer[7]) || (markedPer[8] === markedPer[7] && markedPer[9] === markedPer[7]))){
        ended = true;
        win = markedPer[7];
    } else if (typeof markedPer[5] === 'boolean' && ((markedPer[1] === markedPer[5] && markedPer[9] === markedPer[5]) || (markedPer[3] === markedPer[5] && markedPer[7] === markedPer[5]) || (markedPer[2] === markedPer[5] && markedPer[8] === markedPer[5]) || (markedPer[4] === markedPer[5] && markedPer[6] === markedPer[5]))){
        ended = true;
        win = markedPer[5];
    } else if (marked === 9) {
        ended = true;
    }

    if (ended) {
        let result =  $('#result');
        $('#xo-vez').hide();
        result.removeClass('d-none');
        $('#link-game').addClass('d-none');
        $('#try-again').removeClass('d-none');
        if (win === null) {
            result.html('Empate!');
            result.css({'color':'#ff8c00'});
        }else if(win === be){
            result.html('Vitória!');
            result.css({'color':'#080'});
        }else{
            result.html('Derrota!');
            result.css({'color':'#f33'});
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
        }).done((data) => {
            if (data.success) {
                startGame();
            }else if (!ended){
                showError(data.message);
            }
            networkError = 0;
            $('#network-error').addClass('d-none');
        }).fail(() => {
            networkError++;
            if (networkError < 5) {
                $('#network-error').removeClass('d-none');
                setTimeout(() => sendServer(id), 2000);
            }else{
                let networkAlert = $('#network-error');
                $('#game').hide();
                networkAlert.show().removeClass('alert-warning').addClass('alert-danger');
                $('#network-error-text').html('Parece que você está sem internet!');
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

    for (let i = 1;i < 10; i++) {
        if (dados[i] !== null) {
            if (dados[i] === 1) {
                $('#item-'+i).html(x);
            }else{
                $('#item-'+i).html(o);
            }
        }
    }
}

function waitOpponent(){
    if (!ended) {
        $.ajax({
            url: 'waitOpponent',
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
                    setTimeout(() => waitOpponent(), 2000);
                }
            }else if(!ended){
                showError(data.message);
            }
            networkError = 0;
            $('#network-error').addClass('d-none');
        }).fail(() => {
            networkError++;
            if (networkError < 5) {
                $('#network-error').removeClass('d-none');
                setTimeout(() => waitOpponent(), 2000);
            }else{
                $('#loading').hide();
                let networkAlert = $('#network-error');
                networkAlert.show().removeClass('alert-warning').addClass('alert-danger');
                $('#network-error-text').html('Parece que você está sem internet!');
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
    let item = $('#item-'+id);
    if (item.html() === '') {
        item.html(tag);
        mudarVez();
        sendServer(id);

        checkEnd();
        if (!ended){
            waitOpponent();
        }
    }else{
        showError('Casa já marcada!');
    }
}

casas.on('click', function(){
    if (!ended) {
        if (vez===be) {
            let id = $(this).attr('id').replace('item-', '');
            marcarCasa(id);
        }else{
            showError('Aguarde sua vez!');
        }
    }else{
        showError('Essa partida já acabou!');
    }
});

function showError(error){
    $('#error-text').html(error)
    let toast = new bootstrap.Toast(document.getElementById('error-alert'))
    toast.show();
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
            setTimeout(() => waitOpponent(), 2000);
        }
    }
}

$('#copy').on('click', () => {
    let copyText = $('#copy-text');
    copyText.attr('disabled', false);
    copyText.select();
    document.execCommand('copy');
    copyText.attr('disabled', true);
    let toast = new bootstrap.Toast(document.getElementById('copy-alert'))
    toast.show()
});

joinRoomButton.on('click', () => joinRoom());

$('#code').on('change', () => $('#icode').addClass('d-none'));

$('#btn-join').on('click', () => {
    let code = $('#code').val();
    if (code === '') {
        $('#icode').removeClass('d-none');
    }else if (code.indexOf('https://leone.tec.br/games/tic-tac-toe/play')===0){
        window.location.href = code;
    }else if (code.length === 42) {
        window.location.href = 'play?room_key='+code;
    }else{
        $('#icode').removeClass('d-none');
    }
});