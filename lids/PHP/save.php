<?php declare (strict_types = 1);

namespace lids\PHP;

/**
 * Save Class
 *
 * @author David Pulse <inland14@live.com>
 * @api 3.0.2
 * 
 */
class Save {

    /**
     *   public function save_dataset
     *   @param string $filename Output filename
     *
     *   Creates save file for common list
     *
     *  @return void
     */
    public function save_dataset(Tier &$t, string $filename)
    {
        $temp = $t->head;
        $t->head = array_unique($temp, SORT_REGULAR);
        file_put_contents($filename, serialize($t->head));
    }

    /**
     *   public function load_dataset
     *   @param string $filename Recover dataset object from $filename
     *
     *   Load dataset from file
     *
     *  @return tier
     */
    public function load_dataset(Tier &$t, string $filename)
    {
        $file = file_get_contents($filename);
        $t->head = unserialize($file);
        if (!is_a($t, "Tier")) {
            $t = new Tier();
        }
        return $t;
    }
}

?>