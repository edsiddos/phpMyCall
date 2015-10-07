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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Classe manipula cache dos parametros referente a solicitações
 *
 * @author Ednei Leite da Silva
 */
class Parametros_solicitacoes {

    /**
     * Método que busca todos os parametros referente a solicitações.
     * @return Array Retorna todos os parametros referente a solicitações.
     */
    public static function get_parametros() {
        $ci = &get_instance();
        $parametros = $ci->cache->apc->get(PARAMETROS);

        if (empty($parametros['VISUALIZAR_SOLICITACAO']) || empty($parametros['CORES_SOLICITACOES']) ||
                empty($parametros['DIRECIONAR_CHAMADO']) || empty($parametros['REDIRECIONAR_CHAMADO']) ||
                empty($parametros['EDITAR_SOLICITACAO']) || empty($parametros['ATENDER_SOLICITACAO']) ||
                empty($parametros['ENCERRAR_SOLICITACAO']) || empty($parametros['EXCLUIR_SOLICITACAO'])) {
            $ci->cache->apc->delete(PARAMETROS);

            unset($parametros['VISUALIZAR_SOLICITACAO']);
            unset($parametros['CORES_SOLICITACOES']);
            unset($parametros['DIRECIONAR_CHAMADO']);
            unset($parametros['REDIRECIONAR_CHAMADO']);
            unset($parametros['EDITAR_SOLICITACAO']);
            unset($parametros['ATENDER_SOLICITACAO']);
            unset($parametros['ENCERRAR_SOLICITACAO']);
            unset($parametros['EXCLUIR_SOLICITACAO']);

            $parametros['VISUALIZAR_SOLICITACAO'] = Parametros_solicitacoes::get_dados_parametros('VISUALIZAR_SOLICITACAO');
            $parametros['DIRECIONAR_CHAMADO'] = Parametros_solicitacoes::get_dados_parametros('DIRECIONAR_CHAMADO');
            $parametros['REDIRECIONAR_CHAMADO'] = Parametros_solicitacoes::get_dados_parametros('REDIRECIONAR_CHAMADO');
            $parametros['EDITAR_SOLICITACAO'] = Parametros_solicitacoes::get_dados_parametros('EDITAR_SOLICITACAO');
            $parametros['ATENDER_SOLICITACAO'] = Parametros_solicitacoes::get_dados_parametros('ATENDER_SOLICITACAO');
            $parametros['ENCERRAR_SOLICITACAO'] = Parametros_solicitacoes::get_dados_parametros('ENCERRAR_SOLICITACAO');
            $parametros['EXCLUIR_SOLICITACAO'] = Parametros_solicitacoes::get_dados_parametros('EXCLUIR_SOLICITACAO');

            $query = $ci->db->select('prioridade.nome, prioridade.cor')->from('phpmycall.prioridade')->order_by('prioridade.id')->get();
            $result = $query->result_array();

            foreach ($result as $values) {
                $parametros['CORES_SOLICITACOES'][$values['nome']] = $values['cor'];
            }

            $ci->cache->apc->save(PARAMETROS, $parametros, TTL_CACHE);
        }

        return $parametros;
    }

    /**
     * Pesquisa dados dos parametros de configuração referentes a solicitação
     * @param string $parametro Nome do parametro
     * @return Array Retorna um <b>Array</b> com os perfil.
     */
    private static function get_dados_parametros($parametro) {
        $ci = &get_instance();
        $ci->db->select('TRIM(texto) AS texto')->from('phpmycall.config');
        $perfil = $ci->db->where(array('parametro' => $parametro))->get()->row_array();
        $perfis = preg_split('/(,\s|,)/', $perfil['texto']);

        $result = $ci->db->select('perfil.perfil')->from('phpmycall.perfil')->where_in('perfil.nivel', $perfis)->get()->result_array();
        foreach ($result as $values) {
            $return[] = $values['perfil'];
        }

        return $return;
    }

}
