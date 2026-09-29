# Convalidacions - avaluacions d'ITACA ordenades per any

Issue: #323
Status: ready_for_review

## Escenaris

### Escenari 1: any guardat en incorporar una avaluació

Given una exportació XML d'ITACA vàlida amb l'atribut curso del centre
When Direcció l'afig al gestor
Then el gestor llig l'any acadèmic del contingut XML cada vegada que prepara el llistat
And el nom del fitxer no determina l'any mostrat

### Escenari 2: ordre i recàrrega

Given diverses avaluacions d'anys diferents
When Direcció consulta el llistat
Then apareixen ordenades de l'any més recent al més antic
And pot eliminar una avaluació i tornar a pujar-la sense perdre l'ordre

### Escenari 3: accions no ambigües

Given una avaluació ja incorporada
When Direcció la consulta
Then només veu l'acció Eliminar
But no veu cap opció Substituir

### Escenari 4: període acadèmic llegible

Given una avaluació XML amb `centro@curso="2025"`
When Direcció consulta el llistat
Then la columna s'anomena «Període» i mostra «25-26»
And el període accessible conserva els anys complets «2025-2026»

## Regles de negoci

- L'any és metadada derivada de `centro@curso`; no es guarda en el títol ni en una taula separada.
- En eliminar l'XML, també desapareix la metadada derivada.
- El contingut XML no és descarregable ni accessible fora del procés de convalidacions.
