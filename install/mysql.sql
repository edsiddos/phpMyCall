CREATE DATABASE openmycall;

USE openmycall;

CREATE TABLE openmycall.projeto(
	id SMALLINT AUTO_INCREMENT,
	nome VARCHAR(100) NOT NULL UNIQUE,
	descricao VARCHAR(500) DEFAULT NULL,
	CONSTRAINT pk_problema PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE openmycall.tipo_problema(
	id SMALLINT AUTO_INCREMENT,
	nome VARCHAR(100) NOT NULL UNIQUE,
	CONSTRAINT pk_tipo_problema PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE openmycall.projeto_tipo_problema(
	id SMALLINT AUTO_INCREMENT,
	projeto SMALLINT NOT NULL,
	problema SMALLINT NOT NULL,
	resposta VARCHAR(7) DEFAULT NULL, -- tempo para resposta
	solucao VARCHAR(7) DEFAULT NULL, -- tempo para solução
	descricao VARCHAR(1000) DEFAULT NULL, -- informação geral do tipo de problema
	CONSTRAINT pk_projeto_tipo_problema PRIMARY KEY (id),
	FOREIGN KEY fk_projeto_projeto_tipo_problema(projeto) REFERENCES openmycall.projeto(id),
	FOREIGN KEY fk_problema_projeto_tipo_problema(problema) REFERENCES openmycall.tipo_problema(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE openmycall.opcoes_menu(
	id SMALLINT AUTO_INCREMENT,
	nome VARCHAR(100) NOT NULL, -- Nome a ser mostrado ao usuário
	link VARCHAR(255),
	interno BOOLEAN NOT NULL DEFAULT TRUE, -- o link será interno ou externo
	funcionalidade BOOLEAN NOT NULL DEFAULT TRUE, -- se é uma funcinalidade ou menu
	menu_pai SMALLINT DEFAULT NULL, -- id do menu pai
	CONSTRAINT pk_opcoes_menu PRIMARY KEY (id),
	FOREIGN KEY fk_menu_pai_opcoes_menu(menu_pai) REFERENCES openmycall.opcoes_menu(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO openmycall.opcoes_menu (id, nome, link, interno, funcionalidade, menu_pai) VALUES
(1, 'Solicitação', '', TRUE, FALSE, NULL),
(2, 'Lista de solicitações', 'solicitacao/lista', TRUE, TRUE, 1),
(3, 'Abrir Solicitação', 'solicitacao/abrir', TRUE, TRUE, 1),
(4, 'Administração', '', TRUE, FALSE, NULL),
(5, 'Expediente', 'horarios/alterar_expediente', TRUE, TRUE, 4),
(6, 'Feriados', 'horarios/manter_feriados', TRUE, TRUE, 4),
(7, 'Usuários', 'usuarios/index', TRUE, TRUE, 4),
(8, 'Tipos de Feedback', 'feedback/index', TRUE, TRUE, 4),
(9, 'Projetos e Problemas', 'projetos_problemas/index', TRUE, TRUE, 4),
(10, 'Empresas', 'empresas/index', TRUE, TRUE, 4),
(11, 'Alterar Senha', 'usuarios/alterar_senha', TRUE, TRUE, 4),
(12, 'Configurações', 'administracao/index', TRUE, TRUE, 4);


CREATE TABLE openmycall.perfil(
	id SMALLINT AUTO_INCREMENT,
	perfil VARCHAR(25) NOT NULL UNIQUE,
        nivel SMALLINT UNIQUE,
	CONSTRAINT pk_perfil PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO openmycall.perfil (id, perfil, nivel) VALUES (1, 'Cliente', 1),
(2, 'Atendente', 2),
(3, 'Técnico', 3),
(4, 'Gerente', 4),
(5, 'Administrador de Sistema', 5);

CREATE TABLE openmycall.permissao_perfil(
	id SMALLINT AUTO_INCREMENT,
	menu SMALLINT NOT NULL,
	perfil SMALLINT NOT NULL,
	CONSTRAINT pk_permissao_perfil PRIMARY KEY (id),
	FOREIGN KEY fk_menu_permissao_perfil(menu) REFERENCES openmycall.opcoes_menu (id),
	FOREIGN KEY fk_perfil_permissao_perfil(perfil) REFERENCES openmycall.perfil (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Inicio visualização de solicitações
INSERT INTO openmycall.permissao_perfil (menu, perfil) VALUES (2, 1), (2, 2), (2, 3), (2, 4), (2, 5);
-- Final visualização de solicitação

-- Inicio abertura de solicitação
INSERT INTO openmycall.permissao_perfil (menu, perfil) VALUES (3, 2), (3, 3), (3, 4), (3, 5);
-- Final abertura de solicitação

INSERT INTO openmycall.permissao_perfil (menu, perfil) VALUES (5, 5), (6, 5); -- feriados

-- Inicio manter usuário
INSERT INTO openmycall.permissao_perfil (menu, perfil) VALUES (7, 4), (7, 5);

-- Inicio manter tipos de feedback
INSERT INTO openmycall.permissao_perfil (menu, perfil) VALUES (8, 4), (8, 5);
-- Final manter tipos de feedback

-- Inicio manter projetos e problemas
INSERT INTO openmycall.permissao_perfil (menu, perfil) VALUES (9, 4), (9, 5);
-- Final manter projetos e problemas

-- Inicio manter Empresas
INSERT INTO openmycall.permissao_perfil (menu, perfil) VALUES (10, 4), (10, 5); -- Cadastrar / Alterar / Excluir
-- Final manter Empresas

-- Inicio alterar Senha
INSERT INTO openmycall.permissao_perfil (menu, perfil) VALUES (11, 1), (11, 2), (11, 3), (11, 4), (11, 5);
-- Final alterar Senha

-- Inicio editar configuração
INSERT INTO openmycall.permissao_perfil (menu, perfil) VALUES (12, 5);
-- Final editar 

--
-- Tabela de empresas
--

CREATE TABLE openmycall.empresas(
	id SMALLINT AUTO_INCREMENT,
	empresa VARCHAR(100) NOT NULL UNIQUE,
	endereco VARCHAR(100),
	telefone_fixo VARCHAR(15) NOT NULL UNIQUE,
	telefone_celular VARCHAR(15),
	CONSTRAINT pk_empresas PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tabela de usuários
--

CREATE TABLE openmycall.usuario(
	id SMALLINT AUTO_INCREMENT,
	usuario VARCHAR(15) NOT NULL UNIQUE,
	senha VARCHAR(50) NOT NULL,
	nome VARCHAR(80) NOT NULL,
	email VARCHAR(150) NOT NULL UNIQUE,
	perfil SMALLINT NOT NULL,
	telefone VARCHAR(15),
	empresa SMALLINT,
	dt_troca DATETIME NOT NULL,
	CONSTRAINT pk_usuario PRIMARY KEY (id),
	FOREIGN KEY fk_perfil_usuario(perfil) REFERENCES openmycall.perfil (id),
	FOREIGN KEY fk_empresa_usuario(empresa) REFERENCES openmycall.empresas (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- usuario: admin, senha: admin
INSERT INTO openmycall.usuario (usuario, senha, nome, email, perfil, dt_troca) VALUES ('admin', '90b9aa7e25f80cf4f64e990b78a9fc5ebd6cecad', 'Administrador', 'admin@admin.com', 5, '2025-12-01 00:00');


CREATE TABLE openmycall.prioridade(
        id SMALLINT AUTO_INCREMENT,
        nome VARCHAR(15) NOT NULL UNIQUE,
        nivel SMALLINT NOT NULL UNIQUE,
        padrao BOOLEAN DEFAULT FALSE,
        cor CHAR(7) NOT NULL DEFAULT '#FFFFFF',
        CONSTRAINT pk_prioridade PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO openmycall.prioridade (nome, nivel, cor) VALUES ('Urgente', 1, '#FF8B8B');
INSERT INTO openmycall.prioridade (nome, nivel, cor) VALUES ('Alta', 2, '#FCD56A');
INSERT INTO openmycall.prioridade (nome, nivel, padrao, cor) VALUES ('Normal', 3, TRUE, '#FFFFFF');
INSERT INTO openmycall.prioridade (nome, nivel, cor) VALUES ('Baixa', 4, '#A4FCEF');
INSERT INTO openmycall.prioridade (nome, nivel, cor) VALUES ('Mínima', 5, '#A4FCC4');


CREATE TABLE openmycall.solicitacao(
	id INT AUTO_INCREMENT,
	projeto_problema SMALLINT NOT NULL,
	descricao TEXT NOT NULL,
	solicitante SMALLINT NOT NULL,
	prioridade SMALLINT NOT NULL,
	atendente SMALLINT NOT NULL,
	tecnico SMALLINT,
	abertura DATETIME NOT NULL,
	atendimento DATETIME NOT NULL,
	encerramento DATETIME NOT NULL,
	solicitacao_origem INT DEFAULT NULL,
	avaliacao SMALLINT DEFAULT NULL,
	justificativa_avaliacao VARCHAR(255),
        resolucao TEXT DEFAULT NULL,
	CONSTRAINT pk_solicitacao PRIMARY KEY (id),
	FOREIGN KEY fk_projeto_problema_solicitacao(projeto_problema) REFERENCES openmycall.projeto_tipo_problema (id),
        FOREIGN KEY fk_prioridade_solicitacao(prioridade) REFERENCES openmycall.prioridade (id),
	FOREIGN KEY fk_solicitante_solicitacao(solicitante) REFERENCES openmycall.usuario(id),
	FOREIGN KEY fk_atendente_solicitacao(atendente) REFERENCES openmycall.usuario(id),
	FOREIGN KEY fk_tecnico_solicitacao(tecnico) REFERENCES openmycall.usuario(id),
	FOREIGN KEY fk_solicitacao_origem_solicitacao(solicitacao_origem) REFERENCES openmycall.solicitacao (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE openmycall.arquivos(
	id INT AUTO_INCREMENT,
	nome VARCHAR(100) NOT NULL,
	solicitacao INT NOT NULL,
        tipo VARCHAR(50) NOT NULL,
	caminho VARCHAR(250) NOT NULL UNIQUE,
	CONSTRAINT pk_arquivos PRIMARY KEY (id),
	FOREIGN KEY fk_solicitacao_arquivos(solicitacao) REFERENCES openmycall.solicitacao (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE openmycall.tipo_feedback(
	id SMALLINT AUTO_INCREMENT,
	nome VARCHAR(50) UNIQUE NOT NULL,
	abreviatura VARCHAR(10) UNIQUE NOT NULL,
	descontar BOOLEAN DEFAULT TRUE,
	descricao VARCHAR(250),
	CONSTRAINT pk_tipo_feedback PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE openmycall.feedback(
	id INT AUTO_INCREMENT,
	tipo_feedback SMALLINT NOT NULL,
	pergunta TEXT NOT NULL,
	resposta TEXT,
	inicio DATETIME NOT NULL,
	fim DATETIME NOT NULL,
	solicitacao INT NOT NULL,
	responsavel SMALLINT DEFAULT NULL,
	CONSTRAINT pk_feedback PRIMARY KEY (id),
	FOREIGN KEY fk_tipo_feedback_feedback(tipo_feedback) REFERENCES openmycall.tipo_feedback (id),
	FOREIGN KEY fk_solicitacao_feedback(solicitacao) REFERENCES openmycall.solicitacao (id),
	FOREIGN KEY fk_responsavel_feedback(responsavel) REFERENCES openmycall.usuario (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE openmycall.feriado(
	id SMALLINT AUTO_INCREMENT,
	dia DATE NOT NULL UNIQUE,
	nome VARCHAR(50) NOT NULL,
	CONSTRAINT pk_feriado PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE openmycall.expediente(
	id SMALLINT UNIQUE NOT NULL,
	dia_semana VARCHAR(15) NOT NULL UNIQUE,
	entrada_manha TIME DEFAULT '7:00:00',
	saida_manha TIME DEFAULT '11:00:00',
	entrada_tarde TIME DEFAULT '13:00:00',
	saida_tarde TIME DEFAULT '17:00:00',
	CONSTRAINT pk_expediente PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO openmycall.expediente (id, dia_semana) VALUES(1, 'Domingo');
INSERT INTO openmycall.expediente (id, dia_semana) VALUES(2, 'Segunda-Feira');
INSERT INTO openmycall.expediente (id, dia_semana) VALUES(3, 'Terça-Feira');
INSERT INTO openmycall.expediente (id, dia_semana) VALUES(4, 'Quarta-Feira');
INSERT INTO openmycall.expediente (id, dia_semana) VALUES(5, 'Quinta-Feira');
INSERT INTO openmycall.expediente (id, dia_semana) VALUES(6, 'Sexta-Feira');
INSERT INTO openmycall.expediente (id, dia_semana) VALUES(7, 'Sábado');

CREATE TABLE openmycall.projeto_responsaveis(
	id SMALLINT AUTO_INCREMENT,
	usuario SMALLINT NOT NULL,
	projeto SMALLINT NOT NULL,
	CONSTRAINT pk_projeto_responsaveis PRIMARY KEY(id),
	FOREIGN KEY fk_usuario_projeto_responsaveis(usuario) REFERENCES openmycall.usuario(id),
	FOREIGN KEY fk_projeto_projeto_responsaveis(projeto) REFERENCES openmycall.projeto(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE openmycall.log(
	id BIGINT AUTO_INCREMENT,
	ip VARCHAR(15) NOT NULL,
	data_hora DATETIME NOT NULL,
	dados TEXT NOT NULL,
	usuario SMALLINT NOT NULL,
	CONSTRAINT pk_log PRIMARY KEY(id),
	FOREIGN KEY fk_usuario_log(usuario) REFERENCES openmycall.usuario(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE openmycall.config(
        parametro VARCHAR(30) NOT NULL,
        texto TEXT NOT NULL,
        comentario TEXT NOT NULL,
        CONSTRAINT pk_parametro_config PRIMARY KEY (parametro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('VALIDADE_SENHA_DIAS', '30', 'Periodo de validade da senha.');
INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('UPLOAD_FILE', '10MB', 'Tamanho máximo de upload.');
INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('VISUALIZAR_SOLICITACAO', '2, 3, 4, 5', 'Permite ao perfil visualizar todas as solicitações independente se tenha aberto, atendido ou seja o técnico responsavel pela resolução');
INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('DIRECIONAR_CHAMADO', '3, 4, 5', 'Permite ao atendente do chamado direcionar um chamado a um técnico');
INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('REDIRECIONAR_CHAMADO', '3, 4, 5', 'Permite a um determinado perfil redirecionar um chamado a um técnico');
INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('EDITAR_SOLICITACAO', '2, 3, 4, 5', 'Permite a edição de um solicitação em aberto');
INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('ATENDER_SOLICITACAO', '3, 4, 5', 'Permitir atender solicitação em aberto.');
INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('EXCLUIR_SOLICITACAO', '4, 5', 'Permitir a excluir solicitação em aberto ou atendimento.');
INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('ENCERRAR_SOLICITACAO', '3, 4, 5', 'Permitir o encerramento de um solicitação que esteja em atendimento.');
