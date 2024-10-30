<div class="rss-widget">
	<img src="https://www.bcswebsitesolutions.com/images/Logo-BCS-Website-Solutions.png" class="alignright" />
	
	<ul>
		<?php
		$ic_feed=0;
		//print count($rssPosts->item);
		foreach ($rssPosts->item as $key=>$rssPost) {
			if($ic_feed<5){
			?>
			<li>
				<a href="<?php echo (string) $rssPost->link; ?>" target="_blank" class="rsswidget"><?php echo (string) $rssPost->title; ?></a>
				<span class="rss-date"><?php echo date('F j, Y', strtotime($rssPost->pubDate)); ?></span>
			</li>
			<?php	
			$ic_feed++;
			}else{
			 break;	
			}
		}
		?>
		
		<li>
			<hr />
 		
			<a href="https://www.facebook.com/bcswebsitesols" class="join_facebook" target="_blank"><img class="img_join_facebook" src="<?php print $this->plugin->url;?>/img/fb.png" alt="" />Join us on Facebook</a> |
            
			<a href="https://twitter.com/bcswebsitesols" class="join_twitter" target="_blank"><img class="img_join_twitter" src="<?php print $this->plugin->url;?>/img/twitter.png" alt="" />Join us on Twitter</a> |
            
			<a href="https://plus.google.com/116459236299911139606/about" class="join_gplus" target="_blank"><img class="img_join_gplus" src="<?php print $this->plugin->url;?>/img/gplus.png" alt="" />Join us on Google+</a>
            
         |
            
			<a href="https://www.linkedin.com/company/bcs-website-solutions" class="join_linkedin" target="_blank"><img class="img_join_linkedin" src="<?php print $this->plugin->url;?>/img/linkedin.png" alt="" />Join us on Linkedin</a>    
            
            <!--  |
            <a href="#" class="sub_email" target="_blank"><img class="img_sub_email" src="<?php print $this->plugin->url;?>/img/mail.png" alt="" />Subscribe by email</a>  -->
		</li>
	</ul>
</div>
