<?php

namespace App\Http\Controllers;

use App\Models\Grievance;
use App\Support\Grievance as Lookup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GrievanceController extends Controller
{
    // ── Pages ────────────────────────────────────────────────

    public function dashboard(Request $request)
    {
        return view('citizen.dashboard', ['user' => $request->user()]);
    }

    public function create()
    {
        return view('citizen.submit', ['categories' => Lookup::CATEGORIES]);
    }

    public function track(Request $request)
    {
        return view('citizen.track', ['user' => $request->user()]);
    }

    // ── Create (multipart, called via XHR for the progress bar) ──

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'min:5', 'max:150'],
            'description' => ['required', 'string', 'min:20', 'max:3000'],
            'category'    => ['required', Rule::in(Lookup::categoryKeys())],
            'address'     => ['required', 'string', 'max:255'],
            'photo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'title.min'       => 'Title must be at least 5 characters',
            'description.min' => 'Please describe the issue in at least 20 characters',
            'photo.image'     => 'The photo must be an image (JPG, PNG or WEBP).',
            'photo.max'       => 'The photo must be 5 MB or smaller.',
        ]);

        $photo = $request->file('photo');
        $mime  = $photo ? $photo->getMimeType() : null;
        $b64   = $photo ? base64_encode(file_get_contents($photo->getRealPath())) : null;

        $g = Grievance::create([
            'user_id'     => $request->user()->id,
            'user_name'   => $request->user()->name,
            'title'       => $data['title'],
            'description' => $data['description'],
            'category'    => $data['category'],
            'address'     => $data['address'],
            'status'      => 'pending',
            'has_image'   => $photo !== null,
            'image_mime'  => $mime,
            'image_data'  => $b64,
        ]);

        return response()->json(['ok' => true, 'grievance' => $g->toPayload(),
            'redirect' => route('citizen.dashboard')], 201);
    }

    // ── JSON API ─────────────────────────────────────────────

    /** Citizen: own reports. Admin: every report. */
    public function index(Request $request)
    {
        $q = Grievance::lite()->orderByDesc('created_at')->orderByDesc('id');
        if (! $request->user()->isAdmin()) {
            $q->where('user_id', $request->user()->id);
        }
        return response()->json([
            'data' => $q->get()->map->toPayload()->values(),
        ]);
    }

    /**
     * Look up a report by its numeric id or "SNB-00012" reference.
     * Any signed-in user may view it (same as the Flutter getById),
     * but chat/delete are limited to the owner and admins.
     */
    public function show(Request $request, string $id)
    {
        $numeric = (int) preg_replace('/\D/', '', $id);
        $g = $numeric > 0 ? Grievance::lite()->find($numeric) : null;
        if (! $g) {
            return response()->json(['message' => 'No report found with that ID.'], 404);
        }

        $user  = $request->user();
        $owner = $user->isAdmin() || $g->user_id === $user->id;

        $payload = $g->toPayload();
        $payload['can_chat']   = $owner;
        $payload['can_delete'] = $owner;
        if (! $owner) {
            // Hide the reporter's identity from strangers.
            $payload['user_name'] = 'Anonymous citizen';
            $payload['user_id']   = null;
        }
        return response()->json(['data' => $payload]);
    }

    public function destroy(Request $request, Grievance $grievance)
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || $grievance->user_id === $user->id, 403);

        $grievance->delete(); // messages cascade

        return response()->json(['ok' => true]);
    }

    /** Streams the stored photo (any signed-in user, same as report lookup). */
    public function photo(int $id)
    {
        $row = Grievance::query()->select(['id', 'has_image', 'image_mime', 'image_data'])
            ->find($id);
        abort_unless($row && $row->has_image && $row->image_data, 404);

        return response(base64_decode($row->image_data), 200, [
            'Content-Type'           => $row->image_mime ?: 'image/jpeg',
            'Cache-Control'          => 'private, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
