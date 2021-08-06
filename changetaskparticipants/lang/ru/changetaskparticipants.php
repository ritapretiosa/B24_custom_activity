<?php
defined('B_PROLOG_INCLUDED') || die;

$MESS['ERROR_TASK_ID_EMPTY'] = 'Поле "ID задачи" не заполнено';
$MESS['ERROR_TASK_ID_INVALID'] = 'Значение поля "ID задачи" должно быть числом';
$MESS['ERROR_ORIGINATOR_INVALID'] = '"Постановщик" может быть только один';
$MESS['ERROR_RESPONSIBLE_INVALID'] = '"Ответственный" может быть только один';
$MESS['ERROR_CHANGER_INVALID'] = '"Пользователь от чьего имени изменяется задача" может быть только один';

$MESS['RESULT_SUCCESS'] = 'В задаче ID=#TASK_ID# успешна выполнена смена участников';
$MESS['RESULT_ERROR'] = 'При смене участников в задаче ID=#TASK_ID# произошла ошибка. Текст ошибки: #ERROR_TEXT#';
