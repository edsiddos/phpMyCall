
(function ($) {

    var MultiSelectTransfer = function (elem, data) {

        var template;
        var elemento = elem;
        var data = data;

        /**
         * Método que realiza a geração dos dois select's e dos botões.
         * Preenche os select com as options.
         * Adiciona os eventos aos botões de adicionar e remover.
         */
        this.init = function () {
            template = '<table id="multi-select-transfer">\
                            <tbody>\
                                <tr>\
                                    <td>\
                                        <input type="text" id="filter-origin">\
                                    </td>\
                                    <td></td>\
                                    <td>\
                                        <input type="text" id="filter-destiny">\
                                    </td>\
                                </tr>\
                                <tr>\
                                    <td rowspan="4" class="select">\
                                        <select multiple="" name="' + data.name_select_origin + '" id="origin"></select>\
                                    </td>\
                                    <td class="botoes"><button id="select-add">></button></td>\
                                    <td rowspan="4" class="select">\
                                        <select multiple="" name="' + data.name_select_destiny + '" id="destiny"></select>\
                                    </td>\
                                </tr>\
                                <tr><td class="botoes"><button id="select-add-all">>></button></td></tr>\
                                <tr><td class="botoes"><button id="select-rem"><</button></td></tr>\
                                <tr><td class="botoes"><button id="select-rem-all"><<</button></td></tr>\
                            </tbody>\
                        </table>';

            /*
             * Exibe tabela conforme template acima.
             * Preenche os select's origin e destiny.
             */
            elemento.html(template);
            this.origin();
            this.destiny();

            /*
             * Redimenciona os botões conforme
             * tamanho do select.
             */
            this.resizeButtons();

            /*
             * Adiciona evento de adicionar e remover
             * valores das caixa de seleção
             */
            this.addOption();
            this.addAllOption();
            this.remOption();
            this.remAllOption();

            /*
             * Ordena os valores das select's em ordem alfabetica
             */
            $('#multi-select-transfer #origin option').sortOption();
            $('#multi-select-transfer #destiny option').sortOption();

            $('#multi-select-transfer input').prop('placeholder', data.placeholder_filters);

            /*
             * Cria evento de filtro ao digitar nos input's
             */
            this.filter('#multi-select-transfer #origin option', '#multi-select-transfer #filter-origin');
            this.filter('#multi-select-transfer #destiny option', '#multi-select-transfer #filter-destiny');
        };

        /**
         * Redimenciona botões
         */
        this.resizeButtons = function () {
            var height_select = $("#multi-select-transfer > tbody > tr > td > select").height();
            height_select = parseInt(height_select);
            var height_botoes = parseInt(height_select / 4 * 0.90);
            $("#multi-select-transfer button").height(height_botoes);
        };

        /**
         * Preenche select origin
         * caixa de seleção da esquerda
         */
        this.origin = function () {
            $.each(data.origin, function (key, value) {
                $("#multi-select-transfer #origin").append('<option value="' + value.value + '">' + value.name + '</option>');
            });
        };

        /**
         * Preenche select destiny
         * caixa de seleção da direita
         */
        this.destiny = function () {
            $.each(data.destiny, function (key, value) {
                $("#multi-select-transfer #destiny").append('<option value="' + value.value + '">' + value.name + '</option>');
            });
        };

        /**
         * Evento ao clicar remove options selecionadas de origin para destiny
         */
        this.addOption = function () {
            $('#multi-select-transfer #select-add').click(function () {
                $('#multi-select-transfer #origin option:selected').remove().appendTo('#multi-select-transfer #destiny');
                $('#multi-select-transfer #destiny option').sortOption();
            });
        };

        /**
         * Evento ao clicar remove todas options origin para destiny
         */
        this.addAllOption = function () {
            $('#multi-select-transfer #select-add-all').click(function () {
                $('#multi-select-transfer #origin option').prop('selected', true);
                $('#multi-select-transfer #origin option:selected').remove().appendTo('#multi-select-transfer #destiny');
                $('#multi-select-transfer #destiny option').sortOption();
            });
        };

        /**
         * Evento ao clicar remove options selecionadas em destiny para origin
         */
        this.remOption = function () {
            $('#multi-select-transfer #select-rem').click(function () {
                $('#multi-select-transfer #destiny option:selected').remove().appendTo('#multi-select-transfer #origin');
                $('#multi-select-transfer #origin option').sortOption();
            });
        };

        /**
         * Remove todas as options de destiny e adiciona em origin
         */
        this.remAllOption = function () {
            $('#multi-select-transfer #select-rem-all').click(function () {
                $('#multi-select-transfer #destiny option').prop('selected', true);
                $('#multi-select-transfer #destiny option:selected').remove().appendTo('#multi-select-transfer #origin');
                $('#multi-select-transfer #origin option').sortOption();
            });
        };

        /**
         * Realiza filtragem das inputs.
         * @param string select Seletor da caixa de seleção (select)
         * @param string input Seletor do input
         */
        this.filter = function (select, input) {
            $(input).keyup(function () {
                var search = $(this).val();

                if (search.length > 0) {
                    var obj_search = new RegExp(search, 'i');

                    $(select).each(function (key, value) {
                        if (obj_search.test($(this).text())) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                } else {
                    $(select).show();
                }

            });
        };

    };

    $.fn.multiSelectTransfer = function (data) {
        var elem = $(this);

        var data_select = {
            origin: (typeof data.origin === "undefined" ? "" : data.origin),
            destiny: (typeof data.destiny === "undefined" ? "" : data.destiny),
            placeholder_filters: (typeof data.placeholder_filters === "undefined" ? "" : data.placeholder_filters),
            name_select_origin: (typeof data.name_select_origin === "undefined" ? "origin" : data.name_select_origin),
            name_select_destiny: (typeof data.name_select_destiny === "undefined" ? "destiny" : data.name_select_destiny)
        };

        var multi = new MultiSelectTransfer(elem, data_select);
        multi.init();
    };


    $.fn.sortOption = function () {
        var options = $(this);

        var arr = options.map(function (_, o) {
            return {
                text: $(o).text(),
                value: o.value
            };
        }).get();

        arr.sort(function (option1, option2) {
            return option1.text > option2.text ? 1 : option1.text < option2.text ? -1 : 0;
        });

        options.each(function (i, o) {
            o.value = arr[i].value;
            $(o).text(arr[i].text);
        });
    };
})(jQuery);