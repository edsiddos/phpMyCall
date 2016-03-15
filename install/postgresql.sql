CREATE SCHEMA openmycall;

CREATE TABLE openmycall.projeto(
	id SMALLSERIAL,
	nome VARCHAR(100) NOT NULL UNIQUE,
	descricao VARCHAR(500) DEFAULT NULL,
	CONSTRAINT pk_problema PRIMARY KEY(id)
);

CREATE TABLE openmycall.tipo_problema(
	id SMALLSERIAL,
	nome VARCHAR(100) NOT NULL UNIQUE,
	CONSTRAINT pk_tipo_problema PRIMARY KEY(id)
);

CREATE TABLE openmycall.projeto_tipo_problema(
	id SMALLSERIAL,
	projeto INTEGER NOT NULL,
	problema INTEGER NOT NULL,
	resposta VARCHAR(7) DEFAULT NULL, -- tempo para resposta
	solucao VARCHAR(7) DEFAULT NULL, -- tempo para solução
	descricao VARCHAR(1000) DEFAULT NULL, -- informação geral do tipo de problema
	CONSTRAINT pk_projeto_tipo_problema PRIMARY KEY (id),
	CONSTRAINT fk_projeto_projeto_tipo_problema FOREIGN KEY (projeto) REFERENCES openmycall.projeto(id),
	CONSTRAINT fk_problema_projeto_tipo_problema FOREIGN KEY (problema) REFERENCES openmycall.tipo_problema(id)
);

CREATE TABLE openmycall.opcoes_menu(
	id SMALLSERIAL,
	nome VARCHAR(100) NOT NULL, -- Nome a ser mostrado ao usuário
	link VARCHAR(255),
	interno BOOLEAN NOT NULL DEFAULT TRUE, -- o link será interno ou externo
	funcionalidade BOOLEAN NOT NULL DEFAULT TRUE, -- se é uma funcinalidade ou menu
	menu_pai INTEGER DEFAULT NULL, -- id do menu pai
	CONSTRAINT pk_opcoes_menu PRIMARY KEY (id),
	CONSTRAINT fk_menu_pai_opcoes_menu FOREIGN KEY (menu_pai) REFERENCES openmycall.opcoes_menu(id)
);

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

SELECT SETVAL('openmycall.opcoes_menu_id_seq', 13, TRUE);


CREATE TABLE openmycall.perfil(
	id SMALLSERIAL,
	perfil VARCHAR(25) NOT NULL UNIQUE,
        nivel SMALLINT UNIQUE,
	CONSTRAINT pk_perfil PRIMARY KEY (id)
);

INSERT INTO openmycall.perfil (id, perfil, nivel) VALUES (1, 'Cliente', 1),
(2, 'Atendente', 2),
(3, 'Técnico', 3),
(4, 'Gerente', 4),
(5, 'Administrador de Sistema', 5);

SELECT SETVAL('openmycall.perfil_id_seq', 6, TRUE);

CREATE TABLE openmycall.permissao_perfil(
	id SMALLSERIAL,
	menu INTEGER NOT NULL,
	perfil INTEGER NOT NULL,
	CONSTRAINT pk_permissao_perfil PRIMARY KEY (id),
	CONSTRAINT fk_menu_permissao_perfil FOREIGN KEY (menu) REFERENCES openmycall.opcoes_menu (id),
	CONSTRAINT fk_perfil_permissao_perfil FOREIGN KEY (perfil) REFERENCES openmycall.perfil (id)
);


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
	id SMALLSERIAL,
	empresa VARCHAR(100) NOT NULL UNIQUE,
	endereco VARCHAR(100),
	telefone_fixo VARCHAR(15) NOT NULL UNIQUE,
	telefone_celular VARCHAR(15),
	CONSTRAINT pk_empresas PRIMARY KEY (id)
);

--
-- Tabela de usuários
--

CREATE TABLE openmycall.usuario(
	id SMALLSERIAL,
	usuario VARCHAR(15) NOT NULL UNIQUE,
	senha VARCHAR(50) NOT NULL,
	nome VARCHAR(80) NOT NULL,
	email VARCHAR(150) NOT NULL UNIQUE,
	perfil INTEGER NOT NULL,
	telefone VARCHAR(15),
	empresa INTEGER,
	dt_troca TIMESTAMP NOT NULL,
	CONSTRAINT pk_usuario PRIMARY KEY (id),
	CONSTRAINT fk_perfil_usuario FOREIGN KEY (perfil) REFERENCES openmycall.perfil (id),
	CONSTRAINT fk_empresa_usuario FOREIGN KEY (empresa) REFERENCES openmycall.empresas (id)
);

-- usuario: admin, senha: admin
INSERT INTO openmycall.usuario (usuario, senha, nome, email, perfil, dt_troca) VALUES ('admin', '90b9aa7e25f80cf4f64e990b78a9fc5ebd6cecad', 'Administrador', 'admin@admin.com', 5, '2025-12-01 00:00');


