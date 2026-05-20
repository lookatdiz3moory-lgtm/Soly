<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FaqManagementController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Faq::class);
        $faqs = Faq::orderBy('category')->orderBy('sort_order')->orderBy('id')->get();

        return view()->exists('admin.faqs.index')
            ? view('admin.faqs.index', compact('faqs'))
            : view('admin.placeholder', [
                'pageTitle' => 'FAQs',
                'records'   => $faqs->count() . ' FAQs',
                'faqs'      => $faqs,
            ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Faq::class);
        Faq::create($this->validateFaq($request));
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ added.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $faq = Faq::findOrFail($id);
        Gate::authorize('update', $faq);
        $faq->update($this->validateFaq($request));
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $faq = Faq::findOrFail($id);
        Gate::authorize('delete', $faq);
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ removed.');
    }

    private function validateFaq(Request $request): array
    {
        return $request->validate([
            'category'    => ['nullable', 'string', 'max:80'],
            'question'    => ['required', 'string', 'max:500'],
            'question_ar' => ['nullable', 'string', 'max:500'],
            'answer'      => ['required', 'string', 'max:5000'],
            'answer_ar'   => ['nullable', 'string', 'max:5000'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active'   => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
