<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Common\ContactUsStoreRequest;
use App\Http\Controllers\Api\ApiController;
use App\Services\InquiryService;

class InquiryController extends ApiController
{   
    private $inquiryService;

    public function __construct(InquiryService $inquiryService)
    {
        $this->inquiryService = $inquiryService;
    }

    /**
     * Get business card list
     * @author huydn
     * @return $data
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContactUsStoreRequest $request)
    {
        $request = $request->only([
            'name',
            'email',
            'inquiry_type',
            'body'
        ]);
        $data = $this->inquiryService->create($request);

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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getType()
    {
        $data = $this->inquiryService->getType();

        return $this->sendSuccess($data, trans('response.success'));
    }
}