CREATE TABLE openmycall.prioridade(
        id SMALLSERIAL,
        nome VARCHAR(15) NOT NULL UNIQUE,
        nivel INTEGER NOT NULL UNIQUE,
        padrao BOOLEAN DEFAULT FALSE,
        cor CHAR(7) NOT NULL DEFAULT '#FFFFFF',
        CONSTRAINT pk_prioridade PRIMARY KEY(id)
);

INSERT INTO openmycall.prioridade (nome, nivel, cor) VALUES ('Urgente', 1, '#FF8B8B');
INSERT INTO openmycall.prioridade (nome, nivel, cor) VALUES ('Alta', 2, '#FCD56A');
INSERT INTO openmycall.prioridade (nome, nivel, padrao, cor) VALUES ('Normal', 3, TRUE, '#FFFFFF');
INSERT INTO openmycall.prioridade (nome, nivel, cor) VALUES ('Baixa', 4, '#A4FCEF');
INSERT INTO openmycall.prioridade (nome, nivel, cor) VALUES ('Mínima', 5, '#A4FCC4');


CREATE TABLE openmycall.solicitacao(
	id SERIAL,
	projeto_problema INTEGER NOT NULL,
	descricao TEXT NOT NULL,
	solicitante INTEGER NOT NULL,
	prioridade INTEGER NOT NULL,
	atendente INTEGER NOT NULL,
	tecnico INTEGER,
	abertura TIMESTAMP NOT NULL,
	atendimento TIMESTAMP NOT NULL,
	encerramento TIMESTAMP NOT NULL,
	solicitacao_origem INTEGER DEFAULT NULL,
	avaliacao INTEGER DEFAULT NULL,
	justificativa_avaliacao VARCHAR(255),
        resolucao TEXT DEFAULT NULL,
	CONSTRAINT pk_solicitacao PRIMARY KEY (id),
	CONSTRAINT fk_projeto_problema_solicitacao FOREIGN KEY (projeto_problema) REFERENCES openmycall.projeto_tipo_problema (id),
    CONSTRAINT fk_prioridade_solicitacao FOREIGN KEY (prioridade) REFERENCES openmycall.prioridade (id),
	CONSTRAINT fk_solicitante_solicitacao FOREIGN KEY (solicitante) REFERENCES openmycall.usuario(id),
	CONSTRAINT fk_atendente_solicitacao FOREIGN KEY (atendente) REFERENCES openmycall.usuario(id),
	CONSTRAINT fk_tecnico_solicitacao FOREIGN KEY (tecnico) REFERENCES openmycall.usuario(id),
	CONSTRAINT fk_solicitacao_origem_solicitacao FOREIGN KEY (solicitacao_origem) REFERENCES openmycall.solicitacao (id)
);

CREATE TABLE openmycall.arquivos(
	id SERIAL,
	nome VARCHAR(100) NOT NULL,
	solicitacao INTEGER NOT NULL,
        tipo VARCHAR(50) NOT NULL,
	caminho VARCHAR(250) NOT NULL UNIQUE,
	CONSTRAINT pk_arquivos PRIMARY KEY (id),
	CONSTRAINT fk_solicitacao_arquivos FOREIGN KEY (solicitacao) REFERENCES openmycall.solicitacao (id)
);

CREATE TABLE openmycall.tipo_feedback(
	id SMALLSERIAL,
	nome VARCHAR(50) UNIQUE NOT NULL,
	abreviatura VARCHAR(10) UNIQUE NOT NULL,
	descontar BOOLEAN DEFAULT TRUE,
	descricao VARCHAR(250),
	CONSTRAINT pk_tipo_feedback PRIMARY KEY (id)
);

CREATE TABLE openmycall.feedback(
	id SERIAL,
	tipo_feedback INTEGER NOT NULL,
	pergunta TEXT NOT NULL,
	resposta TEXT,
	inicio TIMESTAMP NOT NULL,
	fim TIMESTAMP NOT NULL,
	solicitacao INTEGER NOT NULL,
	responsavel INTEGER DEFAULT NULL,
	CONSTRAINT pk_feedback PRIMARY KEY (id),
	CONSTRAINT fk_tipo_feedback_feedback FOREIGN KEY (tipo_feedback) REFERENCES openmycall.tipo_feedback (id),
	CONSTRAINT fk_solicitacao_feedback FOREIGN KEY (solicitacao) REFERENCES openmycall.solicitacao (id),
	CONSTRAINT fk_responsavel_feedback FOREIGN KEY (responsavel) REFERENCES openmycall.usuario (id)
);

CREATE TABLE openmycall.feriado(
	id SMALLSERIAL,
	dia DATE NOT NULL UNIQUE,
	nome VARCHAR(50) NOT NULL,
	CONSTRAINT pk_feriado PRIMARY KEY (id)
);

