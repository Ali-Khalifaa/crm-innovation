<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ColorRequest;
use App\Http\Resources\Dashboard\ColorResource;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ColorController extends Controller implements HasMiddleware
{


    public static function middleware(): array
    {
        return [
            new Middleware('can:color read', only: ['index']),
            new Middleware('can:color create', only: ['store']),
            new Middleware('can:color edit', only: ['update', 'show']),
            new Middleware('can:color delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $colors = Color::searchAndFilter()->latest()->paginate(10);

        return responseJson(ColorResource::collection($colors->items()),'',200,getPaginates($colors));
    }



    public function store(ColorRequest $request)
    {
        $data = $request->validated();
        $color = Color::create($data);
        $color->setTranslations($request->translations);
        return responseJson([],'Created Successfully',200);
    }


    public function show($id)
    {
        $color = Color::with('translations')->find($id);
        return responseJson($color,'Data exited successfully',200);
    }

    public function update(ColorRequest $request,$id)
    {
        $data = $request->validated();
        $color = Color::find($id);
        $color->update($data);
        $color->setTranslations($request->translations);
        return responseJson($color,'Updated Successfully',200);
    }

    public function destroy($id)
    {
        $color = Color::find($id);
        $color->delete();
        return responseJson([],'Deleted Successfully',200);
    }

    public function dropdown()
    {
        $color = Color::all();

        return responseJson(ColorResource::collection($color),'',200);
    }
}
