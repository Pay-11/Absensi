<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssessmentCategory;
use App\Http\Controllers\Concerns\ExportsCsv;

class AssessmentCategoryController extends Controller
{
    use ExportsCsv;
    public function index()
    {
        $categories = AssessmentCategory::orderBy('name')->get();
        return view('pages.assessment-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:assessment_categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        AssessmentCategory::create([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => true,
        ]);

        return redirect()->route('assessment-categories.index')
            ->with('success', 'Kategori assessment berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $category = AssessmentCategory::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255|unique:assessment_categories,name,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('assessment-categories.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    public function toggleActive($id)
    {
        $category = AssessmentCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('assessment-categories.index')
            ->with('success', "Kategori berhasil $status");
    }

    public function destroy($id)
    {
        $category = AssessmentCategory::findOrFail($id);

        // Cek apakah sudah dipakai di assessment_details
        if ($category->assessmentDetails()->exists()) {
            return redirect()->route('assessment-categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena sudah digunakan dalam data assessment');
        }

        $category->delete();

        return redirect()->route('assessment-categories.index')
            ->with('success', 'Kategori berhasil dihapus');
    }

    public function export()
    {
        $categories = AssessmentCategory::orderBy('name')->get();

        $headers = ['#', 'Nama Kategori', 'Deskripsi', 'Status'];
        $rows = [];
        foreach ($categories as $i => $cat) {
            $rows[] = [$i+1, $cat->name, $cat->description ?? '-', $cat->is_active ? 'Aktif' : 'Nonaktif'];
        }

        return $this->csvResponse($headers, $rows, 'kategori-assessment-' . now()->format('Ymd'), '7a1a1a', 'faf0f0');
    }
}
