<?php

namespace lids\PHP;

require "vendor/autoload.php";

// tier() is a class for the common list
// that holds all the information

// PNG() is for image manipulation
$png = new PNG();

$dataset = new Tier();
// load your last saved common list
//$dataset->load_dataset("save.txt", $dataset);

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
    $node = $png->find_tier($branch1);
    $dataset->insert_branch($node);
    
    $dataset->search_imgs($node);

    $x++;
    if ($x == 45)
        break;
}

echo "<hr/>";
$branch2 = new Branches();
$branch2->origin = dirname(__FILE__) . "/lids/php" . "/../origin/baselineasc.png";

$branch2->keywords = array("2", ":P pic");

$dataset->add_branch_img($branch2);
$dataset->relabel_img($branch2, array("test", "to", "death"));
$dataset->label_search($branch2);

$object_var = new PNG();
$branch3 = new Branches();
$object_for_action = new Tier();

$branch3->origin = dirname(__FILE__) . "/lids/php" . "/../origin/baselinedesc.png";
$node = $object_var->find_tier($branch3);
$dataset->relabel_img($node, array("therein", "we", "go"));

$dataset->label_search($node);
// Let's get both of our labels here
// save your latest dataset(common list)
$dataset->save_dataset("save.txt");
