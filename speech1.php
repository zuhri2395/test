<?php
require_once __DIR__ . '/vendor/autoload.php';
use Google\Cloud\Speech\SpeechClient;
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Core\ExponentialBackoff;


putenv('GOOGLE_APPLICATION_CREDENTIALS=Speech to text project-1c3b022df4fe.json');
$serviceJson = json_decode(file_get_contents("Speech to text project-1c3b022df4fe.json"));

$dir = __DIR__ . '/resources/';
$allowedExt = array('wav', 'mp3');
$uploadedFile = $_FILES['voice'];
$results = '';

$ext = explode('.', $uploadedFile['name']);
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $uploadedFile['tmp_name']);
$filename = $dir . $ext[0] . '.flac';

/**
 * Convert into FLAC
 **/
$ffmpeg = FFMpeg\FFMpeg::create();
$audio = $ffmpeg->open($uploadedFile['tmp_name']);
$format = new FFMpeg\Format\Audio\Flac();
$audio->save($format, $filename);

$storage = new StorageClient();
$file = fopen($filename, 'r');
$bucket = $storage->bucket('testbucket909');
$object = $bucket->upload($file, [
    'name' => $ext[0]
]);

$projectId = $serviceJson->project_id;

$speech = new SpeechClient([
    'projectId' => $projectId,
    'languageCode' => 'en-US',
]);
$options = ['encoding' => 'FLAC'];
$object = $bucket->object($ext[0]);
$operation = $speech->beginRecognizeOperation($object, $options);
$backoff = new ExponentialBackoff(10);
$backoff->execute(function () use ($operation) {
    print('Waiting for operation to complete' . PHP_EOL);
    $operation->reload();
    if (!$operation->isComplete()) {
        throw new Exception('Job has not yet completed', 500);
    }
});
if ($operation->isComplete()) {
    if (empty($results = $operation->results())) {
        $results = $operation->info();
    }
    print_r($results);
}

// $results = $speech->recognize($object, $options);

// file_put_contents('transcript', serialize($results));
// header('location:result.php');