<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Models\OpportunityReminder;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    public function index(Request $request)
    {
        $open = Opportunity::open()->get();
        $selected = $request->filled('o')
            ? Opportunity::where('slug', $request->string('o'))->where('published', true)->first()
            : null;

        return $this->render($request, $open, $selected ?? $open->first(), false);
    }

    public function show(Request $request, Opportunity $opportunity)
    {
        abort_unless($opportunity->published, 404);

        return $this->render($request, Opportunity::open()->get(), $opportunity, true);
    }

    /** Active ou retire le rappel 48 h avant la date limite pour cet appareil. */
    public function remind(Request $request, Opportunity $opportunity)
    {
        $device = $request->attributes->get('device');
        $existing = OpportunityReminder::where('opportunity_id', $opportunity->id)->where('device_id', $device)->first();
        $on = ! $existing;
        $existing ? $existing->delete() : OpportunityReminder::create(['opportunity_id' => $opportunity->id, 'device_id' => $device]);

        if ($request->expectsJson()) {
            return response()->json(['on' => $on]);
        }

        return redirect()->to(url()->previous(route('opportunities.show', $opportunity)));
    }

    private function render(Request $request, $open, ?Opportunity $sel, bool $isDetail)
    {
        $device = $request->attributes->get('device');

        return view('pages.opportunities', [
            'open' => $open,
            'sel' => $sel,
            'isDetail' => $isDetail,
            'reminded' => $sel ? OpportunityReminder::where('opportunity_id', $sel->id)->where('device_id', $device)->exists() : false,
        ]);
    }
}
