# 🍕 Pepperoni Manager

## 📌 Descrizione

**Pepperoni Manager** è una web application sviluppata in PHP per la gestione di un sistema di ordinazione pizze.

---

## 🛠️ Tecnologie utilizzate

- PHP
- MySQL
- HTML
- CSS
- JavaScript

---

## 🚀 Come avviare l'applicazione

1. Copia il progetto nella directory del server locale:
   - `htdocs` (XAMPP)
   - `www` (WAMP)

2. Avvia il server Apache e MySQL

3. Importa il database MySQL (se presente nel progetto)

4. Apri il browser e visita: http://localhost/PepperoniManager/pages/login.php

---

## ⚠️ Requisiti

- PHP >= 8.x
- MySQL / MariaDB
- Server locale (XAMPP, WAMP o simili)

---

# 📁 Struttura delle cartelle

## 🖥️ `pages/`

Contiene tutte le pagine di interfaccia utente (HTML + PHP minimale).

- Visualizzazione dati
- Form di input
- Nessuna logica SQL diretta (o minima)
- Si occupa solo della presentazione

---

## ⚙️ `actions/`

Contiene la logica applicativa legata alle operazioni POST.

- Gestione form submission
- Validazione dati
- Apertura transazioni
- Redirect finali

---

## 🗄️ `queries/`

Contiene tutte le funzioni dedicate all’accesso al database.

- Query SQL centralizzate
- Operazioni CRUD riutilizzabili
- Nessuna logica di presentazione

---

## 🔐 `config/`

Contiene configurazioni e utility globali.

- Connessione al database
- Gestione sessioni
- Funzioni di autenticazione

---

## 🎨 `assets/`

Contiene risorse statiche.

- CSS
- JavaScript

---

# 👥 Gestione ruoli

Il sistema prevede 3 ruoli principali:

- **UTENTE**
  - crea ordini
  - visualizza storico
  - inserisce recensioni

- **DIPENDENTE**
  - gestisce ordini
  - accetta e completa consegne
  - visualizza turni

- **AMMINISTRATORE**
  - gestione utenti e dipendenti
  - gestione veicoli e revisioni
  - inserimento menu (pizze e ingredienti)
  - visualizzazione statistiche

---

# 🧠 Architettura logica

Il progetto segue una separazione in stile **light MVC**:

- **Pages** → View (UI)
- **Actions** → Controller (logica operativa)
- **Queries** → Model (accesso ai dati)

---

# 🔄 Flusso tipico di una operazione

1. L’utente interagisce con una pagina (`pages/`)
2. Il form invia dati a una action (`actions/`)
3. L’action:
   - valida i dati
   - richiama funzioni in `queries/`
   - esegue eventuali transazioni
4. Il risultato viene reindirizzato alla pagina di visualizzazione
