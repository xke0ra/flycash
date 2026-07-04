<?php

    include_once("../core/libraries/Hybridauth/autoload.php");
    include_once("../core/init.inc.php");
    
    use Hybridauth\Exception\Exception;
    use Hybridauth\Hybridauth;
    use Hybridauth\HttpClient;
    use Hybridauth\Storage\Session;
    
	$configs = new functions($dbo);
	
	// GOOGLE LOGIN
	$google_login_enabled = $configs->getConfig('GOOGLE_LOGIN_WEB');
	$google_clientId = $configs->getConfig('GOOGLE_CLIENT_ID');
	$google_secretId = $configs->getConfig('GOOGLE_SECRET_ID');
	
	// FACEBOOK LOGIN
	$facebook_login_enabled = $configs->getConfig('FACEBOOK_LOGIN_WEB');
	$facebook_appId = $configs->getConfig('FACEBOOK_APP_ID');
	$facebook_secretId = $configs->getConfig('FACEBOOK_SECRET_ID');
	
	$error = false;
	$user_email = 'none';
	$error_message = 'Invalid Oauth Token';
	$auth_provider = '';
	
	$config = [
        'callback' => HttpClient\Util::getCurrentUrl(),
        'providers' => [
            'Google' => [
              'enabled' => $google_login_enabled,
              'keys' => [
                'id' => $google_clientId,
                'secret' => $google_secretId,
              ],
            ],
            'Facebook' => [
              'enabled' => $facebook_login_enabled,
              'keys' => [
                'id' => $facebook_appId,
                'secret' => $facebook_secretId,
              ],
            ],
          ],
        ];

    try{
        
        // Feed configuration array to Hybridauth.
        $hybridauth = new Hybridauth($config);
    
        // Initialize session storage.
        $storage = new Session();
    
        // Hold information about provider when user clicks on Sign In.
        if (isset($_REQUEST['provider'])) {
            $storage->set('provider', $_REQUEST['provider']);
        }
    
        // When provider exists in the storage, try to authenticate user and clear storage.
        if ($auth_provider = $storage->get('provider')) {
            $user_data = $hybridauth->authenticate($auth_provider);
            $storage->set('provider', null);
            
            $user_profile = $user_data->getUserProfile();
            
            if($auth_provider === "Google" || $auth_provider === "Facebook"){
                
                $user_email = $user_profile->email;
                $user_fullname = $user_profile->firstName." ".$user_profile->lastName;
                $user_identifier = $user_profile->identifier;
                $user_image = $user_profile->photoURL;
                
            }else{
                //error_log(json_encode($user_profile));
            }
            
            // This will erase the current user authentication data from session, and any further attempt to communicate with provider.
            $adapter = $hybridauth->getAdapter($auth_provider);
            $adapter->disconnect();
            
        }
    
    }catch (Exception $e) {
        //echo $e->getMessage();
        
        $error = true;
        $error_message = 'Got an error : '.$e->getMessage();
        
    }
    
    function get_u($ue){
        
        $username = explode('@',$ue);
        $username = $username[0];
        
        return strtolower(str_replace('.', '', substr($username, 0, 10)));
    }
    
    if($user_email !== 'none'){
        
        $clientId = 0; // Desktop version
        $username = get_u($user_email);
        
        $access_data = array();
        $user = new account($dbo);
        
        $user_referer = isset($_SESSION["refererCode"]) ? $_SESSION["refererCode"] : '';
        
        if ($helper->isEmailExists($user_email)) {
            
            // LOGIN IN
            $access_data = $user->signin($user_email, $username);
            
            if ($access_data['error'] === false){
                
                account::createAccessToken();
                account::setSession($access_data['accountId'], account::getAccessToken());
                
            }else{
                
                $error = true;
                $error_message = 'user signIn failed, please try again';
            }
            
        }else{
            
            // REGISTER
            $access_data = $user->signup($username, $user_fullname, $username, $user_email, $user_referer, $user_image, $auth_provider);
            
            if($access_data['error'] === false){
                
                account::createAccessToken();
                account::setSession($access_data['accountId'], account::getAccessToken());
                
            }else{
                
                $error = true;
                $error_message = 'user signup failed, please try manual signup';
            }
            
        }
    }
    
    
    if($error){
        
        $_SESSION['login_error'] = $error;
        $_SESSION['login_message'] = $error_message;
    }
    
    // Redirects user to the user dashboard
    HttpClient\Util::redirect('../../dashboard/');
    
    exit();
    
?>