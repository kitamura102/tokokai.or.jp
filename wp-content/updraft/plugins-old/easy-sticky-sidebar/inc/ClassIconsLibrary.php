<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Icon library
 * @since 1.4.5
 */
class Easy_Sticky_Sidebar_Icons_Library {

    /**
     * Fontawesome stylesheet
     * @var string
     */
    private $stylesheet;


    /**
     * Construct
     * @since 1.4.5
     */
    public function __construct() {
        $this->stylesheet = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/css/fontawesome.css';

        add_action( 'admin_footer', [$this, 'icon_library_popup']);

        $this->get_icons();
    }

    public function has_library() {
        return file_exists($this->stylesheet);
    }

    public function get_icons() {
        $icons_json_file = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/icons.json';
        if( !file_exists($icons_json_file) ) {
            return [];
        }

        $icon_list = json_decode(file_get_contents($icons_json_file));
        if ( !is_object($icon_list) ) {
            return [];
        }

        $icons = [];    
        foreach ($icon_list as $icon => $value) {
            foreach ($value->styles as $style) {
                $icons[] = 'fa'.substr($style, 0 ,1).' fa-'.$icon;
            }
        }

        if ( WP_DEBUG ) {
            return $this->get_icons__DEPRECATED__();
        }

        return $icons;
    }

