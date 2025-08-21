<?php

namespace App\Http\Controllers;


use App\Models\postingan;
use App\Models\Ticket;
use App\Models\Sponsorships;
use Illuminate\Http\Request;
use App\Services\PostinganService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }


    public function index(PostinganService $postinganService)
    {
        $postingans = postingan::orderBy("created_at", "desc")->take(9)->get();
        $ticket = Ticket::available()->with("ticket_benefit")->get();
        $dataSponsor = Sponsorships::all();

        $ticket = \App\Models\Ticket::with('ticket_benefit')
            ->where('quantity', '>', 0)
            ->get();

        // Debug
        Log::info('Tickets count: ' . $ticket->count());
        return view('index2', compact('postingans', 'ticket', 'dataSponsor'));
    }

    function sendMedpart()
    {
        return Redirect::away('https://wa.me/6289501822030');
    }
    function sendSponsorship()
    {
        return Redirect::away('https://wa.me/6285643193770');
    }
}
