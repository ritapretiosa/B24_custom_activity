<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
Loader::includeModule('tasks');

Loc::loadMessages(__FILE__);

/**
 * Действие "Звонок клиенту с анкетой".
 */
class CBPchangetaskparticipants extends CBPActivity
{

    public function __construct ($name) {
        parent::__construct($name);
    
        global $USER;
        $this->arProperties = array(
            'TaskId' => '',
            'Originator' => '',
            'Responsible' => '',
            'Accomplices' => '',
            'Auditors' => '',
            'Changer' => $USER->GetID(),
        );
        
        $this->SetPropertiesTypes(array(
            'TaskId' => array('Type' => FieldType::INT),
            'Originator' => array('Type' => FieldType::USER),
            'Responsible' => array('Type' => FieldType::USER),
            'Accomplices' => array('Type' => FieldType::USER),
            'Auditors' => array('Type' => FieldType::USER),
            'Changer' => array('Type' => FieldType::USER),
        ));
    }
    
    /**
     * Начинает выполнение действия.
     * @return int Константа CBPActivityExecutionStatus::*.
     * @throws Exception
     */
    public function Execute () {
        $rootActivity = $this->GetRootActivity();
        $documentId = $rootActivity->GetDocumentId();
    
        $arOriginatorTmp = $this->Originator;
        if (!is_array($arOriginatorTmp)) {
            $arOriginatorTmp = [$arOriginatorTmp];
        }
        $arOriginatorTmp = CBPHelper::ExtractUsers($arOriginatorTmp, $documentId, false);
        
        $arResponsibleTmp = $this->Responsible;
        if (!is_array($arResponsibleTmp)) {
            $arResponsibleTmp = [$arResponsibleTmp];
        }
        $arResponsibleTmp = CBPHelper::ExtractUsers($arResponsibleTmp, $documentId, false);
        
        $arAccomplicesTmp = $this->Accomplices;
        if (!is_array($arAccomplicesTmp)) {
            $arAccomplicesTmp = [$arAccomplicesTmp];
        }
        $arAuditorsTmp = $this->Auditors;
        if (!is_array($arAuditorsTmp)) {
            $arAuditorsTmp = [$arAuditorsTmp];
        }
    
        $arChangerTmp = $this->Changer;
        if (!is_array($arChangerTmp)) {
            $arChangerTmp = [$arChangerTmp];
        }
        $arChangerTmp = CBPHelper::ExtractUsers($arChangerTmp, $documentId, false);
        
        $arFields = [
            "CREATED_BY" => current($arOriginatorTmp),
            "RESPONSIBLE_ID" => current($arResponsibleTmp),
            "ACCOMPLICES" => CBPHelper::ExtractUsers($arAccomplicesTmp, $documentId, false),
            "AUDITORS" => CBPHelper::ExtractUsers($arAuditorsTmp, $documentId, false)
        ];
    
        $oTaskItem = CTaskItem::getInstance($this->TaskId, current($arChangerTmp));
        try {
            $oTaskItem->update($arFields);
            $this->WriteToTrackingService(Loc::getMessage('RESULT_SUCCESS', [
                '#TASK_ID#' => $this->TaskId
            ]));
        }
        catch(Exception $e) {
            $this->WriteToTrackingService(Loc::getMessage('RESULT_ERROR', [
                '#TASK_ID#' => $this->TaskId,
                '#ERROR_TEXT#' => $e->getMessage()
            ]));
        }
    
        return CBPActivityExecutionStatus::Closed;
    }
    
    /**
     * Обработчик ошибки выполнения БП
     * (вызывается, если ошибка произошла во время выполнения данного действия).
     *
     * @param Exception $exception
     * @return int Константа CBPActivityExecutionStatus::*.
     * @throws Exception
     */
    public function HandleFault (Exception $exception) {
        if ($exception == null)
            throw new Exception('exception');
    
        $status = $this->Cancel();
        if ($status == CBPActivityExecutionStatus::Canceling)
            return CBPActivityExecutionStatus::Faulting;
    
        return $status;
    }
    
