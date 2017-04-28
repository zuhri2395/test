<?php
require_once __DIR__ . '/vendor/autoload.php';
use Google\Cloud\Speech\SpeechClient;
use Google\Cloud\Storage\StorageClient;

putenv('GOOGLE_APPLICATION_CREDENTIALS=Speech to text project-1c3b022df4fe.json');
$serviceJson = json_decode(file_get_contents("Speech to text project-1c3b022df4fe.json"));

$dir = __DIR__ . '/resources/';
$allowedExt = array('wav');
$uploadedFile = $_FILES['voice'];
$results = '';

$ext = explode('.', $uploadedFile['name']);
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $uploadedFile['tmp_name']);
$filename = $dir . $ext[0] . '.flac';

if(in_array($ext[1], $allowedExt) AND $uploadedFile['type'] == $mime) {
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
    $results = $speech->recognize($object, $options);

    foreach($results as $transcript) {
        foreach($transcript->alternatives() as $trans) {
            var_dump($trans);
        }
    }
} else {
    echo "not a wav file or not a valid wav";
}