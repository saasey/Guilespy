<?php declare (strict_types = 1);

namespace lids\PHP;

/**
 * Tier Class
 *
 * @author David Pulse <tcp@null.net>
 * @api 2.5

 */
class Tier
{

    /**
     * holds the Branches object
     *
     * @var Branches
     */
    public $head;

    public function __construct()
    {
        $this->head = [];
    }

    /**
     *   public function insert_branch
     *   @param Branches $img The Branch object you want to add
     *
     *   Adds node in common list
     *
     *  @return void
     */
    public function insert_branch(Branches $img)
    {
        $this->head[] = $img;
        return;

    }

    /**
     *   public function add_branch_img
     *   @param Branches &$node Branch to check for existence and add
     *
     *   Inserts new images at end of common list
     *
     *  @return bool
     */
    public function add_branch_img(Branches &$node)
    {
        if (!is_array($node->keywords)) {
            echo "Keywords must be an array. <br/>Please modify $node->origin's Keywords.";
            return 0;
        }
        $png = new PNG();
        $node = $png->find_tier($node);

        if ($this->search_imgs($node)) {
            $this->insert_branch($node);
        }

        return $node;
    }

    /**
     *   public function save_dataset
     *   @param string $filename Output filename
     *
     *   Creates save file for common list
     *
     *  @return void
     */
    public function save_dataset(string $filename)
    {
        $temp = $this->head;
        $this->head = array_unique($temp, SORT_REGULAR);
        file_put_contents($filename, serialize($this->head));
    }

    /**
     *   public function load_dataset
     *   @param string $filename Recover dataset object from $filename
     *
     *   Load dataset from file
     *
     *  @return tier
     */
    public function load_dataset(string $filename, Tier &$t)
    {
        $file = file_get_contents($filename);
        $t->head = unserialize($file);
        if (!is_a($t, "Tier")) {
            $t = new Tier();
        }

    }

    /**
     *   public function search_imgs
     *   @param Branches
     *   &$input Search through files for prefabricated data
     *
     *   Searches for and finds thumbnail_img
     *   name.
     *
     *   @return bool
     */
    public function search_imgs(Branches &$input)
    {

        $png = new PNG();
        $input->crops = null;
        $perc = [];
        $bri_array = [];
        $RETURN = 1;

        $bri = (file_get_contents($input->image_sha1));
        echo "<img tag='" . $input->image_sha1 . "' src='" . $input->origin . "' style='height:70px;width:70px'/>";
        echo json_encode($input->keywords) . "<br/>";
        $cont = 1;
        foreach (scandir(__DIR__ . "/../dataset/") as $file) {
            if ($file[0] == '.' || is_dir(__DIR__ . "/../dataset/" . $file)) {
                continue;
            }
            if (filesize(__DIR__ . "/../dataset/" . $file) == 0) {
                continue;
            }
            if (__DIR__ . "/../dataset/" . $file == $input->image_sha1) {

            }

            $svf = (file_get_contents(__DIR__ . "/../dataset/" . $file));
            $i = 0;
            $intersect = 0;
            while ($i < strlen($bri) && $i < strlen($svf)) {
                if ($bri[$i] == $svf[$i]){
                    $intersect++;
                }
                else if ($intersect/$i < 0.0004) {
                //    break;
                }
                $i++;
            }
            //similar_text($bri, $svf, $intersect);
            
            if ($intersect / $i > 0.070) {
                $input->crops = array($file, $intersect / $i);
                $this->label_search($input);
                $RETURN = 0;
            }
        }
        return $RETURN;
    }
    
    /**
     *   public function label_search
     *   @param string $filename Finds label according to $filename
     *
     *   Runs through common list and gets Label (keywords)
     *
     *   @return string
     */
    public function label_search(Branches &$filename)
    {
        $temp = ($filename->crops);
        if ($temp[1] == 100) {
            return 1;
        }

        for ($i = 0; $i < sizeof($this->head); $i++) {
            $array_temp = (array) ($this->head[$i]->crops);

            if ($this->head[$i]->origin == $filename->origin) {
                continue;
            }
            if (count($this->head) > $i && in_array($temp[0], array_unique($array_temp))) {
                echo "<img tag='" . $this->head[$i]->crops[0] . "' src='" . $this->head[$i]->origin . "' style='height:70px;width:70px'/>";
                echo json_encode($this->head[$i]->keywords) . " ";
                echo $temp[1] . "% Correct<br/>";
                return 1;
                //$filename->crops = null;
                //$array_temp = (array) ($this->head[++$i]->crops);
            }
        }
        // echo "<img src='" . $this->head[$i]->origin . "' style='height:70px;width:70px'/>";
        // echo $temp[1] . "% Correct<br/>";
    }

    /**
     *   public function label_search
     *   @param string $filename Finds label according to $filename
     *
     *   Runs through common list and gets Label and relabels it
     *   according to the filename(keywords)
     *
     *   @return string
     */
    public function relabel_img(Branches $filename, array $newkeywords)
    {
        for ($i = 0; $i < sizeof($this->head); $i++) {
            $array_temp = (array) ($this->head[$i]->crops);
            if (in_array($filename->crops, $array_temp)) {
                $this->head[$i]->keywords = $newkeywords;
                return;
            }
        }
    }

    public function reset_all()
    {
        echo "Please empty " . dirname(__DIR__) . "/PHP/dataset and remove your save file to start over.";
        return;
    }
}
