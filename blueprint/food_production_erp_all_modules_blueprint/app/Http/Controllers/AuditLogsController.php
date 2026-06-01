<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditLogsController extends Controller
{
    public function index() { /* list */ }
    public function create() { /* create form */ }
    public function store(Request $request) { /* save */ }
    public function show($id) { /* view */ }
    public function edit($id) { /* edit form */ }
    public function update(Request $request, $id) { /* update */ }
    public function destroy($id) { /* delete/cancel */ }
}
