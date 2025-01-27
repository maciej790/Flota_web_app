-- Tworzenie bazy danych flota
CREATE DATABASE flota_utf8_WIN1250
WITH
ENCODING 'WIN1250'
LC_COLLATE 'Polish_Poland.1250'
LC_CTYPE 'Polish_Poland.1250'
TEMPLATE template0;

-- Użycie bazy danych
\c flota;

-- Tabela osoby
CREATE TABLE "osoby"
(
  "id_osoby" BigSerial NOT NULL,
  "imie" Character varying NOT NULL,
  "nazwisko" Character varying NOT NULL,
  "pesel" Character varying NOT NULL,
  "mail" Character varying NOT NULL,
  "rola" Character varying NOT NULL,
  "login" Character varying,
  "password" Character varying,
  PRIMARY KEY ("id_osoby"),
  UNIQUE ("pesel")
)
WITH (
  autovacuum_enabled = true
);

-- Tabela pojazdy
CREATE TABLE "pojazdy"
(
  "id_pojazdu" BigSerial NOT NULL,
  "marka" Character varying NOT NULL,
  "model" Character varying NOT NULL,
  "dane_serwisowe" Character varying,
  "naped" Character varying NOT NULL,
  "paliwo" Integer,
  "status" Character varying NOT NULL,
  "data_przegladu" Date NOT NULL,
  "przebieg" Character varying,
  PRIMARY KEY ("id_pojazdu"),
  UNIQUE ("id_pojazdu")
)
WITH (
  autovacuum_enabled = true
);

-- Tabela wypozyczenia
CREATE TABLE "wypozyczenia"
(
  "id_wypozyczenia" BigSerial NOT NULL,
  "id_osoby" Bigint NOT NULL,
  "id_pojazdu" Bigint NOT NULL,
  "data_poczatek" Date NOT NULL,
  "data_koniec" Date NOT NULL,
  PRIMARY KEY ("id_wypozyczenia", "id_osoby", "id_pojazdu")
)
WITH (
  autovacuum_enabled = true
);

-- Tabela zapytania
CREATE TABLE "zapytania"
(
  "id_zapytania" BigSerial NOT NULL,
  "id_osoby" Bigint NOT NULL,
  "id_pojazdu" Bigint NOT NULL,
  "data_poczatek" Date,
  "data_koniec" Date,
  "uzasadnienie" Character varying,
  "decyzja" Boolean,
  PRIMARY KEY ("id_zapytania", "id_osoby", "id_pojazdu"),
  UNIQUE ("id_zapytania")
)
WITH (
  autovacuum_enabled = true
);

-- Klucze obce dla wypozyczenia
ALTER TABLE "wypozyczenia"
  ADD CONSTRAINT "Relationship1"
    FOREIGN KEY ("id_osoby")
    REFERENCES "osoby" ("id_osoby")
      ON DELETE NO ACTION
      ON UPDATE NO ACTION;

ALTER TABLE "wypozyczenia"
  ADD CONSTRAINT "Relationship2"
    FOREIGN KEY ("id_pojazdu")
    REFERENCES "pojazdy" ("id_pojazdu")
      ON DELETE NO ACTION
      ON UPDATE NO ACTION;

-- Klucze obce dla zapytania
ALTER TABLE "zapytania"
  ADD CONSTRAINT "Relationship3"
    FOREIGN KEY ("id_osoby")
    REFERENCES "osoby" ("id_osoby")
      ON DELETE NO ACTION
      ON UPDATE NO ACTION;

ALTER TABLE "zapytania"
  ADD CONSTRAINT "Relationship4"
    FOREIGN KEY ("id_pojazdu")
    REFERENCES "pojazdy" ("id_pojazdu")
      ON DELETE NO ACTION
      ON UPDATE NO ACTION;


