<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreContentController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => StoreSection::orderBy('page')->orderBy('sort_order')->get()->groupBy('page')]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'page' => ['required','in:home,product'], 'sections' => ['present','array'],
            'sections.*.id' => ['nullable','integer','exists:store_sections,id'],
            'sections.*.section_key' => ['required','string','max:80'], 'sections.*.title' => ['nullable','string','max:255'],
            'sections.*.subtitle' => ['nullable','string','max:255'], 'sections.*.body' => ['nullable','string','max:5000'],
            'sections.*.image_url' => ['nullable','string','max:2048'], 'sections.*.mobile_image_url' => ['nullable','string','max:2048'],
            'sections.*.button_label' => ['nullable','string','max:100'], 'sections.*.button_url' => ['nullable','string','max:2048'],
            'sections.*.sort_order' => ['required','integer','min:0','max:10000'], 'sections.*.is_active' => ['required','boolean'],
            'sections.*.settings' => ['nullable','array'],
        ]);
        DB::transaction(function () use ($data) {
            $ids = [];
            foreach ($data['sections'] as $sectionData) {
                $section = isset($sectionData['id']) ? StoreSection::where('page', $data['page'])->findOrFail($sectionData['id']) : new StoreSection();
                $section->fill([...$sectionData, 'page' => $data['page']]);
                $section->save();
                $ids[] = $section->id;
            }
            StoreSection::where('page', $data['page'])->whereNotIn('id', $ids)->delete();
        });
        return response()->json(['data' => StoreSection::where('page', $data['page'])->orderBy('sort_order')->get()]);
    }
}
