<?php

namespace App\Http\Controllers;

use App\Link;
use Illuminate\Http\Request;

class LinksController extends Controller
{
    public function index(Request $request)
    {
        $links = Link::orderBy('title', 'asc')->paginate(2);
        return view('links.index')->withLinks($links);
    }

    public function create(Request $request)
    {
        return view('links.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|max:50',
            'url' => 'required|url|unique:links',
            'description' => 'required|max:200'
        ]);

       Link::create($data);

       return back()->with(['message' => 'Link created successfully']);
    }

    public function edit(Request $request, $id)
    {
        $link = Link::find($id);

        abort_if(!$link, 404, 'Link not found.');

        return view('links.edit')->withLink($link);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|max:50',
            'url' => "required|url|unique:links,url,$id",
            'description' => 'required|max:200'
        ]);

        $link = Link::find($id);
        abort_if(!$link, 404, 'Link not found');

        $link->update($data);

        return back()->with(['message' => 'Link updated successfully']);
    }

    public function destroy(Request $request)
    {
        Link::find($request->linkID)->delete();

        return back()->with(['message' => 'Link Deleted']);
    }
}
