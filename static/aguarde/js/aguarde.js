

var Aguarde = function (img, texto) {

    var config = {
        status: true,
        img: (typeof img === 'undefined' ? '' : img),
        texto: (typeof texto === 'undefined' ? 'Aguarde ...' : texto)
    };

    var template = '<div id="aguarde">';
    template += config.img !== '' ? '<img src="' + config.img + '" />' : '';
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

