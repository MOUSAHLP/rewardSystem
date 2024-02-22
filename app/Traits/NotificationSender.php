<?php

namespace App\Traits;

use App\Helper\NotificationHelper;
use App\Models\MedicalRep;
use App\Models\User;
use Illuminate\Support\Facades\App;
use App\Services\RelatedUserService;

trait NotificationSender
{
    public static function sendNotificationRegisterVisit($medicalRep, $workPlanVisitId, $work_plan_task_id, $work_plan_id)
    {
        $users = RelatedUserService::getParents($medicalRep->users()->get());

        App::setlocale('ar');
        $title = __('messages.NewVisit');
        $description = __('messages.RegisterVisit') . '' . $medicalRep->getUserNameAttribute();

        $data = [
            'title'       => $title,
            'description' => $description,
            'visit_id'    => $workPlanVisitId,
            'image'       => '',
        ];

        $fcmTokens = $users->pluck('fcm_token');

        NotificationHelper::sendPushNotificationToTopic('/topics/control', $data, 'WorkPlanVisit');
       // NotificationHelper::sendPushNotificationToDevice($fcmTokens->toArray(), $data, 'WorkPlanVisit');

        return  $data = [
            'title'   => $title . '(' . $workPlanVisitId . ')',
            'body'    => $description,
            'to_type' => "user",
            'work_plan_id' => $work_plan_id,
            'work_plan_task_id' => $work_plan_task_id,
        ];
    }

    public static function sendNotificationTargetGroup($medicalRep, $targetName, $targetType)
    {
        $users = User::all();

        App::setlocale('ar');

        $title = __('messages.NewTargetGroupRegisterFrom') . '' . $medicalRep->getUserNameAttribute();


        $description = __('messages.TargetGroupName') . ' ' . $targetName . ' ' . "\n" . __('messages.TargetGroupType') . ' ' . $targetType;

        $data = [
            'title' => $title,
            'description' => $description,
            'image' => '',
        ];

        $fcmTokens = $users->pluck('fcm_token');

        //NotificationHelper::sendPushNotificationToDevice($fcmTokens->toArray(), $data , 'TargetGroup');
        NotificationHelper::sendPushNotificationToTopic('/topics/control', $data, 'TargetGroup');

        return $data = [
            'title'   => $title,
            'body'    => $description,
            'to_type' => "user",
        ];
    }

    public static function sendNotificationCreateNewWorkPlanOrRefresh($medicalRep, $startWorkPlan, $endWorkPlan ,$type = null)
    {
        App::setlocale('ar');

        $title = __('messages.CreateNewWorkPlan') . '' . $medicalRep->getUserNameAttribute();

        $description = __('messages.WorkPlanStartDate') . ' ' . $startWorkPlan . ' ' . "\n" . __('messages.WorkPlanEndDate') . ' ' . $endWorkPlan;

        $data = [
            'title'       => $title,
            'description' => $description,
            'type'        => $type,
            'image'       => '',
        ];

        $fcmToken = [$medicalRep->fcm_token];

        
        NotificationHelper::sendPushNotificationToDevice($fcmToken, $data, 'CreateWorkPlan');

        return $data = [
            'title'   => $title,
            'body'    => $description,
            'to_type' => "medical_rep",
            'to_id'   => $medicalRep->id,
            'service' => 'CreateWorkPlan'
        ];
    }

    public static function sendNotificationExternalVisit($medicalRep, $description, $reportType)
    {
        $users = RelatedUserService::getParents($medicalRep->users()->get());

        // $users = RelatedUserService::getParents($medicalRep->users()->get());
        // $adminUsers = User::where('role', 'admin')->get();
        // $combinedUsers = $users->concat($adminUsers);
        // $fcmTokens = $combinedUsers->pluck('fcm_token');

        App::setlocale('ar');

        $title = __('messages.NewExternalVisit') . ' ' .('من قبل المندوب ') . $medicalRep->username;
        $description = __('messages.ExternalVisitReportType') . ' ' . __('messages.' . $reportType) . ' ' . "\n" . __('messages.ExternalVisitDescription') . ' ' .  $description;

        $data = [
            'title'       => $title,
            'description' => $description,
            'image'       => '',
        ];

        $fcmTokens = $users->pluck('fcm_token');

        NotificationHelper::sendPushNotificationToTopic('/topics/control', $data, 'ExternalVisit');
        //NotificationHelper::sendPushNotificationToDevice($fcmTokens->toArray(), $data, 'ExternalVisit');

        return $data = [
            'title'   => $title,
            'body'    => $description,
            'to_type' => "user",
        ];
    }

