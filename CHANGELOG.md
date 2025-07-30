# CHANGELOG

All notable changes to this project will be documented in this file.

## [1.0.5] – 2025-07-30

### Added
- ✅ Support for Kafka `headers`, `key`, `partition`, and `msgflags` via `produce()` options
- ✅ Optional propagation of these options to `produceJson()` and `produceAsync()`
- ✅ JSON `options` field in `kafka_failed_messages` table to persist full Kafka metadata
- ✅ Updated `ProduceKafkaMessage` job to capture and store full `options` on failure
- ✅ `KafkaFailedMessage` model casts `options` as array
- ✅ Improved PHPDoc with array shape for options
- ✅ README fully updated with advanced usage and examples

### Changed
- ♻️ `produce()` now uses `producev()` to allow headers and advanced options
- 🧼 Renamed option key from `msgflags` → `flag` to make it more intuitive

---

## [1.0.4] – 2025-07-29

### Added
- ✅ Dead Letter Queue (DLQ) support via `kafka_failed_messages` table
- ✅ Artisan command `kafka:retry-failed` to replay failed Kafka messages
- ✅ Artisan command `kafka:status` to check broker availability and metadata
- ✅ Asynchronous Kafka sending via `produceAsync()` and `produceAsyncToDefault()`
- ✅ Queue-compatible job: `ProduceKafkaMessage`
- ✅ Full configuration publication tag `laravel-kafka`
- ✅ Partial tags: `laravel-kafka-config`, `laravel-kafka-migrations`

### Changed
- ✨ Replaced direct flush retries with better looped logic (`flush(10000)` with max 10 attempts)
- 📦 Modular refactor: `KafkaProducerFactory` and `KafkaConfigFactory`

---
