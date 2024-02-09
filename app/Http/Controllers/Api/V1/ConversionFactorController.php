<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConversionFactorRequest;
use App\Http\Resources\Conversion\ConversionFactorCollection;
use App\Http\Resources\Conversion\ConversionFactorResource;
use App\Models\ConversionFactor;
use Illuminate\Support\Facades\DB;

class ConversionFactorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return (new ConversionFactorCollection(ConversionFactor::all()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ConversionFactorRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $factor = ConversionFactor::create($data);
            DB::commit();
            return $this->success('conversion factor added successfully', new ConversionFactorResource($factor));
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->fail('add conversion factor is failed');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ConversionFactor $conversionFactor)
    {
        return $this->success('conversion factor data', new ConversionFactorResource($conversionFactor));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ConversionFactorRequest $request, ConversionFactor $conversionFactor)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $conversionFactor->update($data);
            DB::commit();
            return $this->success('conversion factor updated successfully', new ConversionFactorResource($conversionFactor));
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->fail('update conversion factor is failed');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConversionFactor $conversionFactor)
    {
        try {
            DB::beginTransaction();
            $conversionFactor->delete();
            DB::commit();
            return $this->success('conversion factor deleted successfully');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->fail('delete conversion factor is failed');
        }
    }
}
