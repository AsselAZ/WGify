# WGify – WG-Manager

WGify ist eine Webanwendung zur Verwaltung einer Wohngemeinschaft.
Mitglieder, Aufgaben und Ausgaben können über ein Vue-Frontend verwaltet und über ein Laravel-Backend gespeichert werden.

## Voraussetzungen

Installiert sein müssen:

* Git
* Node.js und npm
* PHP
* Composer

## Projekt klonen

```bash
git clone https://github.com/AsselAZ/WGify.git
cd WGify
```

## Backend starten

In den Backend-Ordner wechseln:

```bash
cd backend
```

Abhängigkeiten installieren:

```bash
composer install
```

`.env`-Datei erstellen:

Für Windows CMD:

```cmd
copy .env.example .env
```

Für Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Für Mac/Linux:

```bash
cp .env.example .env
```

App-Key erzeugen:

```bash
php artisan key:generate
```

SQLite-Datei erstellen:

Für Windows CMD:

```cmd
type nul > database\database.sqlite
```

Für Windows PowerShell:

```powershell
New-Item database/database.sqlite -ItemType File
```

Für Mac/Linux:

```bash
touch database/database.sqlite
```

Datenbanktabellen erstellen:

```bash
php artisan migrate
```

Backend starten:

```bash
php artisan serve
```

Das Backend läuft unter:

```text
http://127.0.0.1:8000
```

## Frontend starten

Neues Terminal öffnen und in den Frontend-Ordner wechseln:

```bash
cd WGify
cd Frontend
```

Abhängigkeiten installieren:

```bash
npm install
```

Frontend starten:

```bash
npm run dev
```

Das Frontend läuft unter:

```text
http://localhost:5173
```

## Wichtig

Das Backend muss laufen, damit Daten gespeichert und geladen werden können.



## Dateien von Git ignorieren

Folgende Dateien und Ordner sollen nicht auf GitHub hochgeladen werden:

```text
backend/.env
backend/vendor/
backend/database/database.sqlite
Frontend/node_modules/
```

Diese Einträge müssen in die `.gitignore`:

```gitignore
backend/.env
backend/vendor/
backend/database/database.sqlite
Frontend/node_modules/
```

Einträge per Befehl hinzufügen:

Für Windows CMD:

```cmd
echo backend/.env>> .gitignore
echo backend/vendor/>> .gitignore
echo backend/database/database.sqlite>> .gitignore
echo Frontend/node_modules/>> .gitignore
```

Für Windows PowerShell:

```powershell
Add-Content .gitignore "`nbackend/.env`nbackend/vendor/`nbackend/database/database.sqlite`nFrontend/node_modules/"
```

Für Mac/Linux:

```bash
printf "\nbackend/.env\nbackend/vendor/\nbackend/database/database.sqlite\nFrontend/node_modules/\n" >> .gitignore
```

Falls diese Dateien bereits von Git verfolgt werden:

```bash
git rm --cached backend/.env
git rm --cached backend/database/database.sqlite
git rm --cached -r backend/vendor
git rm --cached -r Frontend/node_modules
```

Änderungen speichern und hochladen:

```bash
git add .gitignore
git commit -m "Ignore local project files"
git push
```

## Änderungen herunterladen

```bash
git pull
```

## Änderungen hochladen

```bash
git add .
git commit -m "Update project"
git push
```

## Falls neue Datenbanktabellen dazukommen

```bash
cd backend
php artisan migrate
```

## Falls neue Backend-Abhängigkeiten dazukommen

```bash
cd backend
composer install
```

## Falls neue Frontend-Abhängigkeiten dazukommen

```bash
cd Frontend
npm install
```
