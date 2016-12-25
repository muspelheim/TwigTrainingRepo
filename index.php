<?php

require_once('lib/Twig/Autoloader.php');
require_once('SampleObject.php');
Twig_Autoloader::register();

function getListFiles(&$all_files, $folder = './views')
{
    $fp = opendir($folder);
    while ($cv_file = readdir($fp)) {
        if (is_file($folder . "/" . $cv_file)) {
            //TODO add filter for html files
            $all_files[$folder][] = [
                'path' => str_ireplace('./views/', '', $folder . "/" . $cv_file),
                'file' => $cv_file,
            ];

        } elseif ($cv_file != "." && $cv_file != ".." && is_dir($folder . "/" . $cv_file)) {
            getListFiles($all_files, $folder . "/" . $cv_file);
        }
    }
    closedir($fp);
}

$all_files = [];
getListFiles($all_files);

$loader = new Twig_Loader_Filesystem('views');

$twig = new Twig_Environment($loader, array(
  'cache' => false,
));

$twig->addExtension(new Twig_Extension_StringLoader());

if (isset($_REQUEST['path'])) {
    echo $twig->render($_REQUEST['path'], array(
        'samples'   => $all_files,
        'object'    => new SampleObject('test', 'hello'),
    ));
} else {
    echo $twig->render('index.html.twig', array(
        'name'      => '',
        'title'     => 'Startpage',
        'samples'   => $all_files,
    ));
}

