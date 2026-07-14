# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

`proj-phoenix` — add a brief description of what this project does here.

## Commands

```
composer install                                      # install dependencies
php bin/phpunit                                       # run all tests
php bin/phpunit tests/Entity/TodoTest.php             # run a single test file
php bin/console doctrine:migrations:migrate           # run database migrations
php -S localhost:8000 -t public/                      # start dev server
docker compose up --build                              # start the whole app in Docker (no local PHP/Composer needed)
docker compose exec app php bin/phpunit                # run tests inside the container
```

## Architecture

Document the high-level structure here once the codebase has meaningful content.

## Styling

- Do not use inline `style=""` attributes in Twig templates.
- All styles go in `public/css/app.css` using BEM-style class names.
- Pico.css is loaded from CDN and provides the base design system; `app.css` layers project-specific styles on top.