    /**
     * Обработчик остановки БП (если остановка произошла во время выполнения
     * данного действия).
     *
     * @return int Константа CBPActivityExecutionStatus::*.
     * @throws Exception
     */
    public function Cancel () {
        return CBPActivityExecutionStatus::Closed;
    }
    
    /**
     * Готовит текущие настройки действия к отображению в форме настройки действия и генерирует HTML формы настройки.
     * @param array $documentType (string модуль, string класс документа, string код типа документа).
     * @param string $activityName Название действия.
     * @param array $arWorkflowTemplate Шаблон БП.
     * @param array $arWorkflowParameters Параметры шаблона БП.
     * @param array $arWorkflowVariables Переменные БП.
     * @param array|null $arCurrentValues Значения параметров действия, если есть.
     * @param string $formName
     * @return string HTML-код формы настройки шага для конструктора БП.
     */
    public static function GetPropertiesDialog($documentType, $activityName, $arWorkflowTemplate, $arWorkflowParameters, $arWorkflowVariables, $arCurrentValues = null, $formName = "")
    {
        if (!is_array($arCurrentValues))
        {
            global $USER;
            $arCurrentValues = array(
                'TaskId' => '',
                'Originator' => '',
                'Responsible' => '',
                'Accomplices' => '',
                'Auditors' => '',
                'Changer' => $USER->GetID(),
            );
            
            $arCurrentActivity= &CBPWorkflowTemplateLoader::FindActivityByName(
                $arWorkflowTemplate,
                $activityName
            );
            if (is_array($arCurrentActivity['Properties'])) {
                $arCurrentValues = array_merge($arCurrentValues, $arCurrentActivity['Properties']);
                $arCurrentValues['Originator'] = CBPHelper::UsersArrayToString(
                    $arCurrentValues['Originator'],
                    $arWorkflowTemplate,
                    $documentType
                );
                $arCurrentValues['Responsible'] = CBPHelper::UsersArrayToString(
                    $arCurrentValues['Responsible'],
                    $arWorkflowTemplate,
                    $documentType
                );
                $arCurrentValues['Accomplices'] = CBPHelper::UsersArrayToString(
                    $arCurrentValues['Accomplices'],
                    $arWorkflowTemplate,
                    $documentType
                );
                $arCurrentValues['Auditors'] = CBPHelper::UsersArrayToString(
                    $arCurrentValues['Auditors'],
                    $arWorkflowTemplate,
                    $documentType
                );
                $arCurrentValues['Changer'] = CBPHelper::UsersArrayToString(
                    $arCurrentValues['Changer'],
                    $arWorkflowTemplate,
                    $documentType
                );
            }
        }
        
        $runtime = CBPRuntime::GetRuntime();
        return $runtime->ExecuteResourceFile(__FILE__, 'propertiesDialog.php',
             array(
                 'arCurrentValues' => $arCurrentValues,
             )
        );
    }
    
