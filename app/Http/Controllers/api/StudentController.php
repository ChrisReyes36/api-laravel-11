<?php

namespace App\Http\Controllers\api;

use App\Classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Interfaces\StudentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    private StudentRepositoryInterface $studentRepositoryInterface;

    public function __construct(StudentRepositoryInterface $studentRepositoryInterface)
    {
        $this->studentRepositoryInterface = $studentRepositoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->studentRepositoryInterface->getAll();
        return ApiResponseHelper::sendResponse(StudentResource::collection($data), '', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request)
    {
        $data = [
            'name' => $request->name,
            'age' => $request->age
        ];
        DB::beginTransaction();
        try {
            $student = $this->studentRepositoryInterface->store($data);
            DB::commit();
            return ApiResponseHelper::sendResponse(new StudentResource($student), 'Student created successfully', 201);
        } catch (\Exception $e) {
            ApiResponseHelper::rollback($e, 'Student creation failed');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = $this->studentRepositoryInterface->getById($id);
        return ApiResponseHelper::sendResponse(new StudentResource($data), '', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, string $id)
    {
        $data = [
            'name' => $request->name,
            'age' => $request->age
        ];
        DB::beginTransaction();
        try {
            $this->studentRepositoryInterface->update($data, $id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Student updated successfully', 200);
        } catch (\Exception $e) {
            ApiResponseHelper::rollback($e, 'Student update failed');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $this->studentRepositoryInterface->delete($id);
            DB::commit();
            return ApiResponseHelper::sendResponse(null, 'Student deleted successfully', 204);
        } catch (\Exception $e) {
            ApiResponseHelper::rollback($e, 'Student deletion failed');
        }
    }
}
