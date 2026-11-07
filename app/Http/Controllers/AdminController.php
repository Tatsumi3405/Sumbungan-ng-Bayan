<?php

namespace App\Http\Controllers;

use App\Models\Grievance;
use App\Support\Grievance as Lookup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('admin.dashboard', [
            'user'       => $request->user(),
            'categories' => Lookup::CATEGORIES,
            'statuses'   => Lookup::STATUSES,
            'sectors'    => Lookup::SECTORS,
        ]);
    }

    /** Admin: update status, optional feedback and responding sector. */
    public function update(Request $request, Grievance $grievance)
    {
        $data = $request->validate([
            'status'            => ['required', Rule::in(Lookup::statusKeys())],
            'responding_sector' => ['nullable', Rule::in(Lookup::sectorIds())],
            'admin_feedback'    => ['nullable', 'string', 'max:2000'],
        ]);

        $grievance->status = $data['status'];
        if (! empty($data['responding_sector'])) {
            $grievance->responding_sector = $data['responding_sector'];
        }
        if (isset($data['admin_feedback']) && $data['admin_feedback'] !== '') {
            $grievance->admin_feedback = $data['admin_feedback'];
        }
        $grievance->save(); // bumps updated_at

        return response()->json(['ok' => true, 'data' => Grievance::lite()->find($grievance->id)->toPayload()]);
    }
}
