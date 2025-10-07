<?php
namespace Intranet\Http\Controllers;

use Intranet\Http\Requests\StoreBustiaRequest;
use Intranet\Entities\BustiaVioleta;
use Intranet\Entities\Alumno;
use Intranet\Entities\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BustiaVioletaController extends ModalController
{
    // Formulari (si uses Blade tradicional)
    public function create()
    {
        return view('bustia.create');
    }

    // Enviament
    public function store(StoreBustiaRequest $request)
    {
        $user = auth()->user(); // al teu projecte: authUser()
        $dni  = $user->dni ?? null;

        // Determina rol a partir del DNI (compartit entre Alumno/Profesor)
        $rol = null;
        if ($dni) {
            $rol = Alumno::find($dni) ? 'alumno' : (Profesor::find($dni) ? 'profesor' : null);
        }

        $anonimo = (bool) $request->boolean('anonimo');

        // Hashos per a limit d’abusos i agrupacions, sense guardar dades crues
        $dniHash = $dni ? hash_hmac('sha256', $dni, config('app.key')) : null;
        $ipHash  = $request->ip() ? hash_hmac('sha256', $request->ip(), config('app.key')) : null;

        $data = [
            'dni'          => $anonimo ? null : $dni, // si és anònim, no guardes DNI en clar
            'rol'          => $rol,
            'anonimo'      => $anonimo,
            'autor_nombre' => $anonimo ? null : ($user->ShortName ?? ($user->name ?? null)),
            'categoria'    => $request->input('categoria'),
            'mensaje'      => $request->input('mensaje'),
            'estado'       => 'nou',
            'publicable'   => false,
            'dni_hash'     => $dniHash,
            'ip_hash'      => $ipHash,
        ];

        // Adjunt
        if ($request->hasFile('adjunto')) {
            $path = $request->file('adjunto')->store('bustia_violeta','public');
            $data['adjunto_path'] = $path;
        }

        $entrada = BustiaVioleta::create($data);

        // Notifica (opcional)
        // \Notification::route('mail','igualtat@centre.edu')->notify(new NovaEntradaBustia($entrada));

        return redirect()
            ->back()
            ->with('success','Missatge enviat. Gràcies per compartir-ho.');
    }

    // Vista admin (llistat)
    public function indexAdmin(Request $request)
    {
        $this->authorize('admin-bustia'); // Policy/Gate
        $q = BustiaVioleta::query()
            ->when($request->filled('estado'), fn($qq) => $qq->where('estado',$request->estado))
            ->latest();

        return view('bustia.admin-index', ['entrades' => $q->paginate(20)]);
    }

    // Canvi d’estat (admin)
    public function updateEstado(BustiaVioleta $entrada, Request $request)
    {
        $this->authorize('admin-bustia');
        $entrada->update([
            'estado' => $request->validate(['estado'=>'required|in:nou,en_revisio,tancat'])['estado'],
            'publicable' => $request->boolean('publicable'),
        ]);
        return back()->with('success','Actualitzat.');
    }

    // Download adjunt (controlat)
    public function downloadAdjunt(BustiaVioleta $entrada)
    {
        $this->authorize('admin-bustia');
        abort_unless($entrada->adjunto_path && Storage::disk('public')->exists($entrada->adjunto_path), 404);
        return Storage::disk('public')->download($entrada->adjunto_path);
    }
}