<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */
	
	include_once("../core/init.inc.php");

    if (!admin::isSession()) {

        header("Location: ../login.php");
		exit;
    }else if(!empty($_POST) && !APP_DEMO){
		
		if (!helper::verifyCsrfToken($_POST['csrf_token'] ?? '')) { header("Location: ../index.php"); exit; }
        
        $settings = new settings($dbo);
		$save = new functions($dbo);
		
		$appname = $_POST['appname'];
		$tagline = $_POST['tagline'];
		$webpanel_url = $_POST['webpanel_url'];
		$result = $settings->saveSettings($appname, $tagline, $webpanel_url);
		
		$support_email = $_POST['support_email'];
		$result = $save->updateConfigs($support_email, 'SUPPORT_EMAIL');
		
		$policy_url = $_POST['policy_url'];
		$result = $save->updateConfigs($policy_url, 'APP_POLICY_URL');
		
		$terms_url = $_POST['terms_url'];
		$result = $save->updateConfigs($terms_url, 'APP_TERMS_URL');
		
		$contact_url = $_POST['contact_url'];
		$result = $save->updateConfigs($contact_url, 'APP_CONTACT_US_URL');
		
		$package = $_POST['package'];
		$result = $save->updateConfigs($package, 'PACKAGE_NAME');
		
		$company_name = $_POST['company_name'];
		$result = $save->updateConfigs($company_name, 'COMPANY_NAME');
		
		$company_site = $_POST['company_site'];
		$result = $save->updateConfigs($company_site, 'COMPANY_SITE');
		
		$company_phone = $_POST['company_phone'];
		$result = $save->updateConfigs($company_phone, 'SUPPORT_PHONE');
		
		$company_email = $_POST['company_email'];
		$result = $save->updateConfigs($company_email, 'COMPANY_EMAIL');
		
		$country = $_POST['country'];
		$result = $save->updateConfigs($country, 'COMPANY_COUNTRY');
		
		$income_activate = $_POST['income_activate'];
		$result = $save->updateConfigs($income_activate, 'INCOME_OVERVIEW');
		
		$admin_ratio = $_POST['admin_ratio'];
		$result = $save->updateConfigs($admin_ratio, 'ADMIN_RATIO');
		
		$firebase_key = $_POST['firebase_key'];
		$result = $save->updateConfigs($firebase_key, 'FIREBASE_API_KEY');
		
		$navbar_enable = $_POST['navbar_enable'];
		$result = $save->updateConfigs($navbar_enable, 'APP_NAVBAR_ENABLE');
		
		$tabs_enable = $_POST['tabs_enable'];
		$result = $save->updateConfigs($tabs_enable, 'APP_TABS_ENABLE');
		
		$apioffers_enable = $_POST['apioffers_enable'];
		$result = $save->updateConfigs($apioffers_enable, 'API_OFFERS_ACTIVE');
		
		$tn_prefix = $_POST['tn_prefix'];
		$result = $save->updateConfigs($tn_prefix, 'TRANSACTION_PREFIX');
		
		$credit_prefix = $_POST['credit_prefix'];
		$result = $save->updateConfigs($credit_prefix, 'TRANSACTION_CREDIT_PREFIX');
		
		$debit_prefix = $_POST['debit_prefix'];
		$result = $save->updateConfigs($debit_prefix, 'TRANSACTION_DEBIT_PREFIX');
		
		// SMTP
		
		$smtp_email = $_POST['smtp_email'];
		$result = $save->updateConfigs($smtp_email, 'SMTP_EMAIL');
		
		$smtp_host = $_POST['smtp_host'];
		$result = $save->updateConfigs($smtp_host, 'SMTP_HOST');
		
		$smtp_secure = $_POST['smtp_secure'];
		$result = $save->updateConfigs($smtp_secure, 'SMTP_SECURE');
		
		$smtp_port = $_POST['smtp_port'];
		$result = $save->updateConfigs($smtp_port, 'SMTP_PORT');
		
		$smtp_auth = $_POST['smtp_auth'];
		$result = $save->updateConfigs($smtp_auth, 'SMTP_AUTH');
		
		$smtp_username = $_POST['smtp_username'];
		$result = $save->updateConfigs($smtp_username, 'SMTP_USERNAME');
		
		$smtp_password = $_POST['smtp_password'];
		$result = $save->updateConfigs($smtp_password, 'SMTP_PASSWORD');
		
		
		// OAOD
		$oaod_enable = $_POST['oaod_enable'];
		$result = $save->updateConfigs($oaod_enable, 'OAOD_CHECK');
		
		// NOTICE TEXTS
		$notice_redeem = $_POST['notice_redeem'];
		$result = $save->updateConfigs($notice_redeem, 'NOTICE_REDEEM');
		
		$notice_refer = $_POST['notice_refer'];
		$result = $save->updateConfigs($notice_refer, 'NOTICE_REFER_AND_EARN');
		
		$notice_transactions = $_POST['notice_transactions'];
		$result = $save->updateConfigs($notice_transactions, 'NOTICE_TRANSACTIONS');
		
		$sharetext = $_POST['sharetext'];
		$result = $save->updateConfigs($sharetext, 'APP_SHARE_TEXT');
		
		$recent_payouts_enable = $_POST['recent_payouts_enable'];
		$result = $save->updateConfigs($recent_payouts_enable, 'WEB_SHOW_RECENT_PAYOUTS');
		
		$feature_notice_enable = $_POST['feature_notice_enable'];
		$result = $save->updateConfigs($feature_notice_enable, 'WEB_SHOW_NEW_FEATURE_NOTICE');
		
		$announce_enable = $_POST['announce_enable'];
		$result = $save->updateConfigs($announce_enable, 'WEB_SHOW_ANNOUNCEMENT');
		
		$announce_text = $_POST['announce_text'];
		$result = $save->updateConfigs($announce_text, 'WEB_ANNOUNCEMENT_TEXT');
		
		
		// Google Login - WEB
		$google_login_enable = $_POST['google_login_enable'];
		$result = $save->updateConfigs($google_login_enable, 'GOOGLE_LOGIN_WEB');
		
		$google_login_clientid = $_POST['google_login_clientid'];
		$result = $save->updateConfigs($google_login_clientid, 'GOOGLE_CLIENT_ID');
		
		$google_login_secret = $_POST['google_login_secret'];
		$result = $save->updateConfigs($google_login_secret, 'GOOGLE_SECRET_ID');
		
		
		// Facebook Login - WEB
		$facebook_login_enable = $_POST['facebook_login_enable'];
		$result = $save->updateConfigs($facebook_login_enable, 'FACEBOOK_LOGIN_WEB');
		
		$facebook_login_appid = $_POST['facebook_login_appid'];
		$result = $save->updateConfigs($facebook_login_appid, 'FACEBOOK_APP_ID');
		
		$facebook_login_secret = $_POST['facebook_login_secret'];
		$result = $save->updateConfigs($facebook_login_secret, 'FACEBOOK_SECRET_ID');
		
		
		// AdBlocker Detection - WEB
		$adblock_enable = $_POST['facebook_login_enable'];
		$result = $save->updateConfigs($adblock_enable, 'ADBLOCK_WEB');
		
		$adblock_notice_title = $_POST['adblock_notice_title'];
		$result = $save->updateConfigs($adblock_notice_title, 'NOTICE_ADBLOCK_TITLE');
		
		$adblock_notice_text = $_POST['adblock_notice_text'];
		$result = $save->updateConfigs($adblock_notice_text, 'NOTICE_ADBLOCK');
		
		
		
		$android_app_share = $_POST['android_share_url'];
		$result = $save->updateConfigs($android_app_share, 'APP_SHARE_URL_ANDROID');
		
		$ios_app_share = $_POST['ios_share_url'];
		$result = $save->updateConfigs($ios_app_share, 'APP_SHARE_URL_IOS');
		
		// Handling Images
		require_once  "../core/class.imagehelper.php";
		
		// Dark Logo
		$image = new Imagehelper\Image($_FILES);
		$image->setSize(0, 5000000);
		$image->setMime(array('png', 'jpg','jpeg'));
		$image->setLocation("../images");
		
		$image->setName("logo_dark_".helper::generateRandomString(5));
			
		if($image["site_logo_dark_image"]){
			
			$upload = $image->upload();

			if($upload){
				$result = $save->updateConfigs($image->getName().'.'.$image->getMime(),'SITE_LOGO_DARK');
			}
			
		}
		
		// Light Logo
		$LightImage = new Imagehelper\Image($_FILES);
		$LightImage->setSize(0, 5000000);
		$LightImage->setMime(array('png', 'jpg','jpeg'));
		$LightImage->setLocation("../images");
		$LightImage->setName("logo_light_".helper::generateRandomString(5));
		
		if($LightImage["site_logo_light_image"]){
			$upload = $LightImage->upload();
			if($upload){
				
				$result = $save->updateConfigs($LightImage->getName().'.'.$LightImage->getMime(),'SITE_LOGO_LIGHT');
				
			}
		}
		
		// Favicon
		$Favicon = new Imagehelper\Image($_FILES);
		$Favicon->setSize(0, 5000000);
		$Favicon->setMime(array('png', 'jpg','jpeg'));
		$Favicon->setLocation("../images");
		$Favicon->setName("favicon_".helper::generateRandomString(5));
		
		if($Favicon["site_favicon_image"]){
			$upload = $Favicon->upload();
			if($upload){
				
				$result = $save->updateConfigs($Favicon->getName().'.'.$Favicon->getMime(),'SITE_FAVICON');
				
			}
		}
		
		if($result){
		    $save->logAudit(admin::getAdminID(), admin::getAdminUsername(), 'update_settings', 'Configuration', 'Updated application settings');
			header("Location: ../settings.php");
		exit;
		}else{
			
			header("Location: ../settings.php");
		exit;
		}
		
	}else{
		
		header("Location: ../settings.php");
		exit;
	}
	
	
	

?>