<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Balance\BalanceCollection;
use App\Http\Resources\Balance\BalanceLogCollection;
use App\Http\Resources\Balance\BalanceResource;
use App\Models\Asset;
use App\Models\Balance;
use App\Models\ConversionFactor;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BalanceController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkUserHasAssets();
        return (new BalanceCollection(Balance::where('user_id', auth()->id())->get()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        $balance = Balance::firstOrCreate(['user_id' => auth()->id(), 'asset_id' => $asset->id]);
        return $this->success('balance data', new BalanceResource($balance));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $balance = Balance::firstOrCreate(['user_id' => auth()->id(), 'asset_id' => $asset->id]);
        $request->validate([
            'change' => ['required', 'numeric', function ($attribute, $value, $fail) use ($balance) {
                if ($balance->amount + $value < 0) {
                    $fail('balance is not enough.');
                }
            }],
        ]);
        try {
            DB::beginTransaction();
            $balance->update(['amount' => $balance->amount + $request->input('change')]);
            $this->createBalanceLog($asset, $request->input('change'), $balance->amount);
            DB::commit();
            return $this->success('balance updated successfully', new BalanceResource($balance));
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->fail('update balance is failed');
        }
    }

    public function convert(Request $request)
    {
        $request->validate([
            'count' => ['required', 'numeric', 'gt:0'],
            'from_asset' => ['required', 'exists:assets,id'],
            'to_asset' => ['required', 'exists:assets,id', 'different:from_asset'],
        ]);

        $fromAsset = Asset::find($request->input('from_asset'));
        $toAsset = Asset::find($request->input('to_asset'));
        $count = $request->input('count');
        $fromAssetBalance = Balance::firstOrCreate(['user_id' => auth()->id(), 'asset_id' => $fromAsset->id]);

        if ($fromAsset->price * $count * (1 - $this->getFee() / 100) < $toAsset->price) {
            return $this->fail("The price of the selected assets is less than the desired asset.");
        } elseif ($fromAssetBalance->amount < $count) {
            return $this->fail("The balance of the selected asset is less than selected count.");
        }

        try {
            DB::beginTransaction();
            $this->updateBalancesInConversion($fromAsset, $toAsset, $count);
            DB::commit();
            return $this->success('asset converted successfully');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->fail('convert asset is failed');
        }
    }

    public function log(Asset $asset)
    {
        $balanceLogs = auth()->user()->balanceLogs()->where('asset_id', $asset->id)->get();
        return (new BalanceLogCollection($balanceLogs));
    }

    private function checkUserHasAssets()
    {
        $assets = Asset::all()->pluck('id')->toArray();
        $userAssetIds = auth()->user()->balances()->pluck('asset_id')->toArray();
        $missingAssets = array_diff($assets, $userAssetIds);
        foreach ($missingAssets as $missingAssetId) {
            auth()->user()->balances()->create([
                'asset_id' => $missingAssetId,
                'amount' => 0,
            ]);
        }
    }

    private function createBalanceLog($asset, $change, $balanceAmount, $convertTo = null)
    {
        $oldBalanceAmount = $balanceAmount - $change;
        $extraDescription = $convertTo ? " due to the conversion of $asset->name to $convertTo->name." : "";
        auth()->user()->balanceLogs()->create([
            'asset_id' => $asset->id,
            'change' => $change,
            'balance' => $balanceAmount,
            'description' => "balance is changed from $oldBalanceAmount to $balanceAmount" . $extraDescription,
        ]);
    }

    private function getFee()
    {
        $fee = ConversionFactor::where('from_asset_id', request('from_asset'))
            ->where('to_asset_id', request('to_asset'))
            ->first();
        return $fee->fee ?? (Setting::where('name', 'fee')->first()->value ?? 0);
    }

    private function updateBalancesInConversion($fromAsset, $toAsset, $count)
    {
        $fee = $this->getFee();
        $fromAssetBalance = $this->getUserAssetBalance($fromAsset);
        $toAssetBalance = $this->getUserAssetBalance($toAsset);

        $fromAssetBalance->update(['amount' => $fromAssetBalance->amount - $count]);
        $this->createBalanceLog($fromAsset, -$count, $fromAssetBalance->amount, $toAsset);

        $toAssetCount = round($fromAsset->price * $count * (1 - $fee / 100) / $toAsset->price, 2);
        $toAssetBalance->update(['amount' => $toAssetBalance->amount + $toAssetCount]);
        $this->createBalanceLog($toAsset, $toAssetCount, $toAssetBalance->amount);
    }

    private function getUserAssetBalance($asset)
    {
        return Balance::firstOrCreate(['user_id' => auth()->id(), 'asset_id' => $asset->id]);
    }

}
