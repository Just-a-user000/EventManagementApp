@extends('layouts.app')

@section('title', 'Preferenze')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="mb-4"><i class="bi bi-gear"></i> Le Mie Preferenze</h1>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('preferences.update') }}">
                        @csrf

                        <h5 class="mb-3">Tipologie di Eventi Preferite</h5>
                        <p class="text-muted small mb-3">Seleziona le tipologie di eventi che ti interessano di più</p>

                        @php
                            $preferredTypes = $user->getPreferredEventTypes();
                        @endphp

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="preferred_types[]" 
                                           value="cultural" id="typeCultural" 
                                           {{ in_array('cultural', $preferredTypes) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeCultural">
                                        <i class="bi bi-palette"></i> Culturale
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="preferred_types[]" 
                                           value="recreational" id="typeRecreational"
                                           {{ in_array('recreational', $preferredTypes) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeRecreational">
                                        <i class="bi bi-emoji-smile"></i> Ricreativo
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="preferred_types[]" 
                                           value="educational" id="typeEducational"
                                           {{ in_array('educational', $preferredTypes) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeEducational">
                                        <i class="bi bi-book"></i> Educativo
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="preferred_types[]" 
                                           value="sports" id="typeSports"
                                           {{ in_array('sports', $preferredTypes) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeSports">
                                        <i class="bi bi-trophy"></i> Sportivo
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="preferred_types[]" 
                                           value="other" id="typeOther"
                                           {{ in_array('other', $preferredTypes) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="typeOther">
                                        <i class="bi bi-tag"></i> Altro
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Salva Preferenze
                            </button>
                            <a href="{{ route('my-events') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Torna ai Miei Eventi
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
