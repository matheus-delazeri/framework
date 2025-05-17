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

### Geração Automática de Aplicações
Este projeto permite a geração automática de aplicações completas (Model, Controller, View) com base na estrutura do banco de dados MySQL configurado. Para isso, utilize o script generate-app.php.

#### Comando Básico
```bash
php generate-app.php --env=.env --app=NomeDaAplicacao --tables=usuarios,perfis
```
**Parâmetros Disponíveis**
- `--env`: Caminho para o arquivo .env com as configurações do banco de dados (opcional, padrão: .env).
- `--app`: Nome da aplicação (obrigatório). Deve começar com letra maiúscula e conter apenas letras e números.
- `--tables`: Lista de tabelas específicas a serem incluídas, separadas por vírgula (opcional se --all for usado).
- `--all`: Indica que todas as tabelas do banco de dados devem ser incluídas.
- `--template`: Nome do template a ser usado (opcional, padrão: TerminalApp).

**Exemplos**
Gerar aplicação chamada `Movies` com as tabelas `movies` e `directors`:
```bash
php generate-app.php --app=Movies --tables=movies,directors
```
Gerar aplicação `Movies` com **todas** as tabelas do banco:

```bash
php generate-app.php --app=Movies --all
```

#### Arquivos Gerados
Ao executar o script `generate-app.php`, o sistema cria automaticamente uma estrutura completa para a aplicação baseada nas tabelas do banco de dados. Entre os arquivos gerados estão:

1. Modelos, Controladores e Visões
Para cada tabela especificada, são gerados os seguintes arquivos dentro do diretório `/app/{APP NAME}/`:

- `Model/{Entidade}.php`: Representa o modelo da entidade, com integração ao banco de dados.
- `Controller/{Entidade}Controller.php`: Controlador responsável pelas operações CRUD.
- `View/Menu.php`: Arquivo de menu que lista as entidades da aplicação e permite a navegação entre elas.
- `Controller/IndexController.php`: Controlador inicial da aplicação, que exibe o menu principal.

2. Exemplos de Uso (CRUD)
O sistema também gera arquivos de exemplo para testar facilmente cada entidade individualmente. Esses arquivos ficam localizados em: `examples/{APP NAME}/Entities/`
Cada arquivo segue o padrão {Entidade}Example.php e executa automaticamente as operações:

- **Create**: Cria uma nova instância da entidade no banco.
- **Read**: Recupera os dados dessa instância.
- **Update**: Atualiza um ou mais campos da instância.
- **Delete**: Remove a instância do banco.

Exemplo de uso para a entidade Director:

```php
echo "== Example: Director CRUD ==\n";

$model = new \App\Test\Model\Director();

// Create
$model->addData('director_id', 82);
$model->addData('first_name', 'First_name');
$model->addData('last_name', 'Last_name');
$model->addData('birth_date', '2024-01-01');
$model->addData('nationality', 'Nationality');

$model->save();
echo "Created with ID: " . $model->getId() . "\n";
$entityId = $model->getId();

// Read
$model = new \App\Test\Model\Director();
$model->load($entityId);
print_r($model->getData());

// Update
$model = new \App\Test\Model\Director();
$model->load($entityId);
    $model->addData('first_name', 'First_name_updated');

$model->save();
echo "Updated.\n";

// Delete
$model->delete();
echo "Deleted.\n";
```

3. Arquivo `app.php` (Exemplo de Execução da Aplicação)
Além dos arquivos de entidade, também é gerado o arquivo: `examples/{APP NAME}/app.php`
Esse script simula a execução da aplicação. Ele inicializa a configuração a partir do `.env`, carrega o controlador principal (IndexController) e exibe um menu com as entidades disponíveis para executar as operações CRUD.

Exemplo de uso:
```bash
php examples/Test/app.php
```