<?php

namespace lids\PHP;

use lids\PHP\Tier;
use lids\PHP\Branches;
use lids\PHP\PNG;

class UI {

    public $root;
    public $width;
    public $layer_count;

    public function full_dataset_breakdown(int $limit = -1)
    {
        $png = new PNG();
        $dataset = new Tier();
        foreach(scandir(dirname(__NAMESPACE__) . '/PHP/../origin') as $file){
            if ($limit < 0)
                {}
            else if ($limit == 0)
                return;
            $limit--;
            if ($file == "." || $file == "..")
                continue;
            // create new input
            if ($file[0] == '.') {
                continue;
            }

            $branch1 = new Branches();
            $branch1->origin = dirname(__NAMESPACE__) . "/PHP/../origin/" . $file;
            $branch1->keywords = array($limit, "dci");
            $branch1->cat[] = "Car";
            echo "<table class='card'><tr><td>Image: $branch1->origin</td></tr>";
            echo "<tr><td class='top'>";
            echo "<img tag='" . $branch1->sha_name . "' src='" . $branch1->origin . "' style='height:70px;width:70px'/></td></tr>";
            echo "<tr><td class='bottom'>" . json_encode($branch1->keywords) . "</td></tr></table><hr width='10%' style='float:left'><br>";
            // create new input
            $node = $png->find_tier($branch1);
            // insert input ($node) into tree
            // Find matches
            $dataset->insert_branch($node);
            $dataset->search_imgs($node);
        }
    }

    public function single_image_on_dataset(Branches $branch1, $limit = -1)
    {
        $png = new PNG();
        $dataset = new Tier();

        foreach(scandir(dirname(__NAMESPACE__) . '/PHP/../origin/') as $file){
            if ($limit < 0)
                {}
            else if ($limit == 0)
                return;
            $limit--;
            if ($file == "." || $file == "..")
                continue;
            // create new input
            if ($file[0] == '.') {
                continue;
            }
            //if ($branch1->origin == dirname(__NAMESPACE__) . "/PHP/../origin/" . $file)
            //    continue;
            $branch2 = new Branches();
            $branch2->origin = dirname(__NAMESPACE__) . "/PHP/../origin/" . $file;
            $branch2->keywords = array($limit, "dci");
            $branch2->cat[] = "Car";
            // create new input
            $node2 = $png->find_tier($branch2);
            $node1 = $png->find_tier($branch1);
            if ($node1->image_sha1 == $node2->image_sha1)
                continue;
            $bri = file_get_contents($node1->image_sha1);
            $svf = file_get_contents($node2->image_sha1);
            $i = 0;
            $intersect = 0;
            while ($i < strlen($bri) && $i < strlen($svf)) {
                if ($bri[$i] == $svf[$i]) {
                    $intersect++;
                }
                $i++;
            }
            if ($i > 0 && ($intersect / $i > 0.070 && $intersect / $i < 0.08) || $intersect / $i == 1) {
                $node1->crops = array($file, $intersect / $i);
                $dataset->label_search($node1);
                $temp_key = null;
                if (!is_array($node1->keywords))
                    $temp_key[] = $node1->keywords;
                else
                    $temp_key = $node1->keywords;
                $temp_key=implode(', ', $temp_key);
                echo "<table class='card'><tr><td>Keywords: " . $temp_key . "</td></tr>";
                echo "<tr><td class='child'>";
                echo "<img src='" . $node1->origin . "' style='height:70px;width:70px'/></td></tr>";
                $cat = array_unique($node1->cat);
                echo "<tr><td class='child'>" . implode(', ', $cat)  . "</td></tr>";
                $percent = ($intersect/$i == 1) ? 100 : round($intersect/$i*1000,4);
                echo "<tr><td class='child'>" . $percent . "% Correct</td></tr></table><br/>";
                $dataset->insert_branch($node2);
                $dataset->search_imgs($node2);
                flush();
                \ob_flush();
            }
        }
    }
}