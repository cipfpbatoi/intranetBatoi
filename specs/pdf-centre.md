# Spec: capçalera i peu dels documents del centre

Abast aprovat: substituir la capçalera del desplegament i incorporar la imatge
de peu en totes les pàgines dels documents que inclouen la capçalera comuna,
també mitjançant parcials compartits. Els recursos gràfics són propis de cada
centre i es despleguen separadament del codi.

## Escenaris

### 1. ✅ Capçalera proporcional

**Given** un document que inclou `pdf.partials.cabecera`  
**When** es genera el PDF  
**Then** es mostra `public/img/pdf/cabecera.png` sense deformar la imatge.

### 2. ✅ Peu en cada pàgina

**Given** un document amb la capçalera comuna i una imatge `public/img/pdf/pie.png`  
**When** es genera el PDF en vertical o horitzontal amb Snappy o DomPDF  
**Then** cada pàgina inclou la imatge del peu amb espai reservat, sense solapar
el contingut, i conserva el codi i l'edició documental si estan configurats.

### 3. ✅ Recursos opcionals del desplegament

**Given** un centre sense imatge de peu, o un document amb una altra capçalera  
**When** es genera el PDF  
**Then** es conserva el peu documental anterior.

## Fitxers i verificació

- `app/Services/Document/PdfService.php`: peu natiu Snappy, també en ZIP, i peu fix DomPDF.
- `resources/views/pdf/partials/cabecera.blade.php`: imatge proporcional i marcador de capçalera comuna.
- `resources/views/pdf/partials/pieCentre.blade.php`: document HTML del peu natiu.
- `resources/views/pdf/partials/imatgePeuCentre.blade.php`: imatge i codi compartits.
- `tests/Unit/Services/PdfServiceTest.php`: 5 proves i 12 assercions correctes.
- Verificació de PDFs amb dades sintètiques: Snappy vertical (2 pàgines),
  Snappy horitzontal (3), DomPDF vertical (3) i DomPDF horitzontal (4).
  Codi present en cada pàgina i comprovació visual del peu i la capçalera.
