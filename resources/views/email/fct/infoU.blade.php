    <div>T'escric per recordar-te l'inici de les pràctiques de FCT. A continuació et passe relació dels alumnes que t'han estat assignat i les dades de començament de les pràctiques.</div>
        <div>
            <p>Data de començament: {{$elemento->desde}} </p>
            <p>Alumnes assignats: </p>
            <ul>
            @foreach ($elemento->Alumnos as $alumno)
                <li> {{$alumno->fullName}} - {{$alumno->email}} </li>
            @endforeach
            </ul>
        </div>
    <div>Aprofite per donar-te les meues dades per si necessites possar-te en contacte amb mi:</div>
    <div>
        Tutor: {{AuthUser()->fullName}} {{AuthUser()->email}} <br/>
        Telèfon centre: {{ config('contacto.telefono') }} <br/>
    </div>
    <div>Així com també informació relevant en cas d'accident laboral que trobaràs en aquest enllaç <a href="http://www.ceice.gva.es/va/web/formacion-profesional/seguro">http://www.ceice.gva.es/va/web/formacion-profesional/seguro</a></div>
    <div>Per qualsevol dubte em tens a la teua disposició</div>
    <div>Salutacions cordials de {{AuthUser()->shortName}}</div>

