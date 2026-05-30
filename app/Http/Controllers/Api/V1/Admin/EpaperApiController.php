<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreEpaperRequest;
use App\Http\Requests\UpdateEpaperRequest;
use App\Http\Resources\Admin\EpaperResource;
use App\Models\Epaper;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EpaperApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('epaper_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new EpaperResource(Epaper::all());
    }

    public function store(StoreEpaperRequest $request)
    {
        $epaper = Epaper::create($request->all());

        if ($request->input('cover_image', false)) {
            $epaper->addMedia(storage_path('tmp/uploads/' . basename($request->input('cover_image'))))->toMediaCollection('cover_image');
        }

        if ($request->input('pdf_file', false)) {
            $epaper->addMedia(storage_path('tmp/uploads/' . basename($request->input('pdf_file'))))->toMediaCollection('pdf_file');
        }

        return (new EpaperResource($epaper))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Epaper $epaper)
    {
        abort_if(Gate::denies('epaper_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new EpaperResource($epaper);
    }

    public function update(UpdateEpaperRequest $request, Epaper $epaper)
    {
        $epaper->update($request->all());

        if ($request->input('cover_image', false)) {
            if (! $epaper->cover_image || $request->input('cover_image') !== $epaper->cover_image->file_name) {
                if ($epaper->cover_image) {
                    $epaper->cover_image->delete();
                }
                $epaper->addMedia(storage_path('tmp/uploads/' . basename($request->input('cover_image'))))->toMediaCollection('cover_image');
            }
        } elseif ($epaper->cover_image) {
            $epaper->cover_image->delete();
        }

        if ($request->input('pdf_file', false)) {
            if (! $epaper->pdf_file || $request->input('pdf_file') !== $epaper->pdf_file->file_name) {
                if ($epaper->pdf_file) {
                    $epaper->pdf_file->delete();
                }
                $epaper->addMedia(storage_path('tmp/uploads/' . basename($request->input('pdf_file'))))->toMediaCollection('pdf_file');
            }
        } elseif ($epaper->pdf_file) {
            $epaper->pdf_file->delete();
        }

        return (new EpaperResource($epaper))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Epaper $epaper)
    {
        abort_if(Gate::denies('epaper_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $epaper->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
