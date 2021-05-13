<?php
  $prev_post = get_previous_post();
  $prev_id = $prev_post->ID;
  $prev_permalink = get_permalink($prev_id);
  $next_post = get_next_post();
  $next_id = $next_post->ID;
  $next_permalink = get_permalink($next_id);
 ?>
 <a href="<?php echo $prev_permalink; ?>" rel="prev">Previous <?php echo $prev_post->post_title; ?></a>
 <a href="<?php echo $next_permalink; ?>">Next <?php echo $next_post->post_title; ?></a>
