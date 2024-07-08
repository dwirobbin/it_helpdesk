<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use App\Enums\RoleEnum;
use App\Models\TicketChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TicketChatRequest;
use App\Http\Resources\TicketChatResource;

class TicketChatController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $ticketChats = TicketChat::query()
            ->with(['ticket', 'user', 'user.role'])
            ->whereRelation('ticket', 'ticket_number', '=', $request->query('ticket_number'))
            ->get();

        $loggedInUserId = auth()->id();

        foreach ($ticketChats as $chat) {
            if ($chat->user->role_id != $loggedInUserId) {
                $chat->update(['is_readed' => true]);
            }
        }

        return app(TicketController::class)->index([
            'ticket_chats' => TicketChatResource::collection($ticketChats),
            'is_open_chat' => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketChatRequest $request)
    {
        $validatedData = $request->validated();

        try {
            $ticket = Ticket::query()
                ->where('ticket_number', '=', $validatedData['ticket_number'])
                ->firstOrFail();

            $user = User::query()
                ->where('slug', '=', $validatedData['user'])
                ->firstOrFail();

            DB::transaction(function () use ($ticket, $user, $validatedData) {
                $ticketChat = TicketChat::query()->create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'text' => $validatedData['text'],
                ]);

                if (preg_match(sprintf('[%1$d|%2$d]', RoleEnum::SUPER_ADMIN->value, RoleEnum::IT_SUPPORT->value), auth()->user()->role_id)) {
                    TicketChat::query()
                        ->where('id', '=', $ticketChat->id)
                        ->update(['seen_for_staff' => true]);
                } else {
                    TicketChat::query()
                        ->where('id', '=', $ticketChat->id)
                        ->update(['seen_for_admin' => true]);
                }
            });

            session()->flash('message', [
                'text' => 'Pesan berhasil terkirim.',
                'type' => 'success',
            ]);
        } catch (\Throwable) {
            return back()->with('message', [
                'text' => 'Terjadi suatu kesalahan.',
                'type' => 'error',
            ]);
        }
    }
}
