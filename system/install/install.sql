
CREATE TABLE projeto(
	id INTEGER AUTO_INCREMENT,
	nome VARCHAR(50) NOT NULL UNIQUE,
	CONSTRAINT pk_area_problema PRIMARY KEY(id)
);

CREATE TABLE tipo_problema(
	id INTEGER AUTO_INCREMENT,
	nome VARCHAR(50) NOT NULL UNIQUE,
	CONSTRAINT pk_tipo_problema PRIMARY KEY(id)
);

CREATE TABLE projeto_tipo_problema(
	id INTEGER AUTO_INCREMENT,
	projeto INTEGER NOT NULL,
	problema INTEGER NOT NULL,
	resposta TIME DEFAULT NULL, -- tempo para resposta
	solucao TIME DEFAULT NULL, -- tempo para solução
	descricao VARCHAR(255) DEFAULT NULL, -- informação geral do tipo de problema
	CONSTRAINT pk_projeto_tipo_problema PRIMARY KEY (id),
	CONSTRAINT fk_projeto_projeto_tipo_problema FOREIGN KEY (projeto) REFERENCES projeto(id),
	CONSTRAINT fk_problema_projeto_tipo_problema FOREIGN KEY (problema) REFERENCES tipo_problema(id)
);

CREATE TABLE opcoes_menu(
	id INTEGER AUTO_INCREMENT,
	nome VARCHAR(100) NOT NULL, -- Nome a ser mostrado ao usuário
	link VARCHAR(255),
	interno BOOLEAN NOT NULL DEFAULT TRUE, -- o link será interno ou externo
	funcionalidade BOOLEAN NOT NULL DEFAULT TRUE, -- se é uma funcinalidade ou menu
	menu_pai INTEGER DEFAULT NULL, -- id do menu pai
	CONSTRAINT pk_opcoes_menu PRIMARY KEY (id),
	CONSTRAINT fk_menu_pai_opcoes_menu FOREIGN KEY (menu_pai) REFERENCES opcoes_menu(id)
);

INSERT INTO opcoes_menu VALUES (1, 'Chat', 'Chat/index', TRUE, TRUE, NULL);
INSERT INTO opcoes_menu VALUES (2, 'Solicitação', '', TRUE, FALSE, NULL);
INSERT INTO opcoes_menu VALUES (3, 'Finalizadas', 'Solicitacao/finalizadas', TRUE, TRUE, 2);
INSERT INTO opcoes_menu VALUES (4, 'Em andamento', 'Solicitacao/andamento', TRUE, TRUE, 2);
INSERT INTO opcoes_menu VALUES (5, 'Em aberto', 'Solicitacao/aberta', TRUE, TRUE, 2);
INSERT INTO opcoes_menu VALUES (6, 'Abrir Solicitação', 'Solicitacao/abrir', TRUE, TRUE, 2);
INSERT INTO opcoes_menu VALUES (7, 'Administração', '', TRUE, FALSE, NULL);
INSERT INTO opcoes_menu VALUES (8, 'Expediente', 'Horarios/alterar_expediente', TRUE, TRUE, 7);
INSERT INTO opcoes_menu VALUES (9, 'Feriados', 'Horarios/manter_feriados', TRUE, TRUE, 7);
INSERT INTO opcoes_menu VALUES (10, 'Usuários', '', TRUE, FALSE, 7);
INSERT INTO opcoes_menu VALUES (11, 'Cadastrar', 'Usuarios/cadastrar_usuario', TRUE, TRUE, 10);
INSERT INTO opcoes_menu VALUES (12, 'Alterar', 'Usuarios/alterar_usuario', TRUE, TRUE, 10);
INSERT INTO opcoes_menu VALUES (13, 'Excluir', 'Usuarios/excluir_usuario', TRUE, TRUE, 10);
INSERT INTO opcoes_menu VALUES (14, 'Tipos de Feedback', '', TRUE, FALSE, 7);
INSERT INTO opcoes_menu VALUES (15, 'Cadastrar', 'Feedback/cadastrar_tipo_feedback', TRUE, TRUE, 14);
INSERT INTO opcoes_menu VALUES (16, 'Alterar', 'Feedback/alterar_tipo_feedback', TRUE, TRUE, 14);
INSERT INTO opcoes_menu VALUES (17, 'Excluir', 'Feedback/excluir_tipo_feedback', TRUE, TRUE, 14);
INSERT INTO opcoes_menu VALUES (18, 'Projetos e Problemas', '', TRUE, FALSE, 7);
INSERT INTO opcoes_menu VALUES (19, 'Cadastrar', 'ProjetosProblemas/cadastrar_projeto_problema', TRUE, TRUE, 18);
INSERT INTO opcoes_menu VALUES (20, 'Alterar', 'ProjetosProblemas/alterar_projeto_problema', TRUE, TRUE, 18);
INSERT INTO opcoes_menu VALUES (21, 'Excluir', 'ProjetosProblemas/excluir_projeto_problema', TRUE, TRUE, 18);
INSERT INTO opcoes_menu VALUES (22, 'Relatórios', '', TRUE, FALSE, NULL);
INSERT INTO opcoes_menu VALUES (23, 'SLA', 'SLA/index', TRUE, TRUE, 22);


