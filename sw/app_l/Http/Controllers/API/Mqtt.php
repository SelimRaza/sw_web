<?php

namespace App\Http\Controllers\API;

use App\BusinessObject\MqttMessage;
use App\BusinessObject\MqttTopic;

use App\BusinessObject\MqttTopicEmployee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Mqtt extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }
    public function createTopic(Request $request)
    {
        $mqttTopic = new MqttTopic();
        $mqttTopic->topic_code = $request->topic_code;
        $mqttTopic->topic_name = $request->topic_name;
        $mqttTopic->status_id = 1;
        $mqttTopic->country_id = $request->country_id;
        $mqttTopic->created_by = $request->emp_id;
        $mqttTopic->updated_by = $request->emp_id;
        $mqttTopic->save();
        return array('column_id' => $request->id);
    }


    public function createMessage(Request $request)
    {
        $mqttMessage = new MqttMessage();
        $mqttMessage->topic_code = $request->topic_code;
        $mqttMessage->topic_name = $request->topic_name;
        $mqttMessage->message = $request->message;
        $mqttMessage->timestamp = $request->timestamp;
        $mqttMessage->message_code = $request->message_code;
        $mqttMessage->status_id = 1;
        $mqttMessage->country_id = $request->country_id;
        $mqttMessage->created_by = $request->up_emp_id;
        $mqttMessage->updated_by = $request->up_emp_id;
        $mqttMessage->save();
        return array('column_id' => $request->id);
    }

    public function employeeTopicAssign(Request $request)
    {
        $mqttTopicEmployee = new MqttTopicEmployee();
        $mqttTopicEmployee->topic_id = $request->topic_id;
        $mqttTopicEmployee->emp_id = $request->employee_id;
        $mqttTopicEmployee->status_id = 1;
        $mqttTopicEmployee->country_id = $request->country_id;
        $mqttTopicEmployee->created_by = $request->emp_id;
        $mqttTopicEmployee->updated_by = $request->emp_id;
        $mqttTopicEmployee->save();
        return array('column_id' => $request->id);
    }
    public function topicList(Request $request)
    {
        $tst = DB::select("SELECT
  concat(t1.topic_code, t1.topic_name) AS column_id,
  concat(t1.topic_code, t1.topic_name) AS token,
  t1.id                                AS topic_id,
  t1.topic_code,
  t1.topic_name
FROM mqtt_topic AS t1
  INNER JOIN mqtt_topic_emp_mapping AS t2 ON t1.topic_code = t2.topic_code
WHERE t2.emp_id = $request->emp_id");
        return Array("mqtt_topic" => array("data" => $tst, "action" => $request->input('emp_id')));
    }
    public function messageList(Request $request)
    {
        $tst = DB::select("SELECT
  concat(t1.id, t2.topic_code, t2.topic_name) AS column_id,
  concat(t1.id, t2.topic_code, t2.topic_name) AS token,
  t1.message,
  t1.topic_code,
  t2.topic_code,
  t2.topic_name,
  t1.created_by,
  t4.name                                     AS created_name
FROM mqtt_massage AS t1
  INNER JOIN mqtt_topic AS t2 ON t1.topic_code = t2.id
  INNER JOIN mqtt_topic_emp_mapping AS t3 ON t2.id = t3.topic_code
  INNER JOIN tbld_employee AS t4 ON t3.created_by = t4.id
WHERE emp_id=$request->emp_id");
        return Array("mqtt_message" => array("data" => $tst, "action" => $request->input('emp_id')));
    }

}
