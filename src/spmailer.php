<?php
/**
 * Simple PHP Mailer
 * 
 * Simple PHP Mailer is a drop in script to help mail forms from a website.
 * 
 * A small amount of configuration is required.  Directions are enclosed
 * in ****.  Configuration options are surrounded by ####.
 *
 * @author Aaron Saray (http://aaronsaray.com)
 * @package SimplePHPMailer
 */
if (!class_exists('SimplePHPMailer')) {
    /**
     * Simple PHP Mailer
     * 
     * Main Class for Simple PHP Mailer
     * @author Aaron Saray (http://aaronsaray.com)
     * @package SimplePHPMailer
     */
    class SimplePHPMailer 
    {
    
        /***********************************************************************
         * CONFIGURATION OPTIONS START HERE  !!!!!!!!!!!!!!!!!!!!!!!!!
         * 
         * The following are sets of configuration options that you can change 
         * to get the mailer to work how you expect it to.  The options are 
         * explained and then you have a set of values to choose from.
         * 
         * Keep all values between the double quotes (").  Do not remove any
         * of the code.
         * 
         * Correct: 
         * $something = "my stuff I typed";
         * 
         * Incorrect:
         * $something = my stuff I typed;  <-- no quotes
         * $something = "my stuff I typed"  <-- no semi colons.
         * 
         * HELPFUL TIP: Each setting is surrounded by a bar of #####
         **********************************************************************/
    
        /**
         * "to" e-mail address.
         * 
         * Please fill in the e-mail address that should receive this e-mail.  
         * 
         * Ex: protected $_toEmail = "myname@myaddress.com";
         * 
         * @var string
         */
        ########################################################################
        protected $_toEmail = "me@example.com"; 
        ########################################################################
        
        /**
         * "from" e-mail address.
         * 
         * Please fill in the e-mail address that this e-mail should originate
         * from.
         * 
         * Ex: protected $_fromEmail = "spmailer@myaddress.com";
         * or: protected $_fromEmail = "myEmail@address.com";
         * 
         * @var string
         */
        ########################################################################
        protected $_fromEmail = "me@example.com";
        ########################################################################
        
        /**
         * Subject line of e-mail
         * 
         * Please fill in the subject line that you'd like this e-mail to have.
         * 
         * Ex: protected $_subject = "Form submitted from our site";
         * or: protected $_subject = "You received a new contact form!";
         * 
         * @var string
         */
        ########################################################################
        protected $_subject = "Form submitted from our site";
        ########################################################################
        
        /**
         * form submit method
         * 
         * When you created your form, the form tag has an attribute called
         * 'method'.  There are two possible values for that, 'get' or 'post'.
         * 
         * Please fill in either get or post for this setting.  If you didn't
         * put a 'method' tag, you should enter 'get' into this setting (it is
         * recommended that you go back and fix your form to add a 'method'
         * attribute, however.  'post' usually is the preferred method.)
         * 
         * Ex: protected $_formMethod = "get";
         * or: protected $_formMethod = "post";
         * 
         * @var string
         */
        ########################################################################
        protected $_formMethod = "post";
        ########################################################################
        
        
        /**
         * When the form is successfully e-mailed, what should we do next?
         * 
         * You have three options.  
         * 1) Print a message to the screen that you define below.
         * 2) Don't have SimplePHPMailer do anything - you'll make your own HTML
         * 3) Redirect to a success page that has a custom message.  *RECOMMENDED*
         * 
         * The finishAction setting can handle all of these options.
         * If you'd like it to print a message to the screen, the value should
         * be 'message'.  If you'd like it to redirect, 'redirect'.  If you'd like
         * it to do nothing, 'nothing'.
         * 
         * For 'message' or 'redirect', you'll configure them later.
         * 
         * Ex: protected $_finishAction = "nothing"; <-- SimplePHPMailer does nothing
         * or: protected $_finishAction = "redirect"; <-- will redirect to a new page
         * or: protected $_finishAction = "message"; <-- a message will appear to the user 
         * 
         * @var string
         */
        ########################################################################
        protected $_finishAction = "redirect";
        ########################################################################
        
        
        /**
         * Which page should we redirect on success?
         * 
         * YOU DO NOT NEED TO CHANGE THIS IF YOU HAVEN'T SET CHOSEN TO REDIRECT
         * YOUR PAGE.
         * 
         * This value should be a complete URL, such as http://www.example.com.
         * You should point to a page that you have created that has a message
         * saying that the form was submitted successfully.
         * 
         * Ex: protected $_redirectURL = "http://www.mysite.com/contact/success.html";
         * 
         * @var string
         */
        ########################################################################
        protected $_redirectURL = "http://www.example.com/success.html";
        ########################################################################
        
        
        /**
         * What message should we display to the user?
         * 
         * YOU DO NOT NEED TO CHANGE THIS IF YOU HAVEN'T CHOSEN TO PRINT A MESSAGE
         * TO THE SCREEN.
         * 
         * This value should be the message that you want to print out to the 
         * visitor on successful form submission. 
         * 
         * DO NOT USE $ OR " IN YOUR MESSAGE.
         * HTML is supported.
         * 
         * Ex: protected $_successMessage = "Thanks!  We've received your message.";
         * 
         * @var string
         */
        ########################################################################
        protected $_successMessage = "Example.com thanks you!";
        ########################################################################
        
		
        /**
         * ADVANCED: What field names should be required and not empty?
         * 
         * Preventing blank forms should happen at both the form side and the PHP side.
         * You may want to use javascript to validate your forms before they are submitted.
         * Here, you can specify specific fields that should not be empty.  Then, you can 
         * put the error you'd like.  This is done by using the => pointer.  
         * 
         * Put the name of the input field first, then => and then the error message.
         * 
         * Ex: protected $_requiredItems = array("firstname"=>"Please enter your first name");
         * Ex: protected $_requiredItems = array("city"=>"Please choose a city", 
         * 										 "state"=>"Please enter your state");
         * 
         * @var array
         */
        ########################################################################
        protected $_requiredItems = array();
        ########################################################################
        
        
        
        
        /***********************************************************************        
         * End Configuration.
         * DO NOT GO ANY FURTHER
         **********************************************************************/    

        /**
         * Version
         * @var string
         */
        protected $_version = '0.2.0';
        
        
        
        /**
         * Constructor: On instantiation, runs the mail
         */
        public function __construct()
        {
            $errors = $this->_validateFunctionality();
            
            if (empty($errors)) {
                $errors = $this->_validateSettings();
            }
            
            if (empty($errors)) {
            	$errors = $this->_validateRequired();
            }
            
            if (!empty($errors)) {
                /** error condition **/
                SimplePHPMailer::displayError($errors);
            }
            else {
                /** looks good to take action **/
                $this->_sendMail();
                $this->_applyFinishAction();
            }
        }
        
        /**
         * Checks for specific functions to exist
         * 
         * @return array
         */
        protected function _validateFunctionality()
        {
            $errors = array();
            
            /**
             * note on function checking.
             * http://us.php.net/manual/en/function.function-exists.php#67947
             * Says that we can use this to determine if the function was disabled
             * in the ini.
             * http://us.php.net/manual/en/function.function-exists.php#77980
             * says this will not defend against suhosin blacklist tho... but
             * I figure thats probably not that common
             */
            
            /**
             * check for mail
             */
            if (!function_exists('mail')) {
                $errors['Mail Function'] = 'The mail function does not exist or is not available.';
            }
            
            return $errors;
        }
        
        /**
         * Validate Setting Options
         * 
         * This function is used to validate all of the settings for the user
         * 
         * @return mixed
         */
        protected function _validateSettings()
        {
            $errors = array();
            
            /**
             * Validate to email
             */
            if (empty($this->_toEmail)) {
                $errors['To E-mail Address'] = 'A "to" e-mail address is needed.'; 
            }
            else if (!$this->_validateEmailAddress($this->_toEmail)) {
                $errors['To E-mail Address'] = 'The "to" e-mail address is not valid.';
            }
            else if ($this->_toEmail == 'me@example.com') {
                $errors['To E-mail Address'] = 'The "to" e-mail address should be set.';
            }
            
            /**
             * Validate from email
             */
            if (empty($this->_fromEmail)) {
                $errors['From E-mail Address'] = 'A "from" e-mail address is needed.'; 
            }
            else if (!$this->_validateEmailAddress($this->_fromEmail)) {
                $errors['From E-mail Address'] = 'The "from" e-mail address is not valid.';
            }
            else if ($this->_fromEmail == 'me@example.com') {
                $errors['From E-mail Address'] = 'The "from" e-mail address should be set.';
            }
            
            /**
             * validate post method
             */
            $this->_formMethod = trim(strtolower($this->_formMethod));
            if ($this->_formMethod != 'post' && $this->_formMethod != 'get') {
                $errors['Form Submit Method'] = 'The form submit method is not valid.'; 
            }
            
            /**
             * Validate Finish Action
             */
            $errors = array_merge($errors, $this->_validateFinishAction());
            
            return $errors;
        }
        
        /**
         * Verify required items exist
         * 
         * This method verifies that the required items exist. If they do not, they are
         * populated with an error.
         * 
         * @return array
         */
        protected function _validateRequired()
        {
        	$errors = array();
        	
        	if (!empty($this->_requiredItems)) {
        		$type = $this->_formMethod == 'post' ? '_POST' : '_GET';
	        	foreach ($this->_requiredItems as $item=>$error) {
	        		if (empty($type[$item])) $errors[$item] = $error;
	        	}
        	}
        	
        	return $errors;
        }
        
        /**
         * Validate the finish action
         * 
         * Determines if the proper values are set for a finishing action
         * the user has chosen
         *
         * @return array
         */
        protected function _validateFinishAction()
        {
            $errors = array();
            
            $this->_finishAction = trim(strtolower($this->_finishAction));
            if ($this->_finishAction != 'redirect' && $this->_finishAction != 'message' && $this->_finishAction != 'nothing') {
                $errors['Finish Action'] = 'The "finish action" is not valid.';
            }
            else {
                /** validate the setting for the specified finish action **/
                switch ($this->_finishAction) {
                    case 'message':
                        if (empty($this->_successMessage)) {
                            $errors['Finish Action'] = 'A success message is needed.';
                        }
                        else if ($this->_successMessage == "Example.com thanks you!") {
                            $errors['Finish Action'] = 'The success message should be set.';
                        }
                        break;
                        
                    case 'redirect':
                        if (empty($this->_redirectURL)) {
                            $errors['Finish Action'] = 'The redirect URL is needed.';
                        }
                        else if ($this->_redirectURL == "http://www.example.com/success.html") {
                            $errors['Finish Action'] = 'The redirect URL should be set.';
                        }
                        else {
                            /** not the greatest match, but should catch simple issues, I hope **/
                            $regex = '|[-\w\.]+://([-\w\.]+)+(:\d+)?(:\w+)?(@\d+)?(@\w+)?([-\w\.]+)(/([\w/_\.]*(\?\S+)?)?)?|';
                            if (!preg_match($regex, $this->_redirectURL)) {
                                $errors['Finish Action'] = 'The redirect URL is not valid.';
                            }
                        }
                        break;
                        
                    /** no case for nothing **/
                }
            }
            
            return $errors;
        }
        
        
        /**
         * Used to send mail 
         */
        protected function _sendMail()
        {
            $headers = $this->_generateHeaders();
            $body = $this->_generateBody();
            $mail = mail($this->_toEmail, $this->_subject, $body, $headers);
            
            if ((bool)$mail !== true) {
                SimplePHPMailer::displayError("There was an error sending the mail.");
                die(); /** normally I don't make die() but this one is serious **/
            }
        }
        
        /**
         * Generates the body of the message
         * 
         * @return string
         */
        protected function _generateBody()
        {
            $body = "Hello\r\nSimplePHPMailer is sending a form with the following information:"
                  . "\r\n-----------------------------------------------------------------\r\n";
                  
            if ($this->_formMethod == 'post') {
                foreach ($_POST as $key=>$value) {
                    $body .= "{$key}: {$value}\r\n";
                }
            }
            else {
                foreach ($_GET as $key=>$value) {
                    $body .= "{$key}: {$value}\r\n";
                }
            }
                              
            $body .= "\r\n";
            
            return $body;
        }
        
        /**
         * Generates the headers for the application
         *
         * @return string
         */
        protected function _generateHeaders()
        {
            $headers = "From: {$this->_fromEmail}\r\nReply-To: {$this->_fromEmail}\r\n"
                     . "X-Mailer: SimplePHPMailer({$this->_version}) (PHP: " . phpversion() . ")";
                     
            return $headers;
        }
        
        /**
         * Finishing action.
         * 
         * Depending on the configuration, this will either send the user
         * to a page or display a message.
         */
        protected function _applyFinishAction()
        {
            switch ($this->_finishAction) {
                case 'redirect':
                    die(header("Location: {$this->_redirectURL}"));
                    break;
                    
                case 'message':
                    echo $this->_successMessage;
                    break;
            }
        }
        
        
        /**
         * Validates if valid e-mail address
         * 
         * Not the most robust of error checking, but should eliminate the 
         * most common of type-os.
         *
         * @param string $email
         * @return boolean
         */        
        protected function _validateEmailAddress($email)
        {
            $regex = '/^[A-Z0-9._%-]+@(?:[A-Z0-9-]+\.)+[A-Z]{2,4}$/i';
            return preg_match($regex, $email);
        }
        
        /**
         * Displays the error to the screen.  
         * 
         * This is only used when there is an 'error' that is caused by the 
         * usage of this script, not necessarily what the user may have entered
         * into the form.  
         * 
         * Including the CSS because we're only using one file here.
         *
         * @param mixed $error
         */
        public static function displayError($error)
        {
            print '<div style="background-color: #ffcccc; 
                   border: 2px solid #ff9999; color: #000000; font-size: 18px; 
                   font-weight: bold; padding: 5px;">' . "\n";
            
            $error = (array)$error;
            
            foreach ($error as $description=>$message) {
                print "{$description}: {$message}<br />\n";
            }
            
            print "\n</div>";
        }
        
    }
    
    new SimplePHPMailer;
}
else {
    /**
     * error when the class is already defined.
     */
    SimplePHPMailer::displayError("SimplePHPMailer already exists on this page.  
                                   Having two instances on a single page may cause 
                                   unforseen configuration issues.");
}
?>