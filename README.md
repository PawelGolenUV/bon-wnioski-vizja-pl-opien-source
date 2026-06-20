# Bon Wnioski Wizja PL

Aplikacja webowa wykonana w Symfony do obsługi wniosków studenckich, zapisów na konsultacje oraz wizyt w biurze BON.
Jest również dostępna w wersji produkcyjnej na help.vizja.pl

## Technologie

- PHP 8.x
- Symfony 7.x
- Doctrine ORM
- MySQL / MariaDB
- Twig
- Tailwind CSS / Flowbite
- Composer

## Instalacja projektu

### Instalacja zależności

```bash
composer install
```

### Instalacja zależności frontendowych

Jeżeli projekt wykorzystuje Tailwind CSS, Webpack Encore lub AssetMapper z dodatkowymi bibliotekami JavaScript, należy zainstalować zależności frontendowe:

#### Yarn

```bash
yarn install
```

#### NPM

```bash
npm install
```

### Budowanie assetów

Tryb developerski:

```bash
yarn dev
```

lub

```bash
npm run dev
```

Tryb produkcyjny:

```bash
yarn build
```

lub

```bash
npm run build
```

### Konfiguracja środowiska

Skopiuj plik `.env`:

```bash
cp .env .env.local
```

Skonfiguruj połączenie z bazą danych:

```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/bon_wnioski?serverVersion=8.0"
```

## Baza danych

### Utworzenie bazy

```bash
php bin/console doctrine:database:create
```

### Migracje

```bash
php bin/console doctrine:migrations:migrate
```

### Fixtures

```bash
php bin/console doctrine:fixtures:load
```

Lub bez czyszczenia danych:

```bash
php bin/console doctrine:fixtures:load --append
```

## Uruchomienie aplikacji

### Symfony CLI

```bash
make start
```

## Przydatne komendy

### Cache

```bash
make clear
```

### Trasy

```bash
php bin/console debug:router
```

### Serwisy

```bash
php bin/console debug:container
```

### Encje

```bash
php bin/console doctrine:mapping:info
```

## Dokumentacja

Generowanie dokumentacji:

```bash
vendor/bin/phpdoc -d src -t docs/api
```

Otwieranie dokumentacji:

```bash
open docs/api/index.html
```

## Diagram ERD

```bash
php bin/console doctrine:diagram:er --filename=docs/diagrams/database --format=svg
```

## Diagram UML

```bash
php bin/console doctrine:diagram:class --filename=docs/diagrams/uml --format=puml
```

Jeżeli PlantUML nie obsługuje namespace'ów:

```bash
cp docs/diagrams/uml.puml docs/diagrams/uml-fixed.puml
perl -pi -e 's/\\\\/./g' docs/diagrams/uml-fixed.puml
plantuml -tsvg docs/diagrams/uml-fixed.puml
```

## Testowanie

W projekcie nie zaimplementowano automatycznych testów jednostkowych.

Testowanie zostało przeprowadzone ręcznie poprzez sprawdzenie:

- logowania użytkownika,
- tworzenia nowego wniosku,
- przechodzenia pomiędzy krokami formularza,
- walidacji pól formularzy,
- dodawania załączników,
- zapisu danych w bazie,
- wyświetlania podsumowania wniosku,
- działania panelu administracyjnego,
- obsługi błędnych danych wejściowych.

Testy ręczne wykonano w środowisku developerskim Symfony.

## Struktura projektu

```text
src/
├── Controller/
├── Database/Entity/
├── Form/
├── Repository/
├── Service/
└── Security/

templates/
├── student/
├── admin/
└── base.html.twig

docs/
├── api/
└── diagrams/
```

## Autor

Projekt wykonany na potrzeby zaliczenia ćwiczeń.
