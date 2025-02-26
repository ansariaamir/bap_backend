<?php

namespace app\models;

use Yii;
use app\models\masters\OptionValue;
/**
 * This is the model class for table "project_configurations".
 *
 * @property int $id
 * @property int|null $country_id
 * @property string|null $entity_ids
 * @property int|null $state_id
 * @property string|null $department_ids
 * @property int|null $dms_type_id
 * @property string|null $cms_in_id
 * @property int|null $dashboards_id
 * @property string|null $mis_reports_ids
 * @property string|null $integrations_with_ids
 * @property string|null $notifications_services_ids
 * @property int|null $social_media
 * @property string|null $help_desks_ids
 * @property string|null $certificate_verification_ids
 * @property string|null $comman_features_ids
 * @property string|null $template_design_ids
 * @property int|null $is_mobile_responsive
 * @property int|null $auditlog_sla_monitor
 * @property int|null $cyber_security_compliant
 * @property int|null $bo_workflow
 * @property string|null $other_modules
 */
class ProjectConfigurations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_configurations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $ruels = [
            [['country_id','entity_ids'],'required'],
            [['country_id', 'state_id', 'dms_type_id','cms_in_id', 'dashboards_id', 'social_media', 'is_mobile_responsive', 'auditlog_sla_monitor', 'cyber_security_compliant'], 'integer'],
            [['bo_workflow','other_modules'],'safe'],
            [['entity_ids', 'department_ids', 'mis_reports_ids', 'integrations_with_ids', 'notifications_services_ids', 'help_desks_ids', 'certificate_verification_ids', 'comman_features_ids', 'template_design_ids'], 'string', 'max' => 50],
        ];
       
        return $ruels;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'country_id' => 'Country',
            'entity_ids' => 'Entitys', // multi
            'state_id' => 'State',
            'department_ids' => 'Departments',//park
            'dms_type_id' => 'DMS Type',
            'cms_in_id' => 'CMS In',
            'dashboards_id' => 'Dashboards',
            'mis_reports_ids' => 'Mis Reports',// multi
            'integrations_with_ids' => 'Integrations With',// multi
            'notifications_services_ids' => 'Notifications Services',// multi
            'social_media' => 'Social Media',//park
            'help_desks_ids' => 'Help Desks',// multi
            'certificate_verification_ids' => 'Certificate Verification',// multi
            'comman_features_ids' => 'Comman Features',// multi
            'template_design_ids' => 'Template Design',//park
            'is_mobile_responsive' => 'Is Mobile Responsive',//park
            'auditlog_sla_monitor' => 'Auditlog Sla Monitor',//park
            'cyber_security_compliant' => 'Cyber Security Compliant',//park
            'bo_workflow' => 'Workflow',
            
        ];
    }

    public static function getDetails(){
        $model = self::find()->one();
        if($model){

            $data = [
                'country' => OptionValue::getValueByID($model->country_id),
                'country_id' => $model->country_id,
                'entity' => OptionValue::getValueByIDs($model->entity_ids),
                'entity_ids' => $model->entity_ids,
                'state' => OptionValue::getValueByID($model->state_id),
                'state_id' => $model->state_id,
                'dms_type' => OptionValue::getValueByID($model->dms_type_id),
                'dms_type_id' => $model->dms_type_id,
                'cms_type' => OptionValue::getValueByID($model->cms_in_id),
                'cms_in_id' => $model->cms_in_id,
                'dashboard' => OptionValue::getValueByID($model->dashboards_id),
                'dashboards_id' => $model->dashboards_id,
                'mis_reports' => OptionValue::getValueByIDs($model->mis_reports_ids),
                'mis_reports_ids' => $model->mis_reports_ids,
                'integrations' => OptionValue::getValueByIDs($model->integrations_with_ids),
                'integrations_with_ids' => $model->integrations_with_ids,
                'notifications' => OptionValue::getValueByIDs($model->notifications_services_ids),
                'notifications_services_ids' => $model->notifications_services_ids,
                'help_desks' => OptionValue::getValueByIDs($model->help_desks_ids),
                'help_desks_ids' => $model->help_desks_ids,
                'certificate_verification' => OptionValue::getValueByIDs($model->certificate_verification_ids),
                'certificate_verification_ids' => $model->certificate_verification_ids,
                'comman_features' => $model->comman_features_ids ? OptionValue::getValueByIDs($model->comman_features_ids) : '',
                'comman_features_ids' => $model->comman_features_ids,
                'bo_workflow'=>$model->bo_workflow,
                'bo_workflow_label'=>$model->bo_workflow,
               

            ];

            return ['status'=>true,'data'=>$data];
        }else{
            return ['status'=>false];
        }
    }
}
