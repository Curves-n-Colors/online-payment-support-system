<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Backend\CategoryService;

class CategoriesController extends Controller
{
    public function index()
    {
        $data = CategoryService::_get();
        return view('backend.categories.index',compact('data'));
    }

    public function create()
    {
        return view('backend.categories.create');
    }

    public function store(Request $request)
    {
        if (CategoryService::_storing($request)) {
            return redirect()->route('categories.index')->with('success', 'The caetgory has been created.');
        }
        return back()->withInput()->with('error', 'Sorry, could not create caetgory at this time. Please try again later.');
    }

    public function change_status($id)
    {
        if (CategoryService::_change_status($id)) {
            return back()->with('success', 'The caetgory status has been changed.');
        }
        return back()->with('error', 'Sorry, could not change status of the caetgory at this time. Please try again later.');
    }

    public function update($id, Request $request)
    {
        if (CategoryService::_updating($request, $id)) {
            return redirect()->route('categories.index')->with('success', 'The caetgory has been updated.');
        }
        return back()->withInput()->with('error', 'Sorry, could not update caetgory at this time. Please try again later.');
    }
}
