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
$dataset->save->load_dataset($dataset, "save.txt");

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
    $node = $png->find_tier($branch1);
    $dataset->insert_branch($node);
    
    $dataset->search_imgs($node);

    $x++;
    if ($x == 25)
        break;
}

echo "<hr/>";
$branch2 = new Branches();
$branch2->origin = dirname(__FILE__) . "/lids/php" . "/../origin/00002.JPG";

$branch2->keywords = array("2", ":P pic");
$branch2->cat = "cars";
$dataset->relabel_img($branch2, array("test", "to", "death"));
$dataset->label_search($branch2);

$object_var = new PNG();
$branch3 = new Branches();
$branch3->cat = "cars";

$branch3->origin = dirname(__FILE__) . "/lids/php" . "/../origin/00024.JPG";
$node = $object_var->find_tier($branch3);
$dataset->relabel_img($node, array("therein", "we", "go"));

$dataset->label_search($node);
// Let's get both of our labels here
// save your latest dataset(common list)
$dataset->save->save_dataset($dataset, "save.txt");

echo "Took " . (time() - $start_timer) . " seconds to complete";