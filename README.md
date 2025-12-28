# Minimalny Todo/Jira/Trello (PHP) + Docker + docker-compose + CI (GitHub Actions)

Ten projekt spełnia wymagania zadania w wersji **minimalnej**:
- Aplikacja webowa w PHP (kanban: **TODO / DOING / DONE**)
- Konteneryzacja: **Dockerfile**
- Uruchamianie wielu usług: **docker-compose (app + PostgreSQL)**
- Pipeline **CI**: **build → test** (GitHub Actions)
- Pipeline uruchamia się automatycznie po każdym pushu na `main` oraz dla Pull Requestów.

> W tej wersji **nie robimy automatycznego deployu na serwer/VPS** (zgodnie z Twoją decyzją).  
> Masz za to pełną automatyzację budowania i testów po każdej zmianie w repo.

---

## 0) Wymagania (lokalnie)
- Git
- Docker + Docker Compose
- Konto na GitHub (repozytorium)

---

## 1) Jak działa aplikacja
- Lista zadań w 3 kolumnach: TODO, DOING, DONE
- Dodawanie zadania
- Przenoszenie między kolumnami
- Usuwanie
- Endpoint `GET /health.php` do testów

Baza: PostgreSQL (tabela `tasks` tworzy się automatycznie przy starcie).

---

## 2) Uruchomienie lokalne w Docker

### Start
W katalogu projektu:

```bash
docker compose up -d --build
```

Aplikacja: http://localhost:8080

Health:
```bash
curl -i http://localhost:8080/health.php
```

### Stop
```bash
docker compose down
```

---

## 3) Repozytorium (GitHub) – krok po kroku

1. GitHub → **New repository** (np. `todolist-devops-php`)
2. W katalogu projektu:

```bash
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin <URL_TWOJEGO_REPO>
git push -u origin main
```

Po pushu pipeline GitHub Actions odpali się automatycznie.

---

## 4) CI (GitHub Actions)

Workflow: `.github/workflows/ci-cd.yml`

### Etapy
- **build**: buduje obraz Dockera (bez wysyłania do registry)
- **test**: uruchamia `docker compose` i robi `curl` na `/health.php`

Test integracyjny: `tests/smoke.sh`

---

## 5) Checklist do oddania
- [x] Repozytorium z kodem (GitHub)
- [x] Dockerfile
- [x] docker-compose (app + db)
- [x] Działanie lokalnie + healthcheck
- [x] CI: build + test po każdym pushu

---

## Struktura projektu
```
.
├─ public/
│  ├─ index.php
│  ├─ health.php
├─ src/
│  ├─ db.php
│  └─ tasks.php
├─ tests/
│  └─ smoke.sh
├─ .github/workflows/ci-cd.yml
├─ Dockerfile
├─ docker-compose.yml
├─ .env.example
└─ README.md
```
