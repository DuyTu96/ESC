<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ImagePath\ImagePathRepositoryInterface;
use App\Enums\ErrorType;
use App\Enums\DBConstant;
use App\Exceptions\SystemException;
use Illuminate\Support\Facades\Storage;

class ImageService extends BaseService
{
    protected $imagePathRepository;

    public function __construct(ImagePathRepositoryInterface $imagePathRepository)
    {
        $this->imagePathRepository = $imagePathRepository;
    }

    public function uploadImageToS3($image, $companyId = null, $businessCardId = null, $imageType = "profiles")
    {
        // Set image object
        if ($businessCardId == null) {
            $object = 'companies';
            $objectId = $companyId;
        } else if ($companyId == null) {
            $object = 'business-cards';
            $objectId = $businessCardId;
        }

        // Create image file name
        $imageFileName = $this->createImageFileName($objectId, $image);

        // Create image directory path
        $imageDirPath = $this->createImageDirPath($object, $objectId, $imageType);

        // Create image file path
        $imageFilePath = config('filesystems.disks.s3.url') . '/' . $imageDirPath . $imageFileName;

        try {
            // Check if the image path entity doesn't exist
            $currentImagePathEntity = $this->imagePathRepository->checkImageExisted($object, $objectId);

            // remove old image if existed
            if (!empty($currentImagePathEntity)) {
                $this->deleteImageFromS3($object, $objectId);
            }

            // Upload to S3
            Storage::disk('s3')->putFileAs($imageDirPath, $image, $imageFileName);

            if (Storage::disk('s3')->exists($imageFilePath)) {
                throw new SystemException(ErrorType::CODE_5003, __('errors.MSG_5003'), ErrorType::STATUS_5003);
            }

            // Create a record in image_paths table.
            $imagePathEntity = $this->imagePathRepository->create([
                'company_id' => $companyId,
                'business_card_id' => $businessCardId,
                'file_name' => $imageFileName,
                'dir_path' => $imageDirPath,
                'image_url' => $imageFilePath,
                'display_order' => DBConstant::IMAGE_PATH_DISPLAY_ORDER
            ]);

            $imageUrl = $imagePathEntity->image_url;

            return $imageUrl;
        } catch (\Exception $e) {
            // Delete the image from Amazon S3 if it exists
            if (Storage::disk('s3')->exists($imageFilePath)) {
                Storage::disk('s3')->delete($object, $objectId);
            }

            return;
        }
    }

    public function deleteImageFromS3($object, $objectId)
    {
        try {
            // Check if the image path entity exists
            $imagePathEntity = $this->imagePathRepository->checkImageExisted($object, $objectId);
            if (empty($imagePathEntity)) {
                return;
            }

            // Create image file path
            $imageFilePath = $imagePathEntity->dir_path . $imagePathEntity->file_name;

            // Delete the image from Amazon S3
            if (Storage::disk('s3')->exists($imageFilePath)) {
                Storage::disk('s3')->delete($imageFilePath);
            }

            // Delete image path entity
            $imagePathEntity->delete();

            return $imagePathEntity;
        } catch (\Exception $e) {
            return;
        }
    }

    public function getS3Url($object, $objectId, $imageType = "profiles")
    {
        try {
            $imagePathEntity = $this->imagePathRepository->checkImageExisted($object, $objectId);
            if (empty($imagePathEntity)) {
                return '';
            }

            $path = $imagePathEntity->dir_path . $imagePathEntity->file_name;

            $disk = Storage::disk('s3');

            if ($disk->exists($path)) {
                $s3Client = $disk->getDriver()->getAdapter()->getClient();
                $command = $s3Client->getCommand(
                    'GetObject',
                    [
                        'Bucket' => config('filesystems.disks.s3.bucket'),
                        'Key' => $path,
                        'ResponseContentDisposition' => 'attachment;',
                    ]
                );

                $request = $s3Client->createPresignedRequest($command, '+1440 minutes');

                return (string) $request->getUri();
            }
        } catch (\Exception $e) {
            return '';
        }
    }

    private function createImageFileName($objectId, $image): string
    {
        $imageFilename = str_pad((string) $objectId, 11, '0', STR_PAD_LEFT) . time() . '.' . $image->getClientOriginalExtension();

        return $imageFilename;
    }

    private function createImageDirPath($object, $objectId, $imageType): string
    {
        $imageDirPath = $object . '/' . $objectId . '/' . $imageType . '/';

        return $imageDirPath;
    }
}
