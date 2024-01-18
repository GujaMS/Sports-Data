<?php
/**
 * Plugin Name: Sports Data
 * Description: Enhance your WordPress site with real-time sports data, configurable from the admin panel. Explore dynamic visualizations and stay updated on your favorite sports.
 * Version: 0.1
 * Author: Guja.M
 **/

class SportsDataPlugin {
    private $api_url;
    private $api_headers;

    public function __construct() {
        $this->api_url = 'https://livescore-sports.p.rapidapi.com/v1/events/live?locale=EN&timezone=0&sport=';
        $this->api_headers = array(
            'X-RapidAPI-Host' => 'livescore-sports.p.rapidapi.com',
            'X-RapidAPI-Key' => '4edc53e372mshd8e5106ad859245p1b8432jsn7ab26aaa07b3',
        );

        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_get_live_data', array($this, 'get_live_data'));
        add_action('wp_ajax_nopriv_get_live_data', array($this, 'get_live_data'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        add_shortcode('sports_data', array($this, 'render_sports_data_shortcode'));
    }

    public function render_sports_data_shortcode() {
        $data = $this->get_sports_data();
        $sections = $this->process_sports_data($data);
        ob_start();
        $this->render_sports_data($sections, $data);
        return ob_get_clean();
    }

    public function add_admin_menu() {
        add_menu_page(
            'Sports Data Plugin Settings',
            'Sports Data',
            'manage_options',
            'sports-data-settings',
            array($this, 'render_settings_page'),
            'dashicons-editor-contract'
        );
    }

    public function render_settings_page() {
        echo '<div class="wrap"><h1>Sports Data Plugin Settings</h1></div>';
        $data = $this->get_sports_data();


        $sections = $this->process_sports_data($data);
        $this->render_sports_data($sections, $data);

        echo '</div>';
    }

    public function get_live_data() {
        $selected_sport = sanitize_text_field($_POST['sport']);
        $response = wp_remote_get($this->api_url . $selected_sport, array('headers' => $this->api_headers));

        if (is_wp_error($response)) {
            echo 'Error fetching live data';
        } else {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body);
            $sections = $this->process_sports_data($data);
            $this->render_sports_data($sections, $data);
        }

        wp_die();
    }

    private function get_sports_data($selected_sport = 'tennis') {
        $response = wp_remote_get($this->api_url . $selected_sport, array('headers' => $this->api_headers));
    
        if (is_wp_error($response)) {
            return false;
        }
    
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);
    
        if (isset($data->detail) && is_array($data->detail)) {
            // Handle API errors here
            foreach ($data->detail as $error) {
                // Output or log the error messages
                echo 'API Error: ' . $error->msg;
            }
    
            return false;
        }
    
        return $data;
    }
    
    

    private function process_sports_data($data) {
        $sections = array();
    
        if (!empty($data->DATA)) {
            
            foreach ($data->DATA as $item) {
                $stage_name = $item->STAGE_NAME;
                $country_name = $item->COUNTRY_NAME;
    
                if (!array_key_exists($country_name, $sections)) {
                    $sections[$country_name] = [];
                }
    
                if (!in_array($stage_name, $sections[$country_name])) {
                    $sections[$country_name][] = $stage_name;
                }
            }
        }
        return $sections;
    }
    

    private function render_sports_data($sections, $data) {
        
        echo '<div class="wrap">';
        echo '<form id="sportSelectorForm">';
        echo '<label for="sportSelector">Select Sport:</label>';
        echo '<select id="sportSelector" name="sport">';
        $sports = ['tennis', 'soccer', 'basketball', 'cricket', 'hockey'];
        echo '<option disabled selected>Select Sport</Option>';
        foreach ($sports as $sport) {
            $selected = $sport === 'tennis' ? "selected" : "";
            $capSport = ucfirst($sport);
            echo "<option value=\"$sport\" $selected>$capSport</option>";
        }
        echo '</select>';
        echo '<button type="button" id="getLiveDataButton">Get Live Data</button>';
        echo '</form>';
        if (!empty($sections)) { ?>
            <div id="sportsData" class="sporst-data">
                <?php foreach ($sections as $section => $stages): ?>
                    <div class="table">
                        <div class="country-name"><?= $section ?></div>
                        <?php foreach ($stages as $stage): ?>
                            <div class="stage-name"><?= $stage ?></div>
                            <div class="games">
                                <?php foreach ($data->DATA as $item): ?>
                                    <?php $matchId = $item->EVENTS[0]->MATCH_ID; ?>
                                    <?php if ($item->COUNTRY_NAME === $section && $item->STAGE_NAME === $stage): ?>
                                        <div class="match">
                                        <button class="add-to-database" data-match-id="<?php echo esc_attr($matchId) ?>">Save ID</button>
                                            <div class="team-home">
                                                <?php foreach ($item->EVENTS[0]->HOME_TEAM as $team_name): ?>
                                                    <span>
                                                        <?php if (isset($team_name->BADGE_SOURCE)): ?>
                                                            <img src="<?= $team_name->BADGE_SOURCE ?>" alt="Badge">
                                                        <?php endif; ?>
                                                        <?= $team_name->NAME ?>
                                                    </span>
                                                <?php endforeach ?>
                                            </div>
                                            <div class="score-time">
                                                <span>
                                                    <?= (isset($item->EVENTS[0]->HOME_SCORE) ? $item->EVENTS[0]->HOME_SCORE : '') . ' : ' . (isset($item->EVENTS[0]->AWAY_SCORE) ? $item->EVENTS[0]->AWAY_SCORE : '') ?>
                                                </span>
                                                <span>
                                                    <?= $item->EVENTS[0]->MATCH_STATUS ?>
                                                </span>
                                            </div>
                                            <div class="team-away">
                                                <?php foreach ($item->EVENTS[0]->AWAY_TEAM as $team_name): ?>
                                                    <span>
                                                        <?php if (isset($team_name->BADGE_SOURCE)): ?>
                                                            <img src="<?= $team_name->BADGE_SOURCE ?>" alt="Badge">
                                                        <?php endif; ?>
                                                        <?= $team_name->NAME ?>
                                                    </span>
                                                <?php endforeach ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php
        } else {
            echo '<div class="no-results">No match is being played right now.</div>';
        }
    }
    

    public function enqueue_scripts() {
        wp_enqueue_script('sports-data-script', plugin_dir_url(__FILE__) . 'sports-data-script.js', array('jquery'), '1.0', true);
        wp_localize_script('sports-data-script', 'ajaxurl', admin_url('admin-ajax.php'));  
        wp_enqueue_style('sports-data-style', plugin_dir_url(__FILE__) . 'sports-data-style.css', array(), '1.0', 'all');
    }

    public function enqueue_styles() {
        wp_enqueue_style('sports-data-style', plugin_dir_url(__FILE__) . 'sports-data-style.css', array(), '1.0', 'all');
    }
}

new SportsDataPlugin();


register_activation_hook(__FILE__, 'sports_data_plugin_activate');

function sports_data_plugin_activate() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'sports_match_ids';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        match_id varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

add_action('wp_ajax_store_match_id', 'store_match_id');

function store_match_id() {
    global $wpdb;

    $matchId = sanitize_text_field($_POST['match_id']);

    $table_name = $wpdb->prefix . 'sports_match_ids';

    $wpdb->insert(
        $table_name,
        array(
            'match_id' => $matchId,
        )
    );


    wp_die();
}
