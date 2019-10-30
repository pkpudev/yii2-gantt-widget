<?php //src/api/ApiIndex.php
namespace pkpudev\gantt\api;

use pkpudev\gantt\model\DataComposer;
use Yii;

class ApiIndex
{
    use RestTrait;

    public function run()
    {
        $request = Yii::$app->request;

        $this->setHeader(200);

        if ($pid = $request->getQueryParam('pid')) {
            $dataComposer = new DataComposer($pid);
            $json['data'] = $dataComposer->compose();
            $json['link'] = [];
        } else {
            $json = ['data'=>[], 'link'=>[]];
        }

        echo json_encode($json, JSON_PRETTY_PRINT);
    }
}