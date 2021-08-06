<?php
defined('B_PROLOG_INCLUDED') || die();

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

Loc::loadMessages(__FILE__);

?>

<!-- ID задачи -->
<tr>
    <td align="right" width="40%"><span class="adm-required-field"><?= Loc::getMessage('CTP_TASK_ID') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("string", 'TaskId', $arCurrentValues['TaskId'], Array('size'=> 10)) ?>
    </td>
</tr>

<!-- Постановщик -->
<tr>
    <td align="right" width="40%"><span><?= Loc::getMessage('CTP_TASK_ORIGINATOR_ID') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("user", 'Originator', $arCurrentValues['Originator']) ?>
    </td>
</tr>

<!-- Ответственный -->
<tr>
    <td align="right" width="40%"><span><?= Loc::getMessage('CTP_TASK_RESPONSIBLE_ID') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("user", 'Responsible', $arCurrentValues['Responsible']) ?>
    </td>
</tr>

<!-- Соисполнители -->
<tr>
    <td align="right" width="40%"><span><?= Loc::getMessage('CTP_TASK_ACCOMPLICES_ID') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("user", 'Accomplices', $arCurrentValues['Accomplices'], array('size' => 50)) ?>
    </td>
</tr>

<!-- Наблюдатели -->
<tr>
    <td align="right" width="40%"><span><?= Loc::getMessage('CTP_TASK_AUDITORS_ID') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("user", 'Auditors', $arCurrentValues['Auditors'], array('size' => 50)) ?>
    </td>
</tr>

<!-- Изменить от имени пользователя: ... -->
<tr>
    <td align="right" width="40%"><span><?= Loc::getMessage('CTP_TASK_CHANGER_ID') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("user", 'Changer', $arCurrentValues['Changer'], array('size' => 50)) ?>
    </td>
</tr>
