<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\ErrorType;
use Illuminate\Http\Request;
use App\Services\PositionService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Admin\Position\CreatePositionRequest;

class PositionController extends ApiController
{
    private $positionService;

    public function __construct(PositionService $positionService)
    {
        $this->positionService = $positionService;
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
     * Store a newly position
     * @param Request $request
     * @return JsonResponse
     *
     */
    public function store(CreatePositionRequest $request)
    {
        $formData = $request->all();
        $data = $this->positionService->createPosition($formData);
        
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
     * Remove the specified position
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $companyId = $this->positionService->getCompanyIdByPosition($id);

        if ($companyId != $this->getGuard()->user()->company_id) {
            return $this->sendError(ErrorType::CODE_4030, ErrorType::STATUS_4030, trans('errors.MSG_4030'));
        }

        $data = $this->positionService->delete($id);
        
        return $this->sendSuccess($data, trans('response.success'));
    }
}
