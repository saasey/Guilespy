<?php

namespace lids\PHP;

require "vendor/autoload.php";
?>
<style>
.child {
    border:lightslategray;
    border-color:black;
    border-radius:4;
}
.card {
    background-color: slategray;
    border-color: black;
    border-radius:4px;
    border-spacing:0px,14px;
    padding-bottom:10px;
}
.top {
    border-top: black solid 1px;
    border-right: black solid 1px;
    border-left: black solid 1px;
    border-radius: 2px;
    border-top-right-radius: 4px; 
    border-top-left-radius: 4px;
}
.bottom {
    border-top: black dashed 1px;
    border-bottom: black solid 1px;
    border-right: black solid 1px;
    border-left: black solid 1px;
    border-radius: 2px;
    border-bottom-right-radius: 4px; 
    border-bottom-left-radius: 4px; 
    
}
</style>
<?php
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
$br = new Branches();
$br->origin = dirname(__NAMESPACE__) . "/PHP/../origin/00014.jpg";
$br->cat[] = "Car";
$test = new UI();
$test->single_image_on_dataset($br,40);

echo "<hr/>";
// Let's get both of our labels here
// save your latest dataset(common list)
$dataset->save->save_dataset($dataset, "save.txt");

echo "Took " . (time() - $start_timer) . " seconds to complete";