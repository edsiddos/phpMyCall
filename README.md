Item               | Descrição
------------------ | ------------
Licença            | GPLv3
Versão atual       | 0.1.2015.08.20

## Requisitos: ##
1. Apache 2;
2. Módulo rewrite habilitado;
3. PHP 5.6;
4. PHP-APC;
5. Banco de Dados Postgresql 9.2

## Instalação ##

### Instalação e configuração da aplicação web ###

Instalar o Apache 2 e o PHP 5 executando o seguinte comando:

```bash
exemplo@exemplo:~ $ sudo apt-get install apache2 php5 php-apc php5-pgsql
```

O passo seguinte será habilitar o módulo rescrita do apache e configurá-lo para utilização de url amigáveis, executando o comando a seguir:

```shell
exemplo@exemplo:~ $ sudo a2enmod rewrite
exemplo@exemplo:~ $ sudo nano /etc/apache2/sites-available/default
```

Editar o arquivo de configuração do apache (/etc/apache2/sites-available/default) para aceitar url amigáveis. Substitua o parâmetro AllowOverride None por AllowOverride All

```apache
DocumentRoot /var/www
<Directory />
    Options FollowSymLinks
    AllowOverride All
</Directory>
<Directory /var/www/>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Order allow,deny
    allow from all
</Directory>
```

Reinicie o apache para aplicar as alterações

```shell
exemplo@exemplo:~ $ sudo service apache2 restart
```

## Instalação e configuração do banco de dados Postgresql 9.2 ##

Instalação dos pacotes necessários

```shell
exemplo@exemplo:~ $ sudo apt-get update
exemplo@exemplo:~ $ sudo apt-get upgrade
exemplo@exemplo:~ $ sudo apt-get install postgresql-9.2
```

### Criando usuários e configurando permissões ###

No terminal execute os comandos seguintes com usuário postgres, para trocar de usuário execute o seguinte comando:

```shell
exemplo@exemplo:~ $ sudo su - postgres
```

Criando novo usuário e acessando o CLI do postgres:

```shell
postgres@exemplo:~ $ createuser dev
postgres@exemplo:~ $ psql
```

Alterando as senhas dos usuários postgres e dev. Em seguida é criando a base dados phpmycall e alterando o OWNER do banco:

```shell
postgres=# ALTER USER postgres WITH PASSWORD 'noVa53nh4postgr35ql';
ALTER ROLE
postgres=# ALTER USER dev WITH PASSWORD 'noVa53nh4';
ALTER ROLE
postgres=# CREATE DATABASE phpmycall;
CREATE DATABASE
postgres=# GRANT ALL PRIVILEGES ON DATABASE phpmycall TO dev;
GRANT
postgres=# ALTER DATABASE phpmycall OWNER TO dev;
ALTER DATABASE
postgres=# \q
```

### Controle de acesso PostgreSQL ###

Edite o arquivo principal do controle de acesso do PostgreSQL:

```shell
exemplo@exemplo:~ $ sudo nano /etc/postgresql/9.2/main/pg_hba.conf
```

Adicione o usuário dev com método 'trust'

```text
# Database administrative login by Unix domain socket
local		all			postgres				peer
local		all			dev				    	trust

# TYPE	DATABASE		USER		ADDRESS		METHOD
```

Permitindo apenas acesso local

```shell
exemplo@exemplo:~ $ sudo nano /etc/postgresql/9.2/main/postgresql.conf
```

Remova o comentário da linha 'listen_addresses':

```text
#------------------------------------------------------------------------------
# CONNECTIONS AND AUTHENTICATION
#------------------------------------------------------------------------------
# - Connection Settings -
listen_addresses = 'localhost'    # what IP address(es) to listen on;
```

Reinicie o servidor PostgreSQL:

```shell
exemplo@exemplo:~ $ sudo service postgresql restart
 * Restarting PostgreSQL 9.2 database server
```

Após a criação da base dados phpmycall importe o script postgres.sql que se encontra dentro da pasta system/install, mude para usuário postgres e execute o seguinte comando:

```shell
exemplo@exemplo:~ $ sudo su postgres
postgres@exemplo:~ $ psql -U dev -h localhost -f postgre.sql phpmycall
```

## Configuração do arquivo config.php ##

Após a configuração do banco de dados postgresql, apache 2 e php 5, e necessário cria o arquivo ***config.php*** dentro da pasta ***system***. Para facilitar este processo, dentro da pasta system existe o arquivo ***config-example.php***.

* Renomeie o arquivo config-example.php para config.php;
* Altere as constantes de acesso ao banco e de endereço da aplicação.

```php
<?php

// Endereço do banco de dados - Linha 32
define('DB_HOST', 'localhost');

// Nome do banco de dados - Linha 35
define('DB_NOME', 'phpmycall');

// Usuário do banco de dados - Linha 38
define('DB_USER', 'dev');

// Senha do usuário do banco de dados - Linha 41
define('DB_PASS', 'dev');

// Caminho absoluto para pasta que armazena os arquivos anexos as solicitações - Linha 45
define('FILES', '/var/files');

// caminho relativo para a pasta do projeto - Linha 48
define('PATH', '/var/www/html/phpmycall');

?>
```

* Crie a pasta dos arquivos anexos (***/var/files***) e altere as permições e usuário da pasta.

```shell
exemplo@exemplo:~ $ sudo mkdir /var/files
exemplo@exemplo:~ $ sudo chown -R www-data.www-data /var/files
exemplo@exemplo:~ $ sudo chmod -R 774 /var/files
```