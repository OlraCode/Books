# 📚 E-Commerce de Livros (Symfony)
Um pequeno e-commerce de livros desenvolvido com Symfony, utilizando Docker e MySQL para facilitar a configuração do ambiente. O projeto conta com funcionalidades como CRUD de livros, sistema de login e carrinho de compras.

# 🚀 Tecnologias Utilizadas
- PHP (Symfony)

- MySQL (Banco de Dados)

- Docker (Containers)

- Twig (Templates)

- Bootstrap (Estilização)

- Doctrine (ORM para banco de dados)

# 📦 Funcionalidades
- ✅ Cadastro, edição e remoção de livros (CRUD)
- ✅ Autenticação de usuários (Login/Logout)
- ✅ Adicionar e remover livros do carrinho
- ✅ Persistência do carrinho no banco de dados

# 🔧 Como Rodar o Projeto
- ## 1️⃣ Clonar o repositório
  ```
  git clone https://github.com/OlraCode/Books.git
  cd Books
  ```
  
- ## 2️⃣ Subir os containers com Docker
  ```
  make up
  ```
  
- ## 3️⃣ Instalar as dependências
  ```
  make install
  ```

- ## 4️⃣ Configurar o banco de dados e Messenger transport
  ```
  make db-create
  make migrate
  make messenger-start
  ```
- ## 5️⃣ Executar os testes
  ```
  make test
  ```
- ## 6️⃣ Rodar o servidor
  ```
  make serve
  ```
  Após isso acesse o site em [http://localhost:8000](http://localhost:8000)
  
  E acesse a rota [http://localhost:8000/user/seed](http://localhost:8000/user/seed) para criar usuários de teste

  ### 🧑 Usuários criados automaticamente:
  
  | Email               | Senha       | Papel  |
  |--------------------|------------|--------|
  | admin@example.com   | admin1234  | Admin  |
  | user@example.com    | user1234   | User   |