    public function get_icons__DEPRECATED__() {
        if ( !$this->has_library() ) {
            return [];
        }

        $icons = array(
            'fa-brands fa-accessible-icon',
            'fa-brands fa-accusoft',
            'fa-solid fa-address-book',
            'fa-regular fa-address-book',
            'fa-solid fa-address-card',
            'fa-regular fa-address-card',
            'fa-brands fa-adn',
            'fa-brands fa-adversal',
            'fa-brands fa-affiliatetheme',
            'fa-brands fa-airbnb',
            'fa-brands fa-algolia',
            'fa-solid fa-align-center',
            'fa-solid fa-align-justify',
            'fa-solid fa-align-left',
            'fa-solid fa-align-right',
            'fa-brands fa-alipay',
            'fa-brands fa-amazon',
            'fa-brands fa-amazon-pay',
            'fa-brands fa-amilia',
            'fa-solid fa-anchor',
            'fa-solid fa-anchor-circle-check',
            'fa-solid fa-anchor-circle-exclamation',
            'fa-solid fa-anchor-circle-xmark',
            'fa-solid fa-anchor-lock',
            'fa-brands fa-android',
            'fa-brands fa-angellist',
            'fa-solid fa-angle-down',
            'fa-solid fa-angle-left',
            'fa-solid fa-angle-right',
            'fa-solid fa-angle-up',
            'fa-solid fa-angles-down',
            'fa-solid fa-angles-left',
            'fa-solid fa-angles-right',
            'fa-solid fa-angles-up',
            'fa-brands fa-angrycreative',
            'fa-brands fa-angular',
            'fa-solid fa-ankh',
            'fa-brands fa-app-store',
            'fa-brands fa-app-store-ios',
            'fa-brands fa-apper',
            'fa-brands fa-apple',
            'fa-brands fa-apple-pay',
            'fa-solid fa-apple-whole',
            'fa-solid fa-archway',
            'fa-solid fa-arrow-down',
            'fa-solid fa-arrow-down-1-9',
            'fa-solid fa-arrow-down-9-1',
            'fa-solid fa-arrow-down-a-z',
            'fa-solid fa-arrow-down-long',
            'fa-solid fa-arrow-down-short-wide',
            'fa-solid fa-arrow-down-up-across-line',
            'fa-solid fa-arrow-down-up-lock',
            'fa-solid fa-arrow-down-wide-short',
            'fa-solid fa-arrow-down-z-a',
            'fa-solid fa-arrow-left',
            'fa-solid fa-arrow-left-long',
            'fa-solid fa-arrow-pointer',
            'fa-solid fa-arrow-right',
            'fa-solid fa-arrow-right-arrow-left',
            'fa-solid fa-arrow-right-from-bracket',
            'fa-solid fa-arrow-right-long',
            'fa-solid fa-arrow-right-to-bracket',
            'fa-solid fa-arrow-right-to-city',
            'fa-solid fa-arrow-rotate-left',
            'fa-solid fa-arrow-rotate-right',
            'fa-solid fa-arrow-trend-down',
            'fa-solid fa-arrow-trend-up',
            'fa-brands fa-facebook',
            'fa-brands fa-facebook-f',
            'fa-brands fa-facebook-messenger',
            'fa-brands fa-facebook-square',
            'fa-solid fa-laptop',
            'fa-solid fa-laptop-code',
            'fa-brands fa-instagram',
            'fa-brands fa-instagram-square',
            'fa-solid fa-hashtag',
            'fa-brands fa-pinterest',
            'fa-brands fa-pinterest-p',
            'fa-brands fa-pinterest-square',
            'fa-solid fa-location-dot',
            'fa-solid fa-location-pin',
            'fa-solid fa-map',
            'fa-regular fa-map',
            'fa-solid fa-map-location',
            'fa-solid fa-map-location-dot',
            'fa-solid fa-link',
            'fa-solid fa-globe',
            'fa-brands fa-linkedin',
            'fa-brands fa-linkedin-in',
            'fa-brands fa-staylinked',
            'fa-brands fa-youtube',
            'fa-brands fa-youtube-square',
            'fa-brands fa-tiktok',
            'fa-brands fa-whatsapp',
            'fa-brands fa-whatsapp-square',
            'fa-brands fa-telegram',
            'fa-brands fa-snapchat',
            'fa-brands fa-snapchat-square',
            'fa-brands fa-soundcloud',
            'fa-solid fa-phone',
            'fa-solid fa-phone-flip',
            'fa-solid fa-phone-slash',
            'fa-solid fa-phone-volume',
            'fa-solid fa-blender-phone',
            'fa-solid fa-square-phone',
            'fa-solid fa-square-phone-flip',
            'fa-solid fa-mobile',
            'fa-solid fa-comment-sms',
            'fa-solid fa-mobile-button',
            'fa-solid fa-mobile-retro',
            'fa-solid fa-mobile-screen',
            'fa-solid fa-mobile-screen-button',
            'fa-solid fa-signal',
            'fa-solid fa-location-crosshairs',
            'fa-solid fa-location-pin',
            'fa-solid fa-location-arrow',
            'fa-solid fa-location-dot',
            'fa-solid fa-location-pin-lock',
            'fa-solid fa-map-location',
            'fa-solid fa-map-location-dot',
            'fa-solid fa-cart-arrow-down',
            'fa-solid fa-cart-plus',
            'fa-solid fa-cart-shopping',
            'fa-solid fa-shirt',
            'fa-solid fa-envelope',
            'fa-regular fa-envelope',
            'fa-solid fa-envelope-circle-check',
            'fa-solid fa-envelope-open',
            'fa-regular fa-envelope-open',
            'fa-solid fa-envelope-open-text',
            'fa-solid fa-envelopes-bulk',
            'fa-solid fa-square-envelope',
            'fa-solid fa-message',
            'fa-regular fa-message',
            'fa-solid fa-comment',
            'fa-regular fa-comment',
            'fa-solid fa-comment-dollar',
            'fa-solid fa-comment-dots',
            'fa-regular fa-comment-dots',
            'fa-solid fa-comment-medical',
            'fa-solid fa-comment-slash',
            'fa-solid fa-comment-sms',
            'fa-solid fa-comments',
            'fa-regular fa-comments',
            'fa-solid fa-comments-dollar',
            'fa-brands fa-twitter',
            'fa-brands fa-twitter-square',
            'fa-brands fa-twitch',
            'fa-thin fa-bowl-hot',
            'fa-solid fa-bowl-hot',
            'fa-regular fa-bowl-hot',
            'fa-light fa-bowl-hot',
            'fa-duotone fa-bowl-hot',
            'fa-solid fa-bag-shopping',
            'fa-solid fa-book',
            'fa-solid fa-book-atlas',
            'fa-solid fa-book-bible',
            'fa-solid fa-book-bookmark',
            'fa-solid fa-book-journal-whills',
            'fa-solid fa-book-medical',
            'fa-solid fa-book-open',
            'fa-solid fa-book-open-reader',
            'fa-solid fa-book-quran',
            'fa-solid fa-book-skull',
            'fa-solid fa-bookmark',
            'fa-regular fa-bookmark',
            'fa-solid fa-book-bookmark',
            'fa-solid fa-user',
            'fa-regular fa-user',
            'fa-solid fa-users',
            'fa-solid fa-user-astronaut',
            'fa-solid fa-user-check',
            'fa-solid fa-user-clock',
            'fa-solid fa-user-doctor',
            'fa-solid fa-user-gear',
            'fa-solid fa-user-graduate',
            'fa-solid fa-user-group',
            'fa-solid fa-user-injured',
            'fa-solid fa-user-large',
            'fa-solid fa-user-large-slash',
            'fa-solid fa-user-lock',
            'fa-solid fa-user-minus',
            'fa-solid fa-user-ninja',
            'fa-solid fa-user-nurse',
            'fa-solid fa-user-pen',
            'fa-solid fa-user-plus',
            'fa-solid fa-user-secret',
            'fa-solid fa-user-shield',
            'fa-solid fa-user-slash',
            'fa-solid fa-user-tag',
            'fa-solid fa-user-tie',
            'fa-solid fa-user-xmark',
            'fa-solid fa-users-between-lines',
            'fa-solid fa-users-gear',
            'fa-solid fa-users-line',
            'fa-solid fa-users-rays',
            'fa-solid fa-users-rectangle',
            'fa-solid fa-users-slash',
            'fa-solid fa-users-viewfinder',
            'fa-solid fa-building-user',
            'fa-solid fa-chalkboard-user',
            'fa-solid fa-circle-user',
            'fa-regular fa-circle-user',
            'fa-solid fa-clipboard-user',
            'fa-solid fa-hospital-user',
            'fa-solid fa-house-user',
            'fa-solid fa-house-chimney-user',
            'fa-solid fa-baby',
            'fa-solid fa-bottle-droplet',
            'fa-solid fa-bottle-water',
            'fa-solid fa-prescription-bottle',
            'fa-solid fa-wine-bottle',
            'fa-solid fa-prescription-bottle-medical',
            'fa-solid fa-check',
            'fa-solid fa-check-double',
            'fa-solid fa-check-to-slot',
            'fa-solid fa-calendar-check',
            'fa-regular fa-calendar-check',
            'fa-solid fa-circle-check',
            'fa-regular fa-circle-check',
            'fa-solid fa-clipboard-check',
            'fa-solid fa-list-check',
            'fa-solid fa-money-check',
            'fa-solid fa-spell-check',
            'fa-solid fa-square-check',
            'fa-regular fa-square-check',
            'fa-solid fa-anchor-circle-check',
            'fa-solid fa-bridge-circle-check',
            'fa-solid fa-building-circle-check',
            'fa-solid fa-envelope-circle-check',
            'fa-solid fa-file-circle-check',
            'fa-solid fa-heart-circle-check',
            'fa-solid fa-house-circle-check',
            'fa-solid fa-person-circle-check',
            'fa-solid fa-plane-circle-check',
            'fa-solid fa-plug-circle-check',
            'fa-solid fa-road-circle-check',
            'fa-solid fa-school-circle-check',
            'fa-solid fa-vial-circle-check',
            'fa-solid fa-house-medical-circle-check',
            'fa-solid fa-bullseye',
            'fa-solid fa-circle-dot',
            'fa-regular fa-circle-dot',
            'fa-solid fa-crosshairs',
            'fa-solid fa-arrow-pointer',
            'fa-solid fa-hand-pointer',
            'fa-regular fa-hand-pointer',
            'fa-solid fa-cake-candles',
            'fa-solid fa-bread-slice',
            'fa-solid fa-burger',
            'fa-solid fa-fire-burner',
            'fa-solid fa-camera',
            'fa-solid fa-camera-retro',
            'fa-solid fa-camera-rotate',
            'fa-solid fa-video',
            'fa-solid fa-car',
            'fa-solid fa-car-battery',
            'fa-solid fa-car-burst',
            'fa-solid fa-car-on',
            'fa-solid fa-car-rear',
            'fa-solid fa-car-side',
            'fa-solid fa-car-tunnel',
            'fa-solid fa-caravan',
            'fa-solid fa-chart-area',
            'fa-solid fa-chart-bar',
            'fa-regular fa-chart-bar',
            'fa-solid fa-chart-column',
            'fa-solid fa-chart-gantt',
            'fa-solid fa-chart-line',
            'fa-solid fa-chart-pie',
            'fa-solid fa-chart-simple',
            'fa-solid fa-magnifying-glass-chart',
            'fa-solid fa-clock',
            'fa-regular fa-clock',
            'fa-solid fa-clock-rotate-left',
            'fa-solid fa-credit-card',
            'fa-regular fa-credit-card',
            'fa-brands fa-cc-discover',
            'fa-brands fa-cc-jcb',
            'fa-brands fa-cc-mastercard',
            'fa-brands fa-cc-paypal',
            'fa-brands fa-cc-stripe',
            'fa-brands fa-cc-visa',
            'fa-brands fa-cc-amazon-pay',
            'fa-brands fa-cc-amex',
            'fa-brands fa-cc-apple-pay',
            'fa-solid fa-database',
            'fa-solid fa-server',
            'fa-solid fa-display',
            'fa-solid fa-computer',
            'fa-solid fa-tv',
            'fa-solid fa-face-angry',
            'fa-regular fa-face-angry',
            'fa-solid fa-face-dizzy',
            'fa-regular fa-face-dizzy',
            'fa-solid fa-face-flushed',
            'fa-regular fa-face-flushed',
            'fa-solid fa-face-frown',
            'fa-regular fa-face-frown',
            'fa-solid fa-face-frown-open',
            'fa-regular fa-face-frown-open',
            'fa-solid fa-face-grimace',
            'fa-regular fa-face-grimace',
            'fa-solid fa-face-grin',
            'fa-regular fa-face-grin',
            'fa-solid fa-face-grin-beam',
            'fa-regular fa-face-grin-beam',
            'fa-solid fa-face-grin-beam-sweat',
            'fa-regular fa-face-grin-beam-sweat',
            'fa-solid fa-face-grin-hearts',
            'fa-regular fa-face-grin-hearts',
            'fa-solid fa-face-grin-squint',
            'fa-regular fa-face-grin-squint',
            'fa-solid fa-face-grin-squint-tears',
            'fa-regular fa-face-grin-squint-tears',
            'fa-solid fa-face-grin-stars',
            'fa-regular fa-face-grin-stars',
            'fa-solid fa-face-grin-tears',
            'fa-regular fa-face-grin-tears',
            'fa-solid fa-face-grin-tongue',
            'fa-regular fa-face-grin-tongue',
            'fa-solid fa-face-grin-tongue-squint',
            'fa-regular fa-face-grin-tongue-squint',
            'fa-solid fa-face-grin-tongue-wink',
            'fa-regular fa-face-grin-tongue-wink',
            'fa-solid fa-face-grin-wide',
            'fa-regular fa-face-grin-wide',
            'fa-solid fa-face-grin-wink',
            'fa-regular fa-face-grin-wink',
            'fa-solid fa-face-kiss',
            'fa-regular fa-face-kiss',
            'fa-solid fa-face-kiss-beam',
            'fa-regular fa-face-kiss-beam',
            'fa-solid fa-face-kiss-wink-heart',
            'fa-regular fa-face-kiss-wink-heart',
            'fa-solid fa-face-laugh',
            'fa-regular fa-face-laugh',
            'fa-solid fa-face-laugh-beam',
            'fa-regular fa-face-laugh-beam',
            'fa-solid fa-face-laugh-squint',
            'fa-regular fa-face-laugh-squint',
            'fa-solid fa-face-laugh-wink',
            'fa-regular fa-face-laugh-wink',
            'fa-solid fa-face-meh',
            'fa-regular fa-face-meh',
            'fa-solid fa-face-meh-blank',
            'fa-regular fa-face-meh-blank',
            'fa-solid fa-face-rolling-eyes',
            'fa-regular fa-face-rolling-eyes',
            'fa-solid fa-face-sad-cry',
            'fa-regular fa-face-sad-cry',
            'fa-solid fa-face-sad-tear',
            'fa-regular fa-face-sad-tear',
            'fa-solid fa-face-smile',
            'fa-regular fa-face-smile',
            'fa-solid fa-face-smile-beam',
            'fa-regular fa-face-smile-beam',
            'fa-solid fa-face-smile-wink',
            'fa-regular fa-face-smile-wink',
            'fa-solid fa-face-surprise',
            'fa-regular fa-face-surprise',
            'fa-solid fa-face-tired',
            'fa-regular fa-face-tired',
        );

        return array_unique($icons);
    }

