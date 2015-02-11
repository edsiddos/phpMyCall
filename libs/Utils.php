<?php

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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace libs;

/**
 * Implenta métodos não categorizados
 *
 * @author Ednei Leite da Silva
 */
final class Utils {
    
    /**
     * Verica a existencia de um valor dentro de um array.
     * @param mixed $values Valor a ser procurado.
     * @param Array $array Array onde será buscado o valor desejado
     * @return boolean Retorna TRUE se valor for encontrado.
     */
    public static function exist_value_array($values, $array){
        return (!is_bool(array_search($values, $array)));
    }
}