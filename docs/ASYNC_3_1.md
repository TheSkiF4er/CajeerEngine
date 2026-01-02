# v3.1 — Async Core & Workers (Production)

## Jobs & Queues
- Настройка: `system/queue.php`
- Драйверы: `db` (production baseline), `redis/sqs/rabbitmq` (adapters foundation stubs)
- DB-очередь поддерживает:
  - visibility timeout
  - retries: exponential backoff + jitter
  - DLQ (dead-letter queue)

### Гарантии доставки
- **At-least-once**: базовая гарантия фоновой обработки
- **Exactly-once (foundation)**: `idempotency_key` для идемпотентной постановки задач

## Async events
- `AsyncEventBus`: persist (optional) → enqueue → deliver
- Хранилище событий: `ce_events`
- Replay: `php cli/cajeer2 events:replay <event_id>`

## Ops
- Graceful shutdown: SIGTERM/SIGINT
- Supervisor:
  - `php cli/cajeer2 jobs:supervise <queue>`
  - `ops/worker-supervisor.sh <queue>`
