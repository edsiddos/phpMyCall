ALTER TABLE phpmycall.perfil ADD COLUMN nivel SMALLINT UNIQUE;

UPDATE phpmycall.perfil SET nivel = id;

UPDATE phpmycall.opcoes_menu SET link = 'Horarios/alterar_Expediente' WHERE link = 'Horarios/alterarExpediente';
UPDATE phpmycall.opcoes_menu SET link = 'Horarios/manter_Feriados' WHERE link = 'Horarios/manterFeriados';
UPDATE phpmycall.opcoes_menu SET link = 'Projetos_Problemas/index' WHERE link = 'ProjetosProblemas/index';
UPDATE phpmycall.opcoes_menu SET link = 'Login/alterar_Senha' WHERE link = 'Login/alterarSenha';

UPDATE phpmycall.opcoes_menu SET link = LOWER(link);

UPDATE phpmycall.opcoes_menu SET link = 'solicitacao/lista', nome = 'Lista de solicitações' WHERE link = 'solicitacao/aberta';

DELETE FROM phpmycall.permissao_perfil WHERE menu IN (
	SELECT id FROM phpmycall.opcoes_menu WHERE link IN ('solicitacao/andamento', 'solicitacao/finalizadas', 'sla/index')
);

DELETE FROM phpmycall.opcoes_menu WHERE link IN('solicitacao/andamento', 'solicitacao/finalizadas', 'sla/index') OR nome = 'Relatórios';

UPDATE phpmycall.expediente SET dia_semana = 'Domingo' WHERE dia_semana = 'DOMINGO';
UPDATE phpmycall.expediente SET dia_semana = 'Segunda-Feira' WHERE dia_semana = 'SEGUNDA-FEIRA';
UPDATE phpmycall.expediente SET dia_semana = 'Terça-Feira' WHERE dia_semana = 'TERÇA-FEIRA';
UPDATE phpmycall.expediente SET dia_semana = 'Quarta-Feira' WHERE dia_semana = 'QUARTA-FEIRA';
UPDATE phpmycall.expediente SET dia_semana = 'Quinta-Feira' WHERE dia_semana = 'QUINTA-FEIRA';
UPDATE phpmycall.expediente SET dia_semana = 'Sexta-Feira' WHERE dia_semana = 'SEXTA-FEIRA';
UPDATE phpmycall.expediente SET dia_semana = 'Sábado' WHERE dia_semana = 'SÁBADO';

UPDATE phpmycall.prioridade SET nome = 'Urgente' WHERE nome = 'URGENTE';
UPDATE phpmycall.prioridade SET nome = 'Alta' WHERE nome = 'ALTA';
UPDATE phpmycall.prioridade SET nome = 'Normal' WHERE nome = 'NORMAL';
UPDATE phpmycall.prioridade SET nome = 'Baixa' WHERE nome = 'BAIXA';
UPDATE phpmycall.prioridade SET nome = 'Mínima' WHERE nome = 'MINIMA';

ALTER TABLE phpmycall.solicitacao ADD COLUMN resolucao TEXT DEFAULT NULL;