    public function icon_library_popup() {
        if ( !is_easy_sticky_sidebar_screen() ) {
            return;
        } ?>
        <div id="easy-sticky-sidebar-icon-library-popup">
            <div class="dialog-container">
                <header class="dialog-header">
                    <h3><?php esc_html_e('Icon Library', 'easy-sticky-sidebar'); ?></h3>
                    <span class="close dashicons dashicons-no-alt"></span>
                </header>
                <div class="dialog-content">
                    <?php
                    if ( !$this->has_library() ) {
                        printf('Fontawesome CSS file is not exists.');
                    }
                    
                    if ( $this->has_library() ) { ?>
                    <form class="form-search-icons">
                        <input type="text" placeholder="<?php esc_attr_e('Search icon...', 'easy-sticky-sidebar'); ?>">
                        <button class="fa-solid fa-magnifying-glass"></button>
                    </form>
                    <?php } ?>                   

                    <div class="easy-sticky-sidebar-icon-grid">
                        <?php
                        $this->get_icons();
                        foreach ($this->get_icons() as $icon_class) {
                            printf('<div class="icon"><span class="%1$s"></span> <i>%1$s</i></div>', esc_attr( $icon_class));
                        } ?>
                    </div>                
                </div>

                <footer class="dialog-footer">
                    <a class="button btn-add-icon btn-wordpress-cta-primary" href="#"><?php esc_html_e('Insert', 'easy-sticky-sidebar'); ?></a>
                </footer>
            </div>
        </div>
        <?php
    }
}
