<?php

namespace App\Http\Controllers;

use App\Models\Grievance;
use App\Models\GrievanceMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    private function authorizeThread(Request $request, Grievance $g): void
    {
        $u = $request->user();
        abort_unless($u->isAdmin() || $g->user_id === $u->id, 403);
    }

    /** Poll endpoint: ?after=<last message id> returns only newer messages. */
    public function index(Request $request, Grievance $grievance)
    {
        $this->authorizeThread($request, $grievance);

        $after = (int) $request->query('after', 0);
        $msgs  = $grievance->messages()->where('id', '>', $after)->get();

        return response()->json([
            'data'          => $msgs->map->toPayload()->values(),
            'message_count' => $grievance->message_count,
        ]);
    }

    public function store(Request $request, Grievance $grievance)
    {
        $this->authorizeThread($request, $grievance);

        $data = $request->validate(['text' => ['required', 'string', 'max:2000']]);
        $user = $request->user();

        $msg = DB::transaction(function () use ($grievance, $user, $data) {
            $m = GrievanceMessage::create([
                'grievance_id' => $grievance->id,
                'sender_id'    => $user->id,
                'sender_name'  => $user->name,
                'sender_role'  => $user->isAdmin() ? 'admin' : 'citizen',
                'text'         => trim($data['text']),
            ]);
            $grievance->increment('message_count'); // denormalised badge counter
            return $m;
        });

        return response()->json(['data' => $msg->toPayload()], 201);
    }
}
