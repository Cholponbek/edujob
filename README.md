# EduJob

Job-маркетплейс для сферы образования в Кыргызстане: учреждения (школы,
детсады, колледжи, вузы) находят и подбирают специалистов через ИИ-скрининг.
Laravel 12, Inertia + Vue 3 (публичная часть) + Filament (админка/бэк-офис)
на одной PostgreSQL-модели данных. Полный проектный контекст, домен,
принятые архитектурные решения и открытые вопросы — в
[ARCHITECTURE.md](ARCHITECTURE.md).

## Стек

Laravel 12 · PHP 8.4+ · Filament 3 · Inertia + Vue 3 + Tailwind ·
PostgreSQL 16 · Redis + Horizon · MinIO · Caddy · Pest · Larastan (уровень 6+)

## Быстрый старт (Docker)

```bash
cp .env.example .env
# заполнить DB_PASSWORD, MINIO_ROOT_PASSWORD, ANTHROPIC_API_KEY,
# SMS_GATEWAY_*, TELEGRAM_BOT_TOKEN в .env

docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app npm install && docker compose exec app npm run build
```

## Тесты

```bash
createdb edujob_testing   # один раз, локальные креды — см. .env.testing
php artisan test
```

Тесты идут против настоящего PostgreSQL, не sqlite (см. `phpunit.xml`).

## Статический анализ

```bash
composer install
vendor/bin/phpstan analyse   # уровень 6+, конфиг в phpstan.neon
vendor/bin/pint --test       # code style
```

> **Известное ограничение среды разработки этой сессии:** в песочнице, где
> собирался этот каркас, исходящий доступ к GitHub API был ограничен рамками
> сессии, из-за чего `phpstan/phpstan` и `anthropic-ai/sdk` (у них нет
> git-источника, только zip-дистрибутив через GitHub API) не докачались. Сам
> `composer.json`/`composer.lock` корректны; на обычной машине или в CI с
> обычным сетевым доступом `composer install` отработает без каких-либо
> дополнительных действий. Из-за этого ИИ-джобы (`App\Jobs\Ai\*`) в этой
> сессии протестированы только с замоканным `App\Services\Ai\ClaudeClient`
> (`tests/Feature/Jobs/Ai`) — реальный вызов `Anthropic\Client` не
> выполнялся ни разу; проверить его стоит первым делом на обычной машине
> с `ANTHROPIC_API_KEY`.

## Структура

- `ARCHITECTURE.md` — домен, стек, killer-фичи под рынок КР, принятые решения, чеклист.
- `app/Models` — Institution, Candidate, StaffRequest, Application, HiringCampaign, CandidateRecommendation, AiUsageLog.
- `app/Jobs/Ai` — ИИ-джобы (скрининг, парсинг резюме, генерация резюме, рекомендации).
- `app/Services/Ai/ClaudeClient` — единая точка вызова Anthropic API (модель, цена, логирование стоимости).
- `app/Services/Sms` — SMS-шлюз за интерфейсом (`log` для dev/CI, `nikita` — smspro.nikita.kg).
- `app/Filament/Resources` — админка/бэк-офис (модерация учреждений и т.д.).
- `database/migrations` — схема ядра домена.
- `docker/` — `Dockerfile` (php-fpm) + `Caddyfile`.

## Статус

Каркас проекта: Laravel + Inertia/Vue + Filament + Horizon установлены,
ядро доменной модели (учреждения, роли director/HR/staff, кандидаты,
вакансии, отклики, пакетный набор) реализовано миграциями и моделями.
Auth по телефону/SMS и ИИ-джобы (скрининг, парсинг резюме, двуязычная
генерация, рекомендации) реализованы. Ещё не реализовано (см. чеклист в
ARCHITECTURE.md): Telegram-бот, биллинг, парсинг DOC/DOCX-резюме,
кандидат-facing UI для новых точек входа (сейчас только backend-роуты).
