<div class="wrap">
    <h1>Rently Newsletter Settings</h1>
    
    <form method="post" action="options.php">
        <?php settings_fields('rently_newsletter_settings'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="rently_mailchimp_enabled">Enable Mailchimp Integration</label>
                </th>
                <td>
                    <input 
                        type="checkbox" 
                        id="rently_mailchimp_enabled" 
                        name="rently_mailchimp_enabled" 
                        value="1" 
                        <?php checked(get_option('rently_mailchimp_enabled'), 1); ?>
                    />
                    <p class="description">Enable to sync subscribers with Mailchimp</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="rently_mailchimp_api_key">Mailchimp API Key</label>
                </th>
                <td>
                    <input 
                        type="text" 
                        id="rently_mailchimp_api_key" 
                        name="rently_mailchimp_api_key" 
                        value="<?php echo esc_attr(get_option('rently_mailchimp_api_key')); ?>" 
                        class="regular-text"
                    />
                    <p class="description">Get your API key from Mailchimp Account > Extras > API keys</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="rently_mailchimp_list_id">Mailchimp List ID</label>
                </th>
                <td>
                    <input 
                        type="text" 
                        id="rently_mailchimp_list_id" 
                        name="rently_mailchimp_list_id" 
                        value="<?php echo esc_attr(get_option('rently_mailchimp_list_id')); ?>" 
                        class="regular-text"
                    />
                    <p class="description">Find your List ID in Mailchimp under Audience > Settings > Audience name and defaults</p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
    
    <hr>
    
    <h2>Subscribers</h2>
    <?php
    global $wpdb;
    $subscribers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rently_subscribers ORDER BY subscribed_at DESC LIMIT 50");
    ?>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Email</th>
                <th>Status</th>
                <th>Source</th>
                <th>Subscribed At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($subscribers)): ?>
                <tr>
                    <td colspan="4">No subscribers yet</td>
                </tr>
            <?php else: ?>
                <?php foreach ($subscribers as $subscriber): ?>
                    <tr>
                        <td><?php echo esc_html($subscriber->email); ?></td>
                        <td><?php echo esc_html($subscriber->status); ?></td>
                        <td><?php echo esc_html($subscriber->source); ?></td>
                        <td><?php echo esc_html($subscriber->subscribed_at); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
