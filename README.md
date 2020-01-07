lids AI API
----------------
Package is fully functional.


1/7/2020

v3.0.1 - Missed a journal again. Sorry. Lost my computer.
    Happy New Year! Anyway, God bless you all. This is
    going to be one of the final updates to this repo.
    In order to categorize the information, you will need
    to set a Tier object's public member variable 'cat'
    before you send it to ->find_tier(). This will make
    the directory. Name it right because it will show in
    the results. Thank you all!

12/27/2019

v2.4.2 - After releasing this morning, I decided
    to watch a video on CNN and NNs in general.
    Well i got to work after watching and noticing
    some things. The apparent reason this wasn't
    the best example of a NN is because I didn't
    have a hidden layer. Well, now, though that was
    debatable, the introduction of the serial dataset
    was tokenized an invented by myself. So, now,
    we have an even newer way to see the things we
    are looking for. 15 times faster though. Muahaha!!

v2.4-patch-1 - patch for saving object file and
    ignoring duplicate returns.

12/27/2019 (4am)

v2.4 - We're gunning for a releasable product now.
    Now when it's me saying we, I mean I. I am getting
    this produced the best I can. So far it looks like
    the speed is great, and the only parts I haven't
    been able to fix, is the saving and reusing of the
    data for futures without having to replay all that
    processing. BTW, the formula is trademark! Yes! That's
    right, this is trade copyrighted! That means no one else
    can use it! BTW, my prorietary tag means, this is for sale.
    It doesn't mean don't clone. S'ok! Use it and make it better!
    Happy New Year!

12/22/2019 (Early Morning Time: 1:54am)

v2.3.5 - Brightness added. Now the numbers are surely right.

12/21/2019

v2.3 - After lazily putting out a README with no
    changes talked about, I sit erady to state that
    finally my numbers are good. It's in full working
    condition.
        TODO:
            - Add what parts of pictures were so
            obvious to the match. That's about it.
            
12/20/2019

v2.2 -I programmed and released without journaling, sorry.

12/19/2019

v2.1 - Refactored and refined the search process
    as well as how I'm saving files. This entails
    using a mathematical process rather than bit
    by bit analysis. This fine-tuning has tripled
    the speed at least. And the answer finally make
    sense! Go figure! Math is still king!

Later on 12/18/2019

v2.0 - All comparisons are correctly functioning.
    We are using a 20% threshold with a max of
    4 outputs. Loving it. Works like a dream.
    Speed is a big problem though. Looking into
    optimizations is a TODO. I will be looking
    at putting in the SHA1 Checksum back into the
    production so that finding originals is simple.
    Thanks!

12/18/2019

v1.8 - New release allows for more than one possible file to 
    be shown for a given dataset. All necessary functions
    refactored for such use. Also the match threshold is reduced
    to 50%.

    -patch-2
        Requirements changed for sake of operational ease.
        (Changed parameters to a function or 2).

v1.7 - Takes advantage of many more subsections of pictures
    this makes more work, but it is solved more easily.

v1.6 - Fully Documented

lids is a fully enabled API to create picture searches.
Much like a TensorFlow, yet brought up from scratch without peeking,
this package is a completely independent Neural Network.

The activity described by the code uses a single image to process
against the dataset. If the dataset is populated with something similar
or exactly the same, then it will bring back the tags associated with the
image that were entered when the image was added to the population.

It will also return with what percent the image was predicted to be a match with.

This project was very fun. I was extremely excited once I got into it.
To be able to curve yourself on the front lines of technology is something
you shoul not refrain from if your talents can take you there. I promise.

There's nothing stopping you from being successful with this project in hand
either if you plan on doing something with the web that needs to be lightweight
and produce quickly, good results. Because this is simple to setup.

To add or search images just do this:

    $object_var = new Tier();
    $branch = new Branches();
    
    $branch->origin = "/path/to/original/image/file.png";
    $branch->keywords = array("first",$second,...);
    
    $object_var->add_branch_img($branch);

that's it!

To relabel an image try this:

    $branch = new Branches();
    
    $branch->origin = "/path/to/original/image/file.png";
    $node = $object_var->find_tier($branch);
    $object_var->relabel_img($node, ["therein","we","go"]);

Remember to use the same Tier() object and you're all set! Done! Bravo!

File List:

Branches.php is a common list extensions acting as nodes

PNG.php is for creating the thumbnails

    - resize_png() to scale and to change color depth
    - ImageTrueColorToPalette2() to create image with new depth and scale
    - crop_png() crops photos in 34 ways and saves to one file
    - find_tier() resize image
    
Tier.php is for searching for the thumbnails

    - insert_branch() used to add to common list of images 
    - add_branch_img() creates new thumbnail
    - save_dataset() saves common list to file
    - load_dataset() loads common list from file
    - search_imgs() searches files for matches
    - kernel_make() creates kernel sampling in 1x50 style
    - label_search() get label of picture found
    - relabel_img() find filename and relabel the image

Thanks for looking.
