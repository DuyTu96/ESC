<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\ErrorType;
use Illuminate\Http\Request;
use App\Services\DepartmentService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Admin\Department\CreateDepartmentRequest;

class DepartmentController extends ApiController
{
    private $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly department
     * @param Request $request
     * @return JsonResponse
     *
     */
    public function store(CreateDepartmentRequest $request)
    {   
        $formData = $request->all();
        $data = $this->departmentService->createDepartment($formData);

        return $this->sendSuccess($data, trans('response.success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified department
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $companyId = $this->departmentService->getCompanyIdByDepartment($id);

        if ($companyId != $this->getGuard()->user()->company_id) {
            return $this->sendError(ErrorType::CODE_4030, ErrorType::STATUS_4030, trans('errors.MSG_4030'));
        }

        $data = $this->departmentService->delete($id);

        return $this->sendSuccess($data, trans('response.success'));
    }
}
