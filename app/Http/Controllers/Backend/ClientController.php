<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Client;
use App\Http\Requests\ClientStore;
use App\Http\Requests\ClientUpdate;

class ClientController extends Controller
{
    public function index()
    {
    	$data = Client::orderBy('name', 'ASC')->get();
        return view('backend.client.index', compact('data'));
    }

    public function create()
    {
        return view('backend.client.create');
    }

    public function store(ClientStore $request)
    {
        if (Client::_storing($request)) {
            return redirect()->route('client.index')->with('success', 'The client has been created.');
        }
        return back()->withInput()->with('error', 'Sorry, could not create client at this time. Please try again later.');
    }

    public function edit($uuid)
    {
    	if ($data = Client::where('uuid', $uuid)->first()) { 
        	return view('backend.client.edit', compact('data'));
        }
        return back()->with('warning', 'The client you want to edit does not exist.');
    }

    public function update(ClientUpdate $request, $uuid)
    {
        if (Client::_updating($request, $uuid)) {
            return redirect()->route('client.index')->with('success', 'The client has been updated.');
        }
        return back()->withInput()->with('error', 'Sorry, could not update client at this time. Please try again later.');
    }

    public function change_status($uuid)
    {
    	if (Client::_change_status($uuid)) {
    		return back()->with('success', 'The client status has been changed.');
    	}
        return back()->with('error', 'Sorry, could not change status of the client at this time. Please try again later.');
    }
}