# Flux de treball

## Flux previst

1. El professor defineix el microrepte.
2. L'alumne treballa al seu repositori.
3. L'alumne fa `push`.
4. El workflow resol el microrepte actiu des del repositori central.
5. El workflow prepara la correcció.
6. Es genera feedback provisional.
7. El professor revisa si cal.

## Microrepte actiu centralitzat pel professor

El fitxer `course/active-challenges.json` indica el microrepte actiu per grup i possibles overrides per alumne.

- Si hi ha override d'alumne, guanya l'alumne.
- Si no hi ha override, s'usa el grup.
- Si no hi ha assignació, el workflow ha de fallar amb un error clar.
