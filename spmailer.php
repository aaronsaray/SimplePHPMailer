<?php
/**
 * Simple PHP Mailer
 *
 * @link http://aaronsaray.github.io/SimplePHPMailer Visit this URL for detailed instructions
 * @author Aaron Saray (http://aaronsaray.com)
 */
namespace AaronSaray\SimplePHPMailer;


/***********************************************************************************************************************
 * 
 *  **** SimplePHPMailer Settings ****
 *
 *  Please change these settings below to fit your needs before you use this file.  Remember, if you need additional help,
 *  http://aaronsaray.github.io/SimplePHPMailer has detailed instructions
 *
 ***********************************************************************************************************************/

/** --------------------------------------------------------------------------------------------------------------------
 *  "To" email address
 *
 *  Replace the to@address.com with the address you'd like the email to be sent to.  Do NOT remove the quotes.  Enter
 *  only an email address, no name or any other items.
 *  -------------------------------------------------------------------------------------------------------------------*/

define('AaronSaray\SimplePHPMailer\TO_EMAIL', "to@address.com");



/** --------------------------------------------------------------------------------------------------------------------
 *  "From" email address
 *
 *  Replace the from@address.com with the address you'd like the email to be sent from.  Do NOT remove the quotes.  Enter
 *  only an email address, no name or any other items.
 *  -------------------------------------------------------------------------------------------------------------------*/

define('AaronSaray\SimplePHPMailer\FROM_EMAIL', "from@address.com");



/** --------------------------------------------------------------------------------------------------------------------
 *  Email Subject
 *
 *  Enter a subject that you'd like the incoming email to have.  Or, you can leave it as is.  Do NOT remove the quotes.
 *  Refrain from using back slashes.  Do not include double quotes inside of your subject.
 *  -------------------------------------------------------------------------------------------------------------------*/

define('AaronSaray\SimplePHPMailer\SUBJECT', "Form submitted from the website");



/** --------------------------------------------------------------------------------------------------------------------
 *  Success URL
 *
 *  Replace this URL with the full URL for the form to redirect to after a submission.
 *  -------------------------------------------------------------------------------------------------------------------*/

define('AaronSaray\SimplePHPMailer\REDIRECT_URL', "http://www.example.com/success.html");



/** --------------------------------------------------------------------------------------------------------------------
 *  - DO NOT PROCEED PAST THIS LINE
 ** -------------------------------------------------------------------------------------------------------------------*/

