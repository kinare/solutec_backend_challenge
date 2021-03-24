<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ApiResponser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class BaseController extends Controller
{
    use ApiResponser;

    protected $model;
    protected $resource;

    public function __construct($model = null, $resource = null)
    {
        $this->model = $model;
        $this->resource = $resource;
    }

    /**
     * Display a listing of the resource.
     * @return Collection|Model[]|\Illuminate\Http\JsonResponse|AnonymousResourceCollection
     */
    public function index()
    {
        try{
            return $this->response($this->model::all());
        }catch (\Exception $exception){
            return $this->error($exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Collection|Model|\Illuminate\Http\JsonResponse|AnonymousResourceCollection|Response
     */
    public function store(Request $request)
    {
        try{
            $model = new $this->model();
            $data = $request->all();
            $model->fill($data);
            $model->save();
            return $this->response($model);
        }catch (\Exception $exception){
            return $this->error($exception->getMessage());

        }
    }

    /**
     * Display the specified resource.
     * @param $id
     * @return Collection|Model|\Illuminate\Http\JsonResponse|AnonymousResourceCollection|Response
     */
    public function show($id)
    {
        try{
            return $this->response( $this->model::find($id));
        }catch (\Exception $exception){
            return $this->error($exception->getMessage());

        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param $id
     * @return Collection|Model|\Illuminate\Http\JsonResponse|AnonymousResourceCollection
     */
    public function update(Request $request, $id)
    {
        try{
            $model = $this->model::find($id);
            $model->fill($request->all());
            $model->save();
            return $this->response($model);
        }catch (\Exception $exception){
            return $this->error($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function destroy($id)
    {
        try{
            $model = $this->model::find($id);
            $model->delete();

            return $this->success([], $this->model.' deleted');
        }catch (\Exception $exception){
            return $this->error($exception->getMessage());
        }
    }

    /**
     * Force remove the specified resource from storage.
     * @param $id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function forceDestroy($id)
    {
        try{
            $model = $this->model::find($id);
            $model->forceDelete();

            return $this->success([], $this->model.' deleted');
        }catch (\Exception $exception){
            return $this->error($exception->getMessage());
        }
    }

    /**
     * Return responses from resources
     * @param $result
     */
    public function response($result)
    {
        if ($result instanceof Model){
            $data = $this->resource ? new $this->resource($result) : new JsonResource($result);
            return $this->success($data);
        }

        if ($result instanceof Collection){
            $data = $this->resource ? $this->resource::collection($result) : JsonResource::collection($result);
            return $this->success($data);
        }
    }
}
