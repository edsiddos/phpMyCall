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

namespace application\models;

/**
 * Manipula cadastro de usuários
 *
 * @author Ednei Leite da Silva
 */
class Usuarios extends \system\Model {

    /**
     * Obtem os perfils que o usuário novo podera ter a partir
     * do usuário que esta criando (um gerente não poderá criar outro gerente)
     *
     * @param string $nome Nome do perfil.
     * @return Array Array com os perfils disponiveis.
     */
    public function getPerfil($nome) {
        $sql = "SELECT * FROM phpmycall.perfil
		WHERE id < (SELECT id FROM phpmycall.perfil WHERE perfil = :nome)";

        return $this->select($sql, array('nome' => $nome));
    }

    /**
     * Grava novo usuário.
     *
     * @param Array $dados Array com os dados necessários para criação de novo usuário.
     * @return boolean TRUE se inserido.
     */
    public function inserirUsuario($dados) {
        return $this->insert('phpmycall.usuario', $dados);
    }

    /**
     * Busca todos os usuários que tem perfil com menor permissão.
     * 
     * @param string $perfil Perfil do usuário.
     * @return Array Retorna Array relação de usuários.
     */
    public function getUsuarios($perfil) {
        $sql = "SELECT usuario.id,
                    usuario.usuario,
                    usuario.nome,
                    usuario.email,
                    perfil.perfil,
                    usuario.telefone
                FROM phpmycall.usuario
                INNER JOIN phpmycall.perfil ON usuario.perfil = perfil.id
                WHERE usuario.perfil < (
                    SELECT perfil.id
                    FROM phpmycall.perfil
                    WHERE perfil = :perfil
                )
                ORDER BY perfil.id DESC, usuario.nome";

        return $this->select($sql, array('perfil' => $perfil));
    }

    /**
     * Verifica se usuário existe
     *
     * @param string $user Usuário
     * @return Array
     */
    public function validaUsuario($user, $id) {
        $sql = "SELECT EXISTS(SELECT * FROM phpmycall.usuario WHERE usuario = :user AND id <> :id) AS exist";

        return $this->select($sql, array('user' => $user, 'id' => $id), false);
    }

    /**
     * Verifica se email existe
     *
     * @param stirng $email
     * @return Array
     */
    public function getEmail($email, $id) {
        $sql = "SELECT EXISTS(SELECT * FROM phpmycall.usuario WHERE email = :email AND id <> :id) AS exist";

        return $this->select($sql, array('email' => $email, 'id' => $id), false);
    }

    /**
     * Busca dados do usuário a partir do ID
     *
     * @param int $id ID do usuário
     * @return Array Retorna array com os dados do usuário
     */
    public function getDadosUsuarios($id) {
        $sql = "SELECT id, usuario, nome, email, telefone, perfil, empresa
                 FROM phpmycall.usuario WHERE id = :id";

        return $this->select($sql, array('id' => $id), false);
    }

    /**
     * Atualiza dados dos usuários (Alterar).
     *
     * @param Array $dados Array com os dados a ser alterado.
     * @param int $id Id do usuário.
     * @return boolean True alteração com sucesso.
     */
    public function atualizaUsuario($dados, $id) {
        return $this->update('phpmycall.usuario', $dados, "id = {$id}");
    }

    /**
     * Realiza a exclusão de usuários.
     *
     * @param int $id Id do usuário.
     * @param string $perfil Perfil do usuário solicitante de exclusão.
     * @return boolean True se excluido com sucesso, False se falha.
     */
    public function excluirUsuario($id, $perfil) {
        $del_usuario = "id = {$id} AND perfil < (SELECT id FROM phpmycall.perfil WHERE perfil = '{$perfil}')";

        $del_projeto = " usuario = (SELECT usuario.id FROM phpmycall.usuario";
        $del_projeto .= " INNER JOIN phpmycall.perfil ON usuario.perfil < perfil.id";
        $del_projeto .= " WHERE usuario.id = {$id} AND perfil.perfil = '{$perfil}')";

        return ($this->delete('phpmycall.projeto_responsaveis', $del_projeto) && $this->delete('phpmycall.usuario', $del_usuario));
    }

    /**
     * Busca todos os projetos existentes
     * e verifica se o usuário é participante
     *
     * @return Array
     */
    public function relacaoProjetos($id_usuario) {
        $sql = "SELECT projeto.id AS value,
                    nome AS name
                FROM phpmycall.projeto
                INNER JOIN phpmycall.projeto_responsaveis ON projeto.id = projeto_responsaveis.projeto
                WHERE usuario = :usuario";
        $dados['participa'] = $this->select($sql, array('usuario' => $id_usuario));

        $participa = array();
        foreach ($dados['participa'] AS $values) {
            $participa[] = $values['value'];
        }

        $participa = implode(',', $participa);

        $sql = "SELECT id AS value, nome AS name FROM phpmycall.projeto ";
        $sql .= empty($participa) ? '' : "WHERE id NOT IN ({$participa})";

        $dados['projeto'] = $this->select($sql);

        return $dados;
    }

    /**
     * Relaciona usuário com projetos.
     *
     * @param string $usuario login do usuário.
     * @param array $projetos Array com os códigos do projetos.
     * @return Array Retorna dois <b>arrays</b> relação de projetos inseridos e excluidos.
     */
    public function ligaUsuarioProjeto($usuario, $projetos) {
        $sql = "SELECT id FROM phpmycall.usuario WHERE usuario = :usuario";

        $id = $this->select($sql, array('usuario' => $usuario), false);

        /*
         * Get projetos
         */
        $sql = "SELECT projeto.id
                FROM phpmycall.projeto
                INNER JOIN phpmycall.projeto_responsaveis ON projeto.id = projeto_responsaveis.projeto
                INNER JOIN phpmycall.usuario ON projeto_responsaveis.usuario = usuario.id
                WHERE usuario.usuario = :usuario";

        $projeto_participante = $this->select($sql, array('usuario' => $usuario));

        $delete = array();
        $insert = is_array($projetos) ? $projetos : array();

        foreach ($projeto_participante as $values) {
            if (!in_array($values ['id'], $insert)) {
                $this->delete('phpmycall.projeto_responsaveis', "projeto = {$values['id']} AND usuario = {$id['id']}");
                $delete [] = $values ['id'];
            }

            $key = array_search($values ['id'], $insert);
            if ($key !== false) {
                unset($insert [$key]);
            }
        }

        foreach ($insert as $values) {
            $this->insert('phpmycall.projeto_responsaveis', array('usuario' => $id ['id'], 'projeto' => $values));
        }

        $return = array(
            'delete' => implode(',', $delete),
            'insert' => implode(',', $insert)
        );

        return $return;
    }

    /**
     * Busca empresas cadastradas.
     * @return Array Retorna empresas cadastradas.
     */
    public function getEmpresas() {
        $sql = "SELECT empresas.id, empresas.empresa FROM phpmycall.empresas";

        return $this->select($sql);
    }

}
