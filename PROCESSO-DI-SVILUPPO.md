# Processo di sviluppo — Asset Manager

## Panoramica veloce

Obiettivo: Dashboard per la gestione degli asset aziendali con una history degli owners, UI in Vue/Inertia, pronto per
build Docker prod (sulla porta 3000).  
Stack: Laravel 12, Vue 3 + Inertia, Tailwind, Postgres, Docker.

---

## Come ho lavorato (in breve)

1. **Bootstrap**: setup progetto, DB Postgres via docker compose, creazione delle migrazioni iniziali
2. **Owners**: configurazione della tabella `owners` con `first_name`, `last_name`, soft deletes, indici.
3. **Assets**: configurazione della tabella `assets` con `reference`, `serial_number (unique)`, `description`, più *
   *`current_owner_id`** e **`current_owned_from`** (denormalizzazione utile per l’indice).
4. **History**: configurazione della tabella `asset_owner_assignments` per la timeline (owned_from → owned_to).
5. **Vincoli DB** (Postgres):
    - **Partial unique**: al massimo una riga “aperta” per asset (`owned_to IS NULL`).
    - **CHECK** coerenza date (es. `owned_to >= owned_from` per evitare che la data di partenza possa essere impostato
      dopo la data di fine) e per i campi correnti (entrambi null o entrambi valorizzati).
6. **Business rule**: action `AssignOwnerToAsset` (transazionale) che chiude/apre i periodi e aggiorna i campi correnti.
7. **UI**: pagine Inertia
    - **Index**: lista con paginazione, owner corrente, azioni.
    - **Create/Edit**: form completo; in edit degli assets
    - **history** History degli owners per ogni assets
8. **Auth**: login tramite password e mail + OTP validation. Rotte app dietro `auth`.
9. **Prod**: build frontend, cache config/route/view, Docker multi-stage, porta **3000**.

---

## Decisioni chiave (e perché)

- **Owners normalizzati + history separata** → dati puliti e timeline chiara, a mio parere più semplice anche la
  gestione
  a DB
- **`current_owner_id` su `assets`** → lista velocissima senza join pesanti.
- **Action per il cambio owner** → logica concentrata in una classe, transazioni chiare, facile da testare.
- **Vincoli in DB** → molte incoerenze diventano impossibili (meno bug applicativi).

---

## Regola di business (cambio owner)

`AssignOwnerToAsset($asset, $newOwner|null, $whenUtc)`:

- lock riga asset (`lockForUpdate`);
- se owner non cambia → **no-op**;
- chiude eventuali righe “aperta” (set `owned_to`);
- se `newOwner` è `null` → azzera `current_*` e stop;
- altrimenti crea nuova riga “aperta” e aggiorna `current_*`.

---

## UI (Inertia + Vue)

- **Index.vue**: tabella (Reference, Serial, Owned By, Owned From, Azioni) + paginazione.
- **Create/Edit.vue**: form; select owner; `datetime-local` per `owned_from` (convertito in **ISO UTC** prima
  dell’invio); in edit mostro la **history**.
- Tipi TS condivisi in `@/types/models` (leggibile, niente abbreviazioni).

---

## Problemi incontrati (e fix)

- **Eloquent cache relazioni**: dopo aver cambiato cambiato `current_owner_id`, `$asset->owner` restava il "vecchio"
  owner associato.  
  → così ho optato per un uso `->refresh()` o `->load('owner')` dopo il salvataggio.
- **Unique con soft deletes** su owners: il semplice `unique(first,last)` blocca anche i record soft-deleted.  
  → risolto con un **unique parziale** con `WHERE deleted_at IS NULL` e `lower()`.

---

## Docker / Production-ready (checklist)

- Build multi-stage: `composer install --no-dev`, `npm ci && npm run build`.
- Laravel optimize: `config:cache`, `route:cache`, `view:cache`.
- Migrate on start (`php artisan migrate --force`), log a stdout.
- Porta **3000** esposta; `APP_URL=http://localhost:3000`; `APP_DEBUG=false`.
- Compose con Postgres e volume per la persistenza.

---

## Extra (MFA via email)

- Flag `users.mfa_email_enabled`.
- Tabella `mfa_email_codes` (hash codice, scadenza 10 min, tentativi, resend cooldown).
- Flusso: login → invio codice → `/mfa/challenge` → verifica → `session('mfa_passed')` → accesso.
- Middleware `mfa.verified` per bloccare l’app finché non verifichi.
- Mailer di default su `log` (README spiega come usare SMTP/Resend).

---
