# Exploració d'importació FCT des d'ITACA SAO

> Documentació de lectura sanejada. No incorporar captures, valors de files,
> credencials, cookies, payloads ni selectors ZK dinàmics.

## Recorregut confirmat

1. Accedir a ITACA SAO i autenticar-se amb un perfil autoritzat.
2. Obrir `GESTIÓN` > `Formación`.
3. Obrir `Búsqueda avanzada`.
4. En el camp `TIPO`, seleccionar una modalitat de FE. S'han confirmat almenys
   `FE: GEN` (general) i `FE: LOG` (LOGSE).
5. Executar la cerca, sense obrir ni modificar cap registre.

## Contracte visible del llistat FE

El llistat filtrat mostra les columnes següents:

- `Alumnet`
- `Cicle`
- `Centre de treball`
- `Tutor/a`
- `Data inici`
- `Data fi`
- `Tipus`
- `Autorització`
- `Confirmació`
- `Hores previstes`
- `Hores realitzades`

Les dades permeten alimentar, com a mínim, l'alumnat, el cicle, el centre de
treball, les dates, el tipus, l'autorització i les hores previstes del flux
local actual.

## Filtres confirmats

La cerca avançada inclou camps d'alumnat, centre de treball, dates i tipus. La
importació haurà d'usar filtres explícits de tipus FE i no interpretar les
files d'altres modalitats com a FCT.

## Pendent abans d'implementar

- Confirmar un identificador extern estable per evitar duplicats locals.
- Confirmar si el detall de la formació aporta les dades d'empresa i de
  l'instructor requerides pel flux actual.
- Determinar el mapping de `Confirmació` i `Hores realitzades`: no formen part
  de l'alta local actual i no s'han d'importar sense una regla de negoci.
- Verificar la resta de modalitats FE visibles per al perfil i curs actius.

## Regles d'exploració

- Les consultes a ITACA són només de lectura.
- No obrir fitxes ni executar accions de creació, edició, eliminació o guardat.
- Els selectors finals han de ser semàntics i acotats a la finestra ZK; no
  dependre d'identificadors dinàmics.
