<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DBConstant;
use App\Enums\ErrorType;
use App\Exceptions\SystemException;
use App\Models\ImagePath;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

class ImageService extends BaseService
{
    /**
     * Upload an image to Amazon S3.
     *
     * @param int    $shopId
     * @param object $image
     * @param int    $imageType
     * @param int    $displayOrder
     * @param int    $imageIdName
     * @return null|object
     */
    public function uploadImageToS3(int $shopId, object $image, int $imageType, int $displayOrder, int $imageIdName)
    {
        // Create image file name
        $imageFileName = $this->createImageFileName($imageIdName, $image, $displayOrder);

        // Create image directory path
        $imageDirPath = $this->createImageDirPath($shopId, $imageType, $imageIdName);

        // Create image file path
        $imageFilePath = $imageDirPath . $imageFileName;

        try {
            // Check if the image path entity doesn't exist
            $currentImagePathEntity = ImagePath::where('shop_id', $shopId)
                ->where('image_type', $imageType)
                ->where('display_order', $displayOrder)
                ->first();
            if (!empty($currentImagePathEntity)) {
                return;
            }
            
            // Upload to S3
            Storage::disk('s3')->putFileAs($imageDirPath, $image, $imageFileName);

            // Confirm if the image was successfully uploaded
            if (!Storage::disk('s3')->exists($imageFilePath)) {
                throw new SystemException(ErrorType::CODE_5003, __('errors.MSG_5003'), ErrorType::STATUS_5003);
            }

            // Create a record in image_paths table.
            $imagePathEntity = new ImagePath();
            $imagePathEntity->shop_id = $shopId;
            $imagePathEntity->image_type = $imageType;
            $imagePathEntity->file_name = $imageFileName;
            $imagePathEntity->dir_path = $imageDirPath;
            $imagePathEntity->image_url = config('app.AWS_URL') . '/' . $imageDirPath . $imageFileName;
            $imagePathEntity->display_order = $displayOrder;
            $imagePathEntity->save();

            return $imagePathEntity;
        } catch (\Exception $e) {
            // Delete the image from Amazon S3 if it exists
            if (Storage::disk('s3')->exists($imageFilePath)) {
                Storage::disk('s3')->delete($imageFilePath);
            }

            return;
        }
    }

    /**
     * Update an image in Amazon S3.
     *
     * @param int    $userId
     * @param int    $imagePathId
     * @param object $image
     * @param int    $imageType
     * @param int    $displayOrder
     * @param int    $shopId
     * @param int    $packageId
     * @return null|object
     */
    public function updateImageInS3(int $shopId, int $imagePathId, object $image, int $imageType, int $displayOrder, int $packageId)
    {
        try {
            // Check if the image path entity exists
            $imagePathEntity = ImagePath::findOrFail($imagePathId);
            if (empty($imagePathEntity)) {
                return;
            }

            // Check number image_paths exist with display_order
            $duplicateImagePathEntity = ImagePath::where('shop_id', $shopId)
                ->where('image_type', $imageType)
                ->where('display_order', $displayOrder)
                ->where('id', '<>', $imagePathId)
                ->first();
            if (!empty($duplicateImagePathEntity)) {
                return;
            }

            // Get image file name

            $imageFileName = $this->createImageFileName($packageId, $image);

            // Get image directory path
            $imageDirPath = $imagePathEntity->dir_path;

            // Upload to S3
            Storage::disk('s3')->putFileAs($imageDirPath, $image, $imageFileName);

            // Create image file path
            $imageFilePath = $imageDirPath . $imageFileName;

            // Confirm if the image was successfully updated
            if (!Storage::disk('s3')->exists($imageFilePath)) {
                throw new SystemException(ErrorType::CODE_5003, __('errors.MSG_5003'), ErrorType::STATUS_5003);
            }

            $imagePathEntity->file_name = $imageFileName;
            $imagePathEntity->dir_path = $imageDirPath;
            $imagePathEntity->image_url = config('app.AWS_URL') . '/' . $imageDirPath . $imageFileName;

            $imagePathEntity->save();

            return $imagePathEntity;
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * Delete an image in Amazon S3.
     *
     * @param int $userId
     * @param int $imagePathId
     * @return null|object
     */
    public function deleteImageFromS3(int $imagePathId)
    {
        try {
            // Check if the image path entity exists
            $imagePathEntity = ImagePath::where('id', $imagePathId)
                ->first();
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

    /**
     * @param $path
     * @return string
     */
    public function getS3Url($path)
    {
        try {
            $disk = Storage::disk('s3');

            if ($disk->exists($path)) {
                $s3Client = $disk->getDriver()->getAdapter()->getClient();
                $command = $s3Client->getCommand(
                    'GetObject',
                    [
                        'Bucket' => config('app.AWS_BUCKET'),
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

    /**
     * Create image file name.
     *
     * @param int      $userId
     * @param object   $image
     * @param int      $shopId
     * @param null|int $displayOrder
     * @return string
     */
    private function createImageFileName(int $shopId, object $image, int $displayOrder = null): string
    {
        if ($displayOrder) {
            return str_pad((string) $shopId, 8, '0', STR_PAD_LEFT) . time() . '_' . $displayOrder . '.' . $image->getClientOriginalExtension();
        } // name image shop profile
        return str_pad((string) $shopId, 8, '0', STR_PAD_LEFT) . time() . '.' . $image->getClientOriginalExtension();
    }

    /**
     * Create image path.
     *
     * @param int        $userId
     * @param object     $image
     * @param int        $imageType
     * @param int        $shopId
     * @param null|mixed $packageId
     * @return string
     */
    private function createImageDirPath(int $shopId, int $imageType, $packageId = null): string
    {
        if ($imageType == ImagePath::IMAGE_TYPE_AGE_VERIFICATION) {
            // Image type is age verification
            $imageDirPath = sprintf(ImagePath::IMAGE_PATH_HOLDER_AGE_VERIFICATION, $shopId);
        } elseif ($imageType == ImagePath::IMAGE_TYPE_SHOP_PACKAGE) {
            // Image tye is shop menu package
            $imageDirPath = sprintf(ImagePath::IMAGE_PATH_SHOP_MENU_PACKAGE, $shopId, $packageId);
        } elseif ($imageType == ImagePath::IMAGE_PATH_SHOP_PROFILE) {
            // Image type is shop profile
            $imageDirPath = sprintf(ImagePath::IMAGE_PATH_HOLDER_SHOP_PROFILE, $shopId);
        } elseif ($imageType == DBConstant::IMAGE_TYPE_CAST_MEMBER_PROFILE) {
            // Image tye is shop menu package
            $imageDirPath = sprintf(ImagePath::IMAGE_PATH_SHOP_MENU_CAST_MEMBER, $shopId, $packageId);
        }

        return $imageDirPath;
    }
}
