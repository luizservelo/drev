CREATE TABLE app_user (
    user_id int AUTO_INCREMENT NOT NULL,
    user_name varchar(255),
    user_lastname varchar(255),
    user_email varchar(255),
    user_password text,

    PRIMARY KEY (user_id)
);

CREATE TABLE app_dre (
    dre_id int AUTO_INCREMENT NOT NULL,
    dre_name varchar(255),
    dre_title varchar(255),
    dre_content text,
    dre_timestamp timestamp DEFAULT CURRENT_TIMESTAMP,
    user_id int,

    PRIMARY KEY (dre_id),
    FOREIGN KEY (user_id) REFERENCES app_user(user_id)
);

CREATE TABLE app_shared(
    shared_id int AUTO_INCREMENT NOT NULL,
    dre_id int,
    user_id int,

    PRIMARY KEY (shared_id),
    FOREIGN KEY (user_id) REFERENCES app_user(user_id),
    FOREIGN KEY (dre_id) REFERENCES app_dre(user_id),
);

--  EM BREVE SERÁ ALTERADO!
--  CADA REGRA DE EXCEÇÃO FICARÁ SOZINHA DENTRO DO BANCO

CREATE TABLE app_processamento(
    processamento_id int AUTO_INCREMENT NOT NULL,
    processamento_timestamp timestamp DEFAULT CURRENT_TIMESTAMP,
    user_id int,
    processamento_title varchar(255),
    processamento_name varchar(255),

    PRIMARY KEY (processamento_id),
    FOREIGN KEY (user_id) REFERENCES app_user(user_id)
);

CREATE TABLE app_regra(
    regra_id int AUTO_INCREMENT NOT NULL,
    regra_content LONGTEXT,
    regra_suporte float,
    regra_confianca float,
    regra_nome varchar(255),
    regra_qtd int,
    processamento_id int,

    PRIMARY KEY (regra_id),
    FOREIGN KEY (processamento_id) REFERENCES app_processamento(processamento_id)

);
