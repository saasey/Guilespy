<?php declare(strict_types = 1);

namespace lids\PHP;

/**
 * CSV Class
 *
 * @author David Pulse <inland14@live.com>
 * @api 3.0.2
 * 
 */
class CSV {

    /**
     * holds the total image object
     *
     * @var string
     */
    public $generations;


    # CSV is written like this:
    # image.ext, category, keywords, to, use, with, this, image;
    # note: finish each image's CSV entry with a semicolon----/`
    function __construct(string $filename) {

        $file = \file_get_contents($filename);

        $generation1 = explode(';', $file);

        foreach ($generation1 as $gen)
        {
            $gen_i = explode(',',$gen);
            
            $keywords = [];
            for ($i = 2 ; $i < count($gen_i); $i++) {
                $keywords[] = $gen_i[$i];
            }
            $this->generations[] = array('filename' => $gen_i[0], 'category' => $gen_i[1], 'keywords' => $keywords);
        }

    }
  
    
}

?>