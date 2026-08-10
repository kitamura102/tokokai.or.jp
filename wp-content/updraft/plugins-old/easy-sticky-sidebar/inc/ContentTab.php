<?php
if (!defined('ABSPATH')) {
	exit;
}

/*
 * Wordpress_CTA_Pro_Content tab option
 * @package sticky-sidebar/inc
 * @since 1.4.5
 */
class Wordpress_CTA_Free_Content_Tab {

    public function __construct() {
        add_action( 'easy_sticky_sidebar_content_image', [$this, 'add_image'], 5);
        add_action( 'easy_sticky_sidebar_content_button', [$this, 'button_text'], 10);
        add_action( 'easy_sticky_sidebar_content_button', [$this, 'button_icon'], 11);
        add_action( 'easy_sticky_sidebar_content_text', [$this, 'content_text'], 15);

        add_action( 'easy_sticky_sidebar_content_link_options', [$this, 'call_to_action_text'], 5);
        add_action( 'easy_sticky_sidebar_content_link_options', [$this, 'tab_cta_url_options'], 5);
    }

    /**
     * Add image 
     * @since 1.4.5
     */
    public function add_image($stickycta) { ?>
<div class="heading"><?php esc_html_e('Image', 'easy-sticky-sidebar') ?></div>
<label><?php esc_html_e("Please select an image", "easy-sticky-sidebar"); ?></label>
<input type="hidden" name="sticky_s_media" id="sticky_s_media"
    value="<?php echo esc_attr($stickycta->sticky_s_media); ?>">

<?php if($stickycta->sticky_s_media){ ?>
<div class='image-preview-wrapper'>
    <img id='image-preview' src='<?php echo esc_attr($stickycta->sticky_s_media); ?>' height='100'>
</div>
<?php } else {?>
<div class='image-preview-wrapper'>
    <img id='image-preview' src='<?php echo esc_url(wp_get_attachment_url($stickycta->sticky_s_media)); ?>' height='100'>
</div>
<?php } ?>

<input id="upload_image_button" type="button" class="button" value="<?php echo esc_attr__('Edit Image', 'easy-sticky-sidebar'); ?>" />
<input type='hidden' name='image_attachment_id' id='image_attachment_id'
    value='<?php echo esc_attr($stickycta->image_attachment_id) ; ?>'>
<?php
    }

    /**
     * CTA button text field
     * @since 1.4.5
     */
    function button_text($stickycta) { ?>
<div class="SSuprydp_field_wrap sticky-sidebar-button_text">
    <div class="heading"><?php esc_html_e("Button Text", "easy-sticky-sidebar"); ?></div>
    <p class="wordpress-cta-instruction">
        <?php esc_html_e('Enter text for the toggle button. Use something that will grab your users attention.', 'easy-sticky-sidebar') ?>
    </p>
    <div class="gap-10"></div>
    <input type="text" name="SSuprydp_button_option_text" class="SSuprydp_input meta_title"
        value="<?php echo esc_attr($stickycta->SSuprydp_button_option_text) ; ?>" placeholder="Enter button text here">
    </div>
    <?php
    }

    /**
     * CTA button icon field (free)
     * @since 1.4.5
     */
    function button_icon($stickycta) { ?>
<div class="SSuprydp_field_wrap button_icon">
    <div class="heading"><?php esc_html_e("Button Icon", "easy-sticky-sidebar"); ?></div>
    <p class="wordpress-cta-instruction"><?php esc_html_e('Select a icon for button', 'easy-sticky-sidebar'); ?></p>
    <div class="gap-10"></div>

    <div class="icon-library-select-button">
        <input class="button-icon" type="hidden" name="button_icon" value="<?php echo esc_attr($stickycta->button_icon); ?>">
        <a href="#" class="button btn-primary btn-select-button-icon"><?php esc_html_e('Select Icon', 'easy-sticky-sidebar') ?></a>
        <a href="#" class="button btn-secondary btn-remove-button-icon"><?php esc_html_e('Remove Icon', 'easy-sticky-sidebar') ?></a>
        <i class="icon <?php echo esc_attr($stickycta->button_icon); ?>"></i>
    </div>
</div>
<?php
    }

