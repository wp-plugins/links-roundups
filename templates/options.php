<?php
$default_html = <<<EOT
<p class='lr-saved-link#!CLASS!#'><a href='#!URL!#'>#!TITLE!#</a>&ndash;<span class='description'>#!DESCRIPTION!#</span><em>#!SOURCE!#</em></p>
EOT;
?>
<div class="wrap">
	<h2><?php _e( 'Link Roundups Options', 'link-roundups' ); ?></h2>

	<?php settings_errors(); ?>
	
	<h4><?php _e( 'Documentation', 'link-roundups' ); ?></h4>
	
	<?php 
		printf(
			'<p>' . __( 'Read about these settings and using this plugin in <a href="%s">the documentation on GitHub</a>.', 'link-roundups' ) . '</p>',
			'https://github.com/INN/link-roundups/tree/master/docs'
		);
	?>
	
	<a href="#rename"><strong><?php _e( 'Rename Link Roundups', 'link-roundups' ); ?></strong></a> | <a href="#html"><strong><?php _e( 'Custom HTML for Displaying Links', 'link-roundups' ); ?></strong></a> | <a href="#mailchimp"><strong><?php _e( 'Mailchimp Integration', 'link-roundups' ); ?></strong></a>
	<form method="post" action="options.php">
		<?php settings_fields( 'lroundups-settings-group' ); ?>
		<?php do_settings_fields( 'lroundups-settings-group', null ); ?>
		<div class="options-wrapper">
			<div id="rename" class="card">
				<h3><?php _e( 'Rename Link Roundups', 'link-roundups' ); ?></h3>
				<p><?php _e( 'You might call it a Daily Digest. Recap. Team Newsletter. Etc.', 'link-roundups' ); ?></p> 
				<?php 
					printf(
						'<p>' . __( 'Modify the Post Type Name displayed in the WordPress Dashboard Menus and Pages and the Post Type Slug (<a href="%s">learn more</a>) used in the public URL for each Link Roundup post.', 'link-roundups' ) . '</p>',
						'http://codex.wordpress.org/Glossary#Post_Slug'
					);
				?>
				<h4><?php _e( 'Custom Name', 'link-roundups' ); ?></h4>
				<h5 style="margin-bottom: 0; padding-bottom: 0;"><?php _e( 'Singular (Default: Link Roundup)', 'link-roundups' ); ?></h5>
				<input type="text" name="lroundups_custom_name_singular" value="<?php echo get_option( 'lroundups_custom_name_singular' ); ?>" />
				<h5 style="margin-bottom: 0; padding-bottom: 0;"><?php _e( 'Plural (Default: Link Roundups)', 'link-roundups' ); ?></h5>
				<input type="text" name="lroundups_custom_name_plural" value="<?php echo get_option( 'lroundups_custom_name_plural' ); ?>" />

				<h4 style="margin-bottom: 2px; padding-bottom: 2px;"><?php _e( 'Custom URL Slug', 'link-roundups' ); ?></h4>
				<h5 style="margin: 0; padding: 0;"><?php _e( 'Current URL Slug for Link Roundups:', 'link-roundups' ); ?></h5>
				<code style="display:block; margin: 0.33em 0;">
					<?php echo get_site_url(); ?>/
					<strong><?php 
					$custom_slug_setting = get_option( 'lroundups_custom_url' );
					
					if(!empty($custom_slug_setting)) { 
						$current_slug = $custom_slug_setting; // apply custom slug
					} else {
						$current_slug = 'roundup'; // set plugin default to roundup
					}
					echo $current_slug; ?>
					</strong>/random-roundup/</code>
				<?php $custom_slug = get_option( 'lroundups_custom_url' ); ?>
				<input type="text" name="lroundups_custom_url" value="<?php echo $custom_slug; ?>" />
				<p><?php _e( 'Must be lowercase with no spaces or special characters -- dashes allowed.', 'link-roundups' ); ?></p>
			    <?php 
				    printf( 
				    	'<p>' . __( '<strong>IMPORTANT</strong>: Whenever you define a new Custom URL Slug, you <strong>must</strong> also update your <a href="%s"><strong>Permalink Settings</strong></a>.', 'link-roundups' ) . '</p>',
			    		admin_url( '/options-permalink.php' )
			    	);
			    ?>
			</div>
			<div id="html" class="card">
				<h3><?php _e( 'Custom HTML for Displaying Links', 'link-roundups' ); ?></h3>
				<p><?php _e( 'Modify the display and style of Saved Links.', 'link-roundups' ); ?></p>
				<textarea name="lroundups_custom_html" cols='70' rows='6' ><?php echo ( get_option( 'lroundups_custom_html' ) != '' ? get_option( 'lroundups_custom_html' )	: $default_html ); ?></textarea>
				<em><?php _e( 'Single quotes are REQUIRED in Custom HTML. Double quotes will be automatically converted to single quotes before use.', 'link-roundups' ); ?></em><br /><br />
				<?php _e( 'The following tags will be replaced with the URL, Title, Description, and Source automatically when the Saved Link is pushed into the Post Editor.', 'link-roundups' ); ?><br />
				<blockquote><ul style="list-style-type:square;">
				<li><code>#!URL!#</code></li>
				<li><code>#!TITLE!#</code></li>
				<li><code>#!DESCRIPTION!#</code></li>
				<li><code>#!SOURCE!#</code></li>
				<li><code>#!CLASS!#</code><em><small><?php _e( 'Intended for the paragraph wrapper', 'link-roundups' ); ?></small></em></li>
				</ul></blockquote>
				<h4 style="margin-bottom:0;padding-bottom:0;"><?php _e( 'Default HTML', 'link-roundups' ); ?></h4><br />
				<code><?php echo htmlspecialchars($default_html); ?></code><br />
				<h4><?php _e( 'Sponsored Link Styling', 'link-roundups' ); ?></h4>
				<?php 
					printf(
						__( '%s is the proper selector to use in your CSS.', 'link-roundups' ),
						'<code>.lr-saved-link.sponsored</code>'
					);
				?>
				<h4><?php _e( 'General Styling of Saved Links Output', 'link-roundups' ); ?></h4>
				<p><?php _e( 'Add custom styles to your theme\'s CSS based on selectors and structure above.', 'link-roundups' ); ?></p>
			</div>
			
			<div id="mailchimp" class="card">
				<h3><?php _e( 'MailChimp Integration', 'link-roundups' ); ?></h3>
					<p style="margin-bottom:5px;">
						<label for="lroundups_use_mailchimp_integration">
							<?php _e( 'Enable MailChimp Integration?', 'link-roundups' ); ?>
							<input type="checkbox" name="lroundups_use_mailchimp_integration"
								<?php checked( get_option( 'lroundups_use_mailchimp_integration' ), 'on', true ); ?> />
						</label>
					</p>
					<p>
						<label for="lroundups_mailchimp_api_key">
							<?php _e( 'MailChimp API Key', 'link-roundups' ); ?>
							<input style="width: 300px;" type="text" name="lroundups_mailchimp_api_key"
								value="<?php echo get_option( 'lroundups_mailchimp_api_key' ); ?>"
								placeholder="Mailchimp API Key" />
						</label>
					</p>
					<p><a href="http://kb.mailchimp.com/accounts/management/about-api-keys#Find-or-Generate-Your-API-Key"><?php _e( 'Find your MailChimp API Key', 'link-roundups' ); ?></a></p>
			<?php if ((bool) get_option('lroundups_use_mailchimp_integration') && !empty($templates)) { ?>
				<h4><?php _e( 'MailChimp Templates', 'link-roundups' ); ?></h4>
					<select name="lroundups_mailchimp_template">
						<option value=""></option>
						<?php foreach ( $templates['user'] as $key => $template ) { ?>
							<option <?php selected(get_option('lroundups_mailchimp_template'), $template['id'], true); ?> value="<?php echo $template['id']; ?>" /><?php echo $template['name']; ?></option>
						<?php } ?>
					</select>
					<p><?php _e( 'Choose a MailChimp template to use as the basis for Link Roundup email campaigns.', 'link-roundups' ); ?></p>
			<?php } ?>
			<?php if ( ( bool ) get_option( 'lroundups_use_mailchimp_integration') && !empty( $lists ) ) { ?>
				<h4><?php _e( 'MailChimp Lists', 'link-roundups' ); ?></h4>
					<select name="lroundups_mailchimp_list">
						<option value=""></option>
						<?php foreach ( $lists['data'] as $key => $list ) { ?>
							<option <?php selected( get_option( 'lroundups_mailchimp_list' ), $list['id'], true ); ?> value="<?php echo $list['id']; ?>" /><?php echo $list['name']; ?></option>
						<?php } ?>
					</select>
					<p><?php _e( 'Choose a MailChimp list that your Link Roundup email campaigns will be sent to.', 'link-roundups' ); ?></p>
			<?php } ?>
				</div>


		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'link-roundups' ) ?>" />
		</p>
	</form>
</div>