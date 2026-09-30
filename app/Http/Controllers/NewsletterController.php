<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email:rfc', 'max:190']], [
            'email.*' => 'Adresse incomplète : il manque le @ ou le domaine.',
        ]);
        $email = Str::lower(trim($data['email']));

        $sub = NewsletterSubscriber::firstOrNew(['email' => $email]);
        $sub->unsubscribe_token ??= Str::random(40);
        $sub->unsubscribed_at = null;
        $sub->save();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('nl', 'ok');
    }

    public function unsubscribe(string $token)
    {
        $sub = NewsletterSubscriber::where('unsubscribe_token', $token)->firstOrFail();
        $sub->update(['unsubscribed_at' => now()]);

        return view('pages.message', [
            'title' => ['Désinscription', 'confirmée'],
            'text' => 'Tu ne recevras plus TechPulse Brief. Ton adresse n’est ni conservée pour autre chose, ni revendue.',
        ]);
    }
}
