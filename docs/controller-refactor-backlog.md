# Backlog Refactor Controllers (Modal + Validació)

Data d'inici: 2026-02-23
Objectiu: acabar la migració de validació a `FormRequest` i deixar controladors prims.

## Fases

### Sprint 1
- [x] 1.1 Inventari real de `store/update` sense `FormRequest`
- [x] 1.2 Crear/ajustar `FormRequest` pendents
- [x] 1.2.a Lot 1 aplicat (`EmpresaController`, `DocumentoController`, `FctController`, `FaltaController`)
- [ ] 1.3 Eliminar validació dispersa de controlador/model on ja hi haja `FormRequest`
- [ ] 1.4 Tests de regressió dels fluxos migrats

### Sprint 2
- [x] 2.1 Revisar autorització (rols/polítiques) en fluxos crítics
- [x] 2.2 Cobrir `Application/*WorkflowService` amb tests unitaris
- [x] 2.3 Neteja final (imports, codi mort, signatures incoherents)
- [x] 2.1.a Autorització explícita de mutacions en `EmpresaController` i `FctController` (rols de tutor/pràctiques/direcció)
- [x] 2.1.b Autorització explícita de mutacions en `PanelColaboracionController` (rol tutor)
- [x] 2.1.c Autorització explícita en fluxos d'importació (`ImportController` i `TeacherImportController`) + `FormRequest` d'importació
- [x] 2.2.a Tests unitaris inicials de `ImportWorkflowService` (pipeline XML i assignació de tutors)
- [x] 2.2.b Tests unitaris de `InstructorWorkflowService` (upsert/attach, detach/delete, última data)
- [x] 2.2.c Tests unitaris de `FaltaItacaWorkflowService` (filtrat, esborrat d'informe, casos no trobats)
- [x] 2.2.d Tests unitaris de `GrupoWorkflowService` (assignació de cicle i selecció d'alumnat)
- [x] 2.2.e Tests unitaris de `PollWorkflowService` (prepare/save/myVotes i casos no trobats)
- [x] 2.3.a Neteja d'imports no usats en `FctController`
- [x] 2.3.b Coherència de signatures en importació: patró híbrid (`Request` + regles de `FormRequest`) i autorització compatible amb execució en cua (`RunImportJob`)
- [x] 2.3.c Coherència de nomenclatura i imports: `sacaCampos` en `TeacherImportController` i `use Illuminate\Support\Facades\DB` en `InstructorWorkflowService`
- [x] 2.3.d Inici migració a `Policies`: `EmpresaPolicy`, `FctPolicy`, `ColaboracionPolicy`, `ImportRunPolicy`, `IncidenciaPolicy`, `ProfesorPolicy`, `ReunionPolicy`, `DocumentoPolicy`, `ActividadPolicy`, `ComisionPolicy`, `TaskPolicy`, `SettingPolicy`, `FaltaPolicy`, `TipoActividadPolicy`, `EspacioPolicy`, `MaterialBajaPolicy`, `CicloPolicy`, `DepartamentoPolicy`, `TipoIncidenciaPolicy`, `ModuloCicloPolicy`, `MenuPolicy`, `CursoPolicy`, `PPollPolicy`, `OptionPolicy`, `LotePolicy`, `ArticuloPolicy` i `IpGuardiaPolicy` aplicades en controladors i registrades en `AuthServiceProvider` (+ tests unitaris de policy)

---

## 1.1 Inventari (fet)

Resum:
- Total signatures `store/update` detectades (sense `API/Core`): **83**
- Amb `FormRequest`: **60**
- Amb `Illuminate\Http\Request` genèric: **23** (19 controladors)

### Pendents de migrar a `FormRequest` (detectat automàticament)

- [x] `app/Http/Controllers/AlumnoController.php` (`update`)
- [x] `app/Http/Controllers/AlumnoGrupoController.php` (`update`)
- [x] `app/Http/Controllers/Auth/Alumno/PerfilController.php` (`update`)
- [x] `app/Http/Controllers/Auth/PerfilController.php` (`update`)
- [x] `app/Http/Controllers/Auth/Profesor/PerfilController.php` (`update`)
- [x] `app/Http/Controllers/ColaboracionController.php` (`update`)
- [x] `app/Http/Controllers/DocumentoController.php` (`store`)
- [x] `app/Http/Controllers/EmpresaController.php` (`store`, `update`)
- [x] `app/Http/Controllers/FaltaController.php` (`store`, `update`)
- [x] `app/Http/Controllers/FctController.php` (`store`, `update`)
- [x] `app/Http/Controllers/FicharController.php` (`store`)
- [x] `app/Http/Controllers/HorarioController.php` (`update`)
- [x] `app/Http/Controllers/ImportController.php` (`store`)
- [x] `app/Http/Controllers/MyMailController.php` (`store`)
- [x] `app/Http/Controllers/PanelColaboracionController.php` (`store`, `update`)
- [x] `app/Http/Controllers/PanelSeguimientoAlumnosController.php` (`store`)
- [x] `app/Http/Controllers/ProfesorController.php` (`update`)
- [x] `app/Http/Controllers/SendAvaluacioEmailController.php` (`store`)
- [x] `app/Http/Controllers/TeacherImportController.php` (`store`)

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
