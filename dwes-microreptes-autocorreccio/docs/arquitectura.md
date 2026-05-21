# Arquitectura

## Visio conceptual

- Repositori central del professor:
  - microreptes;
  - rúbriques;
  - polítiques;
  - microrepte actiu per grup o alumne;
  - workflows de validació.
- Repositoris individuals d'alumnes:
  - codi i evidències;
  - cap configuració local del microrepte actiu.
- GitHub Actions:
  - valida configuració;
  - resol el microrepte actiu;
  - prepara el circuit d'autocorrecció.
- Futur connector amb OpenAI:
  - rebrà un payload acotat;
  - retornarà resposta estructurada;
  - no tancarà casos amb baixa confiança.
- Registre de notes:
  - guardarà resultats provisionals;
  - diferenciarà nota automàtica i nota docent.

## Esquema textual

```text
Professorat -> repositori central -> configuracio, microreptes, rubriques
Alumnat -> repositori individual -> codi, proves, evidencies
Workflow -> resol microrepte -> valida -> genera feedback provisional
```

## Decisions inicials

- No s'assumeix cap framework d'alumne.
- Les rúbriques i assignacions són JSON.
- La nota automàtica és provisional.
- La revisió docent és obligatòria amb flags o baixa confiança.
