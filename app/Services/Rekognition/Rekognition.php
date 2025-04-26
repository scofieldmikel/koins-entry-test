<?php

namespace App\Services\Rekognition;

use Aws\Rekognition\RekognitionClient;

class Rekognition implements RekognitionContract
{
    public RekognitionClient $client;

    public function __construct(RekognitionClient $client)
    {
        $this->client = $client;
    }

    public function detectFace($name): \Aws\Result
    {
        return $this->client->detectFaces([
            'Attributes' => ['ALL'],
            'Image' => [
                'S3Object' => [
                    'Bucket' => config('filesystems.disks.s3.bucket'),
                    'Name' => $name,
                ],
            ],
        ]);
    }

    public function detectLabel($name): \Aws\Result
    {
        return $this->client->detectLabels([
            'Image' => [
                'S3Object' => [
                    'Bucket' => config('filesystems.disks.s3.bucket'),
                    'Name' => $name,
                ],
            ],
            'MaxLabels' => 10,
            'MinConfidence' => 20,
        ]);
    }

    public function DetectCustomLabels($name): \Aws\Result
    {
        // dd($this->checkModelStatus());
        //$this->startStopCustomModel();
        $detectCustomLabels = $this->client->detectCustomLabels([
            'Image' => [
                'S3Object' => [
                    'Bucket' => config('filesystems.disks.s3.bucket'),
                    'Name' => $name,
                ],
            ],
            'MaxLabels' => 10,
            'MinConfidence' => 10,
            'ProjectVersionArn' => 'arn:aws:rekognition:us-east-1:035909636469:project/flickwheel_cars_2/version/flickwheel_cars_2.2023-06-30T21.01.13/1688155274062',
        ]);

        //$this->startStopCustomModel('STOP');
        return $detectCustomLabels;
    }

    public function checkModelStatus(): \Aws\Result
    {
        return $this->client->describeProjectVersions([
            'ProjectArn' => 'arn:aws:rekognition:us-east-1:035909636469:project/flickwheel_cars_2/1687989889429',
        ]);

    }

    public function startStopCustomModel($type = 'START'): \Aws\Result
    {
        if ($type === 'START') {
            return $this->client->startProjectVersion([
                'ProjectVersionArn' => 'arn:aws:rekognition:us-east-1:035909636469:project/flickwheel_cars_2/version/flickwheel_cars_2.2023-06-30T21.01.13/1688155274062',
                'MinInferenceUnits' => 1,
            ]);
        }

        return $this->client->stopProjectVersion([
            'ProjectVersionArn' => 'arn:aws:rekognition:us-east-1:035909636469:project/flickwheel_cars_2/version/flickwheel_cars_2.2023-06-30T21.01.13/1688155274062',
        ]);
    }

    public function detectText($name): \Aws\Result
    {
        return $this->client->detectText([
            'Image' => [
                'S3Object' => [
                    'Bucket' => config('filesystems.disks.s3.bucket'),
                    'Name' => $name,
                ],
            ],
        ]);
    }

    public function compareImages($source, $target): \Aws\Result
    {
        return $this->client->compareFaces([
            'SourceImage' => [
                'S3Object' => [
                    'Bucket' => config('filesystems.disks.s3.bucket'),
                    'Name' => $source,
                ],
            ],
            'TargetImage' => [
                'S3Object' => [
                    'Bucket' => config('filesystems.disks.s3.bucket'),
                    'Name' => $target,
                ],
            ],
            'SimilarityThreshold' => 0,
        ]);
    }

    public function detectFaces($name): \Aws\Result
    {
        return $this->client->detectFaces([
            'Image' => [
                'S3Object' => [
                    'Bucket' => config('filesystems.disks.s3.bucket'),
                    'Name' => $name,
                ],
            ],
            'Attributes' => ['ALL'],
        ]);
    }
}
