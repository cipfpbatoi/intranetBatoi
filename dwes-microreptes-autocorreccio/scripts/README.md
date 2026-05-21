# Scripts

- `validate-config.mjs`: valida fitxers globals, `course/active-challenges.json` i coherència bàsica de microreptes.
- `list-challenges.mjs`: mostra els microreptes disponibles.
- `resolve-active-challenge.mjs`: resol el microrepte actiu per alumne o grup.

## Exemples

```bash
npm run validate
npm run list:challenges
npm run resolve:challenge -- --student cipfpbatoi/dwes-ana-marti --group 2DAW-A
node scripts/resolve-active-challenge.mjs --student cipfpbatoi/dwes-pau-garcia --group 2DAW-B
```

El resolver imprimeix només el `challenge_id` quan tot va bé. Si no troba assignació, ix amb codi `1`.