    /**
     * Сохраняет настройки действия, принимает на вход данные из формы настройки действия.
     * @param array $documentType (string модуль, string класс документа, string код типа документа)
     * @param string $activityName Название действия в шаблоне БП.
     * @param array $arWorkflowTemplate Шаблон БП.
     * @param array $arWorkflowParameters Параметры шаблона БП.
     * @param array $arWorkflowVariables Переменные БП.
     * @param array $arCurrentValues Данные из формы настройки действия.
     * @param array $arErrors [Выходные данные] Ошибки валидации.
     * @return bool true, если настройки дейтсвия сохранены успешно.
     */
    public static function GetPropertiesDialogValues($documentType, $activityName, &$arWorkflowTemplate, &$arWorkflowParameters, &$arWorkflowVariables, $arCurrentValues, &$arErrors)
    {
        $arErrors = array();
        
        if (empty($arCurrentValues['TaskId'])) {
            $arErrors[] = array(
                'code' => 'Empty',
                'message' => Loc::getMessage('ERROR_TASK_ID_EMPTY')
            );
        }
    
        if (!empty($arCurrentValues['TaskId']) && !is_numeric($arCurrentValues['TaskId'])) {
            $arErrors[] = array(
                'code' => 'InvalidValue',
                'message' => Loc::getMessage('ERROR_TASK_ID_INVALID')
            );
        }
        
        if (!empty($arCurrentValues['Originator'])) {
            $arCurrentValues['Originator'] = str_replace("user_", "", $arCurrentValues['Originator']);
            
            $arOriginators = CBPHelper::UsersStringToArray($arCurrentValues['Originator'], $documentType, $arOriginatorErrors);
            if (!empty($arOriginatorErrors)) {
                $arErrors[] = current($arOriginatorErrors);
            } elseif (count($arOriginators) > 1) {
                $arErrors[] = array(
                    'code' => 'InvalidValue',
                    'message' => Loc::getMessage('ERROR_ORIGINATOR_INVALID')
                );
            }
        }
    
        if (!empty($arCurrentValues['Responsible'])) {
            $arCurrentValues['Responsible'] = str_replace("user_", "", $arCurrentValues['Responsible']);
            
            $arResponsibles = CBPHelper::UsersStringToArray($arCurrentValues['Responsible'], $documentType, $arResponsibleErrors);
            if (!empty($arResponsibleErrors)) {
                $arErrors[] = current($arResponsibleErrors);
            } elseif (count($arResponsibles) > 1) {
                $arErrors[] = array(
                    'code' => 'InvalidValue',
                    'message' => Loc::getMessage('ERROR_RESPONSIBLE_INVALID')
                );
            }
        }
    
        if (!empty($arCurrentValues['Accomplices'])) {
            $arCurrentValues['Accomplices'] = str_replace("user_", "", $arCurrentValues['Accomplices']);
            
            $arAccomplices = CBPHelper::UsersStringToArray($arCurrentValues['Accomplices'], $documentType, $arAccomplicesErrors);
            if (!empty($arAccomplicesErrors)) {
                $arErrors[] = current($arAccomplicesErrors);
            }
        }
    
        if (!empty($arCurrentValues['Auditors'])) {
            $arCurrentValues['Auditors'] = str_replace("user_", "", $arCurrentValues['Auditors']);
            
            $arAuditors = CBPHelper::UsersStringToArray($arCurrentValues['Auditors'], $documentType, $arAuditorsErrors);
            if (!empty($arAuditorsErrors)) {
                $arErrors[] = current($arAuditorsErrors);
            }
        }
    
        if (!empty($arCurrentValues['Changer'])) {
            $arCurrentValues['Changer'] = str_replace("user_", "", $arCurrentValues['Changer']);
            
            $arChangers = CBPHelper::UsersStringToArray($arCurrentValues['Changer'], $documentType, $arChangerErrors);
            if (!empty($arChangerErrors)) {
                $arErrors[] = current($arChangerErrors);
            } elseif (count($arChangers) > 1) {
                $arErrors[] = array(
                    'code' => 'InvalidValue',
                    'message' => Loc::getMessage('ERROR_CHANGER_INVALID')
                );
            }
        }
        
        if (!empty($arErrors)) {
            return false;
        }
        
        $arProperties = array(
            'TaskId' => $arCurrentValues['TaskId'],
            'Originator' => (!empty($arOriginators)) ? $arOriginators : "",
            'Responsible' => (!empty($arResponsibles)) ? $arResponsibles : "",
            'Accomplices' => (!empty($arAccomplices)) ? $arAccomplices : "",
            'Auditors' => (!empty($arAuditors)) ? $arAuditors : "",
            'Changer' => (!empty($arChangers)) ? $arChangers : "",
        );
        
        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName(
            $arWorkflowTemplate,
            $activityName
        );
        $arCurrentActivity['Properties'] = $arProperties;
        
        return true;
    }
}