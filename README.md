# Guerrilla

**Guerrilla** jest szablonem projektu opartym na [Concrete CMS](https://www.concretecms.com/) w wersji **9.5**.

Zawiera gotowy pakiet motywu (`packages/guerrilla`) z pełną strukturą Concrete CMS 9.x, Bootstrap 5 i przykładowymi typami stron.

---

## Wymagania

| Zależność | Wersja |
|---|---|
| PHP | ≥ 8.1 |
| MySQL / MariaDB | ≥ 8.0 / 10.4 |
| Composer | ≥ 2.x |
| Node.js (opcjonalnie, dla frontendu) | ≥ 18 |

---

## Instalacja

### 1. Klonowanie repozytorium

```bash
git clone https://github.com/jagdpanzer4/guerrilla.git
cd guerrilla
```

### 2. Instalacja zależności PHP

```bash
composer install
```

### 3. Konfiguracja środowiska

```bash
cp .env.example .env
# Uzupełnij dane bazy danych i URL w pliku .env
```

### 4. Instalacja Concrete CMS

Otwórz przeglądarkę i wejdź pod adres skonfigurowany w `APP_URL`.  
Postępuj zgodnie z kreatorem instalacji Concrete CMS.

### 5. Aktywacja motywu

1. Zaloguj się do panelu administracyjnego (`/index.php/login`).
2. Przejdź do **Dashboard → Extend Concrete → Packages**.
3. Zainstaluj pakiet **Guerrilla**.
4. Przejdź do **Dashboard → Pages & Themes → Themes** i aktywuj motyw **Guerrilla**.

---

## Struktura projektu

```
Guerrilla/
├── application/          # Konfiguracja aplikacji (generowana podczas instalacji)
│   ├── config/
│   └── languages/
├── concrete/             # Rdzeń Concrete CMS (generowany przez Composer, gitignore)
├── packages/
│   └── guerrilla/        # Pakiet motywu Guerrilla
│       ├── controller.php
│       └── themes/
│           └── guerrilla/
│               ├── css/
│               ├── js/
│               ├── images/
│               ├── elements/    # header.php, footer.php
│               ├── page_types/  # Szablony typów stron
│               ├── default.php
│               └── page_theme.php
├── files/                # Pliki użytkowników (gitignore)
├── updates/              # Aktualizacje rdzenia (gitignore)
├── .env.example
└── composer.json
```

---

## Rozwój motywu

Pliki motywu znajdują się w `packages/guerrilla/themes/guerrilla/`.

| Plik / Katalog | Opis |
|---|---|
| `page_theme.php` | Definicja klasy motywu |
| `default.php` | Domyślny szablon strony |
| `elements/header.php` | Nagłówek strony |
| `elements/footer.php` | Stopka strony |
| `page_types/full.php` | Typ strony – pełna szerokość |
| `page_types/left_sidebar.php` | Typ strony – lewy sidebar |
| `css/main.css` | Główny arkusz stylów |
| `js/main.js` | Główny skrypt JavaScript |

---

## Licencja

MIT – szczegóły w pliku [LICENSE](LICENSE).
