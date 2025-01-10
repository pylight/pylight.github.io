<?php

header('Content-Type: text/html; charset=utf-8');

$gid = $_GET["gallery"];
if (empty($gid))
{
    return;
}
printGallery($gid);

function printGallery($galleryId) {

    // prepare folder array and requested gallery id
    $folders = glob('../images/gallery/*', GLOB_ONLYDIR);
    $galleryId = intval($galleryId) - 1;

    // validation of data
    if (!is_int($galleryId) || $galleryId < 0 || $galleryId >= count($folders))
    {
        return;
    }


    $foldername = $folders[$galleryId];

      echo '<div class="imageGroup">';
        $groupName = end(explode('/', $foldername));
        echo '<h3 class="mousefont">' . $groupName . '</h3>';

        // display all images of this gallery
        foreach(glob($foldername.'/*.*') as $filename) {

            // get the name of the image
            $imageName = str_replace($foldername."/", "", $filename);       // remove the folder/path-name from string
            $imageName = substr($imageName, 0, strpos($imageName, '.'));    // remove the file extension from string

            // set title based on filename
            if (!is_numeric($imageName)) {
                $title = $imageName;
            }
            else {
               $title = '';
            }

                echo '<div class="thumbnail square-thumb">';
                echo '<a href="'.$filename.'" data-gallery="'.$galleryId.'"  title="' . $title . '" class="boxer">';
                echo '<img src="'.$filename.'" alt="" />';
                echo '</a>';
            echo '</div>';
        }
        echo '</div>';
}
?>
