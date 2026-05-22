# Istruzioni permanenti per Codex

Queste istruzioni valgono per tutti i progetti di Massimo, salvo indicazione esplicita contraria.

## Principi di collaborazione

- Il codice e l'architettura appartengono all'utente. Codex deve adattarsi allo stile del progetto, non imporre uno stile standard esterno.
- Le scelte non convenzionali, personali o fantasiose devono essere trattate come intenzionali fino a prova contraria.
- Codex deve distinguere chiaramente tra problemi concreti, bug, rischi di sicurezza o manutenzione e semplici differenze rispetto alle pratiche comuni.
- Codex deve proporre modifiche piccole, comprensibili e reversibili, spiegando prima l'intento quando tocca parti importanti del codice.

## Backend, database e query

- Non introdurre framework backend se l'utente non lo chiede esplicitamente.
- Non proporre ORM, Entity Framework, mapper, repository framework o layer dati standardizzati in sostituzione del sistema esistente.
- AdvQuery e' il motore centrale per gestione query, sicurezza query e accesso ai dati.
- AdvQuery non deve essere sostituita, aggirata, duplicata o normalizzata verso framework esterni.
- Qualsiasi modifica che riguarda query, dati, sicurezza, backend o persistenza deve integrarsi con AdvQuery e rispettarne il ruolo centrale.

## Regola operativa

Quando Codex lavora su codice dell'utente, deve prima capire il sistema esistente e poi intervenire dentro quel sistema. Non deve trasformare il progetto in una versione conforme al gusto medio del settore.

## Regola di sintassi

Le regole di sintassi sono le stesse per ogni linguaggio al fine di evitare codici completamente differenti tra loro.
Le variabili sono scritte sempre in Pascal Style
Le costanti sono sempre in maiuscolo
Le variabili private hanno sempre il prefisso __. Ad esempio __Contatore. Fanno eccezione solo Pascal e PHP dove le private iniziano sempre per F.
Ignora sempre le convenzioni sui nomi delle variabili relative al linguaggio utilizzato.

## Divieto Git

- Non usare mai Git in questo repository, salvo richiesta esplicita dell'utente in quello specifico messaggio.
- Non eseguire mai comandi `git`, inclusi ma non limitati a `git status`, `git add`, `git commit`, `git push`, `git pull`, `git checkout`, `git reset`, `git merge`, `git rebase` o simili.
- Non fare mai push, commit, merge, rebase, checkout, reset o altre operazioni che interagiscano con Git.
- Se serve conoscere lo stato dei file, usare strumenti del filesystem o chiedere conferma all'utente, senza invocare Git.
