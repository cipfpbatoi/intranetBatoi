@extends('layouts.intranet')

@section('titulo', 'Accés a les convalidacions')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h3">Accés a les convalidacions</h1>
                    <p>Actualment, l’accés a les convalidacions està restringit perquè estem fent proves. Introduïx la contrasenya facilitada per a continuar.</p>
                    <form method="POST" action="{{ route('convalidacions.unlock') }}">
                        @csrf
                        <label for="password" class="form-label">Contrasenya</label>
                        <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required autofocus autocomplete="current-password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <button type="submit" class="btn btn-primary mt-3">Accedir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
