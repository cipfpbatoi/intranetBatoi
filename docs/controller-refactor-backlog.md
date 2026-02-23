# Backlog Refactor Controllers (Modal + Validació)

Data d'inici: 2026-02-23
Objectiu: acabar la migració de validació a `FormRequest` i deixar controladors prims.

## Fases

### Sprint 1
- [x] 1.1 Inventari real de `store/update` sense `FormRequest`
- [ ] 1.2 Crear/ajustar `FormRequest` pendents
- [x] 1.2.a Lot 1 aplicat (`EmpresaController`, `DocumentoController`, `FctController`, `FaltaController`)
- [ ] 1.3 Eliminar validació dispersa de controlador/model on ja hi haja `FormRequest`
- [ ] 1.4 Tests de regressió dels fluxos migrats

### Sprint 2
- [ ] 2.1 Revisar autorització (rols/polítiques) en fluxos crítics
- [ ] 2.2 Cobrir `Application/*WorkflowService` amb tests unitaris
- [ ] 2.3 Neteja final (imports, codi mort, signatures incoherents)

---

## 1.1 Inventari (fet)

Resum:
- Total signatures `store/update` detectades (sense `API/Core`): **83**
- Amb `FormRequest`: **60**
- Amb `Illuminate\Http\Request` genèric: **23** (19 controladors)

### Pendents de migrar a `FormRequest` (detectat automàticament)

- [x] `app/Http/Controllers/AlumnoController.php` (`update`)
- [x] `app/Http/Controllers/AlumnoGrupoController.php` (`update`)
- [ ] `app/Http/Controllers/Auth/Alumno/PerfilController.php` (`update`)
- [ ] `app/Http/Controllers/Auth/PerfilController.php` (`update`)
- [ ] `app/Http/Controllers/Auth/Profesor/PerfilController.php` (`update`)
- [ ] `app/Http/Controllers/ColaboracionController.php` (`update` - hi ha també versió amb `ColaboracionRequest`)
- [x] `app/Http/Controllers/DocumentoController.php` (`store`)
- [x] `app/Http/Controllers/EmpresaController.php` (`store`, `update`)
- [x] `app/Http/Controllers/FaltaController.php` (`store`, `update`)
- [x] `app/Http/Controllers/FctController.php` (`store`, `update`)
- [ ] `app/Http/Controllers/FicharController.php` (`store`)
- [x] `app/Http/Controllers/HorarioController.php` (`update`)
- [ ] `app/Http/Controllers/ImportController.php` (`store`)
- [ ] `app/Http/Controllers/MyMailController.php` (`store`)
- [x] `app/Http/Controllers/PanelColaboracionController.php` (`store`, `update`)
- [x] `app/Http/Controllers/PanelSeguimientoAlumnosController.php` (`store`)
- [x] `app/Http/Controllers/ProfesorController.php` (`update`)
- [ ] `app/Http/Controllers/SendAvaluacioEmailController.php` (`store`)
- [ ] `app/Http/Controllers/TeacherImportController.php` (`store`)

## Proposta d'ordre per al 1.2

1. `EmpresaController` i `DocumentoController` (impacte alt, ja refactoritzats en serveis).
2. `FctController` i `FaltaController` (flux crític funcional).
3. `PanelColaboracionController` i `AlumnoGrupoController`.
4. `HorarioController`, `ProfesorController`, `AlumnoController`.
5. `FicharController`, `PanelSeguimientoAlumnosController`.
6. `ImportController`, `TeacherImportController`, `SendAvaluacioEmailController`, `MyMailController` (infra/importació).
7. `Auth/*/PerfilController` (especials, possible tractament a banda).

## Notes

- Este document és viu: cada migració ha d'actualitzar estat i commit.
- Limitació tècnica actual: els controladors que hereten de `IntranetController` han de mantindre signatura `store/update(Request $request, ...)`.
  Per tant, en eixos casos usem patró híbrid: `Request` en signatura + regles/missatges centralitzades en classe `FormRequest`.
- Criteri de completat d'una tasca:
  - `FormRequest` aplicat en `store/update` o regles centralitzades en classe `FormRequest` amb validació manual si el controlador depén de `IntranetController`.
  - sense validació de negoci duplicada en controlador.
  - tests del flux en verd.
