<?php declare (strict_types = 1);

namespace lids\PHP;

/**
 * Tier Class
 *
 * @author David Pulse <tcp@null.net>
 * @api 3

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
     *   public function search_imgs_sub_dir
     *   @param Branches &$input   Search through files in sub-dir for prefabricated data
     *
     *   Searches for and finds thumbnail_img
     *   name.
     *
     *   @return bool
     */
    public function search_imgs_sub_dir(Branches &$input, string $dir)
    {
        $RETURN = 0;
        if (file_exists($input->thumb_dir . "/" . "$dir/" . $input->crops[0])) {
            $input->thumb_dir .= "$dir/" . $input->crops[0];
            
            $dir2 = \explode('\\',$input->thumb_dir);
            $dir2 = \explode('/',$dir2[count($dir2)-1]);
            return $input;
        }
        foreach (scandir(__DIR__ . "/../dataset/" . $dir . "/") as $sub_file) {
            if ($sub_file[0] == '.') {
                continue;
            }
            if (is_dir($input->thumb_dir . "/" . $sub_file)) {
                $RETURN = $this->search_imgs_sub_dir($input, $dir . "/" . $sub_file);
                if (is_int($RETURN) && $RETURN == 0)
                    continue;
                return $RETURN;
            }
        }
        return 0;
    }

    /**
     *   public function search_imgs
     *   @param Branches &$input   Search through files for prefabricated data
     *
     *   Searches for and finds thumbnail_img
     *   name.
     *
     *   @return bool
     */
    public function search_imgs(Branches &$input)
    {

        $png = new PNG();
        $perc = [];
        $bri_array = [];
        $RETURN = 1;
        
        $this->search_imgs_sub_dir($input, "");

        $bri = (file_get_contents($input->thumb_dir));
        echo "<img tag='" . $input->image_sha1 . "' src='" . $input->origin . "' style='height:70px;width:70px'/>";
        echo json_encode($input->keywords) . "<br/>";
        $cont = 1;
        foreach (scandir(__DIR__ . "/../dataset/") as $file) {
            if ($file[0] == '.') {
                continue;
            }
            $svf_in = new Branches();
            $svf_in->thumb_dir = __DIR__ . "/../dataset/";
            $svf_in->crops[0] = $file;
            $svf_in->cat = "";
            if (is_dir(__DIR__ . "/../dataset/" . $file)) {
                $this->search_imgs_sub_dir($svf_in, "");
            }
            else if (filesize(__DIR__ . "/../dataset/" . $file) == 0) {
                continue;
            }
            else
                $svf = (file_get_contents($svf_in->thumb_dir . $file));
            $i = 0;
            $intersect = 0;
            while ($i < strlen($bri) && $i < strlen($svf)) {
                if ($bri[$i] == $svf[$i]) {
                    $intersect++;
                }
                $i++;
            }
            if ($intersect / $i > 0.070) {
                $input->crops = array($file, $intersect / $i);
                $this->label_search($input);
                $RETURN = 0;
                flush();
                \ob_flush();
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
        $temp = (array) ($filename->crops);
        if (isset($temp) && count($temp) > 1 && $temp[1] == 100) {
            return 1;
        }

        for ($i = 0; $i < sizeof($this->head); $i++) {
            $array_temp = (array) ($this->head[$i]->crops);

            if ($this->head[$i]->origin == $filename->origin) {
                continue;
            }
            if (count($this->head) > $i && isset($temp) && count($temp) > 1 && in_array($temp[0], array_unique($array_temp))) {
                echo "<img tag='" . $this->head[$i]->crops[0] . "' src='" . $this->head[$i]->origin . "' style='height:70px;width:70px'/>";
                echo json_encode($this->head[$i]->keywords) . " ";
                echo $this->head[$i]->cat . " ";
                echo round($temp[1],4) . "% Correct<br/>";
                return 1;
            }
        }
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
