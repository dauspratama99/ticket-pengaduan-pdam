<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketStoreRequest;
use App\Http\Resources\TicketResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use Exception;

class TicketController extends Controller
{

    public function index(Request $request)
    {
        try {
            $query = Ticket::query();

            $query->orderBy('created_at', 'desc');

            if($request->search){
                $query->where('code', 'like', '%' . $request->search . '%')
                    ->orWhere('title', 'like', '%' . $request->search . '%');
            }

            if($request->status){
                $query->where('status',  $request->status);
            }

            if($request->priority){
                $query->where('priority',  $request->priority);
            }

            if(auth()->user()->role == 'user'){
                $query->where('user_id',  auth()->user()->id);
            }

            $tickets = $query->get();

            return response()->json([
                'message' => 'Data Tiket Berhasil Ditampilkan',
                'data' => TicketResource::collection($tickets)
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi Kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($code)
    {
        try {
            $ticket = Ticket::where('code', $code)->first();

            if(!$ticket){
                return response()->json([
                    'message' => 'Tiket tidak ditemukan'
                ], 404);
            }

            if(auth()->user()->role == 'user' && $ticket->user_id != auth()->user()->id){
                return response()->json([
                    'message' => 'Anda tidak memiliki hak akses melihat tiket ini'
                ], 403);
            }

            return response()->json([
                    'message' => 'Tiket Berhasil Ditampilkan',
                    'data' => new TicketResource($ticket)
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi Kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(TicketStoreRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $ticket = new Ticket;
            $ticket->user_id = auth()->user()->id;
            $ticket->code = 'TICPDAM-' .rand(10000, 99999);
            $ticket->title = $data['title'];
            $ticket->description = $data['description'];
            $ticket->priority = $data['priority'];

            $ticket->save();

            DB::commit();

            return response()->json([
                'message' => 'Tiket Berhasil Di Kirim',
                'data' => new TicketResource($ticket)
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi Kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    
}
