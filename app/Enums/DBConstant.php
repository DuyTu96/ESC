<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * DBConstant enum.
 */
class DBConstant extends BaseEnum
{
    // User type
    const USER_TYPE_MEMBER_USER = 1;

    const USER_TYPE_OPERATING_COMPANY_USER = 2;

    const USER_TYPE_SHOP_GROUP_USER = 3;

    // Member type
    const MEMBER_USER_GENERAL_MEMBER = 1;

    const MEMBER_USER_VIP_MEMBER = 2;

    // Operating company user authority type
    const OPERATING_COMPANY_USER_ADMINISTRATOR = 1;

    const OPERATING_COMPANY_USER_EDITOR = 2;

    // Agency user type
    const AGENCY_USER_BARKER = 1;

    const AGENCY_USER_GUIDE = 2;

    // Shop group user authority type
    const SHOP_GROUP_USER_SHOP_ADMINISTRATOR = 1;

    const SHOP_GROUP_USER_SHOP_MANAGER = 2;

    const SHOP_GROUP_USER_SHOP_ADMINISTRATOR_TITLE = '全店舗管理者';

    const SHOP_GROUP_USER_SHOP_MANAGER_TITLE = '店舗管理者';

    // User name registration flag
    const USER_NAME_REGISTRATION_FLAG_INCOMPLETE = 0;

    const USER_NAME_REGISTRATION_FLAG_COMPLETED = 1;

    // Archive flag
    const ARCHIVE_FLAG_NOT_ARCHIVED = 0;

    const ARCHIVE_FLAG_ARCHIVED = 1;

    // Language code
    const LANG_CODE_JA = 1;

    const LANG_CODE_EN = 2;

    const LANG_CODE_CS = 3;

    const LANG_CODE_CT = 4;

    // Image type
    const IMAGE_TYPE_SHOP_PROFILE = 1;

    const IMAGE_TYPE_SHOP_LICENSE = 2;

    const IMAGE_TYPE_SHOP_MENU_PACKAGE = 3;

    const IMAGE_TYPE_CAST_MEMBER_PROFILE = 4;

    const IMAGE_MAX_DISPLAY_ORDER = 100;

    // VIP package flag
    const VIP_PACKAGE_FLAG_NO_VIP_PACKAGES = 0;

    const VIP_PACKAGE_FLAG_HAS_ONE_OR_MORE_VIP_PACKAGES = 1;

    // Shop approval status
    const SHOP_APPROVAL_STATUS_NOT_APPROVED = 1;

    const SHOP_APPROVAL_STATUS_REJECTED = 2;

    const SHOP_APPROVAL_STATUS_APPROVED = 3;

    const SHOP_APPROVAL_STATUS_NOT_APPROVED_TITLE = "未承認";

    const SHOP_APPROVAL_STATUS_REJECTED_TITLE = "非承認";

    const SHOP_APPROVAL_STATUS_APPROVED_TITLE = "承認";

    // Display flag
    const DISPLAY_FLAG_NOT_DISPLAYED = 0;

    const DISPLAY_FLAG_DISPLAYED = 1;

    // User target type
    const USER_TARGET_TYPE_FOR_MEN = 1;

    const USER_TARGET_TYPE_FOR_WOMEN = 2;

    const USER_TARGET_TYPE_FOR_COUPLE = 3;

    // VIP only flag
    const VIP_ONLY_FLAG_NOT_VIP_ONLY = 0;

    const VIP_ONLY_FLAG_VIP_ONLY = 1;

    // Reservation possible flag
    const RESERVABLE_FLAG_NOT_RESERVABLE = 0;

    const RESERVABLE_FLAG_RESERVABLE = 1;

    // Area category
    const AREA_CATEGORY_HOKKAIDO_TOHOKU = 1;

    const AREA_CATEGORY_KANTO = 2;

    const AREA_CATEGORY_CHUBU = 3;

    const AREA_CATEGORY_KANSAI = 4;

    const AREA_CATEGORY_CHUGOKU_SHIKOKU = 5;

    const AREA_CATEGORY_KYUSHU_OKINAWA = 6;

    // Cast member nomination flag
    const CAST_MEMBER_NOMINATION_FLAG_NOT_POSSIBLE = 0;

    const CAST_MEMBER_NOMINATION_FLAG_POSSIBLE = 1;

    // Point return request possible flag
    const POINT_RETURN_REQUEST_POSSIBLE_FLAG_NOT_POSSIBLE = 0;

    const POINT_RETURN_REQUEST_POSSIBLE_FLAG_POSSIBLE = 1;

    // Open 24 hours flag
    const OPEN_24_HOURS_FLAG_NOT_OPEN_24_HOURS = 0;

    const OPEN_24_HOURS_FLAG_OPEN_24_HOURS = 1;

    // Reservation approval status
    const RESERVATION_APPROVAL_STATUS_NOT_APPROVED = 1;

    const RESERVATION_APPROVAL_STATUS_REJECTED = 2;

    const RESERVATION_APPROVAL_STATUS_APPROVED = 3;

    const RESERVATION_APPROVAL_STATUS_CANCELED = 4;

    // Reservation refund status
    const RESERVATION_REFUND_STATUS_NOT_REFUNDED = 1;

    const RESERVATION_REFUND_STATUS_REFUNDED = 2;

    const RESERVATION_REFUND_STATUS_NO_REFUND_REQUIRED = 3;

    // Settlement cancel flag
    const SETTLEMENT_CANCEL_FLAG_NOT_CANCELED = 0;

    const SETTLEMENT_CANCEL_FLAG_CANCELED = 1;