    public static function sendNotificationTargetGroupVisit($medicalRep, $targetGroupName , $reportType)
    {
        $users = RelatedUserService::getParents($medicalRep->users()->get());

        App::setlocale('ar');
        $title = __('messages.NewTargetGroupVisit');
        
        $description = __('messages.TargetVisitReportType') . ' ' . __('messages.' . $reportType) . ' ' . "\n" . __('messages.medicalRepName') . ' ' .  $medicalRep->getUserNameAttribute(). ' ' . "\n" . __('messages.TargetGroupName') . ' ' .  $targetGroupName;

        $data = [
            'title'       => $title,
            'description' => $description,
            'image'       => '',
        ];

        $fcmTokens = $users->pluck('fcm_token');

        NotificationHelper::sendPushNotificationToTopic('/topics/control', $data, 'targetGroupVisit');
        //NotificationHelper::sendPushNotificationToDevice($fcmTokens->toArray(), $data, 'targetGroupVisit');

        return $data = [
            'title'   => $title,
            'body'    => $description,
            'to_type' => "user",
        ];
    }

    public static function sendNotificationMedicalRepVacation($medicalRep ,$vacactionType)
    {
        //$users = User::all();

        App::setlocale('ar');

        $title = __('messages.NewVacationRequestFrom') . '' . $medicalRep->getUserNameAttribute();


        $description = __('messages.VacactionType') . ' ' . __('messages.' . $vacactionType);

        $data = [
            'title' => $title,
            'description' => $description,
            'image' => '',
            'navigate' => 'vacations',
        ];

        //$fcmTokens = $users->pluck('fcm_token');

        //NotificationHelper::sendPushNotificationToDevice($fcmTokens->toArray(), $data , 'TargetGroup');
        NotificationHelper::sendPushNotificationToTopic('/topics/control', $data, 'MedicalRepVacation');

        return $data = [
            'title'   => $title,
            'body'    => $description,
            'to_type' => "user",
        ];
    }
     public static function sendNotificationMedicalRepVacationUpdate($medicalRep ,$vacactionType)
    {
        //$users = User::all();

        App::setlocale('ar');

        $title = __('messages.UpdateVacationRequestFrom') . '' . $medicalRep->getUserNameAttribute();


        $description = __('messages.VacactionType') . ' ' . __('messages.' . $vacactionType);

        $data = [
            'title' => $title,
            'description' => $description,
            'image' => '',
            'navigate' => 'vacations',
        ];

        //$fcmTokens = $users->pluck('fcm_token');

        //NotificationHelper::sendPushNotificationToDevice($fcmTokens->toArray(), $data , 'TargetGroup');
        NotificationHelper::sendPushNotificationToTopic('/topics/control', $data, 'MedicalRepVacation');

        return $data = [
            'title'   => $title,
            'body'    => $description,
            'to_type' => "user",
        ];
    }
   

    public static function sendNotificationRequest($medicalReps , $title , $body , $type = null, $vacation_id = null)
    {
        
        App::setlocale('ar');

        $data = [
            'title' => $title,
            'description' => $body,
            'type' => $type,
            'image' => '',
                        'vacation_id' => $vacation_id,
        ];

        $fcmTokens = $medicalReps->pluck('fcm_token');

        NotificationHelper::sendPushNotificationToDevice($fcmTokens->toArray(), $data , 'MedicalRepRequest');
    
        return $data = [
            'title'   => $title,
            'body'    => $body,
            'type' => $type,
            'to_type' => "medical_rep",
            'service' => 'MedicalRepRequest',
            'vacation_id' => $vacation_id,
        ];
    }
    public static function sendNotificationForVacationStatus($medicalRep , $title , $body)
    {
        
        App::setlocale('ar');

        $data = [
            'title' => $title,
            'description' => $body,
            'image' => '',
        ];

        $fcmTokens = $medicalRep->pluck('fcm_token');

        NotificationHelper::sendPushNotificationToDevice($fcmTokens->toArray(), $data , 'MedicalRepRequest');
    
        return $data = [
            'title'   => $title,
            'body'    => $body,
            'to_type' => "medical_rep",
            // 'service' => 'MedicalRepRequest'
        ];
    }
}
