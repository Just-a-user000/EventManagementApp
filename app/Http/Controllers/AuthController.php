<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/events')->with('success', 'Benvenuto, ' . auth()->user()->name . '!');
        }

        $user = User::where('email', $request->email)->first();
        
        if ($user) {
            return back()->withErrors([
                'password' => 'La password inserita non è corretta.',
            ])->onlyInput('email');
        } else {
            return back()->withErrors([
                'email' => 'Nessun account trovato con questa email.',
            ])->onlyInput('email');
        }
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Il nome è obbligatorio.',
            'name.max' => 'Il nome non può superare i 255 caratteri.',
            'email.required' => 'L\'email è obbligatoria.',
            'email.email' => 'L\'indirizzo email non è valido. Inserisci un\'email nel formato corretto (es. nome@esempio.com).',
            'email.max' => 'L\'email non può superare i 255 caratteri.',
            'email.unique' => 'Questa email è già registrata.',
            'password.required' => 'La password è obbligatoria.',
            'password.min' => 'La password deve avere almeno 8 caratteri.',
            'password.confirmed' => 'Le password non coincidono.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        auth()->login($user);

        return redirect('/events')->with('success', 'Registrazione completata con successo!');
    }

    public function logout(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout effettuato con successo!');
    }
}
