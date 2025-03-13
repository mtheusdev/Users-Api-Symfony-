# Backend Symfony com Docker

## Requisitos

- [Docker](https://www.docker.com/) e [Docker Compose](https://docs.docker.com/compose/)
- [Symfony CLI](https://symfony.com/download/)
- PHP 8.0+
- Composer

## Configuração do PHP

Certifique-se de que as seguintes extensões estão habilitadas no seu `php.ini`:

```
extension=curl
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=sodium
```

Além disso, adicione as seguintes configurações ao final do arquivo `php.ini` para ativar o Xdebug:

```
zend_extension="C:\php\ext\php_xdebug-3.4.2-8.3-nts-vs16-x86_64.dll"
[xdebug]
xdebug.mode=coverage
xdebug.start_with_request=yes
```

## Subindo o Projeto

1. Clone o repositório e navegue até o diretório do projeto:
   ```sh
   git clone <seu-repositorio>
   cd <seu-repositorio>
   ```
2. Crie um arquivo `.env` na raiz do projeto e configure as variáveis de ambiente conforme necessário:
   ```
   APP_ENV=dev
   MYSQL_DATABASE={ALTERE}
   MYSQL_USER={ALTERE}
   MYSQL_PASSWORD={ALTERE}
   MYSQL_ROOT_PASSWORD={ALTERE}
   
   DATABASE_URL="mysql://${MYSQL_USER}:${MYSQL_PASSWORD}@{ALTERE}:3306/${MYSQL_DATABASE}?serverVersion=8.0&charset=utf8mb4"
   
   ##Altere todos os dados acima##
   
   ###> nelmio/cors-bundle ###
   CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
   ###< nelmio/cors-bundle ###
   
   ###> lexik/jwt-authentication-bundle ###
   JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
   JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
   JWT_PASSPHRASE=test
   ###< lexik/jwt-authentication-bundle ###
   ```
3. O projeto usa o LexikJWTAuthenticationBundle para autenticação JWT. As chaves de assinatura estão na pasta `jwt/`. Se necessário, gere novas chaves executando:
   ```sh
   mkdir -p config/jwt
   openssl genpkey -algorithm RSA -out config/jwt/private.pem -aes256 -pass pass:test
   openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem -passin pass:test
   ```
4. Instale as dependências do Composer:
   ```sh
   composer install
   ```
5. Suba os containers Docker: (Antes disso, configure o ambiente e volte para esse passo)
   ```sh
   docker-compose up -d
   ```
6. Rode as migrations do banco de dados:
   ```sh
   symfony console doctrine:migrations:migrate
   ```
7. Inicie o servidor Symfony:
   ```sh
   symfony server:start --no-tls
   ```

## Scripts Disponíveis

O projeto possui alguns scripts no `composer.json` para facilitar o desenvolvimento:

- **Executar testes:**
  ```sh
  composer test
  ```
- **Executar testes com cobertura de código:**
  ```sh
  composer test:coverage
  ```
- **Iniciar o servidor Symfony:**
  ```sh
  composer start
  ```
