# 📦 Sistema de Gerenciamento de Produtos e Fornecedores
Este é um sistema de gestão simples e eficiente para gerenciar produtos, fornecedores e seus vínculos. Desenvolvido com PHP puro (sem frameworks), MySQL e jQuery, é ideal para pequenas e médias empresas que precisam de um controle organizado de seu estoque e fornecedores.

## Funcionalidades ## 

### Gestão de Produtos
- Cadastro de produtos com código interno, descrição e status
- Filtros por nome, código e status
- Exclusão múltipla de produtos

### Validação de campos obrigatórios
- Gestão de Fornecedores
- Cadastro de fornecedores com CNPJ, e-mail e telefone
- Validação de CNPJ (14 dígitos) e e-mail
- Verificação de duplicidade (CNPJ e e-mail únicos)
- Filtros por nome, CNPJ e status

### Gestão de Vínculos
Associação de fornecedores a múltiplos produtos
Visualização de produtos por fornecedor
Remoção segura de vínculos (sem deletar fornecedores automaticamente)
Interface intuitiva para gerenciar relacionamentos

### Interface Amigável
- Design responsivo para desktop e dispositivos móveis
- Tabelas organizadas com filtros
- Confirmações visuais com SweetAlert2
- Modais para criação e edição de registros
  
### Requisitos
- PHP 8.0+ (testado com PHP 8.2)
- MySQL 8.0+ ou MariaDB 10.5+
- Navegador moderno (Chrome, Firefox, Edge)
- Servidor web (Apache, Nginx ou PHP built-in server)
  
## Como Rodar o Projeto

1. Clone o repositório
   ```
   git clone https://github.com/omarcus212/virtual-market
   ```
2. Configure o ambiente
   ```
   cp .env.example .env
   ```

3. Edite o arquivo .env com suas credenciais do banco de dados:
- DB_HOST=localhost
- DB_NAME=product_supplier_system
- DB_USER=root
- DB_PASS=sua senha
  
4. Configure o banco de dados
   - Acesse o seu gerenciador de banco (MySQL Workbench, phpMyAdmin).
   - Importe o arquivo localizado na pasta (database/product_supplier_system.sql)
   - (Opcional) Para popular o banco com dados de exemplo, importe também seeders (database/seeders.sql)
     
5. Inicie o servidor
   ```
   php -S localhost:8000
   ```
   
6. Acesse a aplicação
   Abra seu navegador e acesse:  http://localhost:8000

### Documentação api:

https://documenter.getpostman.com/view/21065723/2sBXcEizaa
