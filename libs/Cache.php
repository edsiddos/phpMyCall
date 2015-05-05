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
 * Implementa sistema de cache
 *
 * @author Ednei Leite da Silva
 */
class Cache {

    /**
     * Captura dados armazenados em Cache.
     * @param type $chave Conteudo buscado
     * @return Mixed Retorna variavel ou array caso sucesso.
     */
    public static function getCache($chave) {
        return apc_fetch($chave);
    }

    /**
     * Armazena dados.
     * @param string $chave Chave responsavel por armazenar dados.
     * @param Mixed $valor Valor a ser armazendo em cache.
     * @return boolean Caso dados estejam armazenados com sucesso.
     */
    public static function setCache($chave, $valor) {
        return apc_add($chave, $valor);
    }

    /**
     * Remove conteudo do cache.
     * @param string $chave Chave a ser removida
     * @return boolean <b>TRUE</b> caso sucesso, <b>FALSE</b> caso falha
     */
    public static function deleteCache($chave) {
        return apc_delete($chave);
    }

}
