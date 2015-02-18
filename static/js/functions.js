/*
 * Copyright (C) 2015 - Ednei Leite da Silva
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
$(function() {

	/**
	 * Plugin que insere mascará de hora
	 */
	$.fn.mascaraHora = function($value) {
		valor = $value;
		valor = valor.replace(":", "");
		tamanho = valor.length;

		if (tamanho > 2) {
			minutos = valor.substring((tamanho - 2), (tamanho));
			hora = valor.substring(0, (tamanho - 2));

			return hora + ':' + minutos;
		} else {
			return valor;
		}
	}

	/**
	 * Verifica se o valor da hora entre 0:00 e 838:00
	 */
	$.fn.validaHoraMaxima = function($value) {
		patt = new RegExp("^([0-9]{1,2}|[1-7][0-9]{2}|8([0-2][0-9]|3[0-8])):([0-5][0-9])$");
		return patt.test($value);
	}

});