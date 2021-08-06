<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arActivityDescription = array(
    'NAME' => Loc::getMessage('CTP_NAME'),
    'DESCRIPTION' => Loc::getMessage('CTP_DESCRIPTION'),
    'TYPE' => 'activity',
    'CLASS' => 'ChangeTaskParticipants',
    'JSCLASS' => 'BizProcActivity',
    'CATEGORY' => array(
        'ID' => 'interaction',
    ),
    'RETURN' => array(
        'Result' => array(
            'NAME' => Loc::getMessage('CTP_RETURN_RESULT'),
            'TYPE' => FieldType::BOOL
        ),
        'ErrorText' => array(
            'NAME' => Loc::getMessage('CTP_RETURN_ERROR_TEXT'),
            'TYPE' => FieldType::STRING
        )
    )
);