

var Aguarde = function (texto) {

    var config = {
        status: true,
        texto: (typeof texto === 'undefined' ? 'Aguarde ...' : texto)
    };

    var template = '<div id="aguarde">';
    template += '<i class="fa fa-spinner fa-pulse fa-5x"></i>';
    template += '<p>' + config.texto + '</p>';
    template += '</div>';

    $('body').append(template);
    $('#aguarde').hide();

    this.mostrar = function () {
        $('#aguarde').show();
    };

    this.ocultar = function () {
        $('#aguarde').hide();
    }
};

