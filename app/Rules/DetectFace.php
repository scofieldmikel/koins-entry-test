<?php

namespace App\Rules;

use App\Services\Rekognition\RekognitionContract;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class DetectFace implements ValidationRule
{
    private $rekognition;

    public function __construct(RekognitionContract $rekognition)
    {
        $this->rekognition = $rekognition;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $result = $this->rekognition->detectFace($value);
        if (! isset($result['FaceDetails'][0])) {
            $fail('Invalid Image');
        }

        foreach ($result['FaceDetails'] as $faceDetail) {
            //dd($faceDetail);

            if ($faceDetail['Eyeglasses']['Value']) {
                $fail('Kindly Take Off Your Eye Glasses');
            }

            if ($faceDetail['Sunglasses']['Value']) {
                $fail('Kindly Take Off Your Sunglasses');
            }

            //            if (! $faceDetail['EyesOpen']['Value']) {
            //                $fail('Kindly Make Sure Your Eyes Are Open');
            //            }

            if ($faceDetail['FaceOccluded']['Value']) {
                $fail('We cannot detect a face in your picture uploaded');
            }

            if ($faceDetail['Quality']['Brightness'] < 40) {
                $fail('Kindly Stay In Bright Environment');
            }

            if ($faceDetail['Quality']['Sharpness'] < 40) {
                $fail('The Quality Of The Image Is Not Sharp Enough');
            }

            if ($faceDetail['Confidence'] < 71) {
                $fail('We cannot detect a face in your picture uploaded');
            }

            //            if (! $faceDetail['Smile']['Value']) {
            //                $fail('Kindly Smile For The Selfie');
            //            }

        }
    }
}
