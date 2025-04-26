<?php

namespace App\Services\Rekognition;

interface RekognitionContract
{
    public function detectLabel($name);

    public function detectCustomLabels($name);

    public function detectText($name);

    public function compareImages($source, $target);

    public function detectFaces($name);

    public function checkModelStatus();

    public function startStopCustomModel($type = 'START');
}
