<?php

namespace App\Http\Controllers;

use App\Models\PriceTableAddon;
use App\Models\PriceTableEntry;
use App\Models\PriceTableGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PricingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => PriceTableGroup::where('is_active', true)->with(['entries', 'addons'])->orderBy('sort_order')->get()])->header('Cache-Control', 'public, max-age=60');
    }

    public function adminIndex(): JsonResponse
    {
        return response()->json(['data' => PriceTableGroup::with(['entries', 'addons'])->orderBy('sort_order')->get()]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'groups' => ['required','array','min:1'],
            'groups.*.id' => ['nullable','integer','exists:price_table_groups,id'],
            'groups.*.name' => ['required','string','max:80'],
            'groups.*.currency' => ['required','in:BDT,USD'],
            'groups.*.note' => ['nullable','string','max:2000'],
            'groups.*.sort_order' => ['nullable','integer','min:0','max:10000'],
            'groups.*.is_active' => ['required','boolean'],
            'groups.*.entries' => ['required','array','min:1'],
            'groups.*.entries.*.id' => ['nullable','integer','exists:price_table_entries,id'],
            'groups.*.entries.*.fabric' => ['required','string','max:120'],
            'groups.*.entries.*.moq' => ['nullable','integer','min:1','max:100000'],
            'groups.*.entries.*.price' => ['required','integer','min:0'],
            'groups.*.entries.*.offer_price' => ['nullable','integer','min:0'],
            'groups.*.entries.*.sort_order' => ['nullable','integer','min:0','max:10000'],
            'groups.*.addons' => ['present','array'],
            'groups.*.addons.*.id' => ['nullable','integer','exists:price_table_addons,id'],
            'groups.*.addons.*.category' => ['required','in:sleeve,neck,addon'],
            'groups.*.addons.*.label' => ['required','string','max:120'],
            'groups.*.addons.*.amount' => ['required','integer','min:0'],
            'groups.*.addons.*.sort_order' => ['nullable','integer','min:0','max:10000'],
        ]);

        DB::transaction(function () use ($data) {
            $groupIds = [];
            foreach ($data['groups'] as $groupIndex => $row) {
                $group = isset($row['id']) ? PriceTableGroup::findOrFail($row['id']) : new PriceTableGroup();
                $group->fill(['name' => $row['name'], 'slug' => Str::slug($row['name']), 'currency' => $row['currency'], 'note' => $row['note'] ?? null, 'sort_order' => $row['sort_order'] ?? $groupIndex, 'is_active' => $row['is_active']]);
                $group->save();
                $groupIds[] = $group->id;
                $entryIds = [];
                foreach ($row['entries'] as $entryIndex => $entryData) {
                    $entry = isset($entryData['id']) ? $group->entries()->findOrFail($entryData['id']) : new PriceTableEntry();
                    $entry->fill(['fabric' => $entryData['fabric'], 'moq' => $entryData['moq'] ?? null, 'price' => $entryData['price'], 'offer_price' => $entryData['offer_price'] ?? null, 'sort_order' => $entryData['sort_order'] ?? $entryIndex]);
                    $group->entries()->save($entry);
                    $entryIds[] = $entry->id;
                }
                $group->entries()->whereNotIn('id', $entryIds)->delete();
                $addonIds = [];
                foreach ($row['addons'] as $addonIndex => $addonData) {
                    $addon = isset($addonData['id']) ? $group->addons()->findOrFail($addonData['id']) : new PriceTableAddon();
                    $addon->fill(['category' => $addonData['category'], 'label' => $addonData['label'], 'amount' => $addonData['amount'], 'sort_order' => $addonData['sort_order'] ?? $addonIndex]);
                    $group->addons()->save($addon);
                    $addonIds[] = $addon->id;
                }
                $group->addons()->whereNotIn('id', $addonIds ?: [0])->delete();
            }
            PriceTableGroup::whereNotIn('id', $groupIds)->delete();
        });

        return $this->adminIndex();
    }
}
