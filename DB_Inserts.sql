INSERT INTO osoby (imie, nazwisko, pesel, mail, rola, login, password) VALUES
('Jan', 'Kowalski', '12345678901', 'jan.kowalski@email.com', 'admin', 'admin', 'admin'),
('Anna', 'Nowak', '23456789012', 'anna.nowak@email.com', 'serwisant', 'serwisant', 'serwisant'),
('Marek', 'Nowakowski', '34567890123', 'marek.nowakowski@email.com', 'kierownik', 'kierownik', 'kierownik'),
('Ewa', 'Kaczmarek', '45678901234', 'ewa.kaczmarek@email.com', 'pracownik', 'pracownik1', 'pracownik1'),
('Tomasz', 'Kwiatkowski', '56789012345', 'tomasz.kwiatkowski@email.com', 'pracownik', 'pracownik2', 'pracownik2');

INSERT INTO pojazdy (marka, model, dane_serwisowe, rok_produkcji, status, data_przegladu, przebieg) VALUES
('Toyota', 'Corolla', 'Olej, filtry, hamulce', 2020, 'dostepny', '2025-01-01', 15000),
('Ford', 'Focus', 'Wymiana klockow hamulcowych, opony', 2019, 'dostepny', '2025-02-01', 22000),
('Volkswagen', 'Golf', 'Wymiana plynow eksploatacyjnych', 2021, 'dostepny', '2025-03-01', 12000),
('BMW', '320i', 'Sprawdzenie ukladu chlodzenia, olej', 2022, 'dostepny', '2025-04-01', 8000),
('Audi', 'A4', 'Wymiana akumulatora, opony', 2020, 'dostepny', '2025-05-01', 25000),
('Mercedes', 'C-Class', 'Wymiana filtrow, hamulce', 2023, 'dostepny', '2025-06-01', 5000),
('Hyundai', 'i30', 'Wymiana rozrzadu, olej', 2018, 'dostepny', '2025-07-01', 30000),
('Kia', 'Ceed', 'Przeglad klimatyzacji, plyn chlodniczy', 2021, 'dostepny', '2025-08-01', 17000),
('Skoda', 'Octavia', 'Wymiana klockow, olej', 2022, 'dostepny', '2025-09-01', 10000),
('Peugeot', '308', 'Przeglad techniczny, opony', 2020, 'dostepny', '2025-10-01', 20000);
