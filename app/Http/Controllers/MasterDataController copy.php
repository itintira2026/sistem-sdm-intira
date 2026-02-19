<?php

namespace App\Http\Controllers;

use App\Models\ReportCategory;
use App\Models\ReportField;
use App\Models\ValidationAction;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    // =========================================================
    // MIDDLEWARE — superadmin & marketing saja
    // =========================================================

    // public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //         if (! auth()->user()->hasAnyRole(['superadmin', 'marketing'])) {
    //             abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    //         }
    //         return $next($request);
    //     });
    // }

    // =========================================================
    // KATEGORI LAPORAN
    // =========================================================

    public function categoriesIndex()
    {
        $categories = ReportCategory::orderBy('order')->get();

        return view('master.categories.index', compact('categories'));
    }

    public function categoriesStore(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'code'      => 'required|string|max:50|unique:report_categories,code|alpha_dash',
            'color'     => 'required|string|max:50',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ], [
            'code.unique'     => 'Kode kategori sudah digunakan.',
            'code.alpha_dash' => 'Kode hanya boleh huruf, angka, strip, dan underscore.',
        ]);

        ReportCategory::create([
            'name'      => $validated['name'],
            'code'      => $validated['code'],
            'color'     => $validated['color'],
            'order'     => $validated['order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function categoriesUpdate(Request $request, ReportCategory $category)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'code'      => 'required|string|max:50|alpha_dash|unique:report_categories,code,' . $category->id,
            'color'     => 'required|string|max:50',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $category->update([
            'name'      => $validated['name'],
            'code'      => $validated['code'],
            'color'     => $validated['color'],
            'order'     => $validated['order'] ?? $category->order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function categoriesDestroy(ReportCategory $category)
    {
        if ($category->fields()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki field.');
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    // =========================================================
    // FIELD LAPORAN
    // =========================================================

    public function fieldsIndex()
    {
        $fields     = ReportField::with('category')->orderBy('order')->get();
        $categories = ReportCategory::active()->ordered()->get();
        $inputTypes = config('daily_report_fo.input_types', []);

        return view('master.fields.index', compact('fields', 'categories', 'inputTypes'));
    }

    public function fieldsStore(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:150',
            'code'               => 'required|string|max:50|unique:report_fields,code|alpha_dash',
            'report_category_id' => 'required|exists:report_categories,id',
            'input_type'         => 'required|in:' . implode(',', array_keys(config('daily_report_fo.input_types', []))),
            'is_required'        => 'nullable|boolean',
            'is_active'          => 'nullable|boolean',
            'order'              => 'nullable|integer|min:0',
            'helper_text'        => 'nullable|string|max:255',
        ], [
            'code.unique'     => 'Kode field sudah digunakan.',
            'code.alpha_dash' => 'Kode hanya boleh huruf, angka, strip, dan underscore.',
        ]);

        ReportField::create([
            'name'        => $validated['name'],
            'code'        => $validated['code'],
            'category_id' => $validated['report_category_id'],
            'input_type'  => $validated['input_type'],
            'is_required' => $request->boolean('is_required', false),
            'is_active'   => $request->boolean('is_active', true),
            'order'       => $validated['order'] ?? 0,
            'helper_text' => $validated['helper_text'] ?? null,
        ]);

        return back()->with('success', 'Field berhasil ditambahkan.');
    }

    public function fieldsUpdate(Request $request, ReportField $field)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:150',
            'code'               => 'required|string|max:50|alpha_dash|unique:report_fields,code,' . $field->id,
            'report_category_id' => 'required|exists:report_categories,id',
            'input_type'         => 'required|in:' . implode(',', array_keys(config('daily_report_fo.input_types', []))),
            'is_required'        => 'nullable|boolean',
            'is_active'          => 'nullable|boolean',
            'order'              => 'nullable|integer|min:0',
            'helper_text'        => 'nullable|string|max:255',
        ]);

        $field->update([
            'name'        => $validated['name'],
            'code'        => $validated['code'],
            'category_id' => $validated['report_category_id'],
            'input_type'  => $validated['input_type'],
            'is_required' => $request->boolean('is_required', false),
            'is_active'   => $request->boolean('is_active', true),
            'order'       => $validated['order'] ?? $field->order,
            'helper_text' => $validated['helper_text'] ?? null,
        ]);

        return back()->with('success', 'Field berhasil diperbarui.');
    }

    public function fieldsDestroy(ReportField $field)
    {
        if ($field->details()->count() > 0) {
            return back()->with('error', 'Field tidak bisa dihapus karena sudah memiliki data laporan.');
        }

        $field->delete();

        return back()->with('success', 'Field berhasil dihapus.');
    }

    // =========================================================
    // TINDAKAN VALIDASI
    // =========================================================

    public function validationActionsIndex()
    {
        $actions = ValidationAction::orderBy('order')->get();

        return view('master.validation-actions.index', compact('actions'));
    }

    public function validationActionsStore(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'code'      => 'required|string|max:50|unique:validation_actions,code|alpha_dash',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ], [
            'code.unique'     => 'Kode tindakan sudah digunakan.',
            'code.alpha_dash' => 'Kode hanya boleh huruf, angka, strip, dan underscore.',
        ]);

        ValidationAction::create([
            'name'      => $validated['name'],
            'code'      => $validated['code'],
            'order'     => $validated['order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Tindakan validasi berhasil ditambahkan.');
    }

    public function validationActionsUpdate(Request $request, ValidationAction $action)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'code'      => 'required|string|max:50|alpha_dash|unique:validation_actions,code,' . $action->id,
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $action->update([
            'name'      => $validated['name'],
            'code'      => $validated['code'],
            'order'     => $validated['order'] ?? $action->order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Tindakan validasi berhasil diperbarui.');
    }

    public function validationActionsDestroy(ValidationAction $action)
    {
        if ($action->validations()->count() > 0) {
            return back()->with('error', 'Tindakan tidak bisa dihapus karena sudah digunakan di laporan.');
        }

        $action->delete();

        return back()->with('success', 'Tindakan validasi berhasil dihapus.');
    }
}
