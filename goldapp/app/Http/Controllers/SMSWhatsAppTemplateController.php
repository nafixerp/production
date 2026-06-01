<?php

namespace App\Http\Controllers;

use App\Models\SmsWhatsappTemplate;
use Illuminate\Http\Request;

class SMSWhatsAppTemplateController extends Controller
{
    public function index()
    {
        $templates = SmsWhatsappTemplate::orderBy('channel')->orderBy('module')->paginate(20);
        return view('settings.sms-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('settings.sms-templates.form', ['template' => new SmsWhatsappTemplate()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'channel' => 'required|in:sms,whatsapp',
            'template_id' => 'nullable|string|max:80',
            'message' => 'required|string',
            'variables_list' => 'nullable|string',
            'module' => 'required|string|max:60',
            'is_active' => 'nullable|boolean',
        ]);
        SmsWhatsappTemplate::create($data);
        return redirect()->route('sms-templates.index')->with('success', 'Template created.');
    }

    public function show(SmsWhatsappTemplate $smsTemplate)
    {
        return view('settings.sms-templates.form', ['template' => $smsTemplate]);
    }

    public function edit(SmsWhatsappTemplate $smsTemplate)
    {
        return view('settings.sms-templates.form', ['template' => $smsTemplate]);
    }

    public function update(Request $request, SmsWhatsappTemplate $smsTemplate)
    {
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'channel' => 'required|in:sms,whatsapp',
            'template_id' => 'nullable|string|max:80',
            'message' => 'required|string',
            'variables_list' => 'nullable|string',
            'module' => 'required|string|max:60',
            'is_active' => 'nullable|boolean',
        ]);
        $smsTemplate->update($data);
        return redirect()->route('sms-templates.index')->with('success', 'Template updated.');
    }

    public function destroy(SmsWhatsappTemplate $smsTemplate)
    {
        $smsTemplate->delete();
        return redirect()->route('sms-templates.index')->with('success', 'Template deleted.');
    }
}
