# CineRate

O CineRate é uma aplicação web desenvolvida em Laravel para compartilhamento de opiniões e recomendações de filmes e séries.

Os usuários podem criar publicações, recomendar ou não recomendar uma obra e acompanhar publicações de outros usuários.

## Tecnologias

- PHP
- Laravel 12
- Livewire 4
- Alpine.js
- Tailwind CSS
- MariaDB / MySQL
- Vite

## Funcionalidades

- Cadastro e autenticação de usuários
- Feed de publicações
- Criação de publicações sobre filmes e séries
- Recomendar uma publicação
- Não recomendar uma publicação
- Alteração do voto
- Acompanhamento de publicações
- Acompanhamento automático ao votar
- Encerramento de publicações
- Exclusão de publicações conforme regras de negócio
- Visualização das próprias publicações
- Visualização das publicações acompanhadas
- Paginação do feed
- Escolha da quantidade de publicações exibidas por página
- Perfil do usuário
- Upload de foto de perfil

## Regras de negócio

### Votos

Cada usuário pode possuir apenas um voto por publicação.

O voto pode ser:

- Recomendo
- Não recomendo

Caso o usuário vote novamente na mesma publicação, o voto anterior é atualizado.

Ao votar em uma publicação, o usuário passa automaticamente a acompanhá-la.

### Acompanhamento

Um usuário pode acompanhar uma publicação apenas uma vez.

Não é possível iniciar novas interações em uma publicação encerrada.

### Encerramento

Somente o autor pode encerrar sua publicação.

Uma publicação encerrada não aceita novos votos ou acompanhamentos.

### Exclusão

Somente o autor pode excluir sua publicação.

Publicações que já possuem votos ou acompanhamentos não podem ser excluídas.

## Estrutura principal do banco de dados

A aplicação possui as seguintes entidades principais:

### Users

Armazena os usuários da aplicação.

### Posts

Armazena as publicações de filmes e séries.

Cada publicação pertence a um usuário.

### Votes

Armazena os votos realizados nas publicações.

Existe uma restrição única entre `user_id` e `post_id`, impedindo que um usuário possua mais de um voto para a mesma publicação.

### Follows

Armazena quais publicações cada usuário acompanha.

Também existe uma restrição única entre `user_id` e `post_id`.

## Requisitos

Para executar o projeto é necessário possuir:

- PHP 8.1 ou superior
- Composer
- Node.js
- NPM
- MySQL ou MariaDB

## Instalação

Clone o repositório:

```bash
git clone URL_DO_REPOSITORIO