if (!class_exists('AaronSaray\SimplePHPMailer\Mailer')) {

    /**
     * Class Mailer
     * @package AaronSaray\SimplePHPMailer
     */
    class Mailer
    {
        /**
         * @var string
         */
        const VERSION = '1.0.0';

        /**
         * @var array
         */
        protected $errors = array();

        /**
         * Mailer constructor.
         */
        public function __construct()
        {
            $this->validateFunctionality()
                &&
            $this->validateSettings();

            if (empty($this->errors)) {
                $this->sendMail()
                    &&
                $this->processRedirect();
            }

            self::displayError($this->errors);
        }

        /**
         * Validates functionality
         * @return boolean
         */
        protected function validateFunctionality()
        {
            if (!function_exists('mail')) {
                $this->errors['Mail Function'] = 'The mail function does not exist or is not available.';
            }

            return empty($this->errors);
        }

        /**
         * Validates that the user has added all the proper settings
         * @return boolean
         */
        protected function validateSettings()
        {
            /**
             * Validate to email
             */
            if (empty(TO_EMAIL)) {
                $this->errors['To E-mail Address'] = 'A "to" e-mail address is needed.';
            }
            else if (!$this->validateEmailAddress(TO_EMAIL)) {
                $this->errors['To E-mail Address'] = 'The "to" e-mail address is not valid.';
            }
            else if (TO_EMAIL == 'to@address.com') {
                $this->errors['To E-mail Address'] = 'The "to" e-mail address should be set.';
            }

            /**
             * Validate from email
             */
            if (empty(FROM_EMAIL)) {
                $this->errors['From E-mail Address'] = 'A "from" e-mail address is needed.';
            }
            else if (!$this->validateEmailAddress(FROM_EMAIL)) {
                $this->errors['From E-mail Address'] = 'The "from" e-mail address is not valid.';
            }
            else if (FROM_EMAIL == 'from@address.com') {
                $this->errors['From E-mail Address'] = 'The "from" e-mail address should be set.';
            }

            /**
             * Validate Redirect
             */
            if (empty(REDIRECT_URL)) {
                $this->errors['Finish Action'] = 'A success redirect URL is needed.';
            }
            else if (!$this->validateURL(REDIRECT_URL)) {
                $this->errors['Finish Action'] = 'The redirect URL is not valid.';
            }
            else if (REDIRECT_URL == 'http://www.example.com/success.html') {
                $this->errors['Finish Action'] = 'The success redirect URL should be set.';
            }

            return empty($this->errors);
        }

        /**
         * Sends the mail
         *
         * @return boolean
         */
        protected function sendMail()
        {
            $headers = sprintf("From: %s\r\nReply-To: %s\r\nX-Mailer: SimplePHPMailer %s (PHP %s)",
                FROM_EMAIL,
                FROM_EMAIL,
                self::VERSION,
                phpversion()
            );
            $parameters = '-f' . FROM_EMAIL;

            $body = "Hello\r\nSimplePHPMailer is sending a form with the following information:"
                . "\r\n-----------------------------------------------------------------\r\n";

            $body .= array_reduce(array_keys($_POST), function($postValue, $key) use ($_POST) {
                return $postValue . sprintf("%s: %s\r\n", $key, $_POST[$key]);
            });

            $body .= "-----------------------------------------------------------------\r\n";

            if (mail(TO_EMAIL, SUBJECT, $body, $headers, $parameters) !== true) {
                $this->errors['Sending Mail'] = 'There was an error sending the mail.';
            }

            return empty($this->errors);
        }

        /**
         * Process the redirect
         */
        protected function processRedirect()
        {
            die(header('Location: ' . REDIRECT_URL));
        }

        /**
         * Validates if valid e-mail address
         *
         * Uses the built in filter_var if it exists, otherwise not the most robust of error checking, but should
         * eliminate the most common of typos.
         *
         * @param string $email
         * @return boolean
         */
        protected function validateEmailAddress($email)
        {
            if (function_exists('filter_var')) {
                return boolval(filter_var($email, FILTER_VALIDATE_EMAIL));
            }
            else {
                $regex = '/^[A-Z0-9._%-]+@(?:[A-Z0-9-]+\.)+[A-Z]{2,4}$/i';
                return preg_match($regex, $email);
            }
        }

        /**
         * Validate if the URL is valid
         *
         * Uses the built in filter_var if it exists, otherwise uses a regular expression that should catch most
         * of the problems
         *
         * @param string $url
         * @return boolean
         */
        protected function validateURL($url)
        {
            if (function_exists('filter_var')) {
                return boolval(filter_var($url, FILTER_VALIDATE_URL));
            }
            else {
                $regex = '|[-\w\.]+://([-\w\.]+)+(:\d+)?(:\w+)?(@\d+)?(@\w+)?([-\w\.]+)(/([\w/_\.]*(\?\S+)?)?)?|';
                return preg_match($regex, $url);
            }
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
         * @param array $errors
         */
        public static function displayError(array $errors)
        {
            print '<ul style="background-color: #ffcccc;
                   border: 2px solid #ff9999; color: #000000; font-size: 18px;
                   font-weight: bold;">';
            foreach ($errors as $key => $description) {
                printf('<li>%s: %s</li>', $key, $description);
            }
            print '</ul>';
        }
    }

    new Mailer();
}
else {
    Mailer::displayError(array('Potential Error' => 'SimplePHPMailer already exists on this page. More than one instance is not supported.'));
}