CREATE TABLE perfil(
	id INTEGER AUTO_INCREMENT,
	perfil VARCHAR(25) NOT NULL UNIQUE,
	CONSTRAINT pk_perfil PRIMARY KEY (id)
);

INSERT INTO perfil VALUES (1, 'Cliente');
INSERT INTO perfil VALUES (2, 'Atendente');
INSERT INTO perfil VALUES (3, 'Técnico');
INSERT INTO perfil VALUES (4, 'Gerente');
INSERT INTO perfil VALUES (5, 'Administrador de Sistema');

CREATE TABLE permissao_perfil(
	id INTEGER AUTO_INCREMENT,
	menu INTEGER NOT NULL,
	perfil INTEGER NOT NULL,
	CONSTRAINT pk_permissao_perfil PRIMARY KEY (id),
	CONSTRAINT fk_menu_permissao_perfil FOREIGN KEY (menu) REFERENCES opcoes_menu (id),
	CONSTRAINT fk_perfil_permissao_perfil FOREIGN KEY (perfil) REFERENCES perfil (id)
);

-- Inicio permissão - chat
INSERT INTO permissao_perfil VALUES (1, 1, 1);
INSERT INTO permissao_perfil VALUES (2, 1, 2);
INSERT INTO permissao_perfil VALUES (3, 1, 3);
INSERT INTO permissao_perfil VALUES (4, 1, 4);
INSERT INTO permissao_perfil VALUES (5, 1, 5);
-- Final permissão - chat
-- Inicio visualização de solicitações
INSERT INTO permissao_perfil VALUES (6, 3, 1); -- finalizadas
INSERT INTO permissao_perfil VALUES (7, 3, 2);
INSERT INTO permissao_perfil VALUES (8, 3, 3);
INSERT INTO permissao_perfil VALUES (9, 3, 4);
INSERT INTO permissao_perfil VALUES (10, 3, 5);
INSERT INTO permissao_perfil VALUES (11, 4, 1); -- em atendimento
INSERT INTO permissao_perfil VALUES (12, 4, 2);
INSERT INTO permissao_perfil VALUES (13, 4, 3);
INSERT INTO permissao_perfil VALUES (14, 4, 4);
INSERT INTO permissao_perfil VALUES (15, 4, 5);
INSERT INTO permissao_perfil VALUES (16, 5, 1); -- em aberto
INSERT INTO permissao_perfil VALUES (17, 5, 2);
INSERT INTO permissao_perfil VALUES (18, 5, 3);
INSERT INTO permissao_perfil VALUES (19, 5, 4);
INSERT INTO permissao_perfil VALUES (20, 5, 5);
-- Final visualização de solicitação
-- Inicio abertura de solicitação
INSERT INTO permissao_perfil VALUES (21, 6, 2);
INSERT INTO permissao_perfil VALUES (22, 6, 3);
INSERT INTO permissao_perfil VALUES (23, 6, 4);
INSERT INTO permissao_perfil VALUES (24, 6, 5);
-- Final abertura de solicitação
INSERT INTO permissao_perfil VALUES (25, 8, 5); -- expediente
INSERT INTO permissao_perfil VALUES (26, 9, 5); -- feriados
-- Inicio manter usuário
INSERT INTO permissao_perfil VALUES (27, 11, 4); -- cadastrar
INSERT INTO permissao_perfil VALUES (28, 11, 5);
INSERT INTO permissao_perfil VALUES (29, 12, 4); -- alterar
INSERT INTO permissao_perfil VALUES (30, 12, 5);
INSERT INTO permissao_perfil VALUES (31, 13, 4); -- excluir
INSERT INTO permissao_perfil VALUES (32, 13, 5);
-- Final manter usuário
-- Inicio manter tipos de feedback
INSERT INTO permissao_perfil VALUES (33, 15, 4); -- cadastrar
INSERT INTO permissao_perfil VALUES (34, 15, 5);
INSERT INTO permissao_perfil VALUES (35, 16, 4); -- alterar
INSERT INTO permissao_perfil VALUES (36, 16, 5);
INSERT INTO permissao_perfil VALUES (37, 17, 4); -- excluir
INSERT INTO permissao_perfil VALUES (38, 17, 5);
-- Final manter tipos de feedback
-- Inicio manter projetos e problemas
INSERT INTO permissao_perfil VALUES (39, 19, 4); -- cadastrar
INSERT INTO permissao_perfil VALUES (40, 19, 5);
INSERT INTO permissao_perfil VALUES (41, 20, 4); -- alterar
INSERT INTO permissao_perfil VALUES (42, 20, 5);
INSERT INTO permissao_perfil VALUES (43, 21, 4); -- excluir
INSERT INTO permissao_perfil VALUES (44, 21, 5);
-- Final manter projetos e problemas
-- Inicio relatórios
INSERT INTO permissao_perfil VALUES (45, 23, 4); -- SLA
INSERT INTO permissao_perfil VALUES (46, 23, 5);
-- Final relatórios

