# SAO I Selenium

## Fitxers Clau

- `app/Services/Automation/SeleniumService.php`: connexió i login Selenium/SAO.
- `app/Sao/Support/SaoRunner.php`: cicle de vida del driver.
- `app/Sao/SaoAnnexesAction.php`: revisió i descàrrega d'annexos SAO.
- `app/Sao/Documents/A1DocumentService.php`, `A2DocumentService.php`, `A5DocumentService.php`: serveis per annex.

## Docker Selenium

- Contenidor habitual: `intranetbatoi-selenium-1`.
- Shell: `docker exec -it intranetbatoi-selenium-1 /bin/bash`.
- Directori de descàrregues del navegador: `/home/seluser/Downloads`.
- `/srv` pot estar buit en el contenidor Selenium; no assumir muntatges si no es comproven.

## Depuració

- Revisar logs del canal `sao` quan una descàrrega falla.
- Confirmar selectors Selenium en el servei concret abans de canviar esperes o clics.
- Si el problema és de descàrrega, comprovar tant el directori de Selenium com el lloc final on `saveAnnex()` registra el document.