    // Point return request approval status
    const POINT_RETURN_REQUEST_APPROVAL_STATUS_NOT_APPROVED = 1;

    const POINT_RETURN_REQUEST_APPROVAL_STATUS_REJECTED = 2;

    const POINT_RETURN_REQUEST_APPROVAL_STATUS_APPROVED = 3;

    // Sales report
    const SALES_REPORT_MONEY_NOT_RECEIVED = 0;

    const SALES_REPORT_MONEY_RECEIVED = 1;

    // Inquiry from_operating_company_flag
    const INQUIRY_NOT_FROM_OPERATING_COMPANY = 0;

    const INQUIRY_FROM_OPERATING_COMPANY = 1;

    // Inquiry from_system_flag
    const INQUIRY_NOT_FROM_SYSTEM = 0;

    const INQUIRY_FROM_SYSTEM = 1;

    const INQUIRY_FROM_SYSTEM_NAME = 'システム';

    // Inquiry to_operating_company_flag
    const INQUIRY_NOT_TO_OPERATING_COMPANY = 0;

    const INQUIRY_TO_OPERATING_COMPANY = 1;

    const INQUIRY_TO_OPERATING_COMPANY_NAME = '本部';

    // Inquiry message from_system_flag
    const INQUIRY_MESSAGE_NOT_FROM_SYSTEM = 0;

    const INQUIRY_MESSAGE_FROM_SYSTEM = 1;

    //Inquiry is_read
    const INQUIRY_UNREAD = 0;

    const INQUIRY_READ = 1;

    const INQUIRY_UNREAD_NAME = '未読';

    const INQUIRY_READ_NAME = '既読';

    // Read flag
    const READ_FLAG_UNREAD = 0;

    const READ_FLAG_READ = 1;

    // Sent flag
    const SENT_FLAG_NOT_SENT = 0;

    const SENT_FLAG_SENT = 1;

    const SENT_FLAG_ERROR = 2;

    // Notification setting
    const NOTIFICATION_SETTING_OFF = 0;

    const NOTIFICATION_SETTING_ON = 1;

    // Refresh tokens blacklist flag
    const JWT_REFRESH_TOKEN_NOT_BLACKLISTED = 0;

    const JWT_REFRESH_TOKEN_BLACKLISTED = 1;

    // Only for existing user
    const NOT_ONLY_FOR_EXISTING_USER = 0;

    const ONLY_FOR_EXISTING_USER = 1;

    // Draft flag
    const DRAFT_FLAG_NOT_DRAFT = 0;

    const DRAFT_FLAG_DRAFT = 1;

    // Delivered flag
    const DELIVERED_FLAG_NOT_DELIVERED = 0;

    const DELIVERED_FLAG_DELIVERED = 1;

    // Public flag
    const PUBLIC_FLAG_NOT_PUBLIC = 0;

    const PUBLIC_FLAG_PUBLIC = 1;

    // Payment is_paid
    const PAYMENT_NOT_PAID = 1;

    const PAYMENT_PAID = 2;

    const PAYMENT_NOT_PAID_NAME = '未払い';

    const PAYMENT_PAID_NAME = '支払済み';

    // Payment closing_date
    const PAYMENT_CLOSING_DATE_FIRST = 10;

    const PAYMENT_CLOSING_DATE_SECOND = 25;

    // Payment payment_date
    const PAYMENT_PAYMENT_DATE_FIRST = 5;

    const PAYMENT_PAYMENT_DATE_SECOND = 20;

    //Format authority_type of Operation_user_company
    const OC_USER_IS_ADMINISTRATOR = 1;

    const OC_USER_IS_EDITOR = 2;

    const OC_USER_IS_ADMINISTRATOR_NAME = '管理者';

    const OC_USER_IS_EDITOR_NAME = '編集者';

    //Format member_type of user
    const MEMBER_IS_GENERAL = 1;

    const MEMBER_IS_VIP = 2;

    const MEMBER_IS_GENERAL_NAME = '一般';

    const MEMBER_IS_VIP_NAME = 'VIP';

    //Format target_type of ShopCategory
    const FOR_MEN = 1;

    const FOR_WOMEN = 2;

    const FOR_COUPLE = 3;

    const FOR_MEN_NAME = '男性';

    const FOR_WOMEN_NAME = '女性';

    const FOR_COUPLE_NAME = 'カップル';

    //Format is_vip_only of ShopCategory
    const IS_VIP = 1;

    const NO_VIP = 0;

    const IS_VIP_NAME = 'YES';

    const NO_VIP_NAME = 'NO';

    //Format is_reservable of ShopCategory
    const IS_RESERVABLE = 1;

    const NO_RESERVABLE = 0;

    const IS_RESERVABLE_NAME = 'YES';

    const NO_RESERVABLE_NAME = 'NO';

    // Invite
    const INVITE_NOT_INVALID = 0;

    const INVITE_INVALID = 1;

    //Format agcy_type
    const AGCY_IS_BARKER = 1;

    const AGCY_IS_GUIDE = 2;

    const AGCY_IS_BARKER_NAME = 'キャッチ';

    const AGCY_IS_GUIDE_NAME = 'ガイド';

    // register type
    const EMAIL_OPERATION_USER_TYPE = 2;

    const EMAIL_SHOP_GROUP_USER_TYPE = 3;

    const EMAIL_MEMEBER_USER_TYPE = 3;

    //Distance map
    const DEFAULT_LATITUDE_SEARCH_RADIUS = 0.015;
    
    const DEFAULT_LONGITUDE_SEARCH_RADIUS = 0.01;

}
