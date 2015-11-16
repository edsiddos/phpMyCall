

var MultiSelect = function (elem, data) {

    var template;
    var element = document.querySelector(elem);
    var data = {
        origin: (typeof data === "undefined" || typeof data.origin === "undefined" ? "" : data.origin),
        destiny: (typeof data === "undefined" || typeof data.destiny === "undefined" ? "" : data.destiny),
        name_select_origin: (typeof data === "undefined" || typeof data.name_select_origin === "undefined" ? "mst-origin" : data.name_select_origin),
        name_select_destiny: (typeof data === "undefined" || typeof data.name_select_destiny === "undefined" ? "mst-destiny" : data.name_select_destiny)
    };

    /**
     * Método que realiza a geração dos dois select's e dos botões.
     * Preenche os select com as options.
     * Adiciona os eventos aos botões de adicionar e remover.
     */
    this.init = function () {
        template = '<div class="multi-select-transfer">\
                        <div class="mst-filters">\
                            <div class="mst-filters-left">\
                                <input type="text" class="mst-filter-origin" />\
                            </div>\
                            <div class="mst-filters-right">\
                                <input type="text" class="mst-filter-destiny" />\
                            </div>\
                        </div>\
                        <div class="mst-selects">\
                            <div class="mst-left">\
                                <select multiple="multiple" name="' + data.name_select_origin + '[]" class="mst-origin"></select>\
                            </div>\
                            <div class="mst-right">\
                                <div class="mst-buttons">\
                                    <button type="button" class="mst-select-add"> > </button>\
                                    <button type="button" class="mst-select-rem"> < </button>\
                                    <button type="button" class="mst-select-add-all"> >> </button>\
                                    <button type="button" class="mst-select-rem-all"> << </button>\
                                </div>\
                                <div class="mst-select-destiny">\
                                    <select multiple="multiple" name="' + data.name_select_destiny + '[]" class="mst-destiny"></select>\
                                </div>\
                            </div>\
                        </div>\
                    </div>';

        /*
         * Exibe tabela conforme template acima.
         * Preenche os select's origin e destiny.
         */
        element.innerHTML = template;
        origin();
        destiny();

        /*
         * Adiciona evento de adicionar e remover
         * valores das caixa de seleção
         */
        // Ao dar duplo click em um option esta pode ser:
        doubleClickOriginOption(); // Move option para select destiny
        doubleClickDestinyOption(); // Move option para select origin

        addOption();
        remOption();
        addAllOption();
        remAllOption();

        /*
         * Cria evento de filtro ao digitar nos input's
         */
        filter('.multi-select-transfer .mst-origin', '.multi-select-transfer .mst-filter-origin');
        filter('.multi-select-transfer .mst-destiny', '.multi-select-transfer .mst-filter-destiny');
    };

    /**
     * Adiciona opções ao select origin
     * @param {objeto} data_origin objeto com dados a ser inserido no select de origem
     */
    this.setOrigin = function (data_origin) {
        data.origin = data_origin;
        origin();
        doubleClickOriginOption();
    };

    /**
     * Seleciona todas as options da select origin
     */
    this.originSelect = function () {
        var options = element.querySelector('.mst-origin').options;

        for (key in options) {
            options[key].selected = true;
        }
    };

    /**
     * Adiciona opções ao select destiny
     * @param {type} data_destiny objeto com dados a ser inserido no select de destino
     */
    this.setDestiny = function (data_destiny) {
        data.destiny = data_destiny;
        destiny();
        doubleClickDestinyOption();
    };

    /**
     * Seleciona todas as options da select destiny
     */
    this.destinySelect = function () {
        var options = element.querySelector('.mst-destiny').options;

        for (key in options) {
            options[key].selected = true;
        }
    };

    /**
     * Preenche select origin
     * caixa de seleção da esquerda
     */
    var origin = function () {
        if (typeof data.origin === 'object') {
            var options = '';

            for (key in data.origin) {
                options += '<option value="' + data.origin[key]['value'] + '">' + data.origin[key]['name'] + '</option>';
            }

            element.querySelector('.multi-select-transfer .mst-origin').innerHTML = options;
            sortSelect('.mst-origin');
        }
    };

    /**
     * Preenche select destiny
     * caixa de seleção da direita
     */
    var destiny = function () {
        if (typeof data.destiny === 'object') {
            var options = '';

            for (key in data.destiny) {
                options += '<option value="' + data.destiny[key]['value'] + '">' + data.destiny[key]['name'] + '</option>';
            }

            element.querySelector('.multi-select-transfer .mst-destiny').innerHTML = options;
            sortSelect('.mst-destiny');
        }
    };

    /**
     * Ordena as options
     * @param {string} id_select Classe do select
     */
    var sortSelect = function (id_select) {
        var select = element.querySelector(id_select)
        var array = new Array();

        for (var i = 0; i < select.options.length; i++) {
            array[i] = new Array();
            array[i][0] = select.options[i].text;
            array[i][1] = select.options[i].value;
        }

        array.sort();

        while (select.options.length > 0) {
            select.options.remove(-1);
        }

        for (var i = 0; i < array.length; i++) {
            var option = document.createElement('option');
            option.value = array[i][1];
            option.text = array[i][0];
            select.options.add(option, i);
        }
    };

    /**
     * Move options selecionadas
     * @param {string} id_origem Identificador da select de origim
     * @param {string} id_destino Identificador da select de destino
     */
    var transferOptions = function (id_origem, id_destino) {
        var origem = element.querySelector(id_origem);
        var destino = element.querySelector(id_destino);

        while (origem.selectedIndex >= 0) {

            var position = origem.selectedIndex;
            var option = origem.options[position];
            var new_option = document.createElement("option");
            new_option.value = option.value;
            new_option.text = option.text;
            new_option.selected = true;

            try {
                destino.add(new_option, null);
                origem.remove(position, null);
            } catch (error) {
                destino.add(new_option);
                origem.remove(position);
            }
        }

    };

    /**
     * Adiciona evento de mover option do select origin para destiny
     */
    var doubleClickOriginOption = function () {
        var options = element.querySelectorAll('.mst-origin option');

        for (key in options) {
            try {
                options[key].addEventListener('dblclick', eventDoubleClickOriginOption);
            } catch (error) {
            }
        }
    };

    /**
     * Ações executada ao dar duplo click em um option
     * .mst-origin option
     */
    var eventDoubleClickOriginOption = function () {
        this.selected = true;
        transferOptions('.mst-origin', '.mst-destiny');
        sortSelect('.mst-destiny');

        // Adiciona novamente evento pois os options foram removidos e reordenados
        doubleClickDestinyOption();
    };

    /**
     * "Adiciona" evento de mover option do select destiny para origin
     */
    var doubleClickDestinyOption = function () {
        var options = element.querySelectorAll('.mst-destiny option');

        try {
            for (key in options) {
                options[key].addEventListener('dblclick', eventDoubleClickDestinyOption);
            }
        } catch (error) {
        }
    };

    /**
     * Ações executada ao dar duplo click em um option
     * .mst-destiny option
     */
    var eventDoubleClickDestinyOption = function () {
        this.selected = true;
        transferOptions('.mst-destiny', '.mst-origin');
        sortSelect('.mst-origin');

        // Adiciona novamente evento pois os options foram removidos e reordenados
        doubleClickOriginOption();
    };

    /**
     * Evento ao clicar remove options selecionadas de origin para destiny
     */
    var addOption = function () {
        element.querySelector('.multi-select-transfer .mst-select-add').onclick = function () {
            transferOptions('.mst-origin', '.mst-destiny');
            sortSelect('.mst-destiny');
            doubleClickDestinyOption();
        };
    };

    /**
     * Evento ao clicar remove options selecionadas em destiny para origin
     */
    var remOption = function () {
        element.querySelector('.multi-select-transfer .mst-select-rem').onclick = function () {
            transferOptions('.mst-destiny', '.mst-origin');
            sortSelect('.mst-origin');
            doubleClickOriginOption();
        };
    };

    /**
     * Evento ao clicar remove todas options origin para destiny
     */
    var addAllOption = function () {
        element.querySelector('.multi-select-transfer .mst-select-add-all').onclick = function () {
            var options = element.querySelector('.mst-origin').options;

            for (key in options) {
                options[key].selected = true;
            }

            transferOptions('.mst-origin', '.mst-destiny');
            sortSelect('.mst-destiny');
            doubleClickDestinyOption();
        };
    };

    /**
     * Remove todas as options de destiny e adiciona em origin
     */
    var remAllOption = function () {
        element.querySelector('.multi-select-transfer .mst-select-rem-all').onclick = function () {
            var options = element.querySelector('.mst-destiny').options;

            for (key in options) {
                options[key].selected = true;
            }

            transferOptions('.mst-destiny', '.mst-origin');
            sortSelect('.mst-origin');
            doubleClickOriginOption();
        };
    };

    /**
     * Realiza filtragem das inputs.
     * @param string select Seletor da caixa de seleção (select)
     * @param string input Seletor do input
     */
    var filter = function (select, input) {
        element.querySelector(input).onkeyup = function () {
            var search = this.value;

            var obj_search = new RegExp(search, 'i');
            var options = element.querySelector(select).options;

            for (key in options) {
                try {
                    if (obj_search.test(options[key]['text'])) {
                        options[key].style.display = '';
                    } else {
                        options[key].style.display = 'none';
                    }
                } catch (error) {
                }
            }

        };
    };

};