--
-- Tabela de usuários
--

CREATE TABLE usuario(
	id INTEGER AUTO_INCREMENT,
	usuario VARCHAR(15) NOT NULL UNIQUE,
	senha VARCHAR(50) NOT NULL,
	nome VARCHAR(80) NOT NULL,
	email VARCHAR(150) NOT NULL UNIQUE,
	perfil INTEGER NOT NULL,
	dt_troca DATETIME NOT NULL,
	CONSTRAINT pk_usuario PRIMARY KEY (id),
	CONSTRAINT fk_perfil_perfil_usuario FOREIGN KEY (perfil) REFERENCES perfil (id)
);

INSERT INTO usuario VALUES (1, 'admin', sha1(md5('admin')), 'Administrador', 'admin@admin.com', 5, '0000-00-00 00:00');

CREATE TABLE solicitacao(
	id INTEGER AUTO_INCREMENT,
	projeto_problema INTEGER NOT NULL,
	descricao TEXT NOT NULL,
	solicitante INTEGER NOT NULL,
	prioridade INTEGER NOT NULL,
	atendente INTEGER NOT NULL,
	tecnico INTEGER NOT NULL,
	abertura TIMESTAMP NOT NULL,
	atendimento TIMESTAMP NOT NULL,
	encerramento TIMESTAMP NOT NULL,
	solicitacao_origem INTEGER DEFAULT NULL,
	avaliacao INTEGER NOT NULL,
	justificativa_avaliacao VARCHAR(255),
	CONSTRAINT pk_solicitacao PRIMARY KEY (id),
	CONSTRAINT fk_projeto_problema_solicitacao FOREIGN KEY (projeto_problema) REFERENCES projeto_tipo_problema (id),
	CONSTRAINT fk_solicitante_solicitacao FOREIGN KEY (solicitante) REFERENCES usuario(id),
	CONSTRAINT fk_atendente_solicitacao FOREIGN KEY (atendente) REFERENCES usuario(id),
	CONSTRAINT fk_tecnico_solicitacao FOREIGN KEY (tecnico) REFERENCES usuario(id),
	CONSTRAINT fk_solicitacao_origem_solicitacao FOREIGN KEY (solicitacao_origem) REFERENCES solicitacao (id)
);

CREATE TABLE reabrir_solicitacao(
	id INTEGER AUTO_INCREMENT,
	solicitacao INTEGER NOT NULL UNIQUE, -- uma solicitação só pode ser reaberta uma vez
	motivo TEXT NOT NULL, -- justificativa para reabertura do chamado
	resposta TEXT DEFAULT NULL, -- resposta do gerente á reabertura
	abertura TIMESTAMP NOT NULL,
	autorizacao TIMESTAMP NOT NULL,
	encerrado TIMESTAMP NOT NULL,
	autorizado BOOLEAN DEFAULT FALSE, -- aguardando liberação se false
	aberto BOOLEAN DEFAULT TRUE, -- ainda não foi resolvida
	CONSTRAINT pk_reabrir_solicitacao PRIMARY KEY (id),
	CONSTRAINT fk_solicitacao_reabrir_solicitacao FOREIGN KEY (solicitacao) REFERENCES solicitacao (id)
);

