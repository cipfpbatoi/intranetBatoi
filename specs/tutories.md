# Tutories

Especificació del seguiment de feedback de tutories i dels avisos interns als tutors.

## Escenari 1: percentatge de realització

**Given** una tutoria aplicable a quatre grups i tres feedbacks vàlids  
**When** Orientació consulta el llistat  
**Then** veu `3 / 4 (75%)`.

## Escenari 2: observacions sense contingut

**Given** registres amb observacions buides, espais, `&nbsp;` o HTML sense text  
**When** es calcula el progrés  
**Then** estos registres no compten com a feedback realitzat.

## Escenari 3: grups aplicables

**Given** una tutoria configurada per a tots els grups, grau mitjà o grau superior  
**When** es calcula el progrés o els pendents  
**Then** s'aplica la correspondència `0` tots, `1` grau mitjà i `2` grau superior.

## Escenari 4: avís després del termini

**Given** una tutoria finalitzada i un grup aplicable sense feedback vàlid  
**When** s'executa la comprovació diària  
**Then** el tutor rep una notificació interna amb enllaç al formulari de feedback  
**And** no s'envia cap correu electrònic.

## Escenari 5: execució idempotent

**Given** que ja consta un avís per a una combinació de tutoria i grup  
**When** la comprovació torna a executar-se  
**Then** no es crea una altra notificació per a la mateixa combinació.

## Regles de negoci

- Un feedback només és vàlid si conserva text després d'eliminar HTML, entitats i espais invisibles.
- El mateix conjunt de grups aplicables s'utilitza en la graella i en els avisos.
- Les tutories amb `hasta` anterior a hui es revisen diàriament, incloses les no processades en dies anteriors.
- Els grups sense tutor compten en el progrés, però no generen notificació.
- La unicitat de l'avís es garanteix en base de dades per tutoria i grup.
