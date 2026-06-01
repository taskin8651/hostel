<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class DedicatedHostelModuleController extends Controller
{
    protected string $moduleKey;

    protected function hostelController(): HostelModuleController
    {
        return app(HostelModuleController::class);
    }

    public function index(Request $request)
    {
        return $this->hostelController()->index($request, $this->moduleKey);
    }

    public function create()
    {
        return $this->hostelController()->create($this->moduleKey);
    }

    public function store(Request $request)
    {
        return $this->hostelController()->store($request, $this->moduleKey);
    }

    public function show(int $id)
    {
        return $this->hostelController()->show($this->moduleKey, $id);
    }

    public function edit(int $id)
    {
        return $this->hostelController()->edit($this->moduleKey, $id);
    }

    public function update(Request $request, int $id)
    {
        return $this->hostelController()->update($request, $this->moduleKey, $id);
    }

    public function destroy(int $id)
    {
        return $this->hostelController()->destroy($this->moduleKey, $id);
    }

    public function massDestroy(Request $request)
    {
        return $this->hostelController()->massDestroy($request, $this->moduleKey);
    }
}
