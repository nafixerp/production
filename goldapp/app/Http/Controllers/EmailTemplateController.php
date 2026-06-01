<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::orderBy('module')->orderBy('name')->paginate(20);
        return view('settings.email-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('settings.email-templates.form', ['template' => new EmailTemplate()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables_list' => 'nullable|string',
            'module' => 'required|string|max:60',
            'is_active' => 'nullable|boolean',
        ]);
        EmailTemplate::create($data);
        return redirect()->route('email-templates.index')->with('success', 'Email template created.');
    }

    public function show(EmailTemplate $emailTemplate)
    {
        return view('settings.email-templates.preview', ['template' => $emailTemplate]);
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        return view('settings.email-templates.form', ['template' => $emailTemplate]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables_list' => 'nullable|string',
            'module' => 'required|string|max:60',
            'is_active' => 'nullable|boolean',
        ]);
        $emailTemplate->update($data);
        return redirect()->route('email-templates.index')->with('success', 'Template updated.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();
        return redirect()->route('email-templates.index')->with('success', 'Template deleted.');
    }

    public function preview(EmailTemplate $emailTemplate, Request $request)
    {
        $sampleData = [];
        if ($emailTemplate->variables_list) {
            foreach (explode(',', $emailTemplate->variables_list) as $var) {
                $var = trim($var);
                $sampleData[$var] = "[{$var}]";
            }
        }
        $previewBody = $emailTemplate->body;
        foreach ($sampleData as $key => $val) {
            $previewBody = str_replace("{{$key}}", $val, $previewBody);
        }
        return view('settings.email-templates.preview', compact('emailTemplate', 'previewBody'));
    }
}