    /**
     * CTA content text field
     * @since 1.4.5
     */
    function content_text($stickycta) { ?>
<div class="content-text">
    <div class="heading"><?php esc_html_e("Content Text", "easy-sticky-sidebar");?></div>
    <p class="wordpress-cta-instruction">
        <?php esc_html_e('Enter the content text. Use something that explains why the user should click on the CTA or where the CTA will direct them.', 'easy-sticky-sidebar') ?>
    </p>
    <div class="gap-10"></div>
    <textarea type="text" name="SSuprydp_content_option_text" class="SSuprydp_input meta_title"
        placeholder="Enter Content Text"><?php echo esc_attr($stickycta->SSuprydp_content_option_text);?></textarea>
</div>
<?php
    }

    /**
     * Add call to action text
     * @since 1.3.7
     */
    function call_to_action_text($stickycta) { ?>
<div class="call-to-action-button-wrapper">
    <div class="SSuprydp_field_wrap sticky-sidebar-link_text">
        <div class="heading"><?php esc_html_e("Link Text", "easy-sticky-sidebar"); ?></div>
        <p class="wordpress-cta-instruction">
            <?php esc_html_e('Enter text for the link. Use something like "Try Now" to entice the user to click.', 'easy-sticky-sidebar') ?>
        </p>
        <div class="gap-10"></div>
        <input type="text" name="SSuprydp_action_option_text" id="SSuprydp_action_option_text"
            class="SSuprydp_input meta_title" value="<?php echo esc_attr($stickycta->SSuprydp_action_option_text); ?>"
            placeholder="Enter Action Text">
    </div>

    <div class="gap-10"></div>

    <div class="SSuprydp_field_wrap">
        <div class="heading" style="margin-bottom: 5px"><?php esc_html_e("URL", "easy-sticky-sidebar");?></div>
        <input id="SSuprydp_action_option_url" name="SSuprydp_action_option_url" type="text" placeholder="Enter Url"
            value="<?php echo esc_url($stickycta->SSuprydp_action_option_url);?>" class="SSuprydp_input meta_title" />
    </div><!-- end wrap -->

    <!-- Target Blank -->
    <div class="SSuprydp_field_wrap">
        <h4 class="heading"><?php esc_html_e("Target Blank", "easy-sticky-sidebar");?></h4>
        <p class="wordpress-cta-instruction">Opens page or site in a new tab</p>
        <label class="SSuprydp_switch">
            <input type="checkbox" name="SSuprydp_target_blank" value="Yes"
                <?php checked('Yes', $stickycta->SSuprydp_target_blank)?> class="develop_check">
        </label>
    </div><!-- end wrap -->

    <!-- No Follow -->
    <div class="SSuprydp_field_wrap">
        <h4 class="heading" style="margin-top:0; margin-bottom: 7px"><?php esc_html_e("Nofollow", "easy-sticky-sidebar");?></h4>
        <p class="wordpress-cta-instruction">Tells search engines not to follow the outbound link</p>
        <label class="SSuprydp_switch">
            <input type="checkbox" name="SSuprydp_nofollow" value="Yes"
                <?php checked('Yes', $stickycta->SSuprydp_nofollow)?>>
        </label>
    </div>
</div>
<?php
    }

     /**
     * Tab CTA URL Options
     * @since 1.4.0
     */
    function tab_cta_url_options($stickycta) { ?>
<div class="easy-sticky-sidebar-tab-cta-url-options">
    <h4 class="heading" style="margin-bottom: 0"><?php esc_html_e("URL", "easy-sticky-sidebar");?></h4>

    <div class="SSuprydp_field_wrap">
        <label>Call to action url</label>
        <input name="tab_cta_url" type="text" placeholder="Enter Url"
            value="<?php echo esc_url( (string) $stickycta->tab_cta_url ); ?>" class="SSuprydp_input">
    </div><!-- end wrap -->

    <!-- Target Blank -->
    <div class="SSuprydp_field_wrap">
        <h2 class="heading" style="margin-top:0; margin-bottom: 7px">Target Blank</h2>
        <label class="SSuprydp_switch has-label">
            <input type="checkbox" name="tab_cta_target_blank" value="yes"
                <?php checked('yes', $stickycta->tab_cta_target_blank)?>>
            Opens page or site in a new tab
        </label>
    </div><!-- end wrap -->

    <!-- No Follow -->
    <div class="SSuprydp_field_wrap">
        <h2 class="heading" style="margin-top:0; margin-bottom: 7px">Nofollow</h2>
        <label class="SSuprydp_switch has-label">
            <input type="checkbox" name="tab_cta_nofollow" value="yes"
                <?php checked('yes', $stickycta->tab_cta_nofollow)?>>
            Tells search engines not to follow the outbound link
        </label>
    </div>
</div>
<?php
    }
}

