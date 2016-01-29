<?php

/*
 * Copyright (C) 2015 - 2016, Ednei Leite da Silva
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

/**
 * Implenta métodos não categorizados
 *
 * @author Ednei Leite da Silva
 */
final class Utils {

    /**
     * Verica a existencia de um valor dentro de um array.
     *
     * @param mixed $values Valor a ser procurado.
     * @param Array $array Array onde será buscado o valor desejado
     * @return boolean Retorna <b>TRUE</b> se valor for encontrado.
     */
    public static function exist_value_array($values, $array) {
        return (!is_bool(array_search($values, $array)));
    }

    /**
     * Valida string no formato horas minutos.
     * @param type $str String no formata de horas com 4 digitos e minutos
     * @return mixed <b>string</b> no formato da hora se sucesso, <b>NULL</b> em caso de falha
     */
    public static function valida_formato_hora($str) {
        if (preg_match('/^[0-9]{1,4}:[0-5][0-9]$/', $str)) {
            return $str;
        } else {
            return NULL;
        }
    }

    /**
     * Valida valor e se possivel retorna numero inteiro
     * @param mixed $int Valor a ser verificado e retornado
     * @return mixed Retorna um numero inteiro se sucesso, NULL em caso de falha
     */
    public static function valida_inteiro_chave($int) {
        if (empty($int) || $int <= 0) {
            return NULL;
        } else {
            return intval($int);
        }
    }

}
