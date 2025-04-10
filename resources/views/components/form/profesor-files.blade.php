<form id="formulari" method="post" action="{{ route('files.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <table class="table table-bordered">
        <tbody>
        <!-- Fila 1: Imatge i Rúbrica -->
        <tr>
            <td>
                <strong>Imatge</strong><br/>
                <img
                        style="max-width: 50%"
                        src="{{ asset_nocache('storage/fotos/' . $profesor->foto) }}"
                        alt="foto"
                />
                <br/><br/>
                <input type="file" name="foto" />
            </td>
            <td>
                <strong>Rúbrica</strong><br/>
                <img
                        style="max-width: 50%"
                        src="{{ asset_nocache('storage/signatures/' . $profesor->foto) }}"
                        alt="rúbrica"
                />
                <br/><br/>
                <input type="file" name="signatura" />
            </td>
        </tr>

        <!-- Fila 2: Peu email i Certificat Digital -->
        <tr>
            <td>
                <strong>Peu email</strong><br/>
                <img
                        style="max-width: 50%"
                        src="{{ asset_nocache('storage/peus/' . $profesor->foto) }}"
                        alt="peu"
                />
                <br/><br/>
                <input type="file" name="peu" />
            </td>
            <td>
                <strong>Certificat Digital</strong><br/>
                @if (file_exists(storage_path('app/zip/' . $profesor->fileName . '.tmp')))
                    <img
                            style="max-width: 50%"
                            src="{{ asset('/img/certificat_digital.png') }}"
                            alt="digital carregat"
                    />
                @endif
                <br/><br/>
                <input type="file" name="certificat_digital"  id="certificat"/>
                <br/>
                <input type="checkbox" name="eliminar_certificat" value="1" />
                <label>Eliminar Certificat</label>
                <br/>
                <label for="password">Login usuari:</label>
                <input type="password" id="password" name="password" />
            </td>
        </tr>

        <!-- Fila 3: Botó d'enviament -->
        <tr>
            <td colspan="2">
                <input type="submit" value="Enviar" class="btn btn-success" />
            </td>
        </tr>

        <!-- Fila 4: Mostra errors, si n'hi ha -->
        <tr>
            <td colspan="2">
                @if ($errors->any())
                    <div class="alert alert-danger mt-2">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </td>
        </tr>
        </tbody>
    </table>
</form>
