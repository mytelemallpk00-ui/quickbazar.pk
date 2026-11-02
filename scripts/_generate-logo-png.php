<?php

$path = dirname(__DIR__).'/public/images/beautagelogosquare2.png';

$im = imagecreatetruecolor(512, 512);
imagesavealpha($im, true);
$trans = imagecolorallocatealpha($im, 0, 0, 0, 127);
imagefill($im, 0, 0, $trans);

$purple = imagecolorallocate($im, 94, 31, 112);
$pink = imagecolorallocate($im, 224, 37, 168);
$white = imagecolorallocate($im, 255, 255, 255);

imagefilledellipse($im, 256, 256, 460, 460, $purple);
imagefilledellipse($im, 256, 256, 300, 300, $pink);
imagefilledrectangle($im, 200, 160, 240, 360, $white);
imagefilledrectangle($im, 272, 160, 312, 360, $white);
imagefilledrectangle($im, 200, 240, 312, 280, $white);

imagepng($im, $path);
imagedestroy($im);

echo 'Wrote '.filesize($path)." bytes to {$path}\n";