CREATE TABLE arquivos(
	id INTEGER AUTO_INCREMENT,
	nome VARCHAR(100) NOT NULL,
	solicitacao INTEGER NOT NULL,
	conteudo MEDIUMBLOB NOT NULL,
	CONSTRAINT pk_arquivos PRIMARY KEY (id),
	CONSTRAINT fk_solicitacao_arquivos FOREIGN KEY (solicitacao) REFERENCES solicitacao (id)
);

CREATE TABLE tipo_feedback(
	id INTEGER AUTO_INCREMENT,
	nome VARCHAR(50) NOT NULL,
	abreviatura VARCHAR(10) NOT NULL,
	descontar BOOLEAN DEFAULT TRUE,
	responsavel CHAR(1) NOT NULL,
	CONSTRAINT pk_tipo_feedback PRIMARY KEY (id)
);

CREATE TABLE feedback(
	id INTEGER AUTO_INCREMENT,
	tipo_feedback INTEGER NOT NULL,
	pergunta TEXT NOT NULL,
	resposta TEXT NOT NULL,
	inicio TIMESTAMP NOT NULL,
	fim TIMESTAMP NOT NULL,
	solicitacao INTEGER NOT NULL,
	responsavel INTEGER DEFAULT NULL,
	CONSTRAINT pk_feedback PRIMARY KEY (id),
	CONSTRAINT fk_tipo_feedback_feedback FOREIGN KEY (tipo_feedback) REFERENCES tipo_feedback (id),
	CONSTRAINT fk_solicitacao_feedback FOREIGN KEY (solicitacao) REFERENCES solicitacao (id),
	CONSTRAINT fk_responsavel_feedback FOREIGN KEY (responsavel) REFERENCES usuario (id)
);

CREATE TABLE feriado(
	id INTEGER AUTO_INCREMENT,
	dia DATE NOT NULL,
	CONSTRAINT pk_feriado PRIMARY KEY (id)
);

CREATE TABLE expediente(
	id INTEGER AUTO_INCREMENT,
	dia_semana VARCHAR(15) NOT NULL UNIQUE,
	entrada_manha TIME DEFAULT '7:00:00',
	saida_manha TIME DEFAULT '11:00:00',
	entrada_tarde TIME DEFAULT '13:00:00',
	saida_tarde TIME DEFAULT '17:00:00',
	CONSTRAINT pk_expediente PRIMARY KEY (id)
);

INSERT INTO expediente (dia_semana) VALUES('DOMINGO');
INSERT INTO expediente (dia_semana) VALUES('SEGUNDA-FEIRA');
INSERT INTO expediente (dia_semana) VALUES('TERÇA-FEIRA');
INSERT INTO expediente (dia_semana) VALUES('QUARTA-FEIRA');
INSERT INTO expediente (dia_semana) VALUES('QUINTA-FEIRA');
INSERT INTO expediente (dia_semana) VALUES('SEXTA-FEIRA');
INSERT INTO expediente (dia_semana) VALUES('SÁBADO');

CREATE TABLE projeto_responsaveis(
	id INTEGER AUTO_INCREMENT,
	usuario INTEGER NOT NULL,
	projeto INTEGER NOT NULL,
	CONSTRAINT pk_projeto_responsaveis PRIMARY KEY(id),
	CONSTRAINT fk_usuario_projeto_responsaveis FOREIGN KEY (usuario) REFERENCES usuario(id),
	CONSTRAINT fk_projeto_projeto_responsaveis FOREIGN KEY (projeto) REFERENCES projeto(id)
);

CREATE TABLE log(
	id INTEGER AUTO_INCREMENT,
	ip VARCHAR(15) NOT NULL,
	data_hora TIMESTAMP NOT NULL,
	dados TEXT NOT NULL,
	usuario INTEGER NOT NULL,
	CONSTRAINT pk_log PRIMARY KEY(id),
	CONSTRAINT fk_usuario_log FOREIGN KEY (usuario) REFERENCES usuario(id)
);
