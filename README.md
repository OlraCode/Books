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
  docker compose up -d
  ```
  
- ## 3️⃣ Instalar as dependências
  ```
  docker exec -it app bash
  composer install
  ```

- ## 4️⃣ Criar e configurar o banco de dados
  ```
  php bin/console doctrine:database:create
  php bin/console doctrine:migrations:migrate
  ```
- ## 5️⃣ Rodar o servidor
  ```
  php -S 0.0.0.0:8000 -t public/
  ```
