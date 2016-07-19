<?php

namespace Lara\Http\Controllers;

use Illuminate\Http\Request;
use Cache;
use DateTime;
use DateInterval;

use Lara\Http\Requests;
use Lara\Http\Controllers\Controller;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $query = NULL )
    {
        if ( is_null($query) ) {
            // if no parameter specified - empty means "show all"
            $query = "";
        }

        $persons =  \Lara\Person::whereNotNull( "prsn_ldap_id" )
                                // Look for autofill
                                ->where('prsn_name', 'like', '%' . $query . '%')
                                ->orderBy('prsn_name')
                                ->get(['prsn_name',
                                       'prsn_ldap_id',
                                       'prsn_status',
                                       'clb_id']);
                     
        return response()->json($persons);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
