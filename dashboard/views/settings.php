<div class="wrap">
    <h2><?php echo $this->plugin->displayName; ?> &raquo; Settings</h2>
           
    <?php    
    if (isset($this->message)) {
        ?>
        <div class="updated fade"><p><?php echo $this->message; ?></p></div>  
        <?php
    }
    if (isset($this->errorMessage)) {
        ?>
        <div class="error fade"><p><?php echo $this->errorMessage; ?></p></div>  
        <?php
    }
    ?> 
    
    <div id="poststuff">
    	<div id="post-body" class="metabox-holder columns-2">
    		<!-- Content -->
    		<div id="post-body-content">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">                        
	                <form action="edit.php?post_type=<?php echo $this->plugin->posttype; ?>&page=<?php echo $this->plugin->name; ?>" method="post">
		                <div class="postbox">
		                    <h3 class="hndle">Where do you want to Insert Code?</h3>
		                    
		                    <div class="inside">		                    
		                    	<p>
									<?php
									$postTypes = get_post_types(array(
										'public' => true,
									), 'objects');
									if ($postTypes) {
										foreach ($postTypes as $postType) {
											// Skip attachments
											if ($postType->name == 'attachment') {
												continue;
											}
											?>
											<label for="<?php echo $postType->name; ?>"><?php echo $postType->labels->name; ?></label>
											<input type="checkbox" name="<?php echo $this->plugin->name; ?>[<?php echo $postType->name; ?>]" value="1" id="<?php echo $postType->name; ?>" <?php echo (isset($this->settings[$postType->name]) ? ' checked' : ''); ?>/>
											<?php
										}
									}
									?>
								</p> 
								<!--<p>
									<input  type="submit" name="Submit" class="button button-primary" value="Save Settings" /> 
								</p>-->
		                    </div>
		                </div>
		                <!-- /postbox -->
		                
		                <div class="postbox">
		                    <h3 class="hndle">Styling</h3>
		                    
		                    <div class="inside">
                            <p>
									<label for="css">Custom CSS</label>
                                    <textarea style="width: 100%; height: 100px; font-family: Courier; font-size: 12px;" name="<?php echo $this->plugin->name; ?>[custom_css]" id="custom_css"><?php echo (isset($this->settings['custom_css']) ?   $this->settings['custom_css'] :    'clear:both;float:left;width:100%;margin:0 0 20px 0;'); ?></textarea>
 								</p>
		                   		<p>
									<label for="css">Exclude CSS</label>
									<input type="checkbox" name="<?php echo $this->plugin->name; ?>[css]" value="1" id="css" <?php echo (isset($this->settings['css']) ? ' checked' : ''); ?>/>	
								</p>
                                
								<p class="description">
									By default, Insert Code items are wrapped in a container that has some CSS to give support to layout.
								</p>
								                    
								 
		                    </div>
		                </div>
		                <!-- /postbox -->
                        
                        <div class="postbox">
		                    <h3 class="hndle">Update Settings</h3>
		                    
		                    <div class="inside">
                            
								                    
								<p>
									<input name="submit" type="submit" name="Submit" class="button button-primary" value="Save Settings" /> 
								</p>
		                    </div>
		                </div>
	                </form>
	                
	                <div id="bcswebsiteservices" class="postbox">
	                    <h3 class="hndle">Latest from BCS Website Solutions</h3>
	                    
	                    <div class="inside">
	<?php   include_once(WP_PLUGIN_DIR.'/'.$this->plugin->name.'/dashboard/views/dashboard.php');   ?>		

	                    </div>
	                </div>
	                <!-- /postbox -->
				</div>
				<!-- /normal-sortables -->
    		</div>
    		<!-- /post-body-content -->
    		
    		<!-- Sidebar -->
    		<div id="postbox-container-1" class="postbox-container">
    			<?php   include_once(WP_PLUGIN_DIR.'/'.$this->plugin->name.'/dashboard/views/insertcode_sidebar.php');   ?>		
    		</div>
    		<!-- /postbox-container -->
    	</div>
	</div>      
</div>