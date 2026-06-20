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

## Dokumentacja (wygenerowana automatycznie w katalogu Dokumentacja -> index.html)

Generowanie dokumentacji:

```bash
vendor/bin/phpdoc -d src -t docs/api
```

Otwieranie dokumentacji:

```bash
open docs/api/index.html
```

## Wymagania funkcjonalne

1. System umożliwia użytkownikowi logowanie do aplikacji.
2. System rozróżnia role użytkowników: student, pracownik BON oraz administrator.
3. System umożliwia studentowi utworzenie nowego wniosku.
4. System umożliwia studentowi przechodzenie przez kolejne kroki formularza wniosku.
5. System umożliwia zapis danych osobowych oraz danych dotyczących studiów.
6. System umożliwia dodanie informacji o wymaganych adaptacjach.
7. System umożliwia dodawanie załączników do wniosku.
8. System umożliwia podgląd podsumowania wniosku przed wysłaniem.
9. System umożliwia wysłanie gotowego wniosku.
10. System umożliwia studentowi podgląd statusu złożonego wniosku.
11. System umożliwia pracownikowi BON przeglądanie złożonych wniosków.
12. System umożliwia pracownikowi BON weryfikację wniosku.
13. System umożliwia pracownikowi BON dodanie komentarza do wniosku.
14. System umożliwia zmianę statusu wniosku.
15. System umożliwia studentowi zapisanie się na konsultację.
16. System umożliwia studentowi zapisanie się na wizytę w biurze BON.
17. System umożliwia studentowi podgląd zarezerwowanego terminu konsultacji lub wizyty.
18. System umożliwia pracownikowi BON tworzenie terminów konsultacji.
19. System umożliwia pracownikowi BON tworzenie terminów wizyt w biurze BON.
20. System automatycznie tworzy spotkanie Microsoft Teams podczas tworzenia konsultacji online.
21. System zapisuje link do spotkania Teams przy terminie konsultacji.
22. System umożliwia administratorowi zarządzanie danymi słownikowymi.
23. System umożliwia administratorowi zarządzanie użytkownikami.
24. System waliduje dane wprowadzane w formularzach.
25. System zapisuje dane w relacyjnej bazie danych.

## Diagram ERD (wygenerowany już w katalogu Diagramy)

```bash
php bin/console doctrine:diagram:er --filename=docs/diagrams/database --format=svg
```

## Diagram UML (wygenerowany już w katalogu Diagramy)

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

## Wymagania niefunkcjonalne

1. Aplikacja powinna być dostępna przez przeglądarkę internetową.
2. System powinien być wykonany w technologii Symfony.
3. Dane powinny być przechowywane w relacyjnej bazie danych MySQL/MariaDB.
4. System powinien posiadać podział na warstwę prezentacji, logiki biznesowej i dostępu do danych.
5. Interfejs użytkownika powinien być czytelny i responsywny.
6. System powinien walidować dane po stronie serwera.
7. Dostęp do funkcji systemu powinien być ograniczony na podstawie roli użytkownika.
8. Hasła użytkowników powinny być przechowywane w postaci zahashowanej.
9. System powinien zapewniać kontrolę dostępu do danych wniosków oraz konsultacji.
10. Integracja z Microsoft Teams powinna automatyzować tworzenie spotkań online.
11. System powinien zapisywać informacje o terminach konsultacji i wizyt w sposób spójny z bazą danych.
12. Kod aplikacji powinien być zgodny ze strukturą projektu Symfony.
13. System powinien umożliwiać łatwe uruchomienie w środowisku developerskim.
14. Aplikacja powinna umożliwiać dalszą rozbudowę o kolejne typy wniosków i konsultacji.
15. Dokumentacja techniczna powinna zawierać opis kodu, diagram klas oraz diagram bazy danych.

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
