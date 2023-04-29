<?php 
   
   include 'config.php';
   include 'db.php';


   if(isset($_POST))
   {
      // Get data from JSON file and store it
      $data = file_get_contents("php://input");
      $videos = json_decode($data, true);
      
      // Iterate array of videos and add to database
      // if it doesn't already exist
      for ($i = 0, $n = count($videos); $i < $n ; $i++)
      {
      	$video = $videos[$i];
         $id = $video['id'];
         $title = $video['title'];
         $img_src_default = $video['thumbnails']['default']['url'];
         $img_src_high = $video['thumbnails']['high']['url'];
         $img_src_medium = $video['thumbnails']['medium']['url'];

         // check if video is in db
         $qry = $mysqli->prepare("SELECT id FROM videos WHERE id = ?");
         $qry->bind_param("s", $id);
         $qry->execute();
         $qry->store_result();
         if($qry->num_rows != 0)
         {
            // video is already in db
            echo "video already in db \r\n";
         }
         // if it's not in db, then add it
         else
         {
            // add video id, title to videos table
            $qry = $mysqli->prepare("INSERT videos (id, title) VALUES (?,?)");
            $qry->bind_param("ss", $id, $title);
            $qry->execute();
            $qry->close();

            // add thumbnail images' src to video_thumbnails table
            $qry = $mysqli->prepare("INSERT video_thumbnails (id, src_default, src_high, src_medium) VALUES (?,?,?,?)");
            $qry->bind_param("ssss", $id, $img_src_default, $img_src_high, $img_src_medium);
            $qry->execute();
            $qry->close();

            // provide feedback
            echo "video added successfully \r\n";
         }
      }
   }

   // close db connection
   $mysqli->close();

?>