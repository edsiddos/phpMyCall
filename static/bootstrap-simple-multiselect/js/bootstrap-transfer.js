
(function ($) {
    $.fn.bootstrapTransfer = function (options) {
        var settings = $.extend({}, $.fn.bootstrapTransfer.defaults, options);
        var $this;

        /* #=============================================================================== */
        /* # Expose public functions */
        /* #=============================================================================== */

        this.populate = function (input) {
            $this.populate(input);
        };

        this.set_values = function (values) {
            $this.set_values(values);
        };

        this.get_values = function () {
            return $this.get_values();
        };

        return this.each(function () {
            $this = $(this);
            /* #=============================================================================== */
            /* # Add widget markup */
            /* #=============================================================================== */
            $this.append($.fn.bootstrapTransfer.defaults.template);
            $this.addClass("bootstrap-transfer-container");

            /* #=============================================================================== */
            /* # Initialize internal variables */
            /* #=============================================================================== */
            $this.$filter_input = $this.find('.filter-input');
            $this.$remaining_select = $this.find('select.remaining');
            $this.$target_select = $this.find('select.target');
            $this.$add_btn = $this.find('.button-add');
            $this.$remove_btn = $this.find('.button-remove');
            $this.$choose_all_btn = $this.find('.button-chooseall');
            $this.$clear_all_btn = $this.find('.button-clearall');

            $this._remaining_list = [];
            $this._target_list = [];

            /* #=============================================================================== */
            /* # Apply settings */
            /* #=============================================================================== */

            /* target_name */
            if (settings.target_name != '') {
                $this.$target_select.attr({'name': settings.target_name, 'id': settings.target_name});
            }

            /* remaining_name */
            if (settings.remaining_name != '') {
                $this.$remaining_select.attr({'name': settings.remaining_name, 'id': settings.remaining_name});
            }

            /* #=============================================================================== */
            /* # Wire internal events */
            /* #=============================================================================== */

            $this.$add_btn.click(function () {
                $this.move_elems($this.$remaining_select.val(), false, true);
            });

            $this.$remove_btn.click(function () {
                $this.move_elems($this.$target_select.val(), true, false);
            });

            $this.$choose_all_btn.click(function () {
                $this.move_all(false, true);
            });

            $this.$clear_all_btn.click(function () {
                $this.move_all(true, false);
            });

            $this.delegate('select.remaining > option', 'dblclick', function () {
                $this.move_elems($this.$remaining_select.val(), false, true);
            });

            $this.delegate('select.target > option', 'dblclick', function () {
                $this.move_elems($this.$target_select.val(), true, false);
            });

            $this.$filter_input.keyup(function () {
                $this.update_lists(true);
            });
            /* #=============================================================================== */
            /* # Implement public functions */
            /* #=============================================================================== */

            $this.populate = function (input) {
                // input: [{value:_, content:_}]
                $this.$filter_input.val('');
                for (var i in input) {
                    var e = input[i];
                    $this._remaining_list.push([{value: e.value, content: e.content}, true]);
                    $this._target_list.push([{value: e.value, content: e.content}, false]);
                }
                $this.update_lists(true);
            };

            $this.set_values = function (values) {
                if (typeof values === 'undefined' || values.length === 0) {
                    $this.move_all(true, false);
                } else {
                    $this.move_elems(values, false, true);
                }
            };

            $this.get_values = function () {
                return $this.get_internal($this.$target_select);
            };

            /* #=============================================================================== */
            /* # Implement private functions */
            /* #=============================================================================== */
            $this.get_internal = function (selector) {
                var res = [];
                selector.find('option').each(function () {
                    res.push($(this).val());
                })
                return res;
            };

            $this.to_dict = function (list) {
                var res = {};
                for (var i in list)
                    res[list[i]] = true;
                return res;
            };

            $this.update_lists = function (force_hilite_off) {
                var old;
                if (!force_hilite_off) {
                    old = [$this.to_dict($this.get_internal($this.$remaining_select)),
                        $this.to_dict($this.get_internal($this.$target_select))];
                }

                $this.$remaining_select.empty();
                $this.$target_select.empty();
                var lists = [$this._remaining_list, $this._target_list];
                var source = [$this.$remaining_select, $this.$target_select];

                for (var i in lists) {
                    for (var j in lists[i]) {
                        var e = lists[i][j];
                        if (e[1]) {
                            var selected = '';
                            if (!force_hilite_off && settings.hilite_selection && !old[i].hasOwnProperty(e[0].value)) {
                                selected = 'selected="selected"';
                            }
                            source[i].append('<option ' + selected + 'value=' + e[0].value + '>' + e[0].content + '</option>');
                        }
                    }
                }

                $this.$remaining_select.find('option').each(function () {
                    var inner = $this.$filter_input.val().toLowerCase();
                    var outer = $(this).html().toLowerCase();
                    if (outer.indexOf(inner) == -1) {
                        $(this).remove();
                    }
                });
            };

            $this.move_elems = function (values, remaining, target) {
                for (var i in values) {
                    val = values[i];
                    for (var j in $this._remaining_list) {
                        var e = $this._remaining_list[j];
                        if (e[0].value == val) {
                            e[1] = remaining;
                            $this._target_list[j][1] = target;
                        }
                    }
                }
                $this.update_lists(false);
            };

            $this.move_all = function (remaining, target) {
                for (var i in $this._remaining_list) {
                    $this._remaining_list[i][1] = remaining;
                    $this._target_list[i][1] = target;
                }
                $this.update_lists(false);
            };

            $this.data('bootstrapTransfer', $this);
            return $this;
        });
    };

    $.fn.bootstrapTransfer.defaults = {
        'template': '<div class="multi-select-transfer">\
                        <div class="mst-filters">\
                            <div class="mst-filters-left">\
                                <input type="text" class="mst-filter-origin filter-input form-control" />\
                            </div>\
                            <div class="mst-filters-right">\
                            </div>\
                        </div>\
                        <div class="mst-selects">\
                            <div class="mst-left">\
                                <select multiple="multiple" name="remaining" class="filtered remaining mst-origin form-control">\
                                </select>\
                            </div>\
                            <div class="mst-right">\
                                <div class="mst-buttons">\
                                    <button type="button" class="btn btn-default mst-select-add button-add">\
                                        <span class="glyphicon glyphicon-chevron-right"></span>\
                                    </button>\
                                    <button type="button" class="btn btn-default mst-select-rem button-remove">\
                                        <span class="glyphicon glyphicon-chevron-left"></span>\
                                    </button>\
                                    <button type="button" class="btn btn-default mst-select-add-all button-chooseall">\
                                        <span class="glyphicon glyphicon-chevron-right"></span>\
                                        <span class="glyphicon glyphicon-chevron-right"></span>\
                                    </button>\
                                    <button type="button" class="btn btn-default mst-select-rem-all button-clearall">\
                                        <span class="glyphicon glyphicon-chevron-left"></span>\
                                        <span class="glyphicon glyphicon-chevron-left"></span>\
                                    </button>\
                                </div>\
                                <div class="mst-select-destiny">\
                                    <select multiple="multiple" name="target" class="filtered target mst-destiny form-control">\
                                    </select>\
                                </div>\
                            </div>\
                        </div>\
                    </div>',
        hilite_selection: true,
        remaining_name: '',
        target_name: ''
    };
})(jQuery);