<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Suggestion;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public const ROLES = [
        ['Rédiger des résumés', '2 h par semaine'],
        ['Signaler des opportunités', '15 min, quand tu en vois'],
        ['Relire et vérifier', '1 h par semaine'],
        ['Proposer des termes au glossaire', 'Quand tu veux'],
        ['Animer une communauté locale', '1 événement par mois'],
    ];

    public const DOMAINS = ['IA' => 'ia', 'Cyber' => 'cyber', 'Data' => 'data', 'Opportunités' => 'opp'];

    public function index()
    {
        return view('pages.about', [
            'team' => TeamMember::orderBy('position')->get(),
            'roles' => self::ROLES,
            'domains' => self::DOMAINS,
        ]);
    }

    public function contribute(Request $request)
    {
        $data = $request->validate([
            'role' => ['required', 'integer', 'min:0', 'max:'.(count(self::ROLES) - 1)],
            'name' => ['required', 'string', 'max:120'],
            'contact' => ['required', 'string', 'max:190', function ($attr, $v, $fail) {
                if (! self::contactVia($v)) {
                    $fail('Indique un numéro WhatsApp (8 chiffres minimum) ou une adresse e-mail.');
                }
            }],
            'domains' => ['required', 'array', 'min:1'],
            'domains.*' => ['in:'.implode(',', array_keys(self::DOMAINS))],
            'message' => ['nullable', 'string', 'max:2000'],
        ], [
            'name.required' => 'Indique ton nom.',
            'contact.required' => 'Indique un numéro WhatsApp ou une adresse e-mail.',
            'domains.required' => 'Choisis au moins un domaine.',
        ]);

        $via = self::contactVia($data['contact']);
        Contribution::create([
            'role' => self::ROLES[$data['role']][0],
            'name' => trim($data['name']),
            'contact' => trim($data['contact']),
            'contact_via' => $via,
            'domains' => array_values($data['domains']),
            'message' => $data['message'] ?? null,
        ]);

        $payload = ['first' => strtok(trim($data['name']), ' '), 'via' => $via === 'whatsapp' ? 'WhatsApp' : 'e-mail'];

        return $request->expectsJson()
            ? response()->json($payload)
            : redirect()->to(route('about').'#contribuer')->with('contrib', $payload);
    }

    public function suggest(Request $request)
    {
        $data = $request->validate([
            'kind' => ['required', 'in:term,topic,event,startup,community'],
            'text' => ['required', 'string', 'max:190'],
            'details' => ['nullable', 'string', 'max:1000'],
            'contact' => ['nullable', 'string', 'max:190'],
        ]);
        Suggestion::create($data);

        return $request->expectsJson() ? response()->json(['ok' => true]) : back()->with('suggested', true);
    }

    public static function contactVia(string $v): ?string
    {
        $v = trim($v);
        if (preg_match('/^\+?[\d\s]{8,}$/', $v)) {
            return 'whatsapp';
        }

        return filter_var($v, FILTER_VALIDATE_EMAIL) ? 'email' : null;
    }
}
