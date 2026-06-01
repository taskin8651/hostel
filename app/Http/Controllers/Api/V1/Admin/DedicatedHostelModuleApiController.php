<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class DedicatedHostelModuleApiController extends Controller
{
    protected string $moduleKey;

    protected function hostelController(): HostelModuleApiController
    {
        return app(HostelModuleApiController::class);
    }

    public function index(Request $request)
    {
        return $this->hostelController()->index($request, $this->moduleKey);
    }

    public function store(Request $request)
    {
        return $this->hostelController()->store($request, $this->moduleKey);
    }

    public function show(int $id)
    {
        return $this->hostelController()->show($this->moduleKey, $id);
    }

    public function update(Request $request, int $id)
    {
        return $this->hostelController()->update($request, $this->moduleKey, $id);
    }

    public function updateStatus(Request $request, int $id)
    {
        return $this->hostelController()->updateStatus($request, $this->moduleKey, $id);
    }

    public function destroy(int $id)
    {
        return $this->hostelController()->destroy($this->moduleKey, $id);
    }
}
