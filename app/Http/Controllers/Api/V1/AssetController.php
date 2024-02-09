<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetRequest;
use App\Http\Resources\Asset\AssetCollection;
use App\Http\Resources\Asset\AssetResource;
use App\Models\Asset;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return (new AssetCollection(Asset::all()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssetRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $asset = Asset::create($data);
            DB::commit();
            return $this->success('asset added successfully', new AssetResource($asset));
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->fail('add asset is failed');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        return $this->success('asset data', new AssetResource($asset));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssetRequest $request, Asset $asset)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $asset->update($data);
            DB::commit();
            return $this->success('asset updated successfully', new AssetResource($asset));
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->fail('update asset is failed');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        try {
            DB::beginTransaction();
            $asset->delete();
            DB::commit();
            return $this->success('asset deleted successfully');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->fail('delete asset is failed');
        }
    }
}
