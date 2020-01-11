<?php declare (strict_types = 1);

namespace lids\PHP;

/**
 * Tier Class
 *
 * @author David Pulse <inland14@live.com>
 * @api 3.0.4
 *
 */
class Tier extends PNG
{

    /**
     * holds the Branches object
     *
     * @var Branches
     */
    public $head;

    /**
     * holds the Save file IO object
     *
     * @var Save
     */
    public $save;

    public function __construct()
    {
        $this->save = new Save();
        $this->head = [];
    }

    /**
     *   public function retrieve_branch
     *   @param Branches $img The Branch object you want to add
     *
     *   Retrieve node in common list
     *
     *  @return void
     */
    public function retrieve_branch_origin(Branches $img)
    {
        if (!\is_array($this->head))
            return null;

        foreach ($this->head as $k) {
            if (strtolower($k->origin) == strtolower($img->origin)) {
                return ($k);
            }

        }
        return null;
    }

    /**
     *   public function retrieve_branch
     *   @param Branches $img The Branch object you want to add
     *
     *   Retrieve node in common list
     *
     *  @return void
     */
    public function retrieve_branch_sha(string $img)
    {
        if (!\is_array($this->head))
            return null;

        foreach ($this->head as $k) {
            if (strtolower($k->sha_name) == strtolower($img)) {
                return ($k);
            }

        }
        return null;
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
     *   public function replace_branch
     *   @param Branches &$node Branch to check for existence and add
     *
     *   Inserts new images at end of common list
     *
     *  @return bool
     */
    public function replace_branch(Tier $tier, Branches &$node)
    {
        foreach ($tier->head as $hd) {

            if ($hd->origin == $node->origin) {
                continue;
            }
            if ($node->sha_name == $hd->sha_name) {
                $hd = $node;
                return 1;
            }
        }
    }

    /**
     *   public function find_file
     *   @param Branches &$node Branch to check for existence and add
     *
     *   Finds images in common list
     *
     *  @return bool
     */
    public function find_file(Branches $node) {

        $node = $this->find_tier($node);
        $temp_node = $node;
        foreach (scandir(__DIR__ . "/../dataset/") as $folder) {
            if ($folder[0] == ".")
                continue;
            if (\file_exists(__DIR__ . "/../dataset/$folder/" . $node->sha_name)) {
                if (($temp_node = $this->retrieve_branch_sha($node->sha_name)) != null) {
                    $temp_node->cat = $folder;
                    return $temp_node;
                } else if (($temp_node = $this->retrieve_branch_sha($node->sha_name)) == null) {
                    return $this->find_tier($node);
                }
            }
        }
    }

    /**
     *   public function add_branch_img
     *   @param Branches &$node Branch to check for existence and add
     *
     *   Inserts new images at end of common list
     *
     *  @return bool
     */
    public function recover_branch(Branches &$node)
    {
        $png = new PNG();
        $temp_node = $node;
        
        if ($node->origin != "" && ($temp_node = $this->retrieve_branch_origin($node)) != null) {
            return $temp_node;
        } 
        else if ($node->sha_name != "" && ($temp_node = $this->retrieve_branch_sha($node->sha_name)) != null) {
            return $temp_node;
        }
        return null;
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

        if (($temp = $this->recover_branch($input)) == null)
            $input = $this->find_tier($input);
        else
            $input = file_get_contents($temp);

        $bri = (file_get_contents($input->image_sha1));
        echo "<img tag='" . $input->image_sha1 . "' src='" . $input->origin . "' style='height:70px;width:70px'/>";
        echo json_encode($input->keywords) . "<br/>";

        foreach (scandir(__DIR__ . "/../dataset/") as $folder) {
            if ($folder[0] == '.') {
                continue;
            }
            $svf_in = new Branches();
            if (is_dir(__DIR__ . "/../dataset/" . $folder)) { 
                $input = $this->search_imgs_sub_dir($this, $svf_in, __DIR__ . "/../dataset/" . $folder, $bri, true);
            } else {
                continue;
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
        if (is_array($this->head) && sizeof($this->head) == 0)
            return 0;
        foreach ($this->head as $hd) {

            if ($hd->origin == $filename->origin) {
                continue;
            }
            if ($filename->crops[0] == $hd->crops[0]) {
                echo "<img tag='" . $hd->crops[0] . "' src='" . $hd->origin . "' style='height:70px;width:70px'/>";
                echo json_encode($hd->keywords) . " ";
                echo json_encode($hd->cat)  . " ";
                echo round($temp[1], 4) . "% Correct<br/>";
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
        foreach ($this->head as $k) {
            $array_temp = (array) ($k->crops);
            if (in_array($filename->crops, $array_temp)) {
                $k->keywords = $newkeywords;
                return;
            }
        }
    }
}
