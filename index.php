<?php

namespace lids\PHP;

require "vendor/autoload.php";

// tier() is a class for the common list
// that holds all the information
$start_timer = time();
// PNG() is for image manipulation
$png = new PNG();

$dataset = new Tier();
// load your last saved common list
$dataset->head = $dataset->save->load_dataset($dataset, "save.txt");

//instantiate new Branches() object
// Fill in `origin` and `thumb_dir`
// and keywords

$x = 0;
foreach (scandir(dirname(__NAMESPACE__) . "/PHP/../origin/") as $file) {
    if ($file[0] == '.') {
        continue;
    }
    $branch1 = new Branches();
    $branch1->origin = dirname(__NAMESPACE__) . "/PHP/../origin/" . $file;
    echo $file . " ";
    $branch1->keywords = array($x, "dci");
    $branch1->cat = "dog";
    // create new input
    $node = $png->find_tier($branch1);
    // insert input ($node) into tree
    $dataset->insert_branch($node);
    // Find matches
    $dataset->search_imgs($node);

    $x++;
    if ($x == 25)
        break;
}

echo "<hr/>";
$branch2 = new Branches();
$branch2->origin = dirname(__NAMESPACE__) . "/php" . "/../origin/00002.JPG";

$branch2 = $dataset->retrieve_branch_origin($branch2);
$branch2->keywords = array("2", ":P pic");

$dataset->relabel_img($branch2, $branch2->keywords);

$dataset->search_imgs($branch2);

$branch3 = new Branches();

$branch3->origin = dirname(__NAMESPACE__) . "/php" . "/../origin/00024.JPG";

$branch3 = $dataset->retrieve_branch_origin($branch3);
$branch3->keywords = array("24", "hyuk, gee whiz!");

$dataset->relabel_img($branch3, $branch3->keywords);

$dataset->search_imgs($branch3);

// Let's get both of our labels here
// save your latest dataset(common list)
$dataset->save->save_dataset($dataset, "save.txt");

echo "Took " . (time() - $start_timer) . " seconds to complete";