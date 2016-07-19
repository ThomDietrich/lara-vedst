<?php

namespace Lara\Http\Controllers;

use Request;
use Redirect;
use View;

use Lara\Http\Requests;
use Lara\Http\Controllers\Controller;

use Lara\ClubEvent;

class YearController extends Controller {

    /** 
     * Fills missing parameters - if no year specified use current year.
     * 
     * @return void
     */
    public function currentYear()
    {
        return Redirect::action( 'YearController@showYear', ['year' => date("Y")] );                                                          
    }


    /**
     * Generates the view for the list of all events in a year.
     *
     * @param  int $year
     *
     * @return view calendarView
     * @return ClubEvent[] $events
     * @return string $date
     */      
    public function showYear($year)
    {
        $yearStart = $year.'01'.'01';
        $yearEnd = $year.'12'.'31';

        $date = date("Y", strtotime($yearStart));

        $events = ClubEvent::where('evnt_date_start','>=',$yearStart)
                           ->where('evnt_date_start','<=',$yearEnd)
                           ->with('getPlace')
                           ->orderBy('evnt_date_start')
                           ->orderBy('evnt_time_start')
                           ->paginate(15);

        return View::make('listView', compact('events','date'));
    }
}