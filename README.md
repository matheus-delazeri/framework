### Pré-requisitos

- PHP 8.1
- - Com extensão "ext-mysqli"
- XAMPP (https://www.apachefriends.org/pt_br/download.html)
- Composer (https://getcomposer.org/download/)

### Configuração

- O arquivo `.env` deve ser alterado com as informações do banco de dados MySQL que será usado. Os valores presentes no arquivo `.env.example` são os padrões da versão instalada com o XAMPP. 
- A estrutra do banco de dados se encontra na pasta `data/` e pode ser diretamente importada no MySQL utilizando o `phpmyadmin` (que é instalado junto ao XAMPP). Para acessar o `phpmyadmin` inicie o XAMPP e acesse http://127.0.0.1/phpmyadmin/
- É necessário executar o comando `composer install` para iniciar as configurações do projeto.

### Execução

Para executar a aplicação basta executar o arquivo `app.php` utilizando:
```
php app.php
```