# Mapa Notificacions I Correu

## Bounded Context

- Notificacions de panell: `app/Services/Notifications/NotificationService.php` i `app/Notifications/mensajePanel.php`.
- Avisos de domini: `app/Services/Notifications/AdviseService.php`, `AdviseTeacher.php` i serveis específics com `ActividadNotificationService.php`.
- Correu editable/reutilitzable: `app/Services/Mail/MyMail.php`, `MailSender.php`, `RecipientResolver.php` i `EmailPostSendService.php`.
- Controladors d'entrada: `app/Http/Controllers/NotificationController.php`, `MyMailController.php`, `ProfesorController@alerta`, `AlumnoController@alerta`.
- Rutes habituals: `routes/todos.php` per notificacions, `routes/profesor.php`, `routes/alumno.php` i `routes/direccion.php` per missatges a professorat, i `routes/api.php` per lectura API.

## Fluxos

- `NotificationService::send($id, $mensaje, $enlace, $emisor)` resol receptor per NIA/DNI, crea `mensajePanel` i usa el canal `database`.
- `AdviseService` envia avisos segons configuració de model i estat; revisa `config/modelos.php`, `config/avisos.php` o `config/contacto.php` abans de canviar destinataris.
- `AdviseTeacher` calcula professorat afectat per horaris/grups i pot enviar correus a tutors.
- `MyMail` prepara receptors, vista editable, adjunts i dades del correu; `MailSender` executa l'enviament i controla mida estimada, registre d'activitat i postprocessat.

## Reutilització En Altres Laravel

- Tracta la reutilització del sistema de missatgeria com una investigació d'extracció, no com una còpia directa.
- Identifica dependències pròpies abans de proposar integració: namespace `Intranet\`, helpers `authUser()`, `apiAuthUser()`, models `Alumno`/`Profesor`, configuració `modelos.*`, `avisos.*`, `contacto.*`, `AppAlert`, cues, taula `notifications`, plantilles `email.*` i `DocumentRequest`.
- Separa canals: notificació de panell (`database`) no és el mateix que correu SMTP amb `MyMail`.
- Per una app externa, defineix primer contractes mínims per a receptor, emissor, missatge, enllaç, adjunts i postprocessat; després adapta `NotificationService`/`MyMail` darrere d'eixos contractes.

## Tests Útils

- Notificacions: `php artisan test --filter=NotificationServiceTest`.
- Avisos: `php artisan test --filter=AdviseServiceTest` i `php artisan test --filter=AdviseTeacherTest`.
- Correu: `php artisan test --filter=MyMailTest`, `php artisan test --filter=MailSenderTest` i `php artisan test --filter=EmailPostSendServiceTest`.
