<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use App\Http\Resources\Setting\SettingCollection;
use App\Http\Resources\Setting\SettingResource;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
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
        return (new SettingCollection(Setting::all()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SettingRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $setting = Setting::create($data);
            DB::commit();
            return $this->success('setting added successfully', new SettingResource($setting));
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->fail('add setting is failed');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        return $this->success('setting data', new SettingResource($setting));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SettingRequest $request, Setting $setting)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $setting->update($data);
            DB::commit();
            return $this->success('setting updated successfully', new SettingResource($setting));
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->fail('update setting is failed');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        try {
            DB::beginTransaction();
            $setting->delete();
            DB::commit();
            return $this->success('setting deleted successfully');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->fail('delete setting is failed');
        }
    }
}
