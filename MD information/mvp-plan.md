# MVP PLAN: Move-in/out Photo Inspection App

## Архитектура

```
┌─────────────────────────────────────────────┐
│ CLIENT │
│ ┌──────────────────────────────────────────┐ │
│ │ Telegram Mini App (TMA) │ │
│ │ Camera + Geolocation + PDF view │ │
│ └──────────────────────┬───────────────────┘ │
└───────────────────────┼─────────────────────┘
                        │ HTTPS
                        ▼
┌─────────────────────────────────────────────┐
│ API GATEWAY │
│ FastAPI (Python 3.11) │
│ /inspect /report /pdf /user /payment │
└───────┬─────────────┬─────────────┬─────────┘
        │             │             │
 ┌──────▼──┐ ┌──────▼──┐ ┌──────▼──┐ ┌──────▼──┐
 │ User    │ │ Photo   │ │ PDF     │ │ Payment │
 │ Service │ │ Service │ │ Service │ │ Service │
 └────┬────┘ └────┬────┘ └────┬────┘ └────┬────┘
      │           │           │           │
      ▼           ▼           ▼           ▼
┌─────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐
│PostgreSQL│ │ Yandex  │ │ WeasyPrint│ │ Telegram │
│(Yandex) │ │ Storage │ │ (PDF)   │ │ Stars   │
└─────────┘ └──────────┘ └──────────┘ └──────────┘
```

---

## Tech Stack

| Компонент | Технология | Причина |
|-----------|-----------|---------|
| Frontend | Telegram Mini App (HTML/JS) | Быстрый запуск, не нужен App Store |
| Backend | FastAPI (Python 3.11) | Async, easy integration |
| Database | PostgreSQL (Yandex Cloud) | ФЗ-152 compliant |
| Storage | Yandex Object Storage | S3-compatible, cheap |
| Geolocation | Yandex Geolocation API | RU-friendly |
| PDF | WeasyPrint (html2pdf) | Python-native, good rendering |
| Payments | Telegram Stars | Zero-commission для РФ |
| Hosting | Timeweb Cloud | RU-based |

---

## OSS Libraries

| Компонент | Библиотека | Зачем |
|-----------|-----------|-------|
| PDF | `weasyprint` | HTML → PDF generation |
| Geolocation | `yandex-geolocation` | Геолокация по IP или GPS |
| HTTP Client | `aiohttp` | Async HTTP |
| ORM | `SQLAlchemy` | Database abstraction |
| Validation | `Pydantic` | Data validation |
| Telegram SDK | `telegram-web-app.js` (official) | TMA integration |
| File Storage | `boto3` | Yandex Object Storage |

---

## OSS Discovery

| Нужда | Поиск | Результат |
|-------|-------|-----------|
| PDF generation | `html to pdf python` | WeasyPrint ✅ |
| Geolocation | `yandex geolocation api` | Yandex Geolocation API ✅ |
| Telegram Mini App | `telegram web app camera` | `@twa/twa-library` ✅ |
| Photo storage | `yandex object storage python` | boto3 ✅ |

**Fork decision: BUILD** — специфичная связка Telegram Mini App + Yandex stack + PDF generation

---

## Пошаговый план

**Неделя 1: Фундамент**
- [ ] Репозиторий + GitHub Actions CI/CD
- [ ] FastAPI scaffold + Docker
- [ ] PostgreSQL schema (users, inspections, reports)
- [ ] Telegram Mini App shell (камера + геолокация)
- [ ] Yandex Object Storage integration

**Неделя 2: Ядро**
- [ ] Photo capture + metadata (timestamp, GPS, address)
- [ ] Inspection rooms/elements templates
- [ ] PDF generator (WeasyPrint)
- [ ] PDF template — акт приёма-передачи
- [ ] Email/SMS отправка PDF

**Неделя 3: Платежи + UX**
- [ ] Telegram Stars integration (Free → Starter upgrade)
- [ ] User dashboard (история актов)
- [ ] Storage optimization (compress photos)
- [ ] Bug fixes + testing

**Неделя 4: Деплой + Запуск**
- [ ] Timeweb VPS + Docker Compose
- [ ] SSL + домен .ru
- [ ] Soft launch: Telegram-каналы арендаторов
- [ ] Мониторинг первых пользователей

---

## Срок: 3 недели (+ буфер 30% = 4 недели)
## Команда: 1 full-stack Python developer

---

## MVP Cost (месяц)

| Статья | RUB |
|--------|-----|
| VPS (Timeweb: 2vCPU/4GB) | 1,500 |
| Yandex Object Storage (100GB) | 400 |
| Yandex Geolocation API | 500 |
| Database (Yandex Cloud) | 500 |
| Домен + SSL | 300 |
| **Итого** | **~3,200₽/мес** |

---

## MVP Scope

### Must Have (MVP)
- Telegram Mini App: ввод адреса, камера, фото
- Геолокация + timestamp
- Генерация PDF-акта (базовый template)
- Сохранение в историю
- Email/Telegram отправка PDF
- Free tier: 1 акт/мес

### Nice to Have (v1.1)
- Digital signature (ЭЦП)
- Insurance integration (страховые)
- Team access (риелторы)
- API access (B2B)

### Not MVP
- Push notifications
- Multiple languages
- White-label для агентств

---

*Дата: 2026-05-05*
