<?php

declare(strict_types=1);

namespace App\Repositories\Inquiry;

interface InquiryRepositoryInterface
{
    /**
     * Get shop sale report list.
     * @author skrum
     * @param $request
     * @return array
     */
    public function getInquiries($request);

    /**
     * Get detail.
     * @author skrum
     * @param $id
     * @return array
     */
    public function getDetail($id);

    /**
     * Create inquiry.
     * @author skrum
     * @param $request
     * @return boolean
     */
    public function create($request);

    public function send($request);

    public function getNumberInquiryNoRead();

    public function getShopInquirieslist($request);

    public function getShopInquiryDetail($id);

    public function createShopInquiry($request);

    public function sendMessageResponse($request);

    public function getNumberShopInquiryNoRead($request);

    public function updateReadShopInquiry($id);
}