CREATE TABLE openmycall.expediente(
	id SMALLINT UNIQUE NOT NULL,
	dia_semana VARCHAR(15) NOT NULL UNIQUE,
	entrada_manha TIME DEFAULT '7:00:00',
	saida_manha TIME DEFAULT '11:00:00',
	entrada_tarde TIME DEFAULT '13:00:00',
	saida_tarde TIME DEFAULT '17:00:00',
	CONSTRAINT pk_expediente PRIMARY KEY (id)
);

INSERT INTO openmycall.expediente (id, dia_semana) VALUES(1, 'Domingo');
INSERT INTO openmycall.expediente (id, dia_semana) VALUES(2, 'Segunda-Feira');
INSERT INTO openmycall.expediente (id, dia_semana) VALUES(3, 'Terça-Feira');
INSERT INTO openmycall.expediente (id, dia_semana) VALUES(4, 'Quarta-Feira');
INSERT INTO openmycall.expediente (id, dia_semana) VALUES(5, 'Quinta-Feira');
INSERT INTO openmycall.expediente (id, dia_semana) VALUES(6, 'Sexta-Feira');
INSERT INTO openmycall.expediente (id, dia_semana) VALUES(7, 'Sábado');

CREATE TABLE openmycall.projeto_responsaveis(
	id SMALLSERIAL,
	usuario INTEGER NOT NULL,
	projeto INTEGER NOT NULL,
	CONSTRAINT pk_projeto_responsaveis PRIMARY KEY(id),
	CONSTRAINT fk_usuario_projeto_responsaveis FOREIGN KEY (usuario) REFERENCES openmycall.usuario(id),
	CONSTRAINT fk_projeto_projeto_responsaveis FOREIGN KEY (projeto) REFERENCES openmycall.projeto(id)
);

CREATE TABLE openmycall.log(
	id BIGSERIAL,
	ip VARCHAR(15) NOT NULL,
	data_hora TIMESTAMP NOT NULL,
	dados TEXT NOT NULL,
	usuario INTEGER NOT NULL,
	CONSTRAINT pk_log PRIMARY KEY(id),
	CONSTRAINT fk_usuario_log FOREIGN KEY (usuario) REFERENCES openmycall.usuario(id)
);


CREATE TABLE openmycall.config(
        parametro VARCHAR(30) NOT NULL,
        texto TEXT NOT NULL,
        comentario TEXT NOT NULL,
        CONSTRAINT pk_parametro_config PRIMARY KEY (parametro)
);

INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('VALIDADE_SENHA_DIAS', '30', 'Periodo de validade da senha.');
INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('UPLOAD_FILE', '10MB', 'Tamanho máximo de upload.');
INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('VISUALIZAR_SOLICITACAO', '2, 3, 4, 5', 'Permite ao perfil visualizar todas as solicitações independente se tenha aberto, atendido ou seja o técnico responsavel pela resolução');
INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('DIRECIONAR_CHAMADO', '3, 4, 5', 'Permite ao atendente do chamado direcionar um chamado a um técnico');
INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('REDIRECIONAR_CHAMADO', '3, 4, 5', 'Permite a um determinado perfil redirecionar um chamado a um técnico');
INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('EDITAR_SOLICITACAO', '2, 3, 4, 5', 'Permite a edição de um solicitação em aberto');
INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('ATENDER_SOLICITACAO', '3, 4, 5', 'Permitir atender solicitação em aberto.');
INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('EXCLUIR_SOLICITACAO', '4, 5', 'Permitir a excluir solicitação em aberto ou atendimento.');
INSERT INTO openmycall.config (parametro, texto, comentario) VALUES ('ENCERRAR_SOLICITACAO', '3, 4, 5', 'Permitir o encerramento de um solicitação que esteja em atendimento.');


ALTER SCHEMA openmycall OWNER TO dev;
ALTER TABLE openmycall.projeto OWNER TO dev;
ALTER TABLE openmycall.tipo_problema OWNER TO dev;
ALTER TABLE openmycall.projeto_tipo_problema OWNER TO dev;
ALTER TABLE openmycall.opcoes_menu OWNER TO dev;
ALTER TABLE openmycall.perfil OWNER TO dev;
ALTER TABLE openmycall.permissao_perfil OWNER TO dev;
ALTER TABLE openmycall.empresas OWNER TO dev;
ALTER TABLE openmycall.usuario OWNER TO dev;
ALTER TABLE openmycall.solicitacao OWNER TO dev;
ALTER TABLE openmycall.arquivos OWNER TO dev;
ALTER TABLE openmycall.tipo_feedback OWNER TO dev;
ALTER TABLE openmycall.feedback OWNER TO dev;
ALTER TABLE openmycall.feriado OWNER TO dev;
ALTER TABLE openmycall.expediente OWNER TO dev;
ALTER TABLE openmycall.projeto_responsaveis OWNER TO dev;
ALTER TABLE openmycall.log OWNER TO dev;
ALTER TABLE openmycall.prioridade OWNER TO dev;
ALTER TABLE openmycall.config OWNER TO dev;