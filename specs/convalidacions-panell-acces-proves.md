# Convalidacions — control d'accés de proves en Direcció

Issue: #323
Status: ready_to_commit

## Traça i punt d'entrada

- Domini: Convalidacions.
- Punt d'entrada: bloc de control d'accés en `direccion/convalidacions/`.
- Documents/specs llegits: `AGENTS.md`, `docs/agents/conventions.md`, `specs/convalidacions.md` i la configuració/servei actuals de bloqueig.
- Abast: substituir el formulari actual per un interruptor accessible, fer evident quan l'accés de l'alumnat està bloquejat i mostrar en el panell protegit de Direcció la contrasenya de proves activa. No canvia el funcionament del bloqueig ni la validació de la contrasenya.

## Escenaris

### ✅ Escenari 1: estat obert i contrasenya visible a Direcció

Given una persona autenticada de Direcció en el panell de convalidacions
And l'accés de l'alumnat està obert
When consulta el control d'accés
Then veu un interruptor accessible en posició desactivada
And veu la contrasenya de proves activa que ha d'introduir l'alumnat quan el bloqueig s'active
And el bloc no ocupa l'espai ni té l'estil d'alerta groga reservat per al bloqueig

### ✅ Escenari 2: bloquejar l'accés de l'alumnat

Given una persona autenticada de Direcció en el panell de convalidacions
When activa l'interruptor
Then l'accés queda bloquejat amb el mecanisme actual i s'invaliden els accessos temporals anteriors
And l'interruptor apareix activat
And es mostra un requadre groc clar que indica que l'alumnat està restringit per les proves
And el panell mostra la contrasenya activa que l'alumnat necessita per accedir

### ✅ Escenari 3: tornar a obrir l'accés

Given l'accés de l'alumnat està bloquejat
When Direcció desactiva l'interruptor
Then l'accés queda obert amb el mecanisme actual
And desapareix l'avís groc de bloqueig
And l'interruptor apareix desactivat

### ✅ Escenari 4: limitar l'exposició de la contrasenya

Given la contrasenya està visible al panell de Direcció
When un alumne consulta la pantalla d'accés o les seues sol·licituds
Then no rep ni veu la contrasenya en HTML, missatges, respostes o logs
And els usuaris que no són de Direcció no poden accedir al panell que la mostra

## Regles de negoci

- L'interruptor conserva la persistència del bloqueig i la invalidació de sessions actuals mitjançant `ConvalidacioAccessService`; no es creen mecanismes paral·lels.
- El valor mostrat és la contrasenya efectiva de `config('convalidacions_access.password')`, inclosa la configuració `CONVALIDACIONS_ACCESS_PASSWORD` si està definida, no un valor duplicat en la vista.
- Només la vista protegida per rol de Direcció pot rebre i mostrar la contrasenya en clar. Continua sense exposar-se a alumnat, rutes públiques, APIs o logs.
- L'estat bloquejat es destaca amb un avís groc compacte i explícit; quan està obert, no es mostra eixe avís.
- El canvi d'estat s'envia al servidor amb protecció CSRF i continua limitat al perfil de Direcció.
- L'interruptor té etiqueta accessible i comunica textualment si l'accés està obert o restringit; no depén només del color.
- S'actualitza la regla de seguretat existent en `specs/convalidacions.md`: la contrasenya no s'exposa a l'alumnat ni a superfícies públiques, però es mostra en la vista privada de Direcció per a facilitar les proves.

## Fitxers afectats previstos

- `app/Http/Controllers/DireccionConvalidacioController.php` — proporcionar a la vista el valor configurat actiu.
- `resources/views/intranet/convalidacions/direccion/index.blade.php` — canviar el bloc actual pel switch i l'avís d'estat.
- `specs/convalidacions.md` — ajustar l'invariant d'exposició de la contrasenya amb l'excepció de Direcció.
- `tests/Feature/ConvalidacioFlowTest.php` — comprovar canvi d'estat, representació del switch/avís i absència de la contrasenya en les vistes d'alumnat.

## Riscos

- Mostrar la contrasenya en clar augmenta l'exposició a qualsevol persona que puga veure la sessió de Direcció; la vista ja està protegida pel middleware de rol i el valor no s'ha d'incloure en altres superfícies.
- La contrasenya pot diferir del valor per defecte si l'entorn configura `CONVALIDACIONS_ACCESS_PASSWORD`; cal mostrar el valor efectiu, no codificar el valor per defecte en Blade.
- El canvi visual no ha de convertir la lectura de l'estat en una acció: només un canvi de l'interruptor envia l'actualització.

Status: ready_to_commit
