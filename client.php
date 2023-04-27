<?php

/**
 * Client class
 * 
 * Everything about login is in constructor, look at him and you will understand everything
 */

class Client {

    const API_URL = 'https://symfony-skeleton.q-tests.com/';
    // make access token expire in 30min.
    const TOKEN_EXPIRE = 1800;

    
    public function __construct() {
        
        if ( !$this->logged_in() ){
        // not logged in, try to login using password and refresh token
            $this->login_using_pass();
            $this->login_using_refresh();    
        }

        // Maybe logout
        if( isset($_POST['submit']) && $_POST['submit'] == 'Logout' ) {
            $this->remove_cookies();
            $this->logged_in = false; // not needed, just to be sure
            header('Location: /client.php'); 
            exit;
        }

        // now logged in

        // we set the user here, only first time after password login
        if(empty($this->user))
            $this->set_user();
    }
        
        
    
    
    
    // Helper variable to avoid infinite requests to API
    public $logged_in = false;
    
    // User from API, set after login
    private $user = null;
    private $access_token = null;
    private $ref_token = null;





    /**
     * Check if user is logged
     * it is logged if has access token and it is not expired and user can be set
     * @return bool
     */
    private function logged_in() {

        if( !isset($_COOKIE['access-token']) ) 
            return false;
            
        if( empty( $_COOKIE['access-token-expiration'] ) || (int)$_COOKIE['access-token-expiration'] < time() ) 
            return false;
            
        if( $this->set_user() == false )
            return false;
            
        $this->logged_in = true;
        return true;

    }



    /**
     * Set user from API
     * @return bool
     */

    private function set_user() {
         
        if( isset($_COOKIE['access-token']) )
            $this->access_token = $_COOKIE['access-token'];

        $this->logged_in = true; // temp login to check if we can get the user
        
        $response = json_decode( $this->get('api/v2/me'), true);
        if( isset($response['status']) && $response['status'] != 200 ){
            $this->logged_in = false; // logout
            return false;
        }

        $this->user = $response;
        return true;
    }




    /**
     * Login using password
     * @return void
     */
    private function login_using_pass(){

        if( !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['submit']) )
            return;

        $data = [
            'email'     => $_POST['email'],
            'password'  => $_POST['password']
        ];
        
        $response = json_decode( $this->post('api/v2/token', $data), true);

        if( isset($response['status']) && $response['status'] != 200 ) {
            echo 'Error: could not login to API using password!!';
            return false;
        }
        
        $token_expire = time() + self::TOKEN_EXPIRE;
        $this->set_login_cookies($response['token_key'], $token_expire, $response['refresh_token_key']);

        $this->logged_in = true;
        return true;
    }



    /**
     * Login using refresh token
     * @return bool
     */
    private function login_using_refresh(){

        if( !isset($_COOKIE['refresh-token']) || $this->logged_in )
            return false;

        $response = json_decode( $this->get('api/v2/token/refresh/' . $_COOKIE['refresh-token'] ), true);

        if( isset($response['status']) && $response['status'] != 200 ) {
            echo 'Bad refresh token!!';
            return false;
        }

        $token_expire = time() + self::TOKEN_EXPIRE;
        $this->set_login_cookies($response['token_key'], $token_expire, $response['refresh_token_key']);

        $this->logged_in = true;
        return true;
    }


    /**
     * Get user name
     * @return string
     */
    public function get_user_name() {
        return $this->user['first_name'].' '.$this->user['last_name'];
    }



    /*********** Helper functions ************/



    /**
     * Make GET request to API
     * @param string $endpoint
     * @return string
     */
    private function get($endpoint) {
        $curl = curl_init(self::API_URL . $endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        
        
        if( $this->logged_in )
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer '.$this->access_token
            ]);

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }


    /**
     * Make POST request to API
     * @param string $endpoint
     * @param array $data
     * @return string
     */
    private function post($endpoint, $data) {
        $curl = curl_init(self::API_URL . $endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        curl_setopt($curl, CURLOPT_HEADER, 'Content-Type: application/json');
        
        if( $this->logged_in )
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer '.$this->access_token
            ]);

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    
    /**
     * Set login cookies - just a simple way to set cookies - helper function
     * @param string $acc_token
     * @param int $token_expire
     * @param string $ref_token, has fixed expiration date of 2 months
     * @return void
     */
    private function set_login_cookies($acc_token, $token_expire, $ref_token){
        $this->access_token = $acc_token;
        $this->ref_token = $ref_token;
        setcookie('access-token', $acc_token, $token_expire, '/', '', false, true);
        setcookie('access-token-expiration', $token_expire, $token_expire, '/', '', false, true);
        setcookie('refresh-token', $ref_token, strtotime('+2 months'), '/', '', false, true);
    }

    private function remove_cookies(){
        $this->access_token = $this->ref_token = null;
        setcookie('access-token', '', time() - 3600, '/', '', false, true);
        setcookie('access-token-expiration', '', time() - 3600, '/', '', false, true);
        setcookie('refresh-token', '', time() - 3600, '/', '', false, true);
    }

}



$client = new Client();


?>

<html>
    <head>
        <title>Hi from the Client</title>
    </head>
    <body>
        <form method="post">
        
        <?php if( $client->logged_in ): ?>

            <h4>Hello, <?= $client->get_user_name(); ?> you are Logged in</h4>
            <input type="submit" name="submit" value="Logout">

        <?php else: ?>

            <h4>Please Login</h4>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" name="submit" value="Login">

        <?php endif; ?>
                
        </form>
    </body>
</html>