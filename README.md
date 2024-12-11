# Gerenciamento de Eventos

Este repositório contém uma aplicação web desenvolvida para facilitar o gerenciamento de eventos. O sistema é projetado para gerenciar usuários, promoters, recepcionistas e convidados, oferecendo funcionalidades para administração e organização eficiente de eventos.

## Tecnologias Utilizadas

- **Frontend**:

  - HTML5, CSS3 e JavaScript
  - Frameworks/Bibliotecas: jQuery, Font Awesome
  - SweetAlert2 para feedback visual

- **Backend**:

  - PHP (versão 7.4 ou superior)
  - PDO para conexão segura com o banco de dados

- **Banco de Dados**:

  - MySQL

## Funcionalidades Principais

- **Administração de Usuários**:

  - Listagem de promoters e recepcionistas
  - Adição, edição e exclusão de usuários
  - Atualização de status de conta (Ativo/Inativo)

- **Gerenciamento de Eventos**:

  - Cadastro de eventos
  - Associação de promoters e recepcionistas a eventos
  - Controle de listas de convidados

- **Interface de Pesquisa**:

  - Filtro dinâmico para pesquisa de usuários por nome ou e-mail

- **Autenticação e Segurança**:

  - Sistema de login com gerenciamento de sessões
  - Controle de acesso baseado em funções (admin, promoter, recepcionista)

## Estrutura de Diretórios

```
/
|-- conexao/                  # Configurações e classes de conexão com o banco de dados
|-- components/sidebar/       # Componentes reutilizáveis da barra lateral
|-- login/                    # Páginas de login e autenticação
|-- promoters/                # Módulo de gerenciamento de promoters
|-- recepcionistas/           # Módulo de gerenciamento de recepcionistas
|-- eventos/                  # Módulo de gerenciamento de eventos
|-- assets/                   # Recursos estáticos (imagens, estilos, scripts)
|-- README.md                 # Documentação do projeto
```

## Requisitos de Instalação

1. **Clone o repositório**:

   ```bash
   git clone https://github.com/seu-usuario/gerenciamento-eventos.git
   cd gerenciamento-eventos
   ```

2. **Configure o banco de dados**:

   - Crie um banco de dados MySQL.
   - Importe o arquivo SQL fornecido no diretório `database/` para configurar as tabelas.

3. **Configure a conexão com o banco**:

   - Edite o arquivo `conexao/Conexao.php` com as credenciais do seu banco de dados.

4. **Inicie o servidor local**:

   ```bash
   php -S localhost:8000
   ```

5. **Acesse a aplicação**:
   Abra o navegador e acesse [http://localhost:8000](http://localhost:8000).

## Como Contribuir

1. **Fork este repositório**.
2. Crie uma branch para sua feature ou correção de bug:
   ```bash
   git checkout -b minha-nova-feature
   ```
3. Envie suas alterações:
   ```bash
   git push origin minha-nova-feature
   ```
4. Abra um Pull Request.

## Licença

Este projeto está licenciado sob a Licença MIT. Consulte o arquivo `LICENSE` para mais detalhes